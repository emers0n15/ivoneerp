<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$nome = $_POST['nome'];
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$categoria = $_POST['categoria'];

	$sql = "INSERT INTO users(nome, user, pass, categoria) VALUES('$nome', '$user', '$pass', '$categoria')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		echo "<script>alert('Utilizador inserido com sucesso!!'); </script>";
		echo "<script>window.location='../novo_utilizador.php'; </script>";
	}else{
		echo "<script>alert('Utilizador não inserido.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../novo_utilizador.php'; </script>";
	}

}