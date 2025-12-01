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

$id = $_POST['id'];
$s = "SELECT produtofiladeespera FROM filadeespera WHERE idfiladeespera = '$id'";
$r = mysqli_query($db, $s);
if (mysqli_num_rows($r)) {
	$d = mysqli_fetch_array($r);
	$p = $d['produtofiladeespera'];
}

$ss = "SELECT iva FROM produto WHERE idproduto = '$p'";
$rr = mysqli_query($db, $ss);
if (mysqli_num_rows($rr)) {
	$dd = mysqli_fetch_array($rr);
	$iva = $dd['iva'];
	$ivas = $iva/100;
}

$sql = "UPDATE filadeespera SET qtdfiladeespera = qtdfiladeespera - 1, totalfiladeespera = qtdfiladeespera*precofiladeespera, iva = (totalfiladeespera*'$ivas') WHERE idfiladeespera = '$id'";
$rs = mysqli_query($db, $sql);