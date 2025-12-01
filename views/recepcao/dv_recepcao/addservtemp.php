<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;

if (!isset($_POST['idServico'])) {
    echo 40;
    exit;
}

$idServico = $_POST['idServico'];
$empresa_id = isset($_POST['empresa_id']) && $_POST['empresa_id'] != '' && $_POST['empresa_id'] != 'null' ? intval($_POST['empresa_id']) : null;
$fatura_id = isset($_POST['fatura_id']) && $_POST['fatura_id'] != '' && $_POST['fatura_id'] != 'null' ? intval($_POST['fatura_id']) : null;
$preco = isset($_POST['preco']) ? floatval($_POST['preco']) : 0;
$qtd_original = isset($_POST['qtd']) ? intval($_POST['qtd']) : 1;

// Verificar se a tabela temporária existe
$check_table = "SHOW TABLES LIKE 'dv_servicos_temp'";
$table_exists = mysqli_query($db, $check_table);
if(!$table_exists || mysqli_num_rows($table_exists) == 0) {
    error_log("Erro: Tabela dv_servicos_temp não existe");
    echo 4;
    exit;
}

if(!$fatura_id || $fatura_id <= 0) {
    echo 40;
    exit;
}

// Usar preço e quantidade da fatura original
$qt = 1; // Quantidade a devolver (começa com 1)
$total = $preco * $qt;

// Verificar se o serviço já está na tabela temporária
if($empresa_id && $empresa_id > 0) {
    $sql = "SELECT * FROM dv_servicos_temp WHERE servico = ? AND user = ? AND empresa_id = ? AND factura_recepcao_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $idServico, $userID, $empresa_id, $fatura_id);
} else {
    $sql = "SELECT * FROM dv_servicos_temp WHERE servico = ? AND user = ? AND (empresa_id IS NULL OR empresa_id = 0) AND factura_recepcao_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $idServico, $userID, $fatura_id);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    // Se já existe, aumentar quantidade (mas não pode exceder a quantidade original da fatura)
    $dados_existentes = mysqli_fetch_array($result);
    $qtd_atual = intval($dados_existentes['qtd']);
    
    if($qtd_atual >= $qtd_original) {
        echo 7; // Quantidade máxima atingida
        exit;
    }
    
    if($empresa_id && $empresa_id > 0) {
        $sql = "UPDATE dv_servicos_temp SET qtd = qtd + 1, total = (qtd + 1)*preco WHERE servico = ? AND user = ? AND empresa_id = ? AND factura_recepcao_id = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", $idServico, $userID, $empresa_id, $fatura_id);
    } else {
        $sql = "UPDATE dv_servicos_temp SET qtd = qtd + 1, total = (qtd + 1)*preco WHERE servico = ? AND user = ? AND (empresa_id IS NULL OR empresa_id = 0) AND factura_recepcao_id = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $idServico, $userID, $fatura_id);
    }
    echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
    exit;
} else {
    // Inserir novo serviço
    if($empresa_id && $empresa_id > 0) {
        $sql = "INSERT INTO dv_servicos_temp(servico, qtd, preco, total, user, empresa_id, factura_recepcao_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "iiddiii", $idServico, $qt, $preco, $total, $userID, $empresa_id, $fatura_id);
    } else {
        $sql = "INSERT INTO dv_servicos_temp(servico, qtd, preco, total, user, factura_recepcao_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "iiddii", $idServico, $qt, $preco, $total, $userID, $fatura_id);
    }
    $result = mysqli_stmt_execute($stmt);
    if($result) {
        echo 3;
    } else {
        error_log("Erro ao inserir serviço: " . mysqli_error($db));
        echo 31;
    }
    exit;
}
?>

