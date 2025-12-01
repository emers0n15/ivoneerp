<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$idServico = $_POST['idServico'] ?? null;

if(!$idServico) {
    echo "Erro: ID não fornecido";
    exit;
}

$sql = "DELETE FROM nc_servicos_temp WHERE id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idServico);
mysqli_stmt_execute($stmt);
?>

