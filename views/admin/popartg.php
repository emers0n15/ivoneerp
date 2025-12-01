<?php
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit();
}

// Caminho corrigido para a conexão (estamos em views/admin)
include_once '../../conexao/index.php';

// Suporte a DataTables server-side
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 25;
$searchValue = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';

// Total de registros
$countSql = "SELECT COUNT(*) AS total FROM produto";
$countRs = mysqli_query($db, $countSql);
$countRow = $countRs ? mysqli_fetch_assoc($countRs) : ['total' => 0];
$recordsTotal = intval($countRow['total']);

// Construir filtro de pesquisa
$where = '';
$params = [];
$types = '';
if ($searchValue !== '') {
    $where = " WHERE (p.idproduto LIKE ? OR p.nomeproduto LIKE ? OR p.grupo LIKE ? OR p.familia LIKE ?)";
    $sv = "%$searchValue%";
    $params = [$sv, $sv, $sv, $sv];
    $types = 'ssss';
}

// Contar filtrados
$countFilteredSql = "SELECT COUNT(*) AS total FROM produto p" . $where;
if ($where) {
    $stmt = mysqli_prepare($db, $countFilteredSql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    $row = $rs ? mysqli_fetch_assoc($rs) : ['total' => 0];
    $recordsFiltered = intval($row['total']);
} else {
    $recordsFiltered = $recordsTotal;
}

// Consulta de dados com stock agregado
$sql = "
    SELECT 
        p.idproduto,
        p.nomeproduto AS artigo,
        p.preco_compra,
        p.preco,
        p.iva,
        COALESCE(SUM(s.quantidade), 0) AS stock_total
    FROM produto p
    LEFT JOIN stock s ON p.idproduto = s.produto_id
    " . ($where ? $where : '') . "
    GROUP BY p.idproduto, p.nomeproduto, p.preco_compra, p.preco, p.iva
    ORDER BY p.idproduto DESC
    LIMIT ?, ?
";

// Executar consulta com bind
if ($where) {
    $stmt = mysqli_prepare($db, $sql);
    $typesWithLimit = $types . 'ii';
    $paramsWithLimit = array_merge($params, [$start, $length]);
    mysqli_stmt_bind_param($stmt, $typesWithLimit, ...$paramsWithLimit);
} else {
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $start, $length);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'idproduto' => $row['idproduto'],
        'artigo' => $row['artigo'],
        'preco_compra' => $row['preco_compra'],
        'preco' => $row['preco'],
        'iva' => $row['iva'],
        'stock_total' => $row['stock_total']
    ];
}

$response = [
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
];

// Limpar qualquer output anterior
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
exit;
?>