<?php
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit();
}

// Caminho corrigido para a conexão (estamos em views/admin)
include_once '../../conexao/index.php';

function encontrarDuplicados($db) {
    $query = "
        SELECT 
            p.nomeproduto AS nomeproduto,
            COUNT(*) AS total,
            GROUP_CONCAT(p.idproduto ORDER BY p.idproduto) AS ids,
            SUM(COALESCE(s.quantidade, 0)) AS stock_total
        FROM produto p 
        LEFT JOIN stock s ON p.idproduto = s.produto_id
        GROUP BY p.nomeproduto 
        HAVING total > 1
        ORDER BY total DESC
    ";
    
    $result = mysqli_query($db, $query);
    $duplicados = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $duplicados[] = $row;
    }
    
    return $duplicados;
}

function corrigirDuplicados($db, $nomeProduto, $ids, $idManter) {
    $idsArray = array_values(array_unique(array_filter(array_map(function($v){return (int)trim($v);}, explode(',', $ids)), function($v){return $v>0;})));
    $idManter = (int)$idManter;
    if (count($idsArray) < 2) { throw new Exception('Nenhum duplicado para corrigir'); }
    if (!in_array($idManter, $idsArray, true)) { throw new Exception('ID a manter inválido'); }

    mysqli_begin_transaction($db);
    try {
        $resultados = [];
        $stmt = mysqli_prepare($db, "SELECT idproduto, nomeproduto FROM produto WHERE idproduto = ?");
        mysqli_stmt_bind_param($stmt, "i", $idManter);
        mysqli_stmt_execute($stmt);
        $produtoManter = mysqli_stmt_get_result($stmt)->fetch_assoc();
        if (!$produtoManter) { throw new Exception('Produto a manter não encontrado'); }

        $resultados['produto_manter'] = $produtoManter;
        $resultados['produtos_removidos'] = [];
        $resultados['stock_transferido'] = 0;

        foreach ($idsArray as $id) {
            if ($id === $idManter) { continue; }

            $stmt = mysqli_prepare($db, "SELECT SUM(quantidade) as total FROM stock WHERE produto_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $stockResult = mysqli_stmt_get_result($stmt)->fetch_assoc();
            $stockQuantidade = (float)($stockResult['total'] ?? 0);
            if ($stockQuantidade > 0) { $resultados['stock_transferido'] += $stockQuantidade; }

            $stmt = mysqli_prepare($db, "UPDATE stock SET produto_id = ? WHERE produto_id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $idManter, $id);
            mysqli_stmt_execute($stmt);

            $stmt = mysqli_prepare($db, "DELETE FROM produto WHERE idproduto = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $resultados['produtos_removidos'][] = [
                'id' => $id,
                'stock_transferido' => $stockQuantidade
            ];
        }

        $stmt = mysqli_prepare($db, "SELECT idproduto FROM produto WHERE idproduto = ?");
        mysqli_stmt_bind_param($stmt, "i", $idManter);
        mysqli_stmt_execute($stmt);
        $stillExists = mysqli_stmt_get_result($stmt)->fetch_assoc();
        if (!$stillExists) { throw new Exception('Falha na preservação do produto principal'); }

        mysqli_commit($db);
        return $resultados;
    } catch (Exception $e) {
        mysqli_rollback($db);
        throw $e;
    }
}

// Processar ações
$acao = $_POST['acao'] ?? '';
$response = ['success' => false, 'message' => '', 'data' => []];

try {
    if ($acao === 'listar_duplicados') {
        $duplicados = encontrarDuplicados($db);
        $response['success'] = true;
        $response['data'] = $duplicados;
        $response['message'] = count($duplicados) . ' duplicados encontrados';
        
    } elseif ($acao === 'corrigir_duplicado') {
        $nomeProduto = $_POST['nome_produto'] ?? '';
        $ids = $_POST['ids'] ?? '';
        $idManter = $_POST['id_manter'] ?? '';
        
        if (empty($nomeProduto) || empty($ids) || empty($idManter)) {
            throw new Exception('Dados insuficientes');
        }
        
        $resultado = corrigirDuplicados($db, $nomeProduto, $ids, $idManter);
        $response['success'] = true;
        $response['data'] = $resultado;
        $response['message'] = 'Duplicados corrigidos com sucesso!';
        
    } elseif ($acao === 'corrigir_todos') {
        $duplicados = encontrarDuplicados($db);
        $resultados_gerais = [];
        $total_corrigidos = 0;
        $total_stock_transferido = 0;
        
        foreach ($duplicados as $duplicado) {
            $idsArray = array_values(array_unique(array_filter(array_map(function($v){return (int)trim($v);}, explode(',', $duplicado['ids'])), function($v){return $v>0;})));
            if (count($idsArray) < 2) { continue; }
            sort($idsArray, SORT_NUMERIC);
            $idManter = $idsArray[0];
            
            $resultado = corrigirDuplicados($db, $duplicado['nomeproduto'], $duplicado['ids'], $idManter);
            $resultados_gerais[$duplicado['nomeproduto']] = $resultado;
            $total_corrigidos += (count($idsArray) - 1);
            $total_stock_transferido += $resultado['stock_transferido'];
        }
        
        $response['success'] = true;
        $response['data'] = $resultados_gerais;
        $response['message'] = "Correção completa! {$total_corrigidos} duplicados removidos.";
    }
    
} catch (Exception $e) {
    $response['message'] = 'Erro: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>