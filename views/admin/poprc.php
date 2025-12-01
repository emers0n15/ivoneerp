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
$searchValue = "%$searchValue%"; // Adiciona os curinga '%' para a pesquisa

// Consultar o número total de registros
$countSql = "SELECT COUNT(*) AS total FROM recibo";
$countRs = mysqli_query($db, $countSql);
if (!$countRs) {
    echo json_encode([
        'error' => 'Erro ao consultar o banco de dados: ' . mysqli_error($db)
    ]);
    exit;
}
$countRow = mysqli_fetch_assoc($countRs);
$totalRecords = $countRow['total'];

// Consultar o número de registros filtrados
$filterSql = "
    SELECT COUNT(*) AS filtered FROM recibo AS r
    WHERE
        r.id LIKE ? OR
        r.modo LIKE ? OR
        CONCAT('RC#', r.serie, '/', r.n_doc) LIKE ? OR
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = r.cliente) LIKE ? OR
        (SELECT nome FROM users WHERE users.id = r.user) LIKE ?
";
$stmtFiltered = $db->prepare($filterSql);
if (!$stmtFiltered) {
    echo json_encode(['error' => 'Erro de preparação: ' . $db->error]);
    exit;
}

$stmtFiltered->bind_param('sssss', $searchValue, $searchValue, $searchValue, $searchValue, $searchValue);
$stmtFiltered->execute();
$rsFiltered = $stmtFiltered->get_result();
$filteredRow = $rsFiltered->fetch_assoc();
$recordsFiltered = $filteredRow['filtered'];

// Consultar registros com paginação e pesquisa
$sql = "
    SELECT 
        r.id, CONCAT('RC#', r.serie, '/', r.n_doc) AS descricao, r.valor, 
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = r.cliente) as cliente, 
        r.modo, 
        (SELECT nome FROM users WHERE users.id = r.user) as usuario,
        r.data
    FROM 
        recibo AS r
    WHERE
        r.id LIKE ? OR
        r.modo LIKE ? OR
        CONCAT('RC#', r.serie, '/', r.n_doc) LIKE ? OR
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = r.cliente) LIKE ? OR
        (SELECT nome FROM users WHERE users.id = r.user) LIKE ?
    LIMIT $start, $length
";

$stmt = $db->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Erro na preparação da consulta: ' . $db->error]);
    exit;
}

$stmt->bind_param('sssss', $searchValue, $searchValue, $searchValue, $searchValue, $searchValue);
$stmt->execute();
$rs = $stmt->get_result();

// Preparar os dados para DataTables
$data = [];
while ($dados = $rs->fetch_assoc()) {
    $data[] = [
        'id' => $dados['id'],
        'descricao' => $dados['descricao'],
        'valor' => number_format($dados['valor'], 2),
        'modo' => $dados['modo'],
        'cliente' => $dados['cliente'],
        'data' => $dados['data'],
        'usuario' => $dados['usuario']
    ];
}

// Responder com dados no formato JSON
header('Content-Type: application/json');
$response = [
    'draw' => intval($_GET['draw']),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
];

echo json_encode($response);

?>
