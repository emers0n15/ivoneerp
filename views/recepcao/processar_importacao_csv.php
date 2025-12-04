<?php
/**
 * Script de Processamento de Importação de CSV
 * Processa o CSV e importa os dados para a base de dados
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
$erros = [];
$sucessos = [];
$estatisticas = [
    'servicos_criados' => 0,
    'servicos_atualizados' => 0,
    'precos_configurados' => 0,
    'erros' => 0
];

// Verificar se foi enviado um ficheiro
if(!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK){
    die('Erro ao fazer upload do ficheiro.');
}

$arquivo = $_FILES['arquivo'];
$tipo_importacao = $_POST['tipo_importacao'] ?? 'servicos';
$empresa_id = isset($_POST['empresa_id']) && !empty($_POST['empresa_id']) ? intval($_POST['empresa_id']) : null;

// Verificar extensão
$extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
if(!in_array($extensao, ['csv', 'xlsx', 'xls'])){
    die('Formato de ficheiro não suportado. Use CSV, XLSX ou XLS.');
}

// Se for CSV, processar diretamente
if($extensao === 'csv'){
    processarCSV($arquivo['tmp_name'], $tipo_importacao, $empresa_id, $userID);
} else {
    die('Para Excel, instale a biblioteca PHPSpreadsheet. Por enquanto, converta para CSV.');
}

function processarCSV($caminho_arquivo, $tipo_importacao, $empresa_id, $userID){
    global $db, $erros, $sucessos, $estatisticas;
    
    // Abrir ficheiro CSV
    $handle = fopen($caminho_arquivo, 'r');
    if(!$handle){
        die('Erro ao abrir o ficheiro CSV.');
    }
    
    // Ler primeira linha (cabeçalho)
    $cabecalho = fgetcsv($handle, 1000, ';');
    $pular_cabecalho = isset($_POST['pular_cabecalho']) && $_POST['pular_cabecalho'] == '1';
    
    if($pular_cabecalho){
        // Já lemos o cabeçalho, continuamos
    }
    
    $linha_num = $pular_cabecalho ? 1 : 0;
    $servicos_processados = [];
    
    // Processar cada linha
    while(($linha = fgetcsv($handle, 5000, ';')) !== FALSE){
        $linha_num++;
        
        // Ignorar linhas vazias
        if(empty(array_filter($linha))){
            continue;
        }
        
        // Extrair dados da linha
        // Formato esperado: Ordem;Sector;Nome do Procedimento;Preço
        if(count($linha) < 4){
            $erros[] = "Linha $linha_num: Formato inválido (menos de 4 colunas)";
            $estatisticas['erros']++;
            continue;
        }
        
        $codigo = trim($linha[0]);
        $sector = trim($linha[1]);
        $nome = trim($linha[2]);
        $preco_str = trim($linha[3]);
        
        // Remover aspas do preço se existirem
        $preco_str = str_replace(['"', "'"], '', $preco_str);
        
        // Converter preço (formato: "9.557,31" para 9557.31)
        $preco = converterPreco($preco_str);
        
        // Validar dados básicos
        if(empty($codigo) || empty($nome)){
            $erros[] = "Linha $linha_num: Código ou nome vazio";
            $estatisticas['erros']++;
            continue;
        }
        
        if($preco === false || $preco <= 0){
            $erros[] = "Linha $linha_num: Preço inválido ($preco_str)";
            $estatisticas['erros']++;
            continue;
        }
        
        // Criar ou atualizar serviço primeiro (se necessário)
        $servico_id = null;
        
        if($tipo_importacao === 'servicos' || $tipo_importacao === 'ambos'){
            $resultado = criarOuAtualizarServico($db, $codigo, $nome, $sector, $preco, $userID);
            if($resultado && is_numeric($resultado)){
                $servico_id = $resultado;
                $servicos_processados[$codigo] = $servico_id;
            }
        } else {
            // Se apenas importando preços, buscar serviço existente
            $servico_id = buscarServicoPorCodigo($db, $codigo);
        }
        
        // Configurar preço por empresa
        if(($tipo_importacao === 'precos' || $tipo_importacao === 'ambos') && $empresa_id && $servico_id){
            if(configurarPrecoEmpresa($db, $empresa_id, $servico_id, $preco, $userID)){
                $estatisticas['precos_configurados']++;
            } else {
                $erros[] = "Linha $linha_num: Erro ao configurar preço para empresa";
                $estatisticas['erros']++;
            }
        }
    }
    
    fclose($handle);
    
    // Exibir resultados
    exibirResultados($estatisticas, $erros, $sucessos);
}

function converterPreco($preco_str){
    // Formato: "9.557,31" -> 9557.31
    // Remover pontos (separadores de milhar)
    $preco_limpo = str_replace('.', '', $preco_str);
    // Substituir vírgula por ponto (separador decimal)
    $preco_limpo = str_replace(',', '.', $preco_limpo);
    
    $preco = floatval($preco_limpo);
    return $preco > 0 ? $preco : false;
}

function criarOuAtualizarServico($db, $codigo, $nome, $categoria, $preco, $userID){
    global $estatisticas;
    
    // Garantir que categoria existe
    garantirCategoria($db, $categoria, $userID);
    
    $codigo_escape = mysqli_real_escape_string($db, $codigo);
    $nome_escape = mysqli_real_escape_string($db, $nome);
    $categoria_escape = mysqli_real_escape_string($db, $categoria);
    
    // Verificar se serviço já existe
    $sql_check = "SELECT id FROM servicos_clinica WHERE codigo = '$codigo_escape'";
    $rs_check = mysqli_query($db, $sql_check);
    $servico_existente = null;
    
    if($rs_check && mysqli_num_rows($rs_check) > 0){
        $servico_existente = mysqli_fetch_array($rs_check);
    }
    
    if($servico_existente){
        // Atualizar serviço existente
        $sql = "UPDATE servicos_clinica SET 
                nome = '$nome_escape',
                categoria = '$categoria_escape',
                preco = $preco,
                ativo = 1
                WHERE codigo = '$codigo_escape'";
        
        if(mysqli_query($db, $sql)){
            $estatisticas['servicos_atualizados']++;
            return intval($servico_existente['id']);
        } else {
            return false;
        }
    } else {
        // Criar novo serviço
        $sql = "INSERT INTO servicos_clinica (codigo, nome, categoria, preco, ativo, usuario_criacao) 
                VALUES ('$codigo_escape', '$nome_escape', '$categoria_escape', $preco, 1, $userID)";
        
        if(mysqli_query($db, $sql)){
            $estatisticas['servicos_criados']++;
            return mysqli_insert_id($db);
        } else {
            return false;
        }
    }
}

function garantirCategoria($db, $nome_categoria, $userID){
    // Verificar se categoria existe
    $sql_check = "SELECT id FROM categorias_servicos WHERE nome = ?";
    $stmt_check = mysqli_prepare($db, $sql_check);
    if(!$stmt_check){
        return null;
    }
    
    mysqli_stmt_bind_param($stmt_check, "s", $nome_categoria);
    mysqli_stmt_execute($stmt_check);
    $rs_check = mysqli_stmt_get_result($stmt_check);
    $categoria_existente = mysqli_fetch_array($rs_check);
    
    if($categoria_existente){
        return $categoria_existente['id'];
    }
    
    // Criar categoria se não existir
    $nome_escape = mysqli_real_escape_string($db, $nome_categoria);
    $sql_insert = "INSERT INTO categorias_servicos (nome, ativo, usuario_criacao) 
                    VALUES ('$nome_escape', 1, $userID)";
    
    if(mysqli_query($db, $sql_insert)){
        return mysqli_insert_id($db);
    }
    
    return null;
}

function buscarServicoPorCodigo($db, $codigo){
    $codigo_escape = mysqli_real_escape_string($db, $codigo);
    $sql = "SELECT id FROM servicos_clinica WHERE codigo = '$codigo_escape'";
    $rs = mysqli_query($db, $sql);
    if($rs && mysqli_num_rows($rs) > 0){
        $row = mysqli_fetch_array($rs);
        return $row['id'];
    }
    return null;
}

function configurarPrecoEmpresa($db, $empresa_id, $servico_id, $preco, $userID){
    // Verificar se empresa tem tabela de preços
    $tabela_precos_id = obterOuCriarTabelaPrecos($db, $empresa_id, $userID);
    
    if(!$tabela_precos_id){
        return false;
    }
    
    // Verificar se já existe preço configurado
    $sql_check = "SELECT id FROM tabela_precos_servicos 
                   WHERE tabela_precos_id = $tabela_precos_id AND servico_id = $servico_id";
    $rs_check = mysqli_query($db, $sql_check);
    
    if($rs_check && mysqli_num_rows($rs_check) > 0){
        // Atualizar preço existente
        $sql = "UPDATE tabela_precos_servicos SET preco = $preco, ativo = 1 
                WHERE tabela_precos_id = $tabela_precos_id AND servico_id = $servico_id";
    } else {
        // Criar novo preço
        $sql = "INSERT INTO tabela_precos_servicos (tabela_precos_id, servico_id, preco, ativo) 
                VALUES ($tabela_precos_id, $servico_id, $preco, 1)";
    }
    
    return mysqli_query($db, $sql);
}

function obterOuCriarTabelaPrecos($db, $empresa_id, $userID){
    // Verificar se já existe tabela de preços para esta empresa
    $sql_check = "SELECT id FROM tabelas_precos WHERE empresa_id = $empresa_id AND ativo = 1 LIMIT 1";
    $rs_check = mysqli_query($db, $sql_check);
    
    if($rs_check && mysqli_num_rows($rs_check) > 0){
        $row = mysqli_fetch_array($rs_check);
        return $row['id'];
    }
    
    // Criar nova tabela de preços
    $sql_insert = "INSERT INTO tabelas_precos (empresa_id, nome, ativo, usuario_criacao) 
                    VALUES ($empresa_id, 'Tabela Padrão', 1, $userID)";
    
    if(mysqli_query($db, $sql_insert)){
        $tabela_precos_id = mysqli_insert_id($db);
        
        // Atualizar empresa com referência à tabela
        $sql_update = "UPDATE empresas_seguros SET tabela_precos_id = $tabela_precos_id WHERE id = $empresa_id";
        mysqli_query($db, $sql_update);
        
        return $tabela_precos_id;
    }
    
    return false;
}

function exibirResultados($estatisticas, $erros, $sucessos){
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resultado da Importação</title>
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
        </style>
    </head>
    <body>
        <div class="container mt-4">
            <h2>📊 Resultado da Importação</h2>
            
            <div class="stats-box">
                <div class="row">
                    <div class="col-md-3 stat-item">
                        <div class="stat-number"><?php echo $estatisticas['servicos_criados']; ?></div>
                        <div class="stat-label">Serviços Criados</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number"><?php echo $estatisticas['servicos_atualizados']; ?></div>
                        <div class="stat-label">Serviços Atualizados</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number"><?php echo $estatisticas['precos_configurados']; ?></div>
                        <div class="stat-label">Preços Configurados</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number text-danger"><?php echo $estatisticas['erros']; ?></div>
                        <div class="stat-label">Erros</div>
                    </div>
                </div>
            </div>
            
            <?php if(!empty($erros)): ?>
                <div class="alert alert-warning">
                    <h5>⚠️ Erros Encontrados (<?php echo count($erros); ?>)</h5>
                    <ul>
                        <?php foreach($erros as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="importar_excel.php" class="btn btn-secondary">Importar Outro Ficheiro</a>
                <a href="servicos_clinica.php" class="btn btn-primary">Ver Serviços</a>
                <?php if($estatisticas['precos_configurados'] > 0): ?>
                    <a href="tabela_precos.php?empresa=<?php echo $empresa_id; ?>" class="btn btn-success">Ver Preços da Empresa</a>
                <?php endif; ?>
            </div>
        </div>
    </body>
    </html>
    <?php
}

?>

