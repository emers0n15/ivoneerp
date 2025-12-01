<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$descricao = $_POST['descricao'];
	$sector = $_POST['sector'];

	$sql = "INSERT INTO familia_artigos(`descricao`, `setor`) VALUES('$descricao', '$sector')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		echo "<script>alert('Família inserida com sucesso!!'); </script>";
		echo "<script>window.location='../nova_familia_artigos.php'; </script>";
	}else{
		echo "<script>alert('Família não inserida.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../nova_familia_artigos.php'; </script>";
	}

}