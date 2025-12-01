<?php 
include_once '../../../conexao/index.php';

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
	<title>Relatorio de Caixas</title>
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
			<p>Banca Fipag</p>
			<p></p>
		</div>
		<div class="se">
			<p><?php echo "$diaS | $dia/$mes/$ano - $hora"; ?></p>
		</div>
		
	</div>
</div>
	<hr width="95%" size="3">
	<div class="right">
		<h1>RELATORIO CAIXAS</h1>
	</div>
	<!-- <hr width="95%" size="3"> -->
<div class="body">
	<table>
		<thead>
									<tr>
										<th>#</th>
										<th>Status</th>
										<th>Valor de Abertura</th>
										<th>Valor de Fecho</th>
										<th>Data de Abertura</th>
										<th>Data de Fecho</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$sql = "SELECT * FROM periodo";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
											$ab = $dados['aberturaperiodo'];
											$fech = $dados['fechoperiodo'];
									?>
										<tr> 
											<td><?php echo $dados['idperiodo']; ?></td>
											<td><?php echo $dados['diaperiodo']; ?></td>
											<td><?php echo "".number_format($ab,2,".",","); ?></td>
											<td><?php echo "".number_format($fech,2,".",","); ?></td>
											<td><?php echo $dados['dataaberturaperiodo']; ?></td>
											<td><?php echo $dados['datafechoperiodo']; ?></td>
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