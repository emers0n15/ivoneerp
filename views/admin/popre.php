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
$countSql = "SELECT COUNT(*) AS total FROM requisicao_externa";
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
    SELECT COUNT(*) AS filtered FROM requisicao_externa AS d
    WHERE
        d.id LIKE ? OR
        d.motivo LIKE ? OR
        CONCAT('RE#', d.serie, '/', d.n_doc) LIKE ? OR
        (SELECT nome FROM sector WHERE sector.id = d.sector) LIKE ? OR
        (SELECT nome FROM users WHERE users.id = d.user) LIKE ? OR
        (SELECT responsavel FROM sector WHERE sector.id = d.sector) LIKE ? OR
        (SELECT nome FROM fornecedor WHERE fornecedor.id = d.fornecedor) LIKE ?
";
$stmtFiltered = $db->prepare($filterSql);
$stmtFiltered->bind_param('sssssss', $searchValue, $searchValue, $searchValue, $searchValue, $searchValue, $searchValue, $searchValue);
$stmtFiltered->execute();
$rsFiltered = $stmtFiltered->get_result();
$filteredRow = $rsFiltered->fetch_assoc();
$recordsFiltered = $filteredRow['filtered'];

// Consultar registros com paginação e pesquisa
$sql = "
    SELECT 
        d.id, CONCAT('RE#', d.serie, '/', d.n_doc) AS descricao, d.motivo, d.solicitante,
        (SELECT nome FROM fornecedor WHERE fornecedor.id = d.fornecedor) as fornecedors, 
        (SELECT nome FROM sector WHERE sector.id = d.sector) as sectorr,
        (SELECT responsavel FROM sector WHERE sector.id = d.sector) as responsavel,
        d.data, 
        (SELECT nome FROM users WHERE users.id = d.user) as usuario
    FROM 
        requisicao_externa AS d
    WHERE
        d.id LIKE ? OR
        d.motivo LIKE ? OR
        CONCAT('RE#', d.serie, '/', d.n_doc) LIKE ? OR
        (SELECT nome FROM sector WHERE sector.id = d.sector) LIKE ? OR
        (SELECT nome FROM users WHERE users.id = d.user) LIKE ? OR
        (SELECT responsavel FROM sector WHERE sector.id = d.sector) LIKE ? OR
        (SELECT nome FROM fornecedor WHERE fornecedor.id = d.fornecedor) LIKE ?
    LIMIT $start, $length
";

$stmt = $db->prepare($sql);
$stmt->bind_param('sssssss', $searchValue, $searchValue, $searchValue, $searchValue, $searchValue, $searchValue, $searchValue);
$stmt->execute();
$rs = $stmt->get_result();

// Preparar os dados para DataTables
$data = [];
while ($dados = $rs->fetch_assoc()) {
    $data[] = [
        'id' => $dados['id'],
        'descricao' => $dados['descricao'],
        'fornecedor' => $dados['fornecedors'],
        'sectorr' => $dados['sectorr'],
        'responsavel' => $dados['responsavel'],
        'solicitante' => $dados['solicitante'],
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
