<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$userID = $_SESSION['idUsuario'] ?? null;

// Verificar se a tabela temporária existe
$check_table_temp = "SHOW TABLES LIKE 'rc_faturas_temp_recepcao'";
$table_temp_exists = mysqli_query($db, $check_table_temp);
if(!$table_temp_exists || mysqli_num_rows($table_temp_exists) == 0) {
    echo '0,00 MT';
    exit;
}

$sql = "SELECT SUM(valor) as t FROM rc_faturas_temp_recepcao WHERE user = ?";
$stmt = mysqli_prepare($db, $sql);
if($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    $dados = mysqli_fetch_array($rs);
    $total = $dados['t'] ?? 0;
    echo number_format($total, 2, ',', '.') . ' MT';
} else {
    echo '0,00 MT';
}
?>

