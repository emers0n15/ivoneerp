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
    $nome = mysqli_real_escape_string($db, $_POST['nome']);
    $endereco = isset($_POST['endereco']) ? mysqli_real_escape_string($db, $_POST['endereco']) : null;
    $telefone = isset($_POST['telefone']) ? mysqli_real_escape_string($db, $_POST['telefone']) : null;
    $responsavel = isset($_POST['responsavel']) ? mysqli_real_escape_string($db, $_POST['responsavel']) : null;
    $descricao = isset($_POST['descricao']) ? mysqli_real_escape_string($db, $_POST['descricao']) : null;
    $usuario_id = $_SESSION['idUsuario'];
    
    // Validação básica
    if (empty($id) || empty($nome)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'ID e nome do armazém são obrigatórios']);
        exit;
    }
    
    // Verificar se o armazém existe
    $sqlVerificar = "SELECT id FROM armazem WHERE id = $id";
    $resultadoVerificar = mysqli_query($db, $sqlVerificar);
    
    if (!$resultadoVerificar || mysqli_num_rows($resultadoVerificar) == 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Armazém não encontrado']);
        exit;
    }
    
    // Verificar se já existe outro armazém com este nome
    $sqlVerificarNome = "SELECT id FROM armazem WHERE nome = '$nome' AND id != $id";
    $resultadoVerificarNome = mysqli_query($db, $sqlVerificarNome);
    
    if ($resultadoVerificarNome && mysqli_num_rows($resultadoVerificarNome) > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Já existe outro armazém com este nome']);
        exit;
    }
    
    // Atualizar armazém
    $sql = "UPDATE armazem SET 
                nome = '$nome', 
                endereco = " . ($endereco ? "'$endereco'" : "NULL") . ", 
                telefone = " . ($telefone ? "'$telefone'" : "NULL") . ", 
                responsavel = " . ($responsavel ? "'$responsavel'" : "NULL") . ", 
                descricao = " . ($descricao ? "'$descricao'" : "NULL") . ", 
                usuario_atualizacao = $usuario_id,
                data_atualizacao = NOW()
            WHERE id = $id";
    
    if (mysqli_query($db, $sql)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Armazém atualizado com sucesso'
        ]);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Erro ao atualizar armazém: ' . mysqli_error($db)
        ]);
    }
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido']);
}
?>
