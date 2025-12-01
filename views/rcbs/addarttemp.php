<?php
session_start();
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/

$id = $_POST['idfatura'];
$sql = "SELECT * FROM factura WHERE id = '$id'";
$rsx = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rsx) > 0) {
	$dadoxs = mysqli_fetch_assoc($rsx);
	$valor = $dadoxs['valor'];
	$iva = $dadoxs['iva'];
	$serie = $dadoxs['serie'];
	$n_doc = $dadoxs['n_doc'];
	$cliente = $dadoxs['cliente'];
	$novo_valor = $valor + $iva;
}

$sql = "INSERT INTO rc_fact_temp SET factura = '$n_doc', valor = '$valor', iva = '$iva', total = '$novo_valor',serie = '$serie',cliente = '$cliente', user = '$userID'";
$rsx = mysqli_query($db, $sql) or die(mysqli_error($db));
