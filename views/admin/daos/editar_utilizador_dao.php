<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$id = $_POST['id'];
	$nome = $_POST['nome'];
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$categoria = $_POST['categoria'];

	$sql = "UPDATE users SET nome = '$nome', user = '$user', pass = '$pass', categoria = '$categoria' WHERE  id = '$id'";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		echo "<script>alert('Utilizador editado com sucesso!!'); </script>";
		echo "<script>window.location='../utilizadores.php'; </script>";
	}else{
		echo "<script>alert('Utilizador não editado.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../utilizadores.php'; </script>";
	}

}