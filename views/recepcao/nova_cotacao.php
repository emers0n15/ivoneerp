<?php 
// Cotação usa a mesma estrutura de nova_fatura.php
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
header("location:nova_fatura.php?tipo=cotacao");
exit;
?>


