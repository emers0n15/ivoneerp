<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$data_hoje = date('Y-m-d');

$sql = "SELECT valor_inicial FROM caixa_recepcao WHERE data = ? AND status = 'aberto'";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $data_hoje);
mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);

if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $valor = floatval($dados['valor_inicial']);
    echo number_format($valor, 2, ',', '.') . ' MT';
} else {
    echo '<span style="color: #dc3545; font-weight: bold;">Caixa Fechado</span>';
}
?>

