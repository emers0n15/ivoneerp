<?php 
// VDS usa a mesma estrutura de nova_fatura.php
// Redirecionar para nova_fatura.php com parâmetro
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
header("location:nova_fatura.php?tipo=vds");
exit;
?>

