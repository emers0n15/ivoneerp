<?php
/**
 * Script de Importação Específico para CSV Monte Sinai
 * Importa diretamente do ficheiro CSV especificado
 */

session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit;
}

if($_SESSION['categoriaUsuario'] != "recepcao"){
    header("location:../admin/");
    exit;
}

include '../../conexao/index.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$userID = $_SESSION['idUsuario'];

// Caminho do ficheiro CSV
$caminho_csv = 'C:\\Users\\LOSE\\Desktop\\Clínica Médica Monte Sinai fidelidade para arranjos.csv';

// Verificar se ficheiro existe
if(!file_exists($caminho_csv)){
    die('Ficheiro CSV não encontrado no caminho: ' . htmlspecialchars($caminho_csv));
}

// Verificar se empresa Monte Sinai existe
$sql_empresa = "SELECT id, nome FROM empresas_seguros WHERE nome LIKE '%Monte Sinai%' OR nome LIKE '%Sinai%' LIMIT 1";
$rs_empresa = mysqli_query($db, $sql_empresa);
$empresa = mysqli_fetch_array($rs_empresa);

if(!$empresa){
    die('Empresa Monte Sinai não encontrada na base de dados. Por favor, cadastre primeiro a empresa.');
}

$empresa_id = $empresa['id'];
$empresa_nome = $empresa['nome'];

// Estatísticas
$estatisticas = [
    'servicos_criados' => 0,
    'servicos_atualizados' => 0,
    'precos_configurados' => 0,
    'erros' => 0
];
$erros = [];

// Abrir e processar CSV
$handle = fopen($caminho_csv, 'r');
if(!$handle){
    die('Erro ao abrir o ficheiro CSV.');
}

// Ler primeira linha (cabeçalho)
$cabecalho = fgetcsv($handle, 5000, ';');

$linha_num = 1;
$servicos_processados = [];

// Processar cada linha
while(($linha = fgetcsv($handle, 5000, ';')) !== FALSE){
    $linha_num++;
    
    // Ignorar linhas vazias
    if(empty(array_filter($linha)) || count($linha) < 4){
        continue;
    }
    
    // Extrair dados: Ordem;Sector;Nome do Procedimento;Preço
    $codigo = trim($linha[0]);
    $sector = trim($linha[1]);
    $nome = trim($linha[2]);
    $preco_str = trim($linha[3]);
    
    // Validar
    if(empty($codigo) || empty($nome)){
        continue;
    }
    
    // Remover aspas do preço
    $preco_str = str_replace(['"', "'"], '', $preco_str);
    
    // Converter preço (formato: "9.557,31" -> 9557.31)
    $preco = converterPreco($preco_str);
    
    if($preco === false || $preco <= 0){
        $erros[] = "Linha $linha_num: Preço inválido ($preco_str)";
        $estatisticas['erros']++;
        continue;
    }
    
    // Garantir categoria existe
    garantirCategoria($db, $sector, $userID);
    
    // Criar ou atualizar serviço
    $codigo_escape = mysqli_real_escape_string($db, $codigo);
    $nome_escape = mysqli_real_escape_string($db, $nome);
    $sector_escape = mysqli_real_escape_string($db, $sector);
    
    // Verificar se serviço já existe
    $sql_check = "SELECT id FROM servicos_clinica WHERE codigo = '$codigo_escape'";
    $rs_check = mysqli_query($db, $sql_check);
    $servico_existente = null;
    
    if($rs_check && mysqli_num_rows($rs_check) > 0){
        $servico_existente = mysqli_fetch_array($rs_check);
    }
    
    $servico_id = null;
    
    if($servico_existente){
        // Atualizar serviço existente
        $sql = "UPDATE servicos_clinica SET 
                nome = '$nome_escape',
                categoria = '$sector_escape',
                preco = $preco,
                ativo = 1
                WHERE codigo = '$codigo_escape'";
        
        if(mysqli_query($db, $sql)){
            $servico_id = intval($servico_existente['id']);
            $estatisticas['servicos_atualizados']++;
        }
    } else {
        // Criar novo serviço
        $sql = "INSERT INTO servicos_clinica (codigo, nome, categoria, preco, ativo, usuario_criacao) 
                VALUES ('$codigo_escape', '$nome_escape', '$sector_escape', $preco, 1, $userID)";
        
        if(mysqli_query($db, $sql)){
            $servico_id = mysqli_insert_id($db);
            $estatisticas['servicos_criados']++;
        }
    }
    
    if(!$servico_id){
        $erros[] = "Linha $linha_num: Erro ao salvar serviço";
        $estatisticas['erros']++;
        continue;
    }
    
    // Configurar preço para empresa Monte Sinai
    if(configurarPrecoEmpresa($db, $empresa_id, $servico_id, $preco, $userID)){
        $estatisticas['precos_configurados']++;
    } else {
        $erros[] = "Linha $linha_num: Erro ao configurar preço";
        $estatisticas['erros']++;
    }
}

fclose($handle);

// Função para converter preço
function converterPreco($preco_str){
    // Formato: "9.557,31" -> 9557.31
    // Remover pontos (separadores de milhar)
    $preco_limpo = str_replace('.', '', $preco_str);
    // Substituir vírgula por ponto (separador decimal)
    $preco_limpo = str_replace(',', '.', $preco_limpo);
    
    $preco = floatval($preco_limpo);
    return $preco > 0 ? $preco : false;
}

