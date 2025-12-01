<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;
/*********************************************/

$id = $_POST['id'] ?? null;
$novoValor = isset($_POST['novoValor']) ? intval($_POST['novoValor']) : 1;

if(!$id || $novoValor < 1) {
    echo "Erro: Parâmetros inválidos";
    exit;
}

// Atualizar quantidade e recalcular total usando o novoValor
$sql = "UPDATE fa_servicos_temp SET qtd = ?, total = ? * preco WHERE id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "iii", $novoValor, $novoValor, $id);
mysqli_stmt_execute($stmt);
