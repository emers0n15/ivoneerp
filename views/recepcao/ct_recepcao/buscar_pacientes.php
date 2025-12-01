<?php
session_start();
include_once '../../../conexao/index.php';
header('Content-Type: application/json');

$empresa_id = isset($_GET['empresa_id']) ? intval($_GET['empresa_id']) : null;

if($empresa_id && $empresa_id > 0) {
    $sql = "SELECT id, nome, apelido, numero_processo FROM pacientes WHERE empresa_id = ? AND ativo = 1 ORDER BY nome, apelido";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $empresa_id);
} else {
    $sql = "SELECT id, nome, apelido, numero_processo FROM pacientes WHERE ativo = 1 ORDER BY nome, apelido";
    $stmt = mysqli_prepare($db, $sql);
}

mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);

$pacientes = [];
while($row = mysqli_fetch_assoc($rs)) {
    $pacientes[] = $row;
}

echo json_encode($pacientes);
?>

