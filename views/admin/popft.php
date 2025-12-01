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
$countSql = "SELECT COUNT(*) AS total FROM factura";
$countRs = mysqli_query($db, $countSql) or die(mysqli_error($db));
$countRow = mysqli_fetch_assoc($countRs);
$totalRecords = $countRow['total'];

// Consultar registros com paginação e pesquisa
$sql = "
    SELECT 
        `id`, CONCAT('FA#', f.serie, '/', f.n_doc) AS descricao, `valor`, `iva`, `serie`, `prazo`, `metodo`, `statuss`, (SELECT CONCAT('NC#', n.serie, '/', n.n_doc) FROM nota_de_credito as n WHERE n.id = f.nota_credito) as nt, (SELECT CONCAT('RC#', r.serie, '/', r.n_doc) FROM recibo as r WHERE r.id = f.recibo) as recibox, (SELECT CONCAT('ND#', nd.serie, '/', nd.n_doc) FROM nota_debito as nd WHERE nd.id = f.nota_debito) as nota_debitox, (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = f.cliente) as clientex,
        (SELECT nome FROM users WHERE users.id = f.usuario) as usuario
    FROM 
        factura AS f
    WHERE
        f.id LIKE ? OR
        f.prazo LIKE ? OR
        CONCAT('FA#', f.serie, '/', f.n_doc) LIKE ? OR
        usuario LIKE ? OR
        (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = f.cliente) LIKE ?
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
    $data[] = [
        'id' => $dados['id'],
        'serie' => $dados['serie'],
        'descricao' => $dados['descricao'],
        'valor' => $v,
        'iva' => $i,
        'cliente' => $dados['clientex'],
        'prazo' => $dados['prazo'],
        'recibo' => $dados['recibox'],
        'nota_credito' => $dados['nt'],
        'nota_debito' => $dados['nota_debitox'],
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
