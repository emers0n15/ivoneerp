<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$nome = $_POST['nome'];
	$apelido = $_POST['apelido'];
	$nuit = $_POST['nuit'];
	$contacto = $_POST['contacto'];
	$endereco = $_POST['endereco'];
	$desconto = $_POST['desconto'];

	$sql = "INSERT INTO clientes(`nome`, `nuit`, `apelido`, `contacto`, `endereco`, desconto) VALUES('$nome', '$nuit', '$apelido', '$contacto', '$endereco', '$desconto')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		echo "<script>alert('Cliente inserido com sucesso!!'); </script>";
		echo "<script>window.location='../clientes.php'; </script>";
	}else{
		echo "<script>alert('Cliente não inserido.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../novo_cliente.php'; </script>";
	}

}