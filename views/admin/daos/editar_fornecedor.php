<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$id = $_POST['id'];
	$nome = $_POST['nome'];
	$nuit = $_POST['nuit'];
	$contacto = $_POST['contacto'];
	$endereco = $_POST['endereco'];

	$sql = "UPDATE fornecedor SET nome = '$nome', nuit = '$nuit', contacto = '$contacto', endereco = '$endereco' WHERE id = '$id'";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		echo "<script>alert('Fornecedor editado com sucesso!!'); </script>";
		echo "<script>window.location='../fornecedores.php'; </script>";
	}else{
		echo "<script>alert('Fornecedor não editado.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../editar_fornecedor.php?id='"+$id+"''; </script>";
	}

}