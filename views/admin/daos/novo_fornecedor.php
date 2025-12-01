<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$nome = $_POST['nome'];
	$nuit = $_POST['nuit'];
	$contacto = $_POST['contacto'];
	$endereco = $_POST['endereco'];

	$sql = "INSERT INTO fornecedor(`nome`, `nuit`, `contacto`, `endereco`) VALUES('$nome', '$nuit', '$contacto', '$endereco')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		echo "<script>alert('Fornecedor inserido com sucesso!!'); </script>";
		echo "<script>window.location='../fornecedores.php'; </script>";
	}else{
		echo "<script>alert('Fornecedor não inserido.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../novo_fornecedor.php'; </script>";
	}

}