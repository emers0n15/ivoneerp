<?php 
include_once '../../../conexao/index.php';
$data_ini = $_GET['data_inicio'];
$data_fin = $_GET['data_final'];
$hora_ini = $_GET['hora_inicio'];
$hora_fin = $_GET['hora_final'];
$data_inicio = "$data_ini $hora_ini";
$data_fim = "$data_fin $hora_fin";

// echo "$data_inicio <br> $data_fim";

$dia = date('d');
$diaS = date('D');
$mes = date('M');
$mess = date('m');
$ano = date('y');
$hora = date('H:m');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Relatorio de Vendas</title>
	<style type="text/css">
	body{margin: 0;padding: 0;font-family: sans-serif;}
		.header{
			margin: 0;
			width: 100%;
			display: flex;
			flex-direction: row;
			justify-content: space-between;
			/*background-color: #ccc;*/
		}
		.header .left{
			width: 95%;
			margin: 5px auto;
			display: flex;
			flex-direction: row;
			justify-content: space-between;
			
		}
		 .right {
			width: 95%;
			margin: 5px auto;
		}
		.body{
			width: 100%;
			margin: 0;
		}
		.body table{
			width: 95%;
			margin: 10px auto;
			text-align: center;
			border-collapse: collapse;
		}
		/*.body table thead{
			background-color: #ccc;
		}*/
		.rodape{
			width: 100%;
			margin: 10px 0;
			display: flex;
			flex-direction: row;
			justify-content: space-around;
		}
		hr{background-color: #000;color: #000}
		.pr{
			display: flex;
			flex-direction: row;
			justify-content: space-between;
			width: 60%;
		}
		.se{
			width: 30%;
			text-align: right;
		}
		@media print{	
			.print { display:none !important; } 
			body { background: #fff; }
		}
		.body table thead{
			border-top: 1px solid #000;
			border-bottom: 1px solid #000;
		}
	</style>
</head>
<body>
<div class="header">
	<div class="left">
		<div class="pr">
			<p>O Pedaco</p>
			<p></p>
		</div>
		<div class="se">
			<p><?php echo "$diaS | $dia/$mes/$ano - $hora"; ?></p>
		</div>
		
	</div>
</div>
	<hr width="95%" size="3">
	<div class="right">
		<h1>RELATORIO PERIODICO DE VENDAS</h1>
		<p>De <?php echo "$data_inicio - $data_fim"; ?></p>
	</div>
	<!-- <hr width="95%" size="3"> -->
<div class="body">
	<table>
		<thead>
			<th style="text-align: left;">#</th>
			<th style="text-align: left;">Descricao</th>
			<th>Qtd</th>
			<th>Preco</th>
			<th>Total</th>
			<!-- <th>Cliente</th> -->
			<th>Pedido</th>
			<th>Mesa</th>
			<th>Data</th>
		</thead>

		<tbody>
			<?php 
				$sql = "SELECT * FROM pos_mesas_artigos WHERE data BETWEEN '$data_inicio' AND '$data_fim'";
				$rs = mysqli_query($db, $sql);
				while ($dados = mysqli_fetch_array($rs)) {
					$id = $dados['id'];
					$produto = $dados['artigo'];
					$qtd = $dados['qtd'];
					$preco = $dados['preco'];
					$total = $dados['total'];
					// $cliente ="Consumidor Final";
					$factura = $dados['pos'];
					$mesa = $dados['mesa'];
					$dtv = $dados['data'];
			?>
				<tr>
					<td style="text-align: left;"><?php echo $id; ?></td>
					<td style="text-align: left;"><?php echo $produto; ?></td>
					<td><?php echo $qtd; ?></td>
					<td><?php echo "".number_format($preco,2,".",","); ?></td>
					<td><?php echo "".number_format($total,2,".",","); ?></td>
					<!-- <td><?php echo $cliente; ?></td> -->
					<td><?php echo $factura; ?></td>
					<td><?php echo $mesa; ?></td>
					<td><?php echo $dtv; ?></td>
				</tr>
			<?php
				}
				$sal = "SELECT SUM(preco) AS PRECO, SUM(total) AS TOT, SUM(qtd) AS QTD FROM pos_mesas_artigos WHERE data BETWEEN '$data_inicio' AND '$data_fim'";
				$rsa = mysqli_query($db, $sal);
				while ($dado = mysqli_fetch_array($rsa)) {
					$preco = $dado['PRECO'];
					$total = $dado['TOT'];
					$qtd = $dado['QTD'];
			?>
				<tr style="font-weight: bold;padding-top: 10px;font-size: 14pt;border-top: 1px solid #000;">
					<td colspan="2">Totais</td>
					<td><?php echo $qtd; ?></td>
					<td><?php echo "".number_format($preco,2,".",","); ?></td>
					<td><?php echo "".number_format($total,2,".",","); ?></td>
				</tr>
			<?php 
				} 
			?>
		</tbody>
	</table>
</div>
<hr width="95%" size="3">
<div class="rodape">
	<p>Processado por programa - iVone</p>
	<p>Documento Original</p>
</div>
<div class="print" style="text-align: center;">
		<a onclick="window.print();return false" class="print" style="cursor: pointer; " id="print"><img src = "../../../img/config/printer.png" width="32" height="32"></a>
	</div>
</body>
</html>