<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$id = $_POST['id'] ?? null;
$novoValor = isset($_POST['novoValor']) ? intval($_POST['novoValor']) : 1;

if(!$id || $novoValor < 1) {
    echo "Erro: Parâmetros inválidos";
    exit;
}

$sql = "UPDATE nc_servicos_temp SET qtd = ?, total = ? * preco WHERE id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "iii", $novoValor, $novoValor, $id);
mysqli_stmt_execute($stmt);
?>

