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

$id = $_POST['id'] ?? null;
if(!$id) {
    echo "Erro: ID não fornecido";
    exit;
}

// Verificar se a quantidade é maior que 1 antes de diminuir
$sql_check = "SELECT qtd FROM fa_servicos_temp WHERE id = ?";
$stmt_check = mysqli_prepare($db, $sql_check);
mysqli_stmt_bind_param($stmt_check, "i", $id);
mysqli_stmt_execute($stmt_check);
$rs_check = mysqli_stmt_get_result($stmt_check);
$dados_check = mysqli_fetch_array($rs_check);

if($dados_check && $dados_check['qtd'] > 1) {
    // Atualizar quantidade e recalcular total
    $sql = "UPDATE fa_servicos_temp SET qtd = qtd - 1, total = (qtd - 1) * preco WHERE id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
} else {
    // Se quantidade for 1, remover o item
    $sql = "DELETE FROM fa_servicos_temp WHERE id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}
