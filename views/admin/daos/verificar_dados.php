<?php
// Script para verificar se a consulta SQL está retornando dados corretamente
header('Content-Type: text/plain');
include_once '../../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

echo "=== SCRIPT DE DIAGNÓSTICO DE CONSULTA SQL ===\n\n";
echo "Data/Hora: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n";

// Verificar conexão com o banco de dados
if ($db->connect_error) {
    echo "ERRO de conexão com o banco de dados: " . $db->connect_error . "\n";
    exit;
} else {
    echo "Conexão com o banco de dados: OK\n";
    echo "MySQL Version: " . $db->server_info . "\n\n";
}

// Definir o período para consulta
$hoje = date('Y-m-d');
$data_inicio = date('Y-m-d', strtotime('-3 months'));

echo "Período de análise: $data_inicio até $hoje\n\n";

// TESTE 1: Verificar se há produtos no banco
echo "=== TESTE 1: Verificar se há produtos no banco ===\n";
$sql = "SELECT COUNT(*) AS total FROM produto WHERE estado = 'ativo'";
$result = $db->query($sql);

if (!$result) {
    echo "ERRO ao executar consulta: " . $db->error . "\n";
} else {
    $row = $result->fetch_assoc();
    echo "Total de produtos ativos: " . $row['total'] . "\n\n";
}

// TESTE 2: Verificar registros de estoque
echo "=== TESTE 2: Verificar registros de estoque ===\n";
$sql = "SELECT COUNT(*) AS total FROM stock WHERE estado = 'ativo'";
$result = $db->query($sql);

if (!$result) {
    echo "ERRO ao executar consulta: " . $db->error . "\n";
} else {
    $row = $result->fetch_assoc();
    echo "Total de registros de estoque ativos: " . $row['total'] . "\n\n";
}

// TESTE 3: Verificar se há consumo registrado
echo "=== TESTE 3: Verificar registros de consumo (entregas) ===\n";
$sql = "SELECT COUNT(*) AS total FROM entrega WHERE datavenda BETWEEN '$data_inicio' AND '$hoje'";
$result = $db->query($sql);

if (!$result) {
    echo "ERRO ao executar consulta: " . $db->error . "\n";
} else {
    $row = $result->fetch_assoc();
    echo "Total de entregas nos últimos 3 meses: " . $row['total'] . "\n\n";
}

// TESTE 4: Verificar produtos com consumo
echo "=== TESTE 4: Verificar produtos com consumo ===\n";
$sql = "SELECT COUNT(DISTINCT produtoentrega) AS total FROM entrega WHERE datavenda BETWEEN '$data_inicio' AND '$hoje'";
$result = $db->query($sql);

if (!$result) {
    echo "ERRO ao executar consulta: " . $db->error . "\n";
} else {
    $row = $result->fetch_assoc();
    echo "Produtos com algum consumo nos últimos 3 meses: " . $row['total'] . "\n\n";
}

// TESTE 5: Verificar produtos com estoque
echo "=== TESTE 5: Verificar produtos com estoque ===\n";
$sql = "
    SELECT COUNT(DISTINCT p.idproduto) AS total 
    FROM produto p 
    INNER JOIN stock s ON p.idproduto = s.produto_id 
    WHERE s.estado = 'ativo' AND s.quantidade > 0
";
$result = $db->query($sql);

if (!$result) {
    echo "ERRO ao executar consulta: " . $db->error . "\n";
} else {
    $row = $result->fetch_assoc();
    echo "Produtos com estoque positivo: " . $row['total'] . "\n\n";
}

// TESTE 6: Verificar a consulta principal com HAVING
echo "=== TESTE 6: Verificar a consulta principal ===\n";
$sql = "
    SELECT 
        p.idproduto,
        p.codbar,
        p.nomeproduto,
        IFNULL(SUM(s.quantidade), 0) AS stock_atual,
        (
            SELECT 
                IFNULL(SUM(e.qtdentrega), 0) / 
                GREATEST(COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')), 1) 
            FROM entrega e 
            WHERE e.produtoentrega = p.idproduto 
                AND e.datavenda BETWEEN '$data_inicio' AND '$hoje'
        ) AS consumo_medio_mensal
    FROM produto p
    LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
    GROUP BY p.idproduto
    HAVING consumo_medio_mensal > 0
    LIMIT 5
";

$result = $db->query($sql);

if (!$result) {
    echo "ERRO ao executar consulta principal: " . $db->error . "\n";
} else {
    echo "Consulta principal executada com sucesso.\n";
    echo "Registros encontrados: " . $result->num_rows . "\n\n";
    
    if ($result->num_rows > 0) {
        echo "Primeiros 5 produtos encontrados:\n";
        echo str_pad("ID", 5) . " | " . str_pad("Código", 15) . " | " . str_pad("Nome", 30) . " | " . str_pad("Estoque", 10) . " | " . str_pad("Consumo Médio", 15) . "\n";
        echo str_repeat("-", 90) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            echo str_pad($row['idproduto'], 5) . " | " .
                 str_pad($row['codbar'] ?? 'N/A', 15) . " | " .
                 str_pad(substr($row['nomeproduto'], 0, 30), 30) . " | " .
                 str_pad($row['stock_atual'], 10) . " | " .
                 str_pad(number_format($row['consumo_medio_mensal'], 2), 15) . "\n";
        }
    }
}

