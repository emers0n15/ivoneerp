<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$ano = date('Y');
$sql = "SELECT COUNT(*) as total FROM faturas_atendimento WHERE YEAR(data_criacao) = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $ano);
mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);
$total_existente = 0;
if($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $total_existente = intval($dados['total']);
}
$numero_fatura = 'FA-' . $ano . '-' . str_pad($total_existente + 1, 6, '0', STR_PAD_LEFT);
echo $numero_fatura;
?>

