<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$descricao = $_POST['descricao'];

	$sql = "INSERT INTO condicao_pagamento(descricao) VALUES('$descricao')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if ($rs > 0) {
		echo "<script>alert('Condição inserida com sucesso!!'); </script>";
		echo "<script>window.location='../condicoes_pagamento.php'; </script>";
	}else{
		echo "<script>alert('Ocorreu um erro ao adicionar a condição.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../condicoes_pagamento.php'; </script>";
	}

}