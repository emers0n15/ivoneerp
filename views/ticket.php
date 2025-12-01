<?php 

session_start();

include_once '../conexao/index.php';

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



$mesa = $_GET['mesa'];

?>

<!DOCTYPE html>

<html>

<head>

	<title>Ticket Mesa#<?php echo $mesa; ?></title>

	<script type="text/javascript" src="../js/jquery-3.3.1.min.js"></script>
	<style type="text/css">
		*{
			font-size: 8pt;
		}
	</style>

</head>

<body>

<div class="header" style="text-align: center;">
	<img src="logo_pedaco.png" style="width: 110px;">
	<p>Tete - Cidade de Moatize</p>
	<p>Estrada Nacional NR 7</p>
	<p>Cell:855213692  -  NUIT: 401058303</p>
</div>

<div style="display: flex;flex-direction: row;justify-content: center;">

	<p>Mesa</p>

	<p style="margin-left: 30px;"><?php echo "$mesa"; ?></p>

</div>

<table style="width: 95%;margin: 5px auto;text-align: center;">

	<thead>

		<th>#</th>

		<th>Artigo</th>

		<th>Qtd</th>

	</thead>

	<tbody>

		<?php 

			$sql = "SELECT * FROM pos_mesas_temp_artigos WHERE mesa = '$mesa' AND cat_prod = 3";

			$rs = mysqli_query($db, $sql);

			while ($dados = mysqli_fetch_array($rs)) {

		?>

			<tr>

				<td><?php echo $dados['id']; ?></td>

				<td><?php echo $dados['artigo']; ?></td>

				<td><?php echo $dados['qtd']; ?></td>

			</tr>

		<?php

			}

		?>

	</tbody>

</table>

<p style="text-align: center;">Processado por: <?php echo $userNOME; ?></p>

<p style="text-align: center;"><?php echo $data_hora; ?></p>

<script type="text/javascript">

	$(function() {

		window.print();

	});

</script>

</body>

</html>

