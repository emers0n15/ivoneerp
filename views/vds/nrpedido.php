<?php 
session_start();
include_once '../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;
/*********************************************/
$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_array($rs);
	$serie = $dados['serie'];
}

$sql = "SELECT (MAX(n_doc)+1) as id FROM pedido WHERE serie = '$serie'";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
echo "Venda a Dinheiro - ".$dados['id'];
?>

