<?php
// Arquivo para diagnosticar problemas com a tabela de artigos com estoque baixo
session_start();
include_once '../../../conexao/index.php';
header('Content-Type: application/json');
date_default_timezone_set('Africa/Maputo');

// Informações de depuração
$debug = [];

// Informações de sessão
$session_info = [
    'total_produtos_consumo_baixo' => isset($_SESSION['total_produtos_consumo_baixo']) ? $_SESSION['total_produtos_consumo_baixo'] : null,
    'total_produtos_criticos' => isset($_SESSION['total_produtos_criticos']) ? $_SESSION['total_produtos_criticos'] : null,
    'timestamp' => isset($_SESSION['timestamp_produtos_consumo_baixo']) ? $_SESSION['timestamp_produtos_consumo_baixo'] : null,
    'tem_cache' => isset($_SESSION['produtos_consumo_baixo']) && is_array($_SESSION['produtos_consumo_baixo']) && count($_SESSION['produtos_consumo_baixo']) > 0
];

// Informações diretas do banco de dados
$db_info = [];
$queries = [];

try {
    // 1. Total de produtos ativos
    $sql = "SELECT COUNT(*) AS total FROM produto WHERE estado = 'ativo'";
    $queries[] = $sql;
    $result = $db->query($sql);
    if ($result) {
        $db_info['total_produtos'] = $result->fetch_assoc()['total'];
    } else {
        $db_info['total_produtos'] = "ERRO: " . $db->error;
    }
    
    // 2. Produtos com stock
    $sql = "SELECT COUNT(DISTINCT p.idproduto) AS total FROM produto p 
            INNER JOIN stock s ON p.idproduto = s.produto_id 
            WHERE p.estado = 'ativo' AND s.estado = 'ativo' AND s.quantidade > 0";
    $queries[] = $sql;
    $result = $db->query($sql);
    if ($result) {
        $db_info['produtos_com_stock'] = $result->fetch_assoc()['total'];
    } else {
        $db_info['produtos_com_stock'] = "ERRO: " . $db->error;
    }
    
    // 3. Produtos com consumo médio nos últimos 3 meses
    $data_inicio = date('Y-m-d', strtotime('-3 months'));
    $data_fim = date('Y-m-d');
    
    $sql = "SELECT COUNT(DISTINCT p.idproduto) AS total FROM produto p 
            INNER JOIN entrega e ON p.idproduto = e.produtoentrega 
            WHERE p.estado = 'ativo' AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'";
    $queries[] = $sql;
    $result = $db->query($sql);
    if ($result) {
        $db_info['produtos_com_consumo'] = $result->fetch_assoc()['total'];
    } else {
        $db_info['produtos_com_consumo'] = "ERRO: " . $db->error;
    }
    
    // 4. Produtos com stock abaixo do consumo médio
    $sql = "
        SELECT COUNT(*) AS total FROM (
            SELECT 
                p.idproduto,
                IFNULL(SUM(s.quantidade), 0) AS stock_atual,
                (
                    SELECT 
                        IFNULL(SUM(e.qtdentrega), 0) / 
                        GREATEST(COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')), 1) 
                    FROM entrega e 
                    WHERE e.produtoentrega = p.idproduto 
                        AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'
                ) AS consumo_medio_mensal
            FROM produto p
            LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
            WHERE p.estado = 'ativo'
            GROUP BY p.idproduto
            HAVING stock_atual <= consumo_medio_mensal AND consumo_medio_mensal > 0
        ) AS produtos_baixo_consumo
    ";
    $queries[] = $sql;
    $result = $db->query($sql);
    if ($result) {
        $db_info['produtos_baixo_consumo'] = $result->fetch_assoc()['total'];
    } else {
        $db_info['produtos_baixo_consumo'] = "ERRO: " . $db->error;
    }
    
    // Adicionar informações sobre conexão
    $db_info['conexao_ok'] = true;
    $db_info['info_server'] = $db->server_info;
    
} catch (Exception $e) {
    $db_info['erro'] = $e->getMessage();
}

// Responder com todas as informações
$response = [
    'session' => $session_info,
    'db' => $db_info,
    'queries' => $queries,
    'debug' => $debug
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
