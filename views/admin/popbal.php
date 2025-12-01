<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("location:../../");
    exit;
}

// Ocultar erros na saída e registrá-los em log
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error_log'); // Especifique um caminho válido para registrar os erros

include '../../conexao/index.php';

// Limpa qualquer saída anterior
ob_start();

// Configurar parâmetros de paginação e pesquisa
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$searchValue = isset($_GET['search']['value']) ? '%' . $_GET['search']['value'] . '%' : '%';

// Contar registros totais
$countSql = "
    SELECT COUNT(*) AS total
    FROM produto p
    LEFT JOIN ordem_compra_artigos oc ON p.idproduto = oc.artigo
    LEFT JOIN es_artigos es ON p.idproduto = es.artigo
    LEFT JOIN entrega en ON p.idproduto = en.produtoentrega
    LEFT JOIN artigos_devolvidos dev ON p.idproduto = dev.produto
    LEFT JOIN nc_artigos nc ON p.idproduto = nc.artigo
    LEFT JOIN nd_artigos nd ON p.idproduto = nd.artigo
    LEFT JOIN fa_artigos_fact f ON p.idproduto = f.artigo
";
$rsTotal = $db->query($countSql);
$totalRow = $rsTotal->fetch_assoc();
$totalRecords = $totalRow['total'];

// Consultar dados com paginação e pesquisa
$sql = "
    SELECT 
    p.idproduto, 
    p.nomeproduto,
    (SELECT IFNULL(SUM(qtd), 0) FROM ordem_compra_artigos WHERE artigo = p.idproduto) AS quantidade_comprada,
    (SELECT IFNULL(SUM(qtd), 0) FROM es_artigos WHERE artigo = p.idproduto) AS quantidade_estoque,
    (SELECT IFNULL(SUM(qtdentrega), 0) FROM entrega WHERE produtoentrega = p.idproduto) AS quantidade_vendida,
    (SELECT IFNULL(SUM(qtd), 0) FROM fa_artigos_fact WHERE artigo = p.idproduto) AS quantidade_faturada,
    (SELECT IFNULL(SUM(qtd), 0) FROM artigos_devolvidos WHERE produto = p.idproduto) AS quantidade_devolvida,
    (SELECT IFNULL(SUM(qtd), 0) FROM nc_artigos WHERE artigo = p.idproduto) AS quantidade_creditada,
    (SELECT IFNULL(SUM(qtd), 0) FROM nd_artigos WHERE artigo = p.idproduto) AS quantidade_debitada
FROM produto p
LIMIT ?, ?

";
$stmt = $db->prepare($sql);
$stmt->bind_param('ii', $start, $length);
$stmt->execute();
$rs = $stmt->get_result();

$data = [];
while ($row = $rs->fetch_assoc()) {
    $data[] = $row;
}

// Retornar JSON para DataTables
$response = [
    "draw" => intval($_GET['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
];

// Limpa o buffer e envia o JSON
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>
