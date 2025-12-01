<?php 
session_start();
error_reporting(E_ALL);
include '../../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_ini = date("Y-m-d");
$hora_ini = "00:00:00";
$data_fin = date("Y-m-d");
$hora_fin = "23:59:00";
$data_inicial = "$data_ini $hora_ini";
$data_final = "$data_fin $hora_fin";


	$sql = "SELECT (SELECT img FROM produto as p WHERE p.nomeproduto = p.artigo) as imgg, `artigo`, SUM(`qtd`) AS Qtd, SUM(`total`) as Valor FROM pos_mesas_artigos as p WHERE data BETWEEN '$data_inicial' AND '$data_final' GROUP BY `artigo` ORDER BY Qtd DESC LIMIT 8";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
		while ($dados = mysqli_fetch_array($rs)) {
			$preco = $dados['Valor'];
	?>
	<tr>
		<td style="min-width: 200px;">
			<a class="avatar"><img src="../../img/imageProduto/<?php echo $dados['imgg'] ?>"></a>
			<h2><a><?php echo $dados['artigo']; ?> </a></h2>
		</td>                 
		<td>
			<h5 class="time-title p-0">Qtd</h5>
			<p><?php echo $dados['Qtd']; ?></p>
		</td>
		<td>
			<h5 class="time-title p-0">Valor Total</h5>
			<p><?php echo "".number_format($preco,2,".",","); ?></p>
		</td>
	</tr>
<?php
	}
?>