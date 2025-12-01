<?php 
session_start();
include_once '../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/

$status = "Aberto";

$sql = "SELECT aberturaperiodo FROM periodo WHERE diaperiodo = 'Aberto' AND usuario = '$userID'";
$rs = mysqli_query($db, $sql);

if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_array($rs);
	$valor = $dados['aberturaperiodo'];
	echo number_format($valor, 2, '.', ',');
}else{
	echo "Caixa Fechado";
}