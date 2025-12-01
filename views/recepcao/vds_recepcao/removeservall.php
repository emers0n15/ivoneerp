<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$userID = $_SESSION['idUsuario'] ?? null;

$sql = "DELETE FROM vds_servicos_temp WHERE user = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
?>

