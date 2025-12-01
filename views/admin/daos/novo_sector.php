<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$nome = $_POST['nome'];
	$responsavel = $_POST['responsavel'];

	$sql = "INSERT INTO sector(nome, responsavel) VALUES('$nome', '$responsavel')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if ($rs > 0) {
		echo "<script>alert('Sector inserido com sucesso!!'); </script>";
		echo "<script>window.location='../sectores.php'; </script>";
	}else{
		echo "<script>alert('Ocorreu um erro ao adicionar o sector.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../sectores.php'; </script>";
	}

}