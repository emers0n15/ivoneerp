<?php 
session_start();
include '../../../conexao/index.php';

error_reporting(E_ALL);

	$id_artigo = $_POST['id'];
	$id_compra = $_POST['id_compra'];
	$valor = $_POST['valor'];

	$sql = "UPDATE itens_comprados SET qtd = qtd + '$valor' WHERE id_artigo = '$id_artigo' AND id_compra = '$id_compra'";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if ($rs > 0) {
		$sql = "UPDATE produto SET stock = stock + '$valor' WHERE idproduto = '$id_artigo'";
		$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
		if ($rs > 0) {
			echo "Qtd do artigo $id_artigo atualizado com sucesso!";
		}else{
			echo "Erro ao atualizar a qtd do artigo $id_artigo!";
		}
	}else{
		echo "Erro ao inserir a qtd do artigo comprado!";
	}