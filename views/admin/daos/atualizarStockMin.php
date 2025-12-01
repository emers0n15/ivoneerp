<?php 

session_start();

error_reporting(E_ALL);

include '../../../conexao/index.php';

date_default_timezone_set('Africa/Maputo');



$sql = "SELECT COUNT(idproduto) as tot FROM `produto` INNER JOIN stock ON stock.produto_id = produto.idproduto WHERE stock.quantidade <= stock_min";

	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));

	while ($dados = mysqli_fetch_array($rs)) {
		echo $dados['tot'];
	}

?>
