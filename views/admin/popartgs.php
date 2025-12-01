<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("location:../../");
}
error_reporting(E_ALL);
include '../../conexao/index.php';

// Configurar parâmetros de paginação e pesquisa
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Consultar o número total de registros que atendem aos critérios de estoque > 0
$countSql = "
    SELECT COUNT(DISTINCT p.idproduto) AS total 
    FROM stock s 
    INNER JOIN produto p ON s.produto_id = p.idproduto 
    WHERE s.quantidade > 0
";
$countRs = mysqli_query($db, $countSql) or die(mysqli_error($db));
$countRow = mysqli_fetch_assoc($countRs);
$totalRecords = $countRow['total'];

// Consultar registros com paginação e pesquisa, considerando lotes e prazos, e verificar apenas estoques positivos
$sql = "
    SELECT 
        p.idproduto, 
        p.nomeproduto AS artigo, 
        p.stock_min, 
        SUM(s.quantidade) AS stock_atual, 
        s.lote, 
        s.prazo 
    FROM stock s
    INNER JOIN produto p ON s.produto_id = p.idproduto 
    WHERE (p.idproduto LIKE ? 
           OR p.nomeproduto LIKE ? 
           OR p.grupo LIKE ? 
           OR p.familia LIKE ?) 
          AND s.quantidade > 0 
    GROUP BY p.idproduto, s.lote, s.prazo
    HAVING stock_atual > 0
    LIMIT ?, ?
";

// Preparar e executar a consulta com pesquisa
$searchValue = "%$searchValue%";
$stmt = $db->prepare($sql);
$stmt->bind_param('ssssii', $searchValue, $searchValue, $searchValue, $searchValue, $start, $length);
$stmt->execute();
$rs = $stmt->get_result();

// Preparar os dados para DataTables
$data = [];
while ($dados = $rs->fetch_assoc()) {
    $stock_atual = $dados['stock_atual'];

    $data[] = [
        'idproduto' => $dados['idproduto'],
        'artigo' => $dados['artigo'],
        'stock' => $stock_atual,
        'stock_min' => $dados['stock_min'],
        'lote' => $dados['lote'],
        'datas' => $dados['prazo']
    ];
}

// Responder com dados no formato JSON
$response = [
    'draw' => intval($_GET['draw']),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalRecords, // Atualize isto se você implementar filtragem
    'data' => $data
];

echo json_encode($response);

?>
