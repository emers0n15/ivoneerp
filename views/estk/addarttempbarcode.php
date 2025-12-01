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

$artigo = $_POST['codbar'];
$qtd = 1;

$sql = "SELECT * FROM es_artigos_temp WHERE artigo = '$artigo'";
$rs = mysqli_query($db, $sql);
if (mysqli_num_rows($rs) > 0) {
	$sql = "UPDATE `es_artigos_temp`SET qtd = qtd + '$qtd' WHERE artigo = '$artigo'";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	echo 3;
}else{
	$sql = "INSERT INTO `es_artigos_temp`(`artigo`, `qtd`, `user`) VALUES ('$artigo', '$qtd', '$userID')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	echo 3;
}