// Função para garantir categoria existe
function garantirCategoria($db, $nome_categoria, $userID){
    if(empty($nome_categoria)){
        return null;
    }
    
    $nome_escape = mysqli_real_escape_string($db, $nome_categoria);
    
    // Verificar se existe
    $sql_check = "SELECT id FROM categorias_servicos WHERE nome = '$nome_escape'";
    $rs_check = mysqli_query($db, $sql_check);
    
    if($rs_check && mysqli_num_rows($rs_check) > 0){
        $row = mysqli_fetch_array($rs_check);
        return $row['id'];
    }
    
    // Criar categoria
    $sql_insert = "INSERT INTO categorias_servicos (nome, ativo, usuario_criacao) 
                    VALUES ('$nome_escape', 1, $userID)";
    
    if(mysqli_query($db, $sql_insert)){
        return mysqli_insert_id($db);
    }
    
    return null;
}

// Função para configurar preço da empresa
function configurarPrecoEmpresa($db, $empresa_id, $servico_id, $preco, $userID){
    // Obter ou criar tabela de preços
    $tabela_precos_id = obterOuCriarTabelaPrecos($db, $empresa_id, $userID);
    
    if(!$tabela_precos_id){
        return false;
    }
    
    // Verificar se já existe preço
    $sql_check = "SELECT id FROM tabela_precos_servicos 
                   WHERE tabela_precos_id = $tabela_precos_id AND servico_id = $servico_id";
    $rs_check = mysqli_query($db, $sql_check);
    
    if($rs_check && mysqli_num_rows($rs_check) > 0){
        // Atualizar
        $sql = "UPDATE tabela_precos_servicos SET preco = $preco, ativo = 1 
                WHERE tabela_precos_id = $tabela_precos_id AND servico_id = $servico_id";
    } else {
        // Criar
        $sql = "INSERT INTO tabela_precos_servicos (tabela_precos_id, servico_id, preco, ativo) 
                VALUES ($tabela_precos_id, $servico_id, $preco, 1)";
    }
    
    return mysqli_query($db, $sql);
}

// Função para obter ou criar tabela de preços
function obterOuCriarTabelaPrecos($db, $empresa_id, $userID){
    // Verificar se já existe
    $sql_check = "SELECT id FROM tabelas_precos WHERE empresa_id = $empresa_id AND ativo = 1 LIMIT 1";
    $rs_check = mysqli_query($db, $sql_check);
    
    if($rs_check && mysqli_num_rows($rs_check) > 0){
        $row = mysqli_fetch_array($rs_check);
        return $row['id'];
    }
    
    // Criar nova tabela
    $sql_insert = "INSERT INTO tabelas_precos (empresa_id, nome, ativo, usuario_criacao) 
                    VALUES ($empresa_id, 'Tabela Padrão', 1, $userID)";
    
    if(mysqli_query($db, $sql_insert)){
        $tabela_precos_id = mysqli_insert_id($db);
        
        // Atualizar empresa
        $sql_update = "UPDATE empresas_seguros SET tabela_precos_id = $tabela_precos_id WHERE id = $empresa_id";
        mysqli_query($db, $sql_update);
        
        return $tabela_precos_id;
    }
    
    return false;
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Importação - Monte Sinai</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .stats-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .error-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>📊 Resultado da Importação - Monte Sinai</h2>
        
        <div class="success-box">
            <h5>✅ Importação Concluída!</h5>
            <p><strong>Empresa:</strong> <?php echo htmlspecialchars($empresa_nome); ?></p>
            <p><strong>Ficheiro:</strong> Clínica Médica Monte Sinai fidelidade para arranjos.csv</p>
        </div>
        
        <div class="stats-box">
            <div class="row">
                <div class="col-md-3 stat-item">
                    <div class="stat-number text-success"><?php echo $estatisticas['servicos_criados']; ?></div>
                    <div class="stat-label">Serviços Criados</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number text-info"><?php echo $estatisticas['servicos_atualizados']; ?></div>
                    <div class="stat-label">Serviços Atualizados</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number text-primary"><?php echo $estatisticas['precos_configurados']; ?></div>
                    <div class="stat-label">Preços Configurados</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number text-danger"><?php echo $estatisticas['erros']; ?></div>
                    <div class="stat-label">Erros</div>
                </div>
            </div>
        </div>
        
        <?php if(!empty($erros)): ?>
            <div class="error-box">
                <h5>⚠️ Erros Encontrados (<?php echo count($erros); ?>)</h5>
                <ul style="max-height: 300px; overflow-y: auto;">
                    <?php foreach(array_slice($erros, 0, 50) as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                    <?php if(count($erros) > 50): ?>
                        <li><em>... e mais <?php echo count($erros) - 50; ?> erros</em></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="servicos_clinica.php" class="btn btn-primary">Ver Serviços</a>
            <a href="tabela_precos.php?empresa=<?php echo $empresa_id; ?>" class="btn btn-success">Ver Preços da Monte Sinai</a>
            <a href="empresas.php" class="btn btn-secondary">Ver Empresas</a>
        </div>
    </div>
</body>
</html>

