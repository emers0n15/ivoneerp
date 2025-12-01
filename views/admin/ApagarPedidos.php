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
$caixa = $_GET['caixa'];

$sql = "SELECT pagamentopedido FROM pedido WHERE idpedido = '$id'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $valor = $dados['pagamentopedido'];
    $sql = "UPDATE periodo SET fechoperiodo = fechoperiodo - '$valor' WHERE idperiodo = '$caixa'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    if ($rs > 0) {
        $sql = "DELETE FROM entrega WHERE pedidoentrega = '$id'";
        $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
        if ($rs > 0) {
            $sql = "DELETE FROM pedido WHERE idpedido = '$id'";
            $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
        }
    }
}


?>