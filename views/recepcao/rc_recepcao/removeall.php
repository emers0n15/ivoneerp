<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$userID = $_SESSION['idUsuario'] ?? null;

// Verificar se a tabela temporária existe
$check_table_temp = "SHOW TABLES LIKE 'rc_faturas_temp_recepcao'";
$table_temp_exists = mysqli_query($db, $check_table_temp);
if($table_temp_exists && mysqli_num_rows($table_temp_exists) > 0) {
    $sql = "DELETE FROM rc_faturas_temp_recepcao WHERE user = ?";
    $stmt = mysqli_prepare($db, $sql);
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
    }
}
?>

