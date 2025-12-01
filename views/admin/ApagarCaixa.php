<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$id = $_GET['id'];

$sql = "DELETE FROM periodo WHERE idperiodo = '$id' AND diaperiodo = 'Fechado'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if ($rs > 0) {
    $sql = "DELETE FROM pedido WHERE periodo = '$id'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
}
?>