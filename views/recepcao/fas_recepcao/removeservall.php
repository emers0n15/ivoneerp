<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;
/*********************************************/

$sql = "DELETE FROM fa_servicos_temp WHERE user = '$userID'";
$rs = mysqli_query($db, $sql);
