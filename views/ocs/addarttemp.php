<?php
session_start();
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/

$id = $_POST['idProduto'];


$sql = "SELECT preco, iva FROM produto WHERE idproduto = '$id'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_assoc($rs);
	$preco = $dados['preco'];
	$iva = $dados['iva'];
	$iva_incluso = $iva/100;
	$t=$preco*$iva_incluso;
	$sql = "SELECT qtd FROM ordem_compra_artigos_temp WHERE artigo = '$id' AND user = '$userID'";
	$rsx = mysqli_query($db, $sql) or die(mysqli_error($db));
	if (mysqli_num_rows($rsx) > 0) {
				$dadoxs = mysqli_fetch_assoc($rsx);
				$qt = $dadoxs['qtd'];
				$sql = "UPDATE ordem_compra_artigos_temp SET qtd = qtd + 1, total = total + '$preco', iva = total*'$iva_incluso' WHERE artigo = '$id' AND user = '$userID'";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
				if($rs > 0) {
	                echo 3;
	                exit;
	            } else {
	                echo 31;
	                exit;
	            }
			}else{
				$sql = "INSERT INTO ordem_compra_artigos_temp(artigo, qtd, preco,iva, total, user) VALUES('$id', 1, '$preco','$t', '$preco', '$userID')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
				if($rs > 0) {
	                echo 3;
	                exit;
	            } else {
	                echo 31;
	                exit;
	            }
			}
}else{
	echo 1;
}