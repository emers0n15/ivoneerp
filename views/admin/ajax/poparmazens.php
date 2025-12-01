<?php
session_start();
include '../../../conexao/index.php';

// Definição de colunas para ordenação
$columns = array(
    0 => 'id',
    1 => 'nome',
    2 => 'endereco',
    3 => 'telefone',
    4 => 'responsavel',
    5 => 'estado'
);

// Parâmetros do DataTables
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$orderColumn = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Construção da consulta SQL
$sql = "SELECT * FROM armazem WHERE 1=1";

// Filtro de pesquisa
if (!empty($search)) {
    $sql .= " AND (nome LIKE '%$search%' OR endereco LIKE '%$search%' OR telefone LIKE '%$search%' OR responsavel LIKE '%$search%')";
}

// Consulta para contar o total de registros
$sqlCountTotal = "SELECT COUNT(*) as total FROM armazem";
$resultTotal = mysqli_query($db, $sqlCountTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);
$totalRecords = $rowTotal['total'];

// Consulta para contar o total de registros filtrados
$sqlCountFiltered = "SELECT COUNT(*) as total FROM armazem WHERE 1=1";
if (!empty($search)) {
    $sqlCountFiltered .= " AND (nome LIKE '%$search%' OR endereco LIKE '%$search%' OR telefone LIKE '%$search%' OR responsavel LIKE '%$search%')";
}
$resultFiltered = mysqli_query($db, $sqlCountFiltered);
$rowFiltered = mysqli_fetch_assoc($resultFiltered);
$totalRecordsFiltered = $rowFiltered['total'];

// Ordenação
$sql .= " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;

// Paginação
$sql .= " LIMIT $start, $length";

// Execução da consulta
$result = mysqli_query($db, $sql);
$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = array(
        "id" => $row['id'],
        "nome" => $row['nome'],
        "endereco" => $row['endereco'] ?: 'N/A',
        "telefone" => $row['telefone'] ?: 'N/A',
        "responsavel" => $row['responsavel'] ?: 'N/A',
        "estado" => $row['estado']
    );
}

// Resposta no formato esperado pelo DataTables
$response = array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFiltered,
    "data" => $data
);

header('Content-Type: application/json');
echo json_encode($response);
?>
