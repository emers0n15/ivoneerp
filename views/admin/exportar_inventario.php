<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit();
}

include_once '../../conexao/index.php';
require '../../tfpdf/tfpdf.php';
require_once 'includes/pdf_template.php';

// Criar PDF
$pdf = new tFPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Cabeçalho padrão
pdf_add_company_header($pdf, $db, 'RELATÓRIO DE INVENTÁRIO - STOCK ACTUAL');

// Cabeçalho da tabela (larguras somam 190mm: 12+78+22+22+28+28)
$columns = [
    ['ID', 12],
    ['PRODUTO', 78],
    ['STOCK MIN', 22],
    ['STOCK ACTUAL', 22],
    ['LOTE', 28],
    ['PRAZO', 28],
];
pdf_table_header($pdf, $columns);

// Consulta alinhada com a tabela da tela (popartgs.php), sem paginação
$query_itens = "
    SELECT 
        p.idproduto,
        p.nomeproduto AS artigo,
        p.stock_min,
        SUM(s.quantidade) AS stock_atual,
        s.lote,
        s.prazo
    FROM stock s
    INNER JOIN produto p ON s.produto_id = p.idproduto
    WHERE s.quantidade > 0
    GROUP BY p.idproduto, s.lote, s.prazo
    HAVING stock_atual > 0
    ORDER BY p.nomeproduto ASC, s.prazo ASC
";
$result_itens = mysqli_query($db, $query_itens);

$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0, 0, 0); // Texto preto
$total = 0;
$stock_baixo = 0;

if ($result_itens && mysqli_num_rows($result_itens) > 0) {
    while ($row = mysqli_fetch_assoc($result_itens)) {
        $total++;

        $produto_id = $row['idproduto'];
        $nome_produto = $row['artigo'] ?? ('Produto ID ' . $produto_id);
        $stock_minimo = (int)($row['stock_min'] ?? 0);
        $stock_atual = (int)($row['stock_atual'] ?? 0);
        $lote = $row['lote'] ?? 'N/A';
        $prazo = $row['prazo'] ?? '';

        // Verificar stock baixo
        if ($stock_atual <= $stock_minimo && $stock_minimo > 0) {
            $stock_baixo++;
            $pdf->SetFillColor(255, 200, 200); // Vermelho claro
        } else {
            $pdf->SetFillColor(245, 245, 245); // Linhas padrão
        }

        // Converter encoding para caracteres especiais
        if (function_exists('iconv')) {
            $nome_produto = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $nome_produto);
            $lote = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $lote);
            $prazo = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$prazo);
        }

        // Linha da tabela
        $pdf->Cell(12, 6, $produto_id, 1, 0, 'C', true);
        $pdf->Cell(78, 6, substr($nome_produto, 0, 60), 1, 0, 'L', true);
        $pdf->Cell(22, 6, $stock_minimo, 1, 0, 'C', true);
        $pdf->Cell(22, 6, $stock_atual, 1, 0, 'C', true);
        $pdf->Cell(28, 6, substr($lote, 0, 14), 1, 0, 'C', true);
        $pdf->Cell(28, 6, substr($prazo, 0, 14), 1, 1, 'C', true);

        // Quebra de página se necessário
        if ($pdf->GetY() > 260) {
            $pdf->AddPage();
            pdf_table_header($pdf, $columns);
        }
    }
    
    // Resumo estatístico
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 8, 'RESUMO DO INVENTÁRIO', 0, 1, 'L');
   
    if ($stock_baixo > 0) {
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(0, 6, 'Produtos com stock abaixo do mínimo: ' . $stock_baixo, 0, 1);
        $pdf->SetTextColor(0, 0, 0);
    }
    
} else {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 8, 'Nenhum produto encontrado na base de dados.', 1, 1, 'C');
}

// Rodapé padrão
pdf_add_footer($pdf);

$pdf->Output('Relatorio_Inventario_' . date('Y-m-d') . '.pdf', 'I');

if (isset($db)) {
    mysqli_close($db);
}
?>