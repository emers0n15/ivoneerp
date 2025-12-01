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

// Consultar o número total de registros
$countSql = "SELECT COUNT(*) AS total FROM pedido";
$countRs = mysqli_query($db, $countSql) or die(mysqli_error($db));
$countRow = mysqli_fetch_assoc($countRs);
$totalRecords = $countRow['total'];

// Consultar registros com paginação e pesquisa
$sql = "
    SELECT 
        p.idpedido,
        CONCAT('VD#', p.serie, '/', p.n_doc) AS descricao,
        p.pagamentopedido AS valor,
        CONCAT(c.nome, ' ', c.apelido) AS cliente,
        u.nome AS usuario,
        IFNULL(CONCAT('DV#', d.serie, '/', d.n_doc), '-') AS devolucao
    FROM 
        pedido AS p
    LEFT JOIN 
        clientes AS c ON c.id = p.clientepedido
    LEFT JOIN 
        users AS u ON u.id = p.userpedido
    LEFT JOIN 
        devolucao AS d ON d.id = p.devolucao
    WHERE
        p.idpedido LIKE ? OR
        CONCAT('VD#', p.serie, '/', p.n_doc) LIKE ? OR
        p.pagamentopedido LIKE ? OR
        CONCAT(c.nome, ' ', c.apelido) LIKE ? OR
        u.nome LIKE ? OR
        IFNULL(CONCAT('DV#', d.serie, '/', d.n_doc), '-') LIKE ?
    LIMIT ?, ?
";

// Preparar e executar a consulta com pesquisa
$searchValue = "%$searchValue%";
$stmt = $db->prepare($sql);
$stmt->bind_param('ssssssii', $searchValue, $searchValue, $searchValue, $searchValue, $searchValue, $searchValue, $start, $length);
$stmt->execute();
$rs = $stmt->get_result();

// Preparar os dados para DataTables
$data = [];
while ($dados = $rs->fetch_assoc()) {
    $data[] = [
        'idpedido' => $dados['idpedido'],
        'descricao' => $dados['descricao'],
        'valor' => $dados['valor'],
        'cliente' => $dados['cliente'],
        'usuario' => $dados['usuario'],
        'devolucao' => $dados['devolucao']
    ];
}

// Consultar o número total de registros filtrados
$filterSql = "
    SELECT COUNT(*) AS total FROM pedido AS p
    LEFT JOIN clientes AS c ON c.id = p.clientepedido
    LEFT JOIN users AS u ON u.id = p.userpedido
    LEFT JOIN devolucao AS d ON d.id = p.devolucao
    WHERE
        p.idpedido LIKE ? OR
        CONCAT('VD#', p.serie, '/', p.n_doc) LIKE ? OR
        p.pagamentopedido LIKE ? OR
        CONCAT(c.nome, ' ', c.apelido) LIKE ? OR
        u.nome LIKE ? OR
        IFNULL(CONCAT('DV#', d.serie, '/', d.n_doc), '-') LIKE ?
";

$filterStmt = $db->prepare($filterSql);
$filterStmt->bind_param('ssssss', $searchValue, $searchValue, $searchValue, $searchValue, $searchValue, $searchValue);
$filterStmt->execute();
$filterRs = $filterStmt->get_result();
$filterRow = $filterRs->fetch_assoc();
$totalFilteredRecords = $filterRow['total'];

// Responder com dados no formato JSON
$response = [
    'draw' => intval($_GET['draw']),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFilteredRecords, // Contagem de registros filtrados
    'data' => $data
];

echo json_encode($response);
?>
