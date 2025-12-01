<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$ano = $_POST['ano'];

	$sql = "INSERT INTO serie_factura(ano_fiscal) VALUES('$ano')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if ($rs > 0) {
		$sql = "ALTER TABLE `factura` AUTO_INCREMENT=1";
		$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
		if($rs > 0){
		    echo "<script>alert('Ano fiscal inserido com sucesso!!'); </script>";
			echo "<script>window.location='../ano_fiscal.php'; </script>";
		}
	}else{
		echo "<script>alert('Ocorreu um erro ao adicionar o ano fiscal.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../ano_fiscal.php'; </script>";
	}

}