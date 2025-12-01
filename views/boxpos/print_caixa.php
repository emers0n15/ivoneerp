<?php

session_start();

include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

$data_hora = date("Y-m-d H:i:s");

$userID = $_GET['id'];

$userNOME = $_SESSION['nomeUsuario'];

$userCATE = $_SESSION['categoriaUsuario'];

$id = $_GET['id'];

$em = "SELECT * FROM empresa";
$emr = mysqli_query($db, $em);
if(mysqli_num_rows($emr)> 0){
    $dad = mysqli_fetch_array($emr);
}
?>
<html>
<head>
	<title>Fecho de Caixa</title>
	<script type="text/javascript" src="../js/jquery-3.3.1.min.js"></script>
	<style type="text/css">
		*{
			font-size: 10pt;
		}
	</style>
</head>
<body onload="window.print()">
<div class="fatura">
<div class="header" style="text-align: center;">
    <p><?php echo $dad['nome'];?></p>
	<p><?php echo $dad['endereco'];?></p>
	<p>Contacto: <?php echo $dad['contacto'];?>  -  NUIT: <?php echo $dad['nuit'];?></p>
</div>
<div class="fact" style="text-align: center;">
	<p><span>Fecho</span><span class="pd" style="margin-left: 100px;"><?php echo $id; ?></span></p>
</div>
<div class="body" style="text-align: center;">
	<p class="p"><span>Documento Original</span><span style="margin-left: 10px;"><?php echo $data_hora; ?></span></p>
</div>
<table class="prodFactura" style="width: 95%;margin: 3px auto;text-align:center;">
	<thead class="titulo">
		<th>Data</th>
		<th>Abertura (Mt)</th>
		<th>Fecho (Mt)</th>
	</thead>
	<tbody>
		<?php 
			$q = "SELECT * FROM periodo WHERE idperiodo = '$id'";
			$c = mysqli_query($db, $q);
			while ($a = mysqli_fetch_array($c)) {
			$ab = $a['aberturaperiodo'];
			$fc = $a['fechoperiodo'];
		?>
			<tr>
				<td><?php echo $a['datafechoperiodo']; ?></td>
				<td><?php echo "".number_format($ab,2,".",","); ?></td>
				<td><?php echo "".number_format($fc,2,".",","); ?></td>
			</tr>
		<?php

			}
		?>
	</tbody>
</table>
<table class="prodFactura" style="width: 95%;margin: 3px auto;text-align:center;">
	<thead class="titulo">
		<th>Modo de Pagamento</th>
		<th>Valor (Mt)</th>
	</thead>
	<tbody>
		<?php 
			$q = "SELECT DISTINCT (SELECT descricao FROM metodo_pagamento as m WHERE m.id = pedido.modo) as modo, SUM(pagamentopedido+iva) as val FROM `pedido` WHERE periodo = '$id' GROUP BY modo";
			$c = mysqli_query($db, $q);
			while ($a = mysqli_fetch_array($c)) {
			$modo= $a['modo'];
			$val = $a['val'];
		?>
			<tr>
				<td><?php echo $modo; ?></td>
				<td><?php echo "".number_format($val,2,".",","); ?></td>
			</tr>
		<?php

			}
		?>
	</tbody>
</table>
<table class="prodFactura" style="width: 95%;margin: 3px auto;text-align:center;">
	<thead class="titulo">
		<th>Artigo</th>
		<th>Qtd</th>
		<th>Valor</th>
	</thead>
	<tbody>
		<?php 
			$q = "SELECT DISTINCT(SELECT nomeproduto FROM produto as p WHERE p.idproduto = ps.produtoentrega) as atg, sum(qtdentrega) as qtds, sum(totalentrega) as tot FROM entrega as ps WHERE periodo = '$id' GROUP BY atg";
				$d = mysqli_query($db, $q);
				while ($b = mysqli_fetch_array($d)) {
					$atg = $b['atg'];
					$qtds = $b['qtds'];
					$tot = $b['tot'];
					?>
						<tr>
							<td><?php echo $atg; ?></td>
							<td><?php echo $qtds; ?></td>
							<td><?php echo "".number_format($tot,2,".",","); ?></td>
						</tr>
					<?php
				}
		?>
	</tbody>
</table>
<div class="processo" style="text-align: center;">
	<p>iVone - Processado por programa</p>
	<p style="margin: 10px 0px;">Operador: <?php echo $_SESSION['nomeUsuario']; ?></p>
</div>

</div>
<script type="text/javascript">

	$(function() {

		window.print();

	});

</script>
</body>
</html>