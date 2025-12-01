<?php 
session_start();
include_once '../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");
$y = date("Y");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/


$valor = $_POST['valor'];
$usar = $_POST['usar'];
$status = "Aberto";


$sql = "SELECT diaperiodo FROM periodo WHERE diaperiodo = '$status' AND usuario = '$usar' AND serie = '$y'";
$rs = mysqli_query($db, $sql);
if (mysqli_num_rows($rs) > 0) {
	echo "<p class='mt-2'>O caixa ja se encontra aberto para este usuario!</p>";
}else{
	$sql = "INSERT INTO periodo(diaperiodo, serie, aberturaperiodo, fechoperiodo, usuario, dataaberturaperiodo, datafechoperiodo) 
	VALUES('$status', '$y', '$valor', '$valor', '$usar', '$data_hora', '$data_hora')";
	$rs = mysqli_query($db, $sql);
	if ($rs > 0) {
		echo "<p class='mt-2' style='color: gree;'>Caixa aberto com sucesso</p>";
	}else{
		echo "<p class='mt-2' style='color: red;'>Ocorreu um erro ao abrir o caixa!</p>";
	}	

}