<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$data_hoje = date('Y-m-d');

$sql = "SELECT valor_inicial, total_entradas FROM caixa_recepcao WHERE data = ? AND status = 'aberto'";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $data_hoje);
mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);

if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $valor_inicial = floatval($dados['valor_inicial']);
    $total_entradas = floatval($dados['total_entradas']);
    $valor_fecho = $valor_inicial + $total_entradas;
    echo number_format($valor_fecho, 2, ',', '.') . ' MT';
} else {
    echo '0,00 MT';
}
?>

