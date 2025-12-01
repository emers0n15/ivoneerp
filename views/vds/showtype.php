<?php 
session_start();
include_once '../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/

$status = "Aberto";

$sql = "SELECT idperiodo FROM periodo WHERE diaperiodo = '$status' AND usuario = '$userID'";
$rs = mysqli_query($db, $sql);

if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_array($rs);
	$id = $dados['idperiodo'];
	$sqls = "SELECT DISTINCT (SELECT descricao FROM metodo_pagamento as m WHERE m.id = pedido.modo) as modo, SUM(pagamentopedido + iva) as v FROM `pedido` WHERE periodo = '$id' GROUP BY modo";
    $rss = mysqli_query($db, $sqls);
    while($dados1 = mysqli_fetch_array($rss)){
?>
    <nav class="navbar navbar-light bg-light col-sm-11 mt-1" style="margin-left: 4%;color:#333">
        <div class="col-sm-8" style="display: flex;flex-direction: row;justify-content: flex-start;">
            <h4 class="mt-3"><?php echo $dados1['modo']?></h4>
        </div>
        <div class="col-sm-4">
            <h2><?php echo number_format($dados1['v'], 2, '.', ',');?></h2>
        </div>
    </nav>
<?php
    }
}else{
	
}