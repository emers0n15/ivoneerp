<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$id = $_POST['id'] ?? null;
if(!$id) {
    echo "Erro: ID não fornecido";
    exit;
}

$sql = "UPDATE ct_servicos_temp SET qtd = qtd + 1, total = (qtd + 1) * preco WHERE id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
?>

