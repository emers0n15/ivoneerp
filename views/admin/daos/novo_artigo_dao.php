<?php 
session_start();
include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
	$nome_artigo = $_POST['nome_artigo'];
	$stock = $_POST['stock'];
	$preco = $_POST['preco'];
	$preco_compra = $_POST['preco_compra'];
	$codbar = $_POST['codbar'];
	$grupo = $_POST['grupo'];
    $familia = $_POST['familia'];
    $iva = $_POST['iva'];
    $prefixo = $_POST['prefixo'];
    $stocavel = $_POST['stocavel'];

	$sql = "SELECT * FROM produto WHERE nomeproduto = '$nome_artigo'";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if (mysqli_num_rows($rs) > 0) {
		echo "<script>alert('Este artigo já se encontra registrado!')</script>";
		echo "<script>window.location.href='../novo_artigo.php'</script>";
	}
		    $sql = "INSERT INTO produto(nomeproduto, stock_min,stocavel, preco_compra, preco, iva, codbar, grupo, familia, prefico) VALUES('$nome_artigo','$stock','$stocavel','$preco_compra','$preco', '$iva', '$codbar', '$grupo', '$familia', '$prefixo')";
		    $rs = mysqli_query($db, $sql) or die (mysqli_error($db));
		    if($rs > 0){
		        echo "<script>alert('Artigo inserido com sucesso!!'); </script>";
				echo "<script>window.location='../novo_artigo.php'; </script>";
		    }else{
		        echo "<script>alert('Artigo não inserido.....Por favor tente novamente!'); </script>";
				echo "<script>window.location='../novo_artigo.php'; </script>";
		    }

}