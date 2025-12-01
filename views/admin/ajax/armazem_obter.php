<?php
session_start();
include_once '../../../conexao/index.php';

if (!isset($_SESSION['idUsuario'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int) mysqli_real_escape_string($db, $_GET['id']);
    
    $sql = "SELECT * FROM armazem WHERE id = $id";
    $resultado = mysqli_query($db, $sql);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $armazem = mysqli_fetch_assoc($resultado);
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success',
            'data' => $armazem
        ]);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Armazém não encontrado']);
    }
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'ID do armazém não informado']);
}
?>
