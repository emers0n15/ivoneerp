<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$descricao = $_POST['descricao'];
	$familia = $_POST['familia'];

	$sql = "INSERT INTO grupo_artigos(`descricao`, `familia`) VALUES('$descricao', '$familia')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		echo "<script>alert('Grupo inserido com sucesso!!'); </script>";
		echo "<script>window.location='../novo_grupo_artigos.php'; </script>";
	}else{
		echo "<script>alert('Grupo não inserido.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../novo_grupo_artigos.php'; </script>";
	}

}