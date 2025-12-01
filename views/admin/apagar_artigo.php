<?php
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit();
}

// Caminho corrigido para a conexão (estamos em views/admin)
include_once '../../conexao/index.php';

$response = ['success' => false, 'message' => ''];

try {
    if (!isset($_POST['acao']) || $_POST['acao'] !== 'apagar_artigo') {
        throw new Exception('Ação inválida');
    }
    
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('ID do artigo não informado');
    }
    
    $id = intval($_POST['id']);
    
    if ($id <= 0) {
        throw new Exception('ID do artigo inválido');
    }

    // Verificar se o artigo existe
    $sql_check = "SELECT idproduto, nomeproduto AS artigo FROM produto WHERE idproduto = ?";
    $stmt = mysqli_prepare($db, $sql_check);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        throw new Exception('Artigo não encontrado');
    }
    
    $artigo = mysqli_fetch_assoc($result);

    // Verificar se há stock
    $sql_stock = "SELECT SUM(quantidade) as total_stock FROM stock WHERE produto_id = ?";
    $stmt_stock = mysqli_prepare($db, $sql_stock);
    mysqli_stmt_bind_param($stmt_stock, "i", $id);
    mysqli_stmt_execute($stmt_stock);
    $stock_result = mysqli_stmt_get_result($stmt_stock)->fetch_assoc();
    $total_stock = $stock_result['total_stock'] ?? 0;

    // Iniciar transação
    mysqli_begin_transaction($db);

    try {
        // Remover stock primeiro
        $sql_delete_stock = "DELETE FROM stock WHERE produto_id = ?";
        $stmt_stock = mysqli_prepare($db, $sql_delete_stock);
        mysqli_stmt_bind_param($stmt_stock, "i", $id);
        mysqli_stmt_execute($stmt_stock);

        // Remover produto
        $sql_delete_produto = "DELETE FROM produto WHERE idproduto = ?";
        $stmt_produto = mysqli_prepare($db, $sql_delete_produto);
        mysqli_stmt_bind_param($stmt_produto, "i", $id);
        mysqli_stmt_execute($stmt_produto);

        mysqli_commit($db);
        
        $response['success'] = true;
        $response['message'] = "Artigo '{$artigo['artigo']}' apagado com sucesso!" . 
                             ($total_stock > 0 ? " (Stock removido: {$total_stock} unidades)" : "");

    } catch (Exception $e) {
        mysqli_rollback($db);
        throw new Exception('Erro ao apagar: ' . $e->getMessage());
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>