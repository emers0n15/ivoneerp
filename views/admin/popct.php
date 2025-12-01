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
$countSql = "SELECT COUNT(*) AS total FROM cotacao";
$countRs = mysqli_query($db, $countSql) or die(mysqli_error($db));
$countRow = mysqli_fetch_assoc($countRs);
$totalRecords = $countRow['total'];

// Consultar registros com paginação e pesquisa
$sql = "
    SELECT 
        `id`, CONCAT('CT#', c.serie, '/', c.n_doc) as `descrica`, `valor`, `iva`, `disconto`, `prazo`, (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = c.cliente) as cliente, (SELECT nome FROM users WHERE users.id = c.usuario) as usuario, `data`
    FROM 
        cotacao AS c
    WHERE
        c.id LIKE ? OR
        c.prazo LIKE ? OR
        CONCAT('CT#', c.serie, '/', c.n_doc) LIKE ? OR
        usuario LIKE ? OR
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = c.cliente) LIKE ?
    LIMIT $start, $length
";

// Preparar e executar a consulta com pesquisa
$searchValue = "%$searchValue%";
$stmt = $db->prepare($sql);
$stmt->bind_param('sssss', $searchValue, $searchValue, $searchValue, $searchValue, $searchValue);
$stmt->execute();
$rs = $stmt->get_result();

// Preparar os dados para DataTables
$data = [];
while ($dados = $rs->fetch_assoc()) {
    $v = number_format($dados['valor'], 2);
    $i = number_format($dados['iva'], 2);
    $d = number_format($dados['disconto'], 2);
    $data[] = [
        'id' => $dados['id'],
        'descricao' => $dados['descrica'],
        'valor' => $v,
        'iva' => $i,
        'disc' => $d,
        'cliente' => $dados['cliente'],
        'prazo' => $dados['prazo'],
        'data' => $dados['data'],
        'usuario' => $dados['usuario']
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
