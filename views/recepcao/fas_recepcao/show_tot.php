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

$empresa_id = isset($_GET['empresa']) ? intval($_GET['empresa']) : null;

if($empresa_id && $empresa_id > 0) {
    $sql = "SELECT SUM(total) as t FROM fa_servicos_temp as st WHERE user = ? AND empresa_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $empresa_id);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT SUM(total) as t FROM fa_servicos_temp as st WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
}

$dados = mysqli_fetch_array($rs);
$total = $dados['t'] ?? 0;
echo number_format($total, 2, ',', '.') . ' MT';
?>

