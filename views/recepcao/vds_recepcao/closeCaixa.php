<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = intval($_POST['usar'] ?? $_SESSION['idUsuario']);
$data_hoje = date('Y-m-d');

$sql = "SELECT * FROM caixa_recepcao WHERE data = ? AND status = 'aberto'";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $data_hoje);
mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);

if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $valor_inicial = floatval($dados['valor_inicial']);
    $total_entradas = floatval($dados['total_entradas']);
    $saldo_final = $valor_inicial + $total_entradas;
    
    $sql_update = "UPDATE caixa_recepcao SET 
                  status = 'fechado',
                  saldo_final = ?,
                  usuario_fechamento = ?,
                  data_fechamento = NOW()
                  WHERE data = ? AND status = 'aberto'";
    $stmt_update = mysqli_prepare($db, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "dis", $saldo_final, $userID, $data_hoje);
    
    if(mysqli_stmt_execute($stmt_update)) {
        echo "<p class='mt-2' style='color: green;'>Caixa fechado com sucesso! Saldo final: " . number_format($saldo_final, 2, ',', '.') . " MT</p>";
    } else {
        echo "<p class='mt-2' style='color: red;'>Ocorreu um erro ao fechar o caixa!</p>";
    }
} else {
    echo "<p class='mt-2' style='color: red;'>Não existe nenhum caixa aberto para hoje!</p>";
}
?>

