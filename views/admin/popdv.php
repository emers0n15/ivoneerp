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
$countSql = "SELECT COUNT(*) AS total FROM devolucao";
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
    SELECT COUNT(*) AS filtered FROM devolucao AS d
    WHERE
        d.id LIKE ? OR
        d.motivo LIKE ? OR
        CONCAT('DV#', d.serie, '/', d.n_doc) LIKE ? OR
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = d.idcliente) LIKE ? OR
        (SELECT nome FROM users WHERE users.id = d.iduser) LIKE ?
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
        d.id, CONCAT('DV#', d.serie, '/', d.n_doc) AS descricao, d.valor, d.modo, 
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = d.idcliente) as cliente,
        (SELECT CONCAT('VD#', p.serie, '/', p.n_doc) FROM pedido AS p WHERE p.idpedido = d.idpedido) AS pedidos, 
        d.motivo,
        d.data, 
        (SELECT nome FROM users WHERE users.id = d.iduser) as usuario
    FROM 
        devolucao AS d
    WHERE
        d.id LIKE ? OR
        d.motivo LIKE ? OR
        CONCAT('DV#', d.serie, '/', d.n_doc) LIKE ? OR
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = d.idcliente) LIKE ? OR
        (SELECT nome FROM users WHERE users.id = d.iduser) LIKE ?
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
        'modo' => $dados['modo'],
        'cliente' => $dados['cliente'],
        'pedidos' => $dados['pedidos'],
        'motivo' => $dados['motivo'],
        'usuario' => $dados['usuario'],
        'datax' => $dados['data']
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
