<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$id = $_POST['id'];
	$nome = $_POST['nome'];
	$apelido = $_POST['apelido'];
	$nuit = $_POST['nuit'];
	$contacto = $_POST['contacto'];
	$endereco = $_POST['endereco'];
	$desconto = $_POST['desconto'];

	$sql = "UPDATE clientes SET nome = '$nome', apelido = '$apelido', nuit = '$nuit', contacto = '$contacto', endereco = '$endereco', desconto = '$desconto' WHERE id = '$id'";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if($rs > 0){
		echo "<script>alert('Cliente editado com sucesso!!'); </script>";
		echo "<script>window.location='../clientes.php'; </script>";
	}else{
		echo "<script>alert('Cliente não editado.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../editar_cliente.php?id_cliente='"+$id+"''; </script>";
	}

}