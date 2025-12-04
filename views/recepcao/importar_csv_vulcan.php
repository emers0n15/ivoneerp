<?php
/**
 * Script de Importação Específico para CSV da Seguradora Vulcan
 * Importa diretamente do ficheiro CSV especificado no desktop do utilizador.
 *
 * Formato do CSV "Cópia de Vulcan Prices - Final to providers.csv":
 * Sr.no;SECTION;PROVIDER DISCRIPTION; NEW PRICE ; CONTRA-PROPOSTA MONTE SINAI ;;
 *
 * Estratégia:
 * - Código: usa a coluna "Sr.no" (ex.: 1, 2, 3...) como código interno.
 * - Categoria: usa a coluna "SECTION".
 * - Nome do serviço: usa a coluna "PROVIDER DISCRIPTION".
 * - Preço:
 *     1) Tenta usar "CONTRA-PROPOSTA MONTE SINAI" (preço negociado).
 *     2) Se não for numérico/válido, cai para a coluna "NEW PRICE".
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

// Caminho do ficheiro CSV (conforme indicado pelo utilizador)
$caminho_csv = 'C:\\Users\\LOSE\\Desktop\\Cópia de Vulcan Prices - Final to providers.csv';

// Verificar se ficheiro existe
if(!file_exists($caminho_csv)){
    die('Ficheiro CSV não encontrado no caminho: ' . htmlspecialchars($caminho_csv));
}

// Verificar se empresa/seguradora Vulcan existe
$sql_empresa = "SELECT id, nome FROM empresas_seguros 
                WHERE nome LIKE '%Vulcan%' 
                ORDER BY id DESC LIMIT 1";
$rs_empresa  = mysqli_query($db, $sql_empresa);
$empresa     = mysqli_fetch_array($rs_empresa);

if(!$empresa){
    die('Empresa/Seguradora "Vulcan" não encontrada na base de dados. '
       .'Por favor, cadastre primeiro a empresa em Recepção → Empresas/Seguros → Nova Empresa.');
}

$empresa_id   = (int)$empresa['id'];
$empresa_nome = $empresa['nome'];

// Estatísticas
$estatisticas = [
    'servicos_criados'     => 0,
    'servicos_atualizados' => 0,
    'precos_configurados'  => 0,
    'erros'                => 0
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

// Processar cada linha
while(($linha = fgetcsv($handle, 8000, ';')) !== FALSE){
    $linha_num++;

    // Ignorar linhas totalmente vazias
    if(empty(array_filter($linha))){
        continue;
    }

    // Esperamos pelo menos 4 colunas (Sr.no;SECTION;PROVIDER;NEW PRICE;[CONTRA]...)
    if(count($linha) < 4){
        $erros[] = "Linha $linha_num: Formato inválido (menos de 4 colunas)";
        $estatisticas['erros']++;
        continue;
    }

    $codigo_raw   = trim($linha[0]); // Sr.no
    $sector       = isset($linha[1]) ? trim($linha[1]) : '';
    $nome         = isset($linha[2]) ? trim($linha[2]) : '';
    $preco_new    = isset($linha[3]) ? trim($linha[3]) : '';
    $preco_contra = isset($linha[4]) ? trim($linha[4]) : '';

    // Alguns registos podem ter "Sr.no" vazio; usamos combinação SECTION+NOME como código fallback
    if($codigo_raw === ''){
        $codigo = substr($sector . '-' . $nome, 0, 50);
    } else {
        $codigo = 'VULCAN' . trim($codigo_raw);
    }

    if(empty($nome)){
        // Se nem o nome existe, a linha não é útil
        continue;
    }

    // Remover aspas/espacos dos preços
    $preco_contra = str_replace(['"', "'"], '', $preco_contra);
    $preco_new    = str_replace(['"', "'"], '', $preco_new);
    $preco_contra = trim($preco_contra);
    $preco_new    = trim($preco_new);

    // 1º tentar usar contra-proposta Monte Sinai
    $preco = null;
    if($preco_contra !== ''){
        $preco = converterPrecoVulcan($preco_contra);
    }

    // 2º se contra-proposta não for válida, tentar NEW PRICE
    if($preco === false || $preco === null || $preco <= 0){
        if($preco_new !== ''){
            $preco = converterPrecoVulcan($preco_new);
        }
    }

    if($preco === false || $preco === null || $preco <= 0){
        $erros[] = "Linha $linha_num: Preço inválido (Contra: $preco_contra / Novo: $preco_new)";
        $estatisticas['erros']++;
        continue;
    }

    // Garantir categoria existe
    garantirCategoriaVulcan($db, $sector, $userID);

    // Criar ou atualizar serviço
    $codigo_escape = mysqli_real_escape_string($db, $codigo);
    $nome_escape   = mysqli_real_escape_string($db, $nome);
    $sector_escape = mysqli_real_escape_string($db, $sector);

    // Verificar se serviço já existe pelo código
    $sql_check = "SELECT id FROM servicos_clinica WHERE codigo = '$codigo_escape'";
    $rs_check  = mysqli_query($db, $sql_check);
    $servico_existente = $rs_check && mysqli_num_rows($rs_check) > 0
        ? mysqli_fetch_array($rs_check)
        : null;

    $servico_id = null;

    if($servico_existente){
        $sql = "UPDATE servicos_clinica SET 
                    nome      = '$nome_escape',
                    categoria = '$sector_escape',
                    preco     = $preco,
                    ativo     = 1
                WHERE codigo = '$codigo_escape'";

        if(mysqli_query($db, $sql)){
            $servico_id = (int)$servico_existente['id'];
            $estatisticas['servicos_atualizados']++;
        } else {
            $erros[] = "Linha $linha_num: Erro ao atualizar serviço (" . mysqli_error($db) . ")";
            $estatisticas['erros']++;
            continue;
        }
    } else {
        $sql = "INSERT INTO servicos_clinica (codigo, nome, categoria, preco, ativo, usuario_criacao)
                VALUES ('$codigo_escape', '$nome_escape', '$sector_escape', $preco, 1, $userID)";

        if(mysqli_query($db, $sql)){
            $servico_id = mysqli_insert_id($db);
            $estatisticas['servicos_criados']++;
        } else {
            $erros[] = "Linha $linha_num: Erro ao criar serviço (" . mysqli_error($db) . ")";
            $estatisticas['erros']++;
            continue;
        }
    }

    if(!$servico_id){
        $erros[] = "Linha $linha_num: Serviço não pôde ser identificado.";
        $estatisticas['erros']++;
        continue;
    }

    // Configurar preço específico para a seguradora Vulcan
    if(configurarPrecoEmpresaVulcan($db, $empresa_id, $servico_id, $preco, $userID)){
        $estatisticas['precos_configurados']++;
    } else {
        $erros[] = "Linha $linha_num: Erro ao configurar preço para Vulcan.";
        $estatisticas['erros']++;
    }
}

fclose($handle);

// ===== Funções auxiliares específicas deste script =====

function converterPrecoVulcan($preco_str){
    // Alguns campos contêm códigos tipo "NT", "AL", etc. — não são numéricos.
    $preco_str = trim($preco_str);
    if($preco_str === '' || preg_match('/[A-Za-z#]/', $preco_str)){
        return false;
    }

    // Remover separador de milhar e espaços
    $preco_limpo = str_replace('.', '', $preco_str);
    $preco_limpo = str_replace(' ', '', $preco_limpo);
    // Trocar vírgula decimal por ponto
    $preco_limpo = str_replace(',', '.', $preco_limpo);

    if($preco_limpo === '' || !is_numeric($preco_limpo)){
        return false;
    }

    $preco = (float)$preco_limpo;
    return $preco > 0 ? $preco : false;
}

function garantirCategoriaVulcan($db, $nome_categoria, $userID){
    if(empty($nome_categoria)){
        return null;
    }

    $nome_escape = mysqli_real_escape_string($db, $nome_categoria);

    $sql_check = "SELECT id FROM categorias_servicos WHERE nome = '$nome_escape'";
    $rs_check  = mysqli_query($db, $sql_check);

    if($rs_check && mysqli_num_rows($rs_check) > 0){
        $row = mysqli_fetch_array($rs_check);
        return (int)$row['id'];
    }

    $sql_insert = "INSERT INTO categorias_servicos (nome, ativo, usuario_criacao)
                   VALUES ('$nome_escape', 1, $userID)";

    if(mysqli_query($db, $sql_insert)){
        return mysqli_insert_id($db);
    }

    return null;
}

function configurarPrecoEmpresaVulcan($db, $empresa_id, $servico_id, $preco, $userID){
    $tabela_precos_id = obterOuCriarTabelaPrecosVulcan($db, $empresa_id, $userID);

    if(!$tabela_precos_id){
        return false;
    }

    $sql_check = "SELECT id FROM tabela_precos_servicos
                  WHERE tabela_precos_id = $tabela_precos_id
                    AND servico_id       = $servico_id";
    $rs_check  = mysqli_query($db, $sql_check);

    if($rs_check && mysqli_num_rows($rs_check) > 0){
        $sql = "UPDATE tabela_precos_servicos
                SET preco = $preco, ativo = 1
                WHERE tabela_precos_id = $tabela_precos_id
                  AND servico_id       = $servico_id";
    } else {
        $sql = "INSERT INTO tabela_precos_servicos (tabela_precos_id, servico_id, preco, ativo)
                VALUES ($tabela_precos_id, $servico_id, $preco, 1)";
    }

    return mysqli_query($db, $sql);
}

function obterOuCriarTabelaPrecosVulcan($db, $empresa_id, $userID){
    $sql_check = "SELECT id FROM tabelas_precos
                  WHERE empresa_id = $empresa_id
                    AND ativo      = 1
                  LIMIT 1";
    $rs_check  = mysqli_query($db, $sql_check);

    if($rs_check && mysqli_num_rows($rs_check) > 0){
        $row = mysqli_fetch_array($rs_check);
        return (int)$row['id'];
    }

    $sql_insert = "INSERT INTO tabelas_precos (empresa_id, nome, ativo, usuario_criacao)
                   VALUES ($empresa_id, 'Tabela Padrão', 1, $userID)";

    if(mysqli_query($db, $sql_insert)){
        $tabela_precos_id = mysqli_insert_id($db);

        $sql_update = "UPDATE empresas_seguros
                       SET tabela_precos_id = $tabela_precos_id
                       WHERE id = $empresa_id";
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
    <title>Resultado da Importação - Vulcan</title>
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
        <h2>📊 Resultado da Importação - Seguradora Vulcan</h2>

        <div class="success-box">
            <h5>✅ Importação Concluída!</h5>
            <p><strong>Empresa/Seguradora:</strong> <?php echo htmlspecialchars($empresa_nome); ?></p>
            <p><strong>Ficheiro:</strong> Cópia de Vulcan Prices - Final to providers.csv</p>
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
            <a href="tabela_precos.php?id=<?php echo $empresa_id; ?>" class="btn btn-success">Ver Preços da Vulcan</a>
            <a href="empresas.php" class="btn btn-secondary">Ver Empresas</a>
        </div>
    </div>
</body>
</html>


