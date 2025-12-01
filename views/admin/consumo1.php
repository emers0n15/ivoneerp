<?php
include '../../conexao/index.php';

// Configurar cabeçalho para JSON
header('Content-Type: application/json');

// Parâmetros de paginação e pesquisa
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Mapear os nomes das colunas para o banco de dados
$columns = [
    0 => 'p.idproduto',
    1 => 'p.nomeproduto',
    2 => 'stock_atual', // Estoque vem da tabela 'stock'
    3 => 'consumo_medio_mensal',
    4 => 'recomendacao'
];

$orderColumn = $columns[$orderColumnIndex];

// Consultar o número total de registros com filtro de datas
$totalRecordsQuery = "
    SELECT COUNT(DISTINCT p.idproduto) AS total
    FROM produto p
    LEFT JOIN entrega e ON p.idproduto = e.produtoentrega 
    WHERE e.datavenda BETWEEN ? AND ?
";
$stmt = $db->prepare($totalRecordsQuery);
$stmt->bind_param("ss", $_GET['data1'], $_GET['data2']);
$stmt->execute();
$result = $stmt->get_result();
$totalRecords = $result->fetch_assoc()['total'];

// Consulta para obter os dados com paginação, ordenação e pesquisa
$sql = "
    SELECT 
        p.idproduto,
        p.nomeproduto,
        IFNULL(SUM(s.quantidade), 0) AS stock_atual, -- Somar o estoque da tabela 'stock'
        IFNULL(SUM(e.qtdentrega), 0) AS total_entregas,
        COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')) AS meses
    FROM produto p
    LEFT JOIN stock s ON p.idproduto = s.produto_id -- Unir com a tabela 'stock'
    LEFT JOIN entrega e ON p.idproduto = e.produtoentrega 
        AND e.datavenda BETWEEN ? AND ?
    WHERE p.nomeproduto LIKE ? 
    GROUP BY p.idproduto
    ORDER BY $orderColumn $orderDir
    LIMIT ?, ?
";

$searchValue = '%' . $searchValue . '%';
$stmt = $db->prepare($sql);
$stmt->bind_param("sssii", $_GET['data1'], $_GET['data2'], $searchValue, $start, $length);
$stmt->execute();
$result = $stmt->get_result();

// Preparar os dados para DataTables
$data = [];
while ($row = $result->fetch_assoc()) {
    $idproduto = $row['idproduto'];
    $nomeproduto = $row['nomeproduto'];
    $stock_atual = $row['stock_atual'];
    $total_entregas = $row['total_entregas'];
    $meses = $row['meses'];
    $consumo_medio_mensal = $meses > 0 ? $total_entregas / $meses : 0;
    $recomendacao = $stock_atual < $consumo_medio_mensal ? 'Reabastecimento Necessário' : 'Estoque Adequado';
    
    $data[] = [
        "idproduto" => $idproduto,
        "nomeproduto" => $nomeproduto,
        "stock" => number_format($stock_atual, 0),
        "consumo_medio_mensal" => number_format($consumo_medio_mensal, 0),
        "recomendacao" => $recomendacao
    ];
}

// Responder com dados no formato JSON
$response = [
    "draw" => intval($_GET['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
];

echo json_encode($response);
?>
