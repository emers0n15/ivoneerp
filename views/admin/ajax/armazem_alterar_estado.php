<?php
session_start();
include_once '../../../conexao/index.php';

if (!isset($_SESSION['idUsuario'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter dados do formulário
    $id = (int) mysqli_real_escape_string($db, $_POST['id']);
    $estado = mysqli_real_escape_string($db, $_POST['estado']);
    $usuario_id = $_SESSION['idUsuario'];
    
    // Validação básica
    if (empty($id) || !in_array($estado, ['ativo', 'inativo'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Dados inválidos']);
        exit;
    }
    
    // Verificar se o armazém existe
    $sqlVerificar = "SELECT id, estado FROM armazem WHERE id = $id";
    $resultadoVerificar = mysqli_query($db, $sqlVerificar);
    
    if (!$resultadoVerificar || mysqli_num_rows($resultadoVerificar) == 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Armazém não encontrado']);
        exit;
    }
    
    $armazem = mysqli_fetch_assoc($resultadoVerificar);
    
    // Verificar se o estado já é o mesmo
    if ($armazem['estado'] == $estado) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success', 
            'message' => 'O armazém já está com o estado ' . ($estado == 'ativo' ? 'ativo' : 'inativo')
        ]);
        exit;
    }
    
    // Atualizar estado
    $sql = "UPDATE armazem SET 
                estado = '$estado', 
                usuario_atualizacao = $usuario_id,
                data_atualizacao = NOW()
            WHERE id = $id";
    
    if (mysqli_query($db, $sql)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Estado do armazém alterado com sucesso para ' . ($estado == 'ativo' ? 'ativo' : 'inativo')
        ]);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Erro ao alterar estado do armazém: ' . mysqli_error($db)
        ]);
    }
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido']);
}
?>
