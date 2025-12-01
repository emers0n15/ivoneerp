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
$countSql = "SELECT COUNT(*) AS total FROM nota_debito";
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
    SELECT COUNT(*) AS filtered FROM nota_debito AS n
    WHERE
        n.id LIKE ? OR
        n.motivo LIKE ? OR
        CONCAT('ND#', n.serie, '/', n.n_doc) LIKE ? OR
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = n.cliente) LIKE ? OR
        (SELECT nome FROM users WHERE users.id = n.usuario) LIKE ?
";
$stmtFiltered = $db->prepare($filterSql);
$stmtFiltered->bind_param('sssss', $searchValue, $searchValue, $searchValue, $searchValue, $searchValue);
$stmtFiltered->execute();
$rsFiltered = $stmtFiltered->get_result();
$filteredRow = $rsFiltered->fetch_assoc();
$recordsFiltered = $filteredRow['filtered'];

// Consultar registros com paginação e pesquisa
$sql = "
    SELECT 
        n.id, CONCAT('ND#', n.serie, '/', n.n_doc) AS descricao, n.valor, n.iva, 
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = n.cliente) as cliente,
        (SELECT CONCAT('FA#', f.serie, '/', f.n_doc) FROM factura AS f WHERE f.id = n.id_factura) AS factura, 
        n.motivo, 
        (SELECT nome FROM users WHERE users.id = n.usuario) as usuario
    FROM 
        nota_debito AS n
    WHERE
        n.id LIKE ? OR
        n.motivo LIKE ? OR
        CONCAT('ND#', n.serie, '/', n.n_doc) LIKE ? OR
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = n.cliente) LIKE ? OR
        (SELECT nome FROM users WHERE users.id = n.usuario) LIKE ?
    LIMIT $start, $length
";

$stmt = $db->prepare($sql);
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
        'iva' => number_format($dados['iva'], 2),
        'cliente' => $dados['cliente'],
        'factura' => $dados['factura'],
        'motivo' => $dados['motivo'],
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
