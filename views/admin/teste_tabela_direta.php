<?php
// Teste direto de dados sem DataTable
session_start();
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste Direto de Tabela</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { padding: 3px 8px; border-radius: 3px; color: white; font-size: 12px; }
        .badge-danger { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-success { background-color: #28a745; }
        .debug { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin-top: 20px; }
        pre { margin: 0; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Teste de Tabela - Artigos com Estoque Baixo</h1>
    
    <div class="debug">
        <h3>Informações de Debug</h3>
        <pre>
<?php
// Informações de debug
echo "Data/Hora: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "MySQL Version: " . mysqli_get_server_info($db) . "\n\n";

// Verificar conexão
if ($db->connect_error) {
    echo "Erro de conexão: " . $db->connect_error . "\n";
} else {
    echo "Conexão com o banco de dados: OK\n";
}

// Data atual
$hoje = date('Y-m-d');
$data_inicio = date('Y-m-d', strtotime('-3 months'));
echo "\nPeríodo para cálculo: $data_inicio até $hoje\n\n";

// Consulta simplificada
$sql = "
    SELECT 
        p.idproduto,
        p.codigobarra,
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
    WHERE p.estado = 'ativo'
    GROUP BY p.idproduto
    HAVING stock_atual < consumo_medio_mensal AND consumo_medio_mensal > 0
    ORDER BY (stock_atual / consumo_medio_mensal) ASC
    LIMIT 50
";

echo "SQL: " . str_replace("\n", " ", $sql) . "\n\n";

// Executar a consulta
$result = $db->query($sql);

if (!$result) {
    echo "ERRO na consulta: " . $db->error . "\n";
} else {
    echo "Consulta executada com sucesso. Registros encontrados: " . $result->num_rows . "\n";
}
?>
        </pre>
    </div>
    
    <h2>Produtos com Estoque Abaixo do Consumo Médio</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código de Barras</th>
                <th>Nome do Produto</th>
                <th>Estoque Atual</th>
                <th>Consumo Médio Mensal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $idproduto = $row['idproduto'];
                    $codigobarra = $row['codigobarra'] ?? 'N/A';
                    $nomeproduto = $row['nomeproduto'];
                    $stock_atual = $row['stock_atual'];
                    $consumo_medio_mensal = $row['consumo_medio_mensal'];
                    
                    // Determinar o status
                    $percentual = $consumo_medio_mensal > 0 ? ($stock_atual / $consumo_medio_mensal) * 100 : 0;
                    
                    if ($percentual <= 30) {
                        $status = '<span class="badge badge-danger">Crítico</span>';
                    } elseif ($percentual <= 70) {
                        $status = '<span class="badge badge-warning">Baixo</span>';
                    } else {
                        $status = '<span class="badge badge-success">Adequado</span>';
                    }
                    
                    echo "<tr>";
                    echo "<td>{$idproduto}</td>";
                    echo "<td>{$codigobarra}</td>";
                    echo "<td>{$nomeproduto}</td>";
                    echo "<td>{$stock_atual}</td>";
                    echo "<td>" . number_format($consumo_medio_mensal, 2) . "</td>";
                    echo "<td>{$status}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center'>Nenhum produto encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <div class="debug">
        <h3>Consulta direta básica para verificar produtos e estoque</h3>
        <pre>
<?php
// Verificar se existem produtos
$sql = "SELECT COUNT(*) AS total FROM produto WHERE estado = 'ativo'";
$result = $db->query($sql);
$total_produtos = $result->fetch_assoc()['total'];
echo "Total de produtos ativos: $total_produtos\n";

// Verificar se existem registros de estoque
$sql = "SELECT COUNT(*) AS total FROM stock WHERE estado = 'ativo'";
$result = $db->query($sql);
$total_stock = $result->fetch_assoc()['total'];
echo "Total de registros de estoque ativos: $total_stock\n";

// Verificar se existem registros de entrega (consumo)
$sql = "SELECT COUNT(*) AS total FROM entrega WHERE datavenda BETWEEN '$data_inicio' AND '$hoje'";
$result = $db->query($sql);
$total_entregas = $result->fetch_assoc()['total'];
echo "Total de entregas nos últimos 3 meses: $total_entregas\n";

// Verificar produtos com consumo
$sql = "
    SELECT COUNT(DISTINCT produtoentrega) AS total 
    FROM entrega 
    WHERE datavenda BETWEEN '$data_inicio' AND '$hoje'
";
$result = $db->query($sql);
$produtos_com_consumo = $result->fetch_assoc()['total'];
echo "Produtos com algum consumo nos últimos 3 meses: $produtos_com_consumo\n";
?>
        </pre>
    </div>
</body>
</html>
