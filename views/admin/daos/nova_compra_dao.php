<?php 
session_start();
include '../../../conexao/index.php';

error_reporting(E_ALL);

if (isset($_POST['btn'])) {
	$descricao = $_POST['descricao'];
	$custo = $_POST['custo'];
	$data = $_POST['data'];
	$hora = $_POST['hora'];

	$sql = "INSERT INTO `compras`(`descricao`, `valor`, `data`, `hora`) VALUES ('$descricao', '$custo', '$data', '$hora')";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	if ($rs > 0) {
		$sql1 = "SELECT MAX(id) as id FROM compras";
		$rs1 = mysqli_query($db, $sql1) or die(mysqli_error($db));
		$dados = mysqli_fetch_assoc($rs1);
		$id = $dados['id'];
		if (mysqli_num_rows($rs1) > 0) {
			$sql2 = "SELECT idproduto FROM produto";
			$rs2 = mysqli_query($db, $sql2) or die(mysqli_error($db));
			while ($dados2 = mysqli_fetch_assoc($rs2)) {
				$id_artigo = $dados2['idproduto'];
				$sql3 = "INSERT INTO itens_comprados(id_artigo, qtd, id_compra) VALUES('$id_artigo', '0', '$id')";
				$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
			}
		}
		echo "<script>window.location.href='../itens_comprados.php?id_compra=$id'</script>";
	}
}