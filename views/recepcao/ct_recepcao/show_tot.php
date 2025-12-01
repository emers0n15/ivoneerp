<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$userID = $_SESSION['idUsuario'] ?? null;
$empresa_id = isset($_GET['empresa']) ? intval($_GET['empresa']) : null;

if($empresa_id && $empresa_id > 0) {
    $sql = "SELECT SUM(total) as t FROM ct_servicos_temp as st WHERE user = ? AND empresa_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $empresa_id);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT SUM(total) as t FROM ct_servicos_temp as st WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
}

$dados = mysqli_fetch_array($rs);
$total = $dados['t'] ?? 0;
echo number_format($total, 2, ',', '.') . ' MT';
?>

