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

$sql = "SELECT * FROM `pos_mesas` WHERE data BETWEEN '$data_inicial' AND '$data_final' ORDER BY id DESC";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	while ($dados = mysqli_fetch_array($rs)) {
		$preco = $dados['pagamento'];
?>
<tr>
	<td>Venda - <?php echo $dados['id']; ?></td>
	<td><?php echo "".number_format($preco,2,".",","); ?></td>
	<td><?php echo $dados['descricao']; ?></td>
</tr>
<?php
	}
?>