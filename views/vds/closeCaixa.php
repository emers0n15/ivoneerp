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

$usar = $_POST['usar'];
$status = "Aberto";

$sql = "SELECT diaperiodo, fechoperiodo FROM periodo WHERE diaperiodo = '$status' AND usuario = '$usar'";
$rs = mysqli_query($db, $sql);
if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_array($rs);
	$val = $dados['fechoperiodo'];
	$sql = "UPDATE periodo SET diaperiodo = 'Fechado' WHERE diaperiodo = '$status' AND usuario = '$usar'";
	$rs = mysqli_query($db, $sql);
	$sqlx = "SELECT * FROM balanco_saldo";
	$rsx = mysqli_query($db, $sqlx);
	if (mysqli_num_rows($rsx)) {
		$sql = "UPDATE balanco_saldo SET saldo = saldo + '$val', data1 = '$data_hora' WHERE 1";
		$rs = mysqli_query($db, $sql);
	}else{
		$sql = "INSERT INTO balanco_saldo(saldo, data1) VALUES('$val', '$data_hora')";
		$rs = mysqli_query($db, $sql);
	}
	if ($rs > 0) {
		echo "<p class='mt-2' style='color: gree;'>Caixa fechado com sucesso</p>";
	}else{
		echo "<p class='mt-2' style='color: red;'>Ocorreu um erro ao fechar o caixa!</p>";
	}	
}else{
    echo "<p class='mt-2' style='color: red;'>Nao existe nenhum caixa aberto para este usuario!</p>";
}