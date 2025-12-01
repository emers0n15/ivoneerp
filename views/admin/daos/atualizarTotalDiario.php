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

$sql = "SELECT COUNT(`idpedido`) as totped, SUM(`pagamentopedido`) as totval FROM `pedido` WHERE data BETWEEN '$data_inicial' AND '$data_final'";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
	while ($dados = mysqli_fetch_array($rs)) {
		$vl = $dados['totval'];
?>
	<h4><?php echo "".number_format($vl,2,".",","); ?></h4>
	<span class="widget-title1">Total diario <i class="fa fa-check" aria-hidden="true"></i></span>
<?php
	}
?>