<?php 
session_start();
include_once '../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;
/*********************************************/

$sql = "SELECT SUM(total+iva) as t FROM ordem_compra_artigos_temp as ps WHERE user = '$userID'";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
echo number_format($dados['t'], 2, ',', '.');
?>

