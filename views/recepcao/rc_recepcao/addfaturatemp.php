<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;

if (!isset($_POST['fatura_id'])) {
    echo 40;
    exit;
}

$fatura_id = intval($_POST['fatura_id']);
$valor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;

// Verificar se a tabela temporária existe
$check_table_temp = "SHOW TABLES LIKE 'rc_faturas_temp_recepcao'";
$table_temp_exists = mysqli_query($db, $check_table_temp);
if(!$table_temp_exists || mysqli_num_rows($table_temp_exists) == 0) {
    echo 4;
    exit;
}

// Verificar se a fatura já foi adicionada
$sql_check = "SELECT * FROM rc_faturas_temp_recepcao WHERE factura_recepcao_id = ? AND user = ?";
$stmt_check = mysqli_prepare($db, $sql_check);
if($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "ii", $fatura_id, $userID);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    
    if ($result_check && mysqli_num_rows($result_check) > 0) {
        echo 2;
        exit;
    }
}

// Buscar informações da fatura
$check_table = "SHOW TABLES LIKE 'factura_recepcao'";
$table_exists = mysqli_query($db, $check_table);
if(!$table_exists || mysqli_num_rows($table_exists) == 0) {
    echo 4;
    exit;
}

$sql_fatura = "SELECT f.*, p.nome, p.apelido, 
                COALESCE((SELECT SUM(valor_pago) FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id), 0) as total_pago
                FROM factura_recepcao f 
                INNER JOIN pacientes p ON f.paciente = p.id 
                WHERE f.id = ?";
$stmt_fatura = mysqli_prepare($db, $sql_fatura);
mysqli_stmt_bind_param($stmt_fatura, "i", $fatura_id);
mysqli_stmt_execute($stmt_fatura);
$result_fatura = mysqli_stmt_get_result($stmt_fatura);

if (!$result_fatura || mysqli_num_rows($result_fatura) == 0) {
    echo 10;
    exit;
}

$fatura_data = mysqli_fetch_array($result_fatura);
$valor_total = floatval($fatura_data['valor']);
$total_pago = floatval($fatura_data['total_pago']);
$valor_pendente = $valor_total - $total_pago;

// Se não foi informado valor, usar o valor pendente
if($valor <= 0) {
    $valor = $valor_pendente;
}

// Validar que o valor não seja maior que o pendente
if($valor > $valor_pendente) {
    $valor = $valor_pendente;
}

// Inserir na tabela temporária
$sql = "INSERT INTO rc_faturas_temp_recepcao(factura_recepcao_id, valor, user) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "idi", $fatura_id, $valor, $userID);
$result = mysqli_stmt_execute($stmt);

if($result) {
    echo 3;
} else {
    error_log("Erro ao inserir fatura: " . mysqli_error($db));
    echo 31;
}
?>

