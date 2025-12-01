<?php
// Garantir que apenas JSON seja retornado
header('Content-Type: application/json');

// Desativar a exibição de erros para evitar HTML no output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Verificar sessão
session_start();
if (!isset($_SESSION['idUsuario'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Sessão expirada. Por favor, faça login novamente.'
    );
    echo json_encode($response);
    exit();
}

include '../../../conexao/index.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = array(
        'status' => 'error',
        'message' => 'Método de requisição inválido.'
    );
    echo json_encode($response);
    exit();
}

// Capturar dados do formulário
$armazem_id = isset($_POST['armazem_id']) ? intval($_POST['armazem_id']) : 0;
$stock_id = isset($_POST['stock_id']) ? intval($_POST['stock_id']) : 0;
$produto_id = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;
$quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
$observacao = isset($_POST['observacao']) ? mysqli_real_escape_string($db, $_POST['observacao']) : '';
$usuario_id = $_SESSION['idUsuario'];
$data_transferencia = date('Y-m-d H:i:s');

// Validar dados
if ($armazem_id <= 0 || $stock_id <= 0 || $produto_id <= 0 || $quantidade <= 0) {
    $response = array(
        'status' => 'error',
        'message' => 'Dados inválidos. Verifique os campos obrigatórios.'
    );
    echo json_encode($response);
    exit();
}

// Verificar se o stock existe e tem quantidade suficiente
$sqlVerificarStock = "SELECT id, lote, quantidade, prazo FROM armazem_stock WHERE id = $stock_id AND armazem_id = $armazem_id AND estado = 'ativo'";
$resultadoStock = mysqli_query($db, $sqlVerificarStock);

if (mysqli_num_rows($resultadoStock) == 0) {
    $response = array(
        'status' => 'error',
        'message' => 'Stock não encontrado ou inativo.'
    );
    echo json_encode($response);
    exit();
}

$stock = mysqli_fetch_assoc($resultadoStock);
if ($stock['quantidade'] < $quantidade) {
    $response = array(
        'status' => 'error',
        'message' => 'Quantidade insuficiente em stock. Disponível: ' . $stock['quantidade'] . ' unidades.'
    );
    echo json_encode($response);
    exit();
}

// Iniciar transação
mysqli_begin_transaction($db);

try {
    // Desativar verificação de chave estrangeira temporariamente
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");
    
    // Atualizar quantidade no armazém
    $novaQuantidade = $stock['quantidade'] - $quantidade;
    $estado = $novaQuantidade > 0 ? 'ativo' : 'inativo';
    
    $sqlAtualizarStock = "UPDATE armazem_stock SET 
                        quantidade = $novaQuantidade,
                        estado = '$estado'
                        WHERE id = $stock_id";
    
    if (!mysqli_query($db, $sqlAtualizarStock)) {
        throw new Exception("Erro ao atualizar o stock: " . mysqli_error($db));
    }
    
    // Registrar o movimento de saída
    $sqlRegistrarSaida = "INSERT INTO armazem_movimentos (armazem_id, stock_id, produto_id, tipo_movimento, quantidade, usuario_id, data_movimento, observacao) 
                        VALUES ($armazem_id, $stock_id, $produto_id, 'transferencia', $quantidade, $usuario_id, '$data_transferencia', '$observacao')";
    
    if (!mysqli_query($db, $sqlRegistrarSaida)) {
        throw new Exception("Erro ao registrar movimento de saída: " . mysqli_error($db));
    }
    
    // Verificar se já existe o lote nas prateleiras de venda
    $lote = $stock['lote'];
    $prazo = $stock['prazo'];
    $prazoSql = $prazo ? "'$prazo'" : "NULL";
    
    $sqlVerificarPrateleira = "SELECT id, quantidade FROM stock WHERE produto_id = $produto_id AND lote = '$lote'";
    $resultadoPrateleira = mysqli_query($db, $sqlVerificarPrateleira);
    
    if (mysqli_num_rows($resultadoPrateleira) > 0) {
        // Atualizar quantidade na prateleira
        $prateleira = mysqli_fetch_assoc($resultadoPrateleira);
        $prateleira_id = $prateleira['id'];
        $novaQuantidadePrateleira = $prateleira['quantidade'] + $quantidade;
        
        $sqlAtualizarPrateleira = "UPDATE stock SET 
                                quantidade = $novaQuantidadePrateleira,
                                estado = 'ativo'
                                WHERE id = $prateleira_id";
        
        if (!mysqli_query($db, $sqlAtualizarPrateleira)) {
            throw new Exception("Erro ao atualizar a prateleira: " . mysqli_error($db));
        }
    } else {
        // Inserir novo registo na prateleira
        $sqlInserirPrateleira = "INSERT INTO stock (produto_id, lote, quantidade, prazo, origem, data_entrada, estado) 
                              VALUES ($produto_id, '$lote', $quantidade, $prazoSql, 'transferencia_armazem', '$data_transferencia', 'ativo')";
        
        if (!mysqli_query($db, $sqlInserirPrateleira)) {
            throw new Exception("Erro ao inserir na prateleira: " . mysqli_error($db));
        }
    }
    
    // Reativar verificação de chave estrangeira
    mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");
    
    // Registrar o movimento
    $data_movimento = date('Y-m-d H:i:s');
    $usuario_id = $_SESSION['idUsuario'];
    $observacao = "Transferência de stock do armazém para prateleira de vendas";
    
    $sqlMovimento = "INSERT INTO armazem_movimentos 
                     (armazem_id, stock_id, produto_id, data_movimento, tipo_movimento, quantidade, usuario_id, observacao)
                     VALUES ($armazem_id, $stock_id, $produto_id, '$data_movimento', 'transferencia', $quantidade, $usuario_id, '$observacao')";
    
    if (!mysqli_query($db, $sqlMovimento)) {
        error_log("Erro ao registrar movimento: " . mysqli_error($db) . " - SQL: " . $sqlMovimento);
    }
    
    // Confirmar a transação
    mysqli_commit($db);
    
    $response = array(
        'status' => 'success',
        'message' => 'Stock transferido com sucesso para as prateleiras de venda!'
    );
    echo json_encode($response);
    
} catch (Exception $e) {
    // Em caso de erro, reverter a transação
    mysqli_rollback($db);
    
    $response = array(
        'status' => 'error',
        'message' => 'Erro ao transferir stock: ' . $e->getMessage()
    );
    echo json_encode($response);
}
