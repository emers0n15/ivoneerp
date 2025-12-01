<?php 
include_once '../../conexao/index.php';
error_reporting(E_ALL);
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
	<title>Extrato de Facturas - Geral</title>
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
		table tr td{
			padding: 10px;
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
			<p>iProjects</p>
			<p></p>
		</div>
		<div class="se">
			<p><?php echo "$diaS | $dia/$mes/$ano - $hora"; ?></p>
		</div>
		
	</div>
</div>
	<hr width="95%" size="3">
	<div class="right">
		<h1>EXTRATO DE FACTURAS - GERAL</h1>
	</div>
	<!-- <hr width="95%" size="3"> -->
<div class="body">
	<table>
		<thead>
			<th style="text-align: left;">#</th>
			<th style="text-align: left;">Descricao</th>
			<th>Valor</th>
            <th>IVA(16%)</th>
            <th>Cliente</th>
            <th>Prazo</th>
            <th>Status</th>
            <th>Recibo</th>
            <th>N/ Crédito</th>
            <th>N/ Débito</th>
		</thead>

		<tbody>
			<?php 
				$sql = "SELECT `id`, `descricao`, `valor`, `iva`, `serie`, `prazo`, `metodo`, `statuss`, `nota_credito`, `recibo`, `nota_debito`, `cotacao`, (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = factura.cliente) as cliente, (SELECT nome FROM users WHERE users.id = factura.usuario) as usuario FROM `factura`";
				$rs = mysqli_query($db, $sql);
				while ($dados = mysqli_fetch_array($rs)) {
					$id = $dados['id'];
					$valor = $dados['valor'];
			?>
				<tr>
					<td><?php echo $dados['id']; ?></td>
					<td><?php echo "FA#".$dados['id']."/".$dados['serie']; ?></td>
                    <td>
                    	<?php 
                    		if ($dados['nota_credito'] != 0) {
                    			$sql1 = "SELECT valor FROM nota_de_credito WHERE id_factura = '$id'";
                    			$rs1 = mysqli_query($db, $sql1);
                    			$dados1 = mysqli_fetch_array($rs1);
                    			$nota = $dados1['valor'];
                    			$t = $valor - $nota;
                    			echo "".number_format($t,2,".",",");
                    		}else{
                    			echo "".number_format($valor,2,".",",");
                    		}
                    	?>
                	</td>
                    <td><?php echo $dados['iva']; ?></td>
                    <td><?php echo $dados['cliente']; ?></td>
                    <td><?php echo $dados['prazo']; ?></td>
                    <td>
                    	<?php 
				        	if ($dados['statuss'] == 1) {
					    		echo "<b style='color: green'>Paga</b>";
					    	}else{
					    		"<b style='color: red'>Pendente</b>";
					    	}
				         ?>
                    </td>
                    <td><?php echo $dados['recibo']; ?></td>
                    <td><?php echo $dados['nota_credito']; ?></td>
                    <td><?php echo $dados['nota_debito']; ?></td>
				</tr>
				<?php 
					if ($dados['recibo'] != 0) {
	                 	$sql3 = "SELECT * FROM recibo WHERE factura = '$id'";
	                 	$rs3 = mysqli_query($db, $sql3);
	                 	while ($dados3 = mysqli_fetch_array($rs3)) {
	            ?>
	            	<tr>
	            		<td> - </td>
						<td><?php echo "RC#".$dados3['id']."/".$dados3['serie']." - FA#".$dados['id']."/".$dados['serie']; ?></td>
						<td><?php echo $dados3['valor']; ?></td>
						<td> - </td>
						<td><?php echo $dados['cliente']; ?></td>
						<td colspan="5"> - </td>
	            	</tr>
	            <?php
	                 	}
                	}
				?>
				<?php 
					if ($dados['nota_credito'] != 0) {
	                 	$sql2 = "SELECT * FROM nota_de_credito WHERE id_factura = '$id'";
	                 	$rs2 = mysqli_query($db, $sql2);
	                 	while ($dados2 = mysqli_fetch_array($rs2)) {
	            ?>
	            	<tr>
	            		<td> - </td>
						<td><?php echo "NC#".$dados2['id']."/".$dados2['serie']." - FA#".$dados['id']."/".$dados['serie']; ?></td>
						<td><?php echo $dados2['valor']; ?></td>
						<td> - </td>
						<td><?php echo $dados['cliente']; ?></td>
						<td colspan="5"> - </td>
	            	</tr>
	            <?php
	                 	}
                	}
				?>
			<?php
				}
			?>
			<?php 
	                $sql4 = "SELECT SUM(`valor`) as total FROM `factura` WHERE nota_credito = 0 AND `recibo` = 0";
	                $rs4 = mysqli_query($db, $sql4);
	                while ($dados4 = mysqli_fetch_array($rs4)) {
	            ?>
	            	<tr>
	            		<td> - </td>
						<td><b>Total em Divida</b></td>
						<td><?php echo $dados4['total']; ?></td>
						<td colspan="7"> - </td>
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