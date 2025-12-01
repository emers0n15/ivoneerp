<?php
include '../../conexao/index.php';

$data_inicio = $_GET['data1'];
$data_fim = $_GET['data2'];

// Consulta para obter o total de produtos entregues
$sql = "
    SELECT 
        'Total Entregas' as tabela,
        IFNULL(SUM(e.qtdentrega), 0) AS total
    FROM entrega e
    WHERE e.datavenda BETWEEN '$data_inicio' AND '$data_fim'
";

$result = mysqli_query($db, $sql);
$row = mysqli_fetch_assoc($result);
$total_por_receber = $row['total'];

$data = [
    [
        "tabela" => "Total Entregas",
        "total" => $total_por_receber
    ]
];

// Resultado final
$response = [
    "draw" => intval($_GET['draw']),
    "recordsTotal" => count($data),
    "recordsFiltered" => count($data),
    "data" => $data
];

echo json_encode($response);
?>
