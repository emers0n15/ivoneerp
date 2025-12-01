<?php
include '../../conexao/index.php';
include_once 'includes/tcpdf_template.php';

$data_inicio = $_GET['data1'];
$data_fim = $_GET['data2'];

// Dados da Empresa
$sqlEmpresa = "SELECT * FROM empresa";
$rsEmp = mysqli_query($db, $sqlEmpresa);
$dadosEmp = mysqli_fetch_array($rsEmp);
$empresa = [
    'nome' => $dadosEmp['nome'] ?? '',
    'endereco' => $dadosEmp['endereco'] ?? '',
    'nuit' => $dadosEmp['nuit'] ?? '',
    'contacto' => $dadosEmp['contacto'] ?? '',
    'email' => $dadosEmp['email'] ?? '',
    'pais' => $dadosEmp['pais'] ?? '',
    'provincia' => $dadosEmp['provincia'] ?? '',
    'img' => $dadosEmp['img'] ?? ''
];

// Documento com cabeçalho padronizado
$titulo = 'Relatório de Consumo Médio Mensal';
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $empresa, $titulo);
$pdf->AddPage();

// Cabeçalhos da tabela
$columns = [
    ['ID Produto', 25, 'L'],
    ['Nome Produto', 75, 'L'],
    ['Estoque Atual', 30, 'C'],
    ['Consumo Médio Mensal', 40, 'C'],
    ['Recomendação', 30, 'L'],
];
tcpdf_table_header($pdf, $columns);

// Consulta para obter os dados
$sql = "
    SELECT 
        p.idproduto,
        p.nomeproduto,
        IFNULL(SUM(s.quantidade), 0) AS stock_atual,
        IFNULL(SUM(e.qtdentrega), 0) AS total_entregas,
        COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')) AS meses
    FROM produto p
    LEFT JOIN stock s ON p.idproduto = s.produto_id
    LEFT JOIN entrega e ON p.idproduto = e.produtoentrega 
        AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'
    GROUP BY p.idproduto
";

$result = mysqli_query($db, $sql);
$rowIndex = 0;
$pdf->SetFont('helvetica', '', 8);

while ($row = mysqli_fetch_assoc($result)) {
    $idproduto = $row['idproduto'];
    $nomeproduto = $row['nomeproduto'];
    $stock = (float)$row['stock_atual'];
    $total_entregas = (float)$row['total_entregas'];
    $meses = (int)$row['meses'];
    $consumo_medio_mensal = $meses > 0 ? ($total_entregas / $meses) : 0;
    $recomendacao = $stock < $consumo_medio_mensal ? 'Reabastecimento Necessário' : 'Estoque Adequado';

    // Page break handling with header repeat
    tcpdf_should_addpage_and_header($pdf, 260, $columns);

    // Zebra row styling
    list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex, $stock < $consumo_medio_mensal);
    if ($fill) { $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]); } else { $pdf->SetFillColor(255,255,255); }

    $pdf->Cell(25, 7, $idproduto, 1, 0, 'L', $fill);
    $pdf->Cell(75, 7, $nomeproduto, 1, 0, 'L', $fill);
    $pdf->Cell(30, 7, number_format($stock, 0, ',', '.'), 1, 0, 'C', $fill);
    $pdf->Cell(40, 7, number_format($consumo_medio_mensal, 2, ',', '.'), 1, 0, 'C', $fill);
    $pdf->Cell(30, 7, $recomendacao, 1, 0, 'L', $fill);
    $pdf->Ln();
    $rowIndex++;
}

// Saída do PDF
$pdf->Output('relatorio_consumo.pdf', 'I');
?>
