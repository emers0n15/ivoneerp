<?php
session_start();
include_once '../../../conexao/index.php';

// Definir a codificação UTF-8 para garantir que os caracteres especiais sejam tratados corretamente
mysqli_set_charset($db, "utf8");

if (!isset($_SESSION['idUsuario'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter dados do formulário
    $nome = mysqli_real_escape_string($db, $_POST['nome']);
    $endereco = isset($_POST['endereco']) ? mysqli_real_escape_string($db, $_POST['endereco']) : null;
    $telefone = isset($_POST['telefone']) ? mysqli_real_escape_string($db, $_POST['telefone']) : null;
    $responsavel = isset($_POST['responsavel']) && !empty($_POST['responsavel']) ? mysqli_real_escape_string($db, $_POST['responsavel']) : null;
    $descricao = isset($_POST['descricao']) ? mysqli_real_escape_string($db, $_POST['descricao']) : null;
    $usuario_id = $_SESSION['idUsuario'];
    
    // Validação básica
    if (empty($nome)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'O nome do armazém é obrigatório'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Verificar se já existe um armazém com este nome
    $sqlVerificar = "SELECT id FROM armazem WHERE nome = '$nome'";
    $resultadoVerificar = mysqli_query($db, $sqlVerificar);
    
    if ($resultadoVerificar && mysqli_num_rows($resultadoVerificar) > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'Já existe um armazém com este nome'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Inserir novo armazém
    $sql = "INSERT INTO armazem (
                nome, 
                endereco, 
                telefone, 
                responsavel, 
                descricao, 
                usuario_cadastro, 
                data_cadastro,
                estado
            ) VALUES (
                '$nome', 
                " . ($endereco ? "'$endereco'" : "NULL") . ", 
                " . ($telefone ? "'$telefone'" : "NULL") . ", 
                " . ($responsavel ? "'$responsavel'" : "NULL") . ", 
                " . ($descricao ? "'$descricao'" : "NULL") . ", 
                $usuario_id, 
                NOW(),
                'ativo'
            )";
    
    if (mysqli_query($db, $sql)) {
        $id = mysqli_insert_id($db);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Armazém cadastrado com sucesso',
            'armazem_id' => $id
        ], JSON_UNESCAPED_UNICODE);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Erro ao cadastrar armazém: ' . mysqli_error($db)
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido'], JSON_UNESCAPED_UNICODE);
}
?>
