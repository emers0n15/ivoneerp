<?php 
session_start();
error_reporting(E_ALL);
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$nome_artigo = $_POST['nome_artigo'];
	$stock = $_POST['stock'];
	$preco = $_POST['preco'];
	$preco_compra = $_POST['preco_compra'];
	$codbar = $_POST['codbar'];
	$stocavel = $_POST['stocavel'];

	$id = $_POST['id'];

	$sql = "UPDATE produto SET nomeproduto = '$nome_artigo', stock_min = '$stock', preco_compra = '$preco_compra', preco = '$preco', codbar = '$codbar', stocavel = '$stocavel' WHERE idproduto = '$id'";
	$rs = mysqli_query($db, $sql) or die (mysqli_error());
	if($rs > 0){
		echo "<script>alert('Artigo editado com sucesso!!'); </script>";
		echo "<script>window.location='../produtos.php'; </script>";
	}else{
		echo "<script>alert('Artigo não editado.....Por favor tente novamente!'); </script>";
		echo "<script>window.location='../produtos.php'; </script>";
	}
}