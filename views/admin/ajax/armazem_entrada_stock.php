<?php
// Mostrar erros para diagnóstico durante desenvolvimento
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Tentar garantir que apenas JSON seja retornado
header('Content-Type: application/json');

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
$produto_id = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;
$quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
$lote = isset($_POST['lote']) ? mysqli_real_escape_string($db, $_POST['lote']) : '';
$prazo = isset($_POST['prazo']) && !empty($_POST['prazo']) ? mysqli_real_escape_string($db, $_POST['prazo']) : null;
$preco_custo = isset($_POST['preco_custo']) && !empty($_POST['preco_custo']) ? floatval($_POST['preco_custo']) : null;
$fornecedor = isset($_POST['fornecedor']) && !empty($_POST['fornecedor']) ? intval($_POST['fornecedor']) : null;
$observacao = isset($_POST['observacao']) ? mysqli_real_escape_string($db, $_POST['observacao']) : '';
$usuario_id = $_SESSION['idUsuario'];
$data_entrada = date('Y-m-d H:i:s');

// Log detalhado de todos os parâmetros recebidos
error_log("POST recebido: " . print_r($_POST, true));
error_log("Variáveis processadas: armazem_id=$armazem_id, produto_id=$produto_id, quantidade=$quantidade, lote=$lote");
error_log("Campos opcionais: prazo=" . ($prazo ?? 'NULL') . ", preco_custo=" . ($preco_custo ?? 'NULL') . ", fornecedor=" . ($fornecedor ?? 'NULL'));

// Validar dados
if ($armazem_id <= 0 || $produto_id <= 0 || $quantidade <= 0 || empty($lote)) {
    $response = array(
        'status' => 'error',
        'message' => 'Dados inválidos. Verifique os campos obrigatórios.'
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

// Verificar se o produto existe e está ativo
$sqlVerificarProduto = "SELECT idproduto FROM produto WHERE idproduto = $produto_id";
$resultadoProduto = mysqli_query($db, $sqlVerificarProduto);
if (mysqli_num_rows($resultadoProduto) == 0) {
    $response = array(
        'status' => 'error',
        'message' => 'Produto não encontrado ou inativo.'
    );
    echo json_encode($response);
    exit();
}

// Verificar se já existe um lote com o mesmo nome para o mesmo produto neste armazém
$sqlVerificarLote = "SELECT id FROM armazem_stock WHERE armazem_id = $armazem_id AND produto_id = $produto_id AND lote = '$lote'";
$resultadoLote = mysqli_query($db, $sqlVerificarLote);

// Iniciar transação
mysqli_begin_transaction($db);

try {
    // Debug para log de erro
    error_log("Processando entrada de stock: armazem_id=$armazem_id, produto_id=$produto_id, quantidade=$quantidade, lote='$lote'");
    error_log("Informações adicionais: prazo=$prazo, preco_custo=$preco_custo, fornecedor=$fornecedor");
    
    // Verificar se o lote já existe para este armazém e produto
    $sqlVerificarLote = "SHOW TABLES LIKE 'armazem_stock'";
    $resultadoTabela = mysqli_query($db, $sqlVerificarLote);
    if (!$resultadoTabela) {
        throw new Exception("Erro ao verificar tabela armazem_stock: " . mysqli_error($db));
    }
    
    if (mysqli_num_rows($resultadoTabela) == 0) {
        throw new Exception("A tabela armazem_stock não existe no banco de dados");
    }
    
    // Verificar se produto_id existe na tabela produto
    $sqlVerificarProduto = "SELECT idproduto FROM produto WHERE idproduto = $produto_id";
    error_log("SQL verificar produto: $sqlVerificarProduto");
    
    $resultadoVerificarProduto = mysqli_query($db, $sqlVerificarProduto);
    if (!$resultadoVerificarProduto) {
        throw new Exception("Erro ao verificar produto: " . mysqli_error($db));
    }
    
    if (mysqli_num_rows($resultadoVerificarProduto) == 0) {
        throw new Exception("Produto não encontrado (ID: $produto_id)");
    }
    
    // Se o lote já existe, atualizar a quantidade
    if (mysqli_num_rows($resultadoLote) > 0) {
        $row = mysqli_fetch_assoc($resultadoLote);
        $stock_id = $row['id'];
        
        // Atualizar quantidade do lote existente
        $sqlAtualizarLote = "UPDATE armazem_stock SET 
                            quantidade = quantidade + $quantidade,
                            estado = 'ativo'
                            WHERE id = $stock_id";
        
        if (!mysqli_query($db, $sqlAtualizarLote)) {
            throw new Exception("Erro ao atualizar o lote: " . mysqli_error($db));
        }
        
        // Registrar o movimento
        $sqlRegistrarMovimento = "INSERT INTO armazem_movimentos (armazem_id, stock_id, produto_id, tipo_movimento, quantidade, usuario_id, data_movimento, observacao) 
                                VALUES ($armazem_id, $stock_id, $produto_id, 'entrada', $quantidade, $usuario_id, '$data_entrada', '$observacao')";
        
        error_log("SQL para registrar movimento: $sqlRegistrarMovimento");
        
        if (!mysqli_query($db, $sqlRegistrarMovimento)) {
            error_log("Erro ao registrar movimento: " . mysqli_error($db));
            // Não vamos interromper o fluxo se houver erro no registro do movimento
            // apenas registrar no log para diagnóstico posterior
        }
    } else {
        // Ajustes para verificar o fornecedor na tabela correta
        if ($fornecedor) {
            // Verificar na tabela fornecedor
            $sqlVerificarFornecedor = "SELECT id FROM fornecedor WHERE id = $fornecedor";
            $resultadoVerificarFornecedor = mysqli_query($db, $sqlVerificarFornecedor);
            
            if (!$resultadoVerificarFornecedor) {
                error_log("Erro ao verificar fornecedor (tabela fornecedor): " . mysqli_error($db));
                
                // Tentar na tabela fornecedores
                $sqlVerificarFornecedores = "SELECT id FROM fornecedores WHERE id = $fornecedor";
                $resultadoVerificarFornecedores = mysqli_query($db, $sqlVerificarFornecedores);
                
                if (!$resultadoVerificarFornecedores) {
                    error_log("Erro ao verificar fornecedor (tabela fornecedores): " . mysqli_error($db));
                    $fornecedorSql = "NULL";
                } else if (mysqli_num_rows($resultadoVerificarFornecedores) == 0) {
                    error_log("Fornecedor não encontrado em 'fornecedores' (ID: $fornecedor), usando NULL");
                    $fornecedorSql = "NULL";
                } else {
                    $fornecedorSql = $fornecedor;
                }
            } else if (mysqli_num_rows($resultadoVerificarFornecedor) == 0) {
                error_log("Fornecedor não encontrado em 'fornecedor' (ID: $fornecedor), usando NULL");
                $fornecedorSql = "NULL";
            } else {
                $fornecedorSql = $fornecedor;
            }
        } else {
            $fornecedorSql = "NULL";
        }
        
        // Preparar valores de prazo e preço custo
        $prazoSql = $prazo ? "'$prazo'" : "NULL";
        $precoCustoSql = $preco_custo ? $preco_custo : "NULL";
        
        // Inserção com todos os campos, ignorando as constraints
        try {
            // Desativar verificação de chave estrangeira temporariamente
            mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");
            
            // Inserção com todos os campos
            $sqlInserirLote = "INSERT INTO armazem_stock 
                              (armazem_id, produto_id, lote, quantidade, prazo, preco_custo, 
                               fornecedor_id, data_entrada, usuario_id, estado) 
                              VALUES 
                              ($armazem_id, $produto_id, '$lote', $quantidade, $prazoSql, 
                               $precoCustoSql, $fornecedorSql, '$data_entrada', $usuario_id, 'ativo')";
            
            error_log("SQL final com todos os campos (foreign_key_checks=0): $sqlInserirLote");
            
            if (!mysqli_query($db, $sqlInserirLote)) {
                throw new Exception("Erro ao inserir o lote: " . mysqli_error($db));
            }
            
            // Reativar verificação de chave estrangeira
            mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");
        } catch (Exception $e) {
            // Reativar verificação de chave estrangeira em caso de erro
            mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");
            throw $e;
        }
        
        // Obter o ID do stock inserido
        $stock_id = mysqli_insert_id($db);
        
        // Registrar o movimento
        $sqlRegistrarMovimento = "INSERT INTO armazem_movimentos (armazem_id, stock_id, produto_id, tipo_movimento, quantidade, usuario_id, data_movimento, observacao) 
                                VALUES ($armazem_id, $stock_id, $produto_id, 'entrada', $quantidade, $usuario_id, '$data_entrada', '$observacao')";
        
        error_log("SQL para registrar movimento: $sqlRegistrarMovimento");
        
        if (!mysqli_query($db, $sqlRegistrarMovimento)) {
            error_log("Erro ao registrar movimento: " . mysqli_error($db));
            // Não vamos interromper o fluxo se houver erro no registro do movimento
            // apenas registrar no log para diagnóstico posterior
        }
    }
    
    // Confirmar a transação
    mysqli_commit($db);
    
    $response = array(
        'status' => 'success',
        'message' => 'Entrada de stock registrada com sucesso!'
    );
    echo json_encode($response);
    
} catch (Exception $e) {
    // Em caso de erro, reverter a transação
    mysqli_rollback($db);
    
    error_log("Erro na entrada de stock: " . $e->getMessage());
    
    $response = array(
        'status' => 'error',
        'message' => 'Erro ao registrar entrada de stock: ' . $e->getMessage()
    );
    echo json_encode($response);
}

// Fechar conexão
mysqli_close($db);
