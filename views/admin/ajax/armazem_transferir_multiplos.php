<?php
include '../../../settings/config.php';
include '../../../settings/connect.php';

// Verificar sessão
session_start();
if (!isset($_SESSION['username'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Sessão expirada. Por favor, faça login novamente.'
    );
    echo json_encode($response);
    exit();
}

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
$produtos_ids = isset($_POST['produtos']) ? $_POST['produtos'] : array();
$quantidades = isset($_POST['quantidades']) ? $_POST['quantidades'] : array();
$observacao = isset($_POST['observacao']) ? mysqli_real_escape_string($db, $_POST['observacao']) : '';
$usuario_id = $_SESSION['id'];
$data_transferencia = date('Y-m-d H:i:s');

// Validar dados
if ($armazem_id <= 0 || empty($produtos_ids)) {
    $response = array(
        'status' => 'error',
        'message' => 'Nenhum produto selecionado para transferência.'
    );
    echo json_encode($response);
    exit();
}

// Verificar se o armazém existe e está ativo
$sqlVerificarArmazem = "SELECT id FROM armazem WHERE id = $armazem_id AND estado = 'ativo'";
$resultadoArmazem = mysqli_query($db, $sqlVerificarArmazem);
if (mysqli_num_rows($resultadoArmazem) == 0) {
    $response = array(
        'status' => 'error',
        'message' => 'Armazém não encontrado ou inativo.'
    );
    echo json_encode($response);
    exit();
}

// Iniciar transação
mysqli_begin_transaction($db);

try {
    $produtosTransferidos = 0;
    $erros = array();
    
    foreach ($produtos_ids as $stock_id) {
        $stock_id = intval($stock_id);
        $quantidade = isset($quantidades[$stock_id]) ? intval($quantidades[$stock_id]) : 0;
        
        if ($quantidade <= 0) {
            $erros[] = "Quantidade inválida para um dos produtos.";
            continue;
        }
        
        // Verificar se o stock existe e tem quantidade suficiente
        $sqlVerificarStock = "SELECT id, produto_id, lote, quantidade, prazo FROM armazem_stock 
                            WHERE id = $stock_id AND armazem_id = $armazem_id AND estado = 'ativo'";
        $resultadoStock = mysqli_query($db, $sqlVerificarStock);
        
        if (mysqli_num_rows($resultadoStock) == 0) {
            $erros[] = "Stock ID $stock_id não encontrado ou inativo.";
            continue;
        }
        
        $stock = mysqli_fetch_assoc($resultadoStock);
        $produto_id = $stock['produto_id'];
        
        if ($stock['quantidade'] < $quantidade) {
            $erros[] = "Quantidade insuficiente para o produto no lote " . $stock['lote'] . ". Disponível: " . $stock['quantidade'] . " unidades.";
            continue;
        }
        
        // Atualizar quantidade no armazém
        $novaQuantidade = $stock['quantidade'] - $quantidade;
        $estado = $novaQuantidade > 0 ? 'ativo' : 'inativo';
        
        $sqlAtualizarStock = "UPDATE armazem_stock SET 
                            quantidade = $novaQuantidade,
                            estado = '$estado'
                            WHERE id = $stock_id";
        
        if (!mysqli_query($db, $sqlAtualizarStock)) {
            throw new Exception("Erro ao atualizar o stock ID $stock_id: " . mysqli_error($db));
        }
        
        // Registrar o movimento de saída
        $sqlRegistrarSaida = "INSERT INTO armazem_movimentos 
                            (armazem_id, stock_id, produto_id, tipo_movimento, quantidade, usuario_id, data_movimento, observacao) 
                            VALUES ($armazem_id, $stock_id, $produto_id, 'transferencia', $quantidade, $usuario_id, '$data_transferencia', '$observacao')";
        
        if (!mysqli_query($db, $sqlRegistrarSaida)) {
            throw new Exception("Erro ao registrar movimento de saída para stock ID $stock_id: " . mysqli_error($db));
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
                throw new Exception("Erro ao atualizar a prateleira para stock ID $stock_id: " . mysqli_error($db));
            }
        } else {
            // Inserir novo registo na prateleira
            $sqlInserirPrateleira = "INSERT INTO stock (produto_id, lote, quantidade, prazo, origem, data_entrada, estado) 
                                VALUES ($produto_id, '$lote', $quantidade, $prazoSql, 'transferencia_armazem', '$data_transferencia', 'ativo')";
            
            if (!mysqli_query($db, $sqlInserirPrateleira)) {
                throw new Exception("Erro ao inserir na prateleira para stock ID $stock_id: " . mysqli_error($db));
            }
        }
        
        $produtosTransferidos++;
    }
    
    // Se não houve nenhuma transferência bem-sucedida, desfaz a transação
    if ($produtosTransferidos == 0) {
        throw new Exception("Nenhum produto foi transferido. " . implode(" ", $erros));
    }
    
    // Confirmar a transação
    mysqli_commit($db);
    
    // Mensagem de resposta
    if (empty($erros)) {
        $response = array(
            'status' => 'success',
            'message' => $produtosTransferidos . ' produto(s) transferido(s) com sucesso para as prateleiras de venda!'
        );
    } else {
        $response = array(
            'status' => 'partial',
            'message' => $produtosTransferidos . ' produto(s) transferido(s) com sucesso, porém ocorreram os seguintes erros: ' . implode(" ", $erros)
        );
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Em caso de erro, reverter a transação
    mysqli_rollback($db);
    
    $response = array(
        'status' => 'error',
        'message' => 'Erro ao transferir produtos: ' . $e->getMessage()
    );
    echo json_encode($response);
}