// TESTE 7: Verificar a consulta principal sem a condição HAVING
echo "\n=== TESTE 7: Verificar a consulta principal sem HAVING ===\n";
$sql = "
    SELECT 
        p.idproduto,
        p.codbar,
        p.nomeproduto,
        IFNULL(SUM(s.quantidade), 0) AS stock_atual,
        (
            SELECT 
                IFNULL(SUM(e.qtdentrega), 0) / 
                GREATEST(COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')), 1) 
            FROM entrega e 
            WHERE e.produtoentrega = p.idproduto 
                AND e.datavenda BETWEEN '$data_inicio' AND '$hoje'
        ) AS consumo_medio_mensal
    FROM produto p
    LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
    GROUP BY p.idproduto
    ORDER BY consumo_medio_mensal DESC
    LIMIT 5
";

$result = $db->query($sql);

if (!$result) {
    echo "ERRO ao executar consulta: " . $db->error . "\n";
} else {
    echo "Consulta executada com sucesso.\n";
    echo "Registros encontrados: " . $result->num_rows . "\n\n";
    
    if ($result->num_rows > 0) {
        echo "Primeiros 5 produtos (sem HAVING):\n";
        echo str_pad("ID", 5) . " | " . str_pad("Código", 15) . " | " . str_pad("Nome", 30) . " | " . str_pad("Estoque", 10) . " | " . str_pad("Consumo Médio", 15) . "\n";
        echo str_repeat("-", 90) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            echo str_pad($row['idproduto'], 5) . " | " .
                 str_pad($row['codbar'] ?? 'N/A', 15) . " | " .
                 str_pad(substr($row['nomeproduto'], 0, 30), 30) . " | " .
                 str_pad($row['stock_atual'], 10) . " | " .
                 str_pad(number_format($row['consumo_medio_mensal'], 2), 15) . "\n";
        }
    }
}

// TESTE 8: Verificar a condição de estoque < consumo
echo "\n=== TESTE 8: Verificar produtos com estoque < consumo médio ===\n";
$sql = "
    SELECT 
        p.idproduto,
        p.codbar,
        p.nomeproduto,
        IFNULL(SUM(s.quantidade), 0) AS stock_atual,
        (
            SELECT 
                IFNULL(SUM(e.qtdentrega), 0) / 
                GREATEST(COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')), 1) 
            FROM entrega e 
            WHERE e.produtoentrega = p.idproduto 
                AND e.datavenda BETWEEN '$data_inicio' AND '$hoje'
        ) AS consumo_medio_mensal
    FROM produto p
    LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
    GROUP BY p.idproduto
    HAVING stock_atual < consumo_medio_mensal AND consumo_medio_mensal > 0
    LIMIT 5
";

$result = $db->query($sql);

if (!$result) {
    echo "ERRO ao executar consulta: " . $db->error . "\n";
} else {
    echo "Consulta executada com sucesso.\n";
    echo "Registros encontrados: " . $result->num_rows . "\n\n";
    
    if ($result->num_rows > 0) {
        echo "Produtos com estoque < consumo médio:\n";
        echo str_pad("ID", 5) . " | " . str_pad("Código", 15) . " | " . str_pad("Nome", 30) . " | " . str_pad("Estoque", 10) . " | " . str_pad("Consumo Médio", 15) . "\n";
        echo str_repeat("-", 90) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            echo str_pad($row['idproduto'], 5) . " | " .
                 str_pad($row['codbar'] ?? 'N/A', 15) . " | " .
                 str_pad(substr($row['nomeproduto'], 0, 30), 30) . " | " .
                 str_pad($row['stock_atual'], 10) . " | " .
                 str_pad(number_format($row['consumo_medio_mensal'], 2), 15) . "\n";
        }
    }
}

echo "\n=== FIM DOS TESTES ===\n";
?>
