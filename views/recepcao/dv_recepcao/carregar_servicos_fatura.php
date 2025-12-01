<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;
$fatura_id = isset($_POST['fatura_id']) && $_POST['fatura_id'] != '' && $_POST['fatura_id'] != 'null' ? intval($_POST['fatura_id']) : null;

// Log para debug
error_log("DV carregar_servicos: userID=" . ($userID ?? 'null') . ", fatura_id=" . ($fatura_id ?? 'null') . ", POST=" . json_encode($_POST));

if(!$fatura_id || $fatura_id <= 0) {
    error_log("DV carregar_servicos: fatura_id inválido - " . ($_POST['fatura_id'] ?? 'não definido'));
    echo 40; // Parâmetros insuficientes
    exit;
}

if(!$userID || $userID <= 0) {
    error_log("DV carregar_servicos: userID inválido - " . ($userID ?? 'não definido'));
    echo 40;
    exit;
}

// Verificar se a tabela temporária existe
$check_table = "SHOW TABLES LIKE 'dv_servicos_temp'";
$table_exists = mysqli_query($db, $check_table);
if(!$table_exists || mysqli_num_rows($table_exists) == 0) {
    error_log("Erro: Tabela dv_servicos_temp não existe");
    echo 4;
    exit;
}

// Limpar serviços temporários anteriores do usuário para esta fatura
$sql_limpar = "DELETE FROM dv_servicos_temp WHERE user = ? AND factura_recepcao_id = ?";
$stmt_limpar = mysqli_prepare($db, $sql_limpar);
mysqli_stmt_bind_param($stmt_limpar, "ii", $userID, $fatura_id);
mysqli_stmt_execute($stmt_limpar);

// Buscar informações da fatura
$sql_fatura = "SELECT * FROM factura_recepcao WHERE id = ?";
$stmt_fatura = mysqli_prepare($db, $sql_fatura);
mysqli_stmt_bind_param($stmt_fatura, "i", $fatura_id);
mysqli_stmt_execute($stmt_fatura);
$rs_fatura = mysqli_stmt_get_result($stmt_fatura);

if(!$rs_fatura || mysqli_num_rows($rs_fatura) == 0) {
    echo 10; // Fatura não encontrada
    exit;
}

$fatura_data = mysqli_fetch_array($rs_fatura);
$empresa_id = isset($fatura_data['empresa_id']) ? intval($fatura_data['empresa_id']) : null;

// Buscar serviços da fatura
$check_table_serv = "SHOW TABLES LIKE 'fa_servicos_fact_recepcao'";
$table_serv_exists = mysqli_query($db, $check_table_serv);
if(!$table_serv_exists || mysqli_num_rows($table_serv_exists) == 0) {
    echo 4; // Tabela não existe
    exit;
}

$sql_servicos = "SELECT fs.*, s.nome as servico_nome 
                 FROM fa_servicos_fact_recepcao fs 
                 INNER JOIN servicos_clinica s ON fs.servico = s.id 
                 WHERE fs.factura = ?";
$stmt_servicos = mysqli_prepare($db, $sql_servicos);
mysqli_stmt_bind_param($stmt_servicos, "i", $fatura_id);
mysqli_stmt_execute($stmt_servicos);
$rs_servicos = mysqli_stmt_get_result($stmt_servicos);

if(!$rs_servicos || mysqli_num_rows($rs_servicos) == 0) {
    echo 7; // Nenhum serviço encontrado
    exit;
}

// Inserir todos os serviços na tabela temporária
$erro = false;
while($servico = mysqli_fetch_array($rs_servicos)) {
    $servico_id = intval($servico['servico']);
    $qtd = intval($servico['qtd']);
    $preco = floatval($servico['preco']);
    $total = $preco * $qtd;
    
    if($empresa_id && $empresa_id > 0) {
        $sql_insert = "INSERT INTO dv_servicos_temp(servico, qtd, preco, total, user, empresa_id, factura_recepcao_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($db, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiddiii", $servico_id, $qtd, $preco, $total, $userID, $empresa_id, $fatura_id);
    } else {
        $sql_insert = "INSERT INTO dv_servicos_temp(servico, qtd, preco, total, user, factura_recepcao_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($db, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiddii", $servico_id, $qtd, $preco, $total, $userID, $fatura_id);
    }
    
    if(!mysqli_stmt_execute($stmt_insert)) {
        $erro = true;
        error_log("Erro ao inserir serviço na devolução: " . mysqli_error($db));
        break;
    }
}

if($erro) {
    echo 31; // Erro ao inserir serviços
} else {
    echo 3; // Sucesso
}
?>

