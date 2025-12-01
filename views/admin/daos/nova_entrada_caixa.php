<?php 
session_start();
include '../../../conexao/index.php';

date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");
$data = date("Y-m-d");

/*Variaveis do Sistema*/
/*********************************************/
$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/

if (isset($_POST['btn'])) {
	$valor = $_POST['valor'];
	$caixa = $_POST['caixa'];

	$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if (mysqli_num_rows($rs) > 0) {
		$dados = mysqli_fetch_array($rs);
		$serie = $dados['serie'];
	}

	$sql = "INSERT INTO entrada_caixa(`serie`, `valor`, `user`, `caixa`) VALUES('$serie', '$valor', '$userID', '$caixa')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		$sql = "UPDATE periodo SET fechoperiodo = fechoperiodo + '$valor' WHERE diaperiodo = 'Aberto' AND idperiodo = '$caixa'";
		$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
		if ($rs > 0) {
			echo "<script>alert('Entrada inserida com sucesso!!'); </script>";
			echo "<script>window.location='../entrada_caixa.php'; </script>";
		}else{
		echo "<script>alert('Cliente não inserido.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../entrada_caixa_conf.php'; </script>";
	}
	}

}