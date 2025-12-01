<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$percentagem = $_POST['percentagem'];
	$motivo = $_POST['motivo'];

	$sql = "INSERT INTO iva(percentagem, motivo) VALUES('$percentagem', '$motivo')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if ($rs > 0) {
		echo "<script>alert('IVA inserido com sucesso!!'); </script>";
		echo "<script>window.location='../tipos_iva.php'; </script>";
	}else{
		echo "<script>alert('Ocorreu um erro ao adicionar o IVA.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../tipos_iva.php'; </script>";
	}

}