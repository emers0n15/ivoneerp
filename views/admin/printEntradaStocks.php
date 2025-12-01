<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../../conexao/index.php';

$utilizador = $_GET['utilizador'];
$serie = $_GET['serie'];
$data1 = $_GET['data1'];
$data2 = $_GET['data2'];

// Template TCPDF unificado com cabeçalho/rodapé e logotipo
require_once 'includes/tcpdf_template.php';

// Dados da Empresa
$sql = "SELECT * FROM empresa LIMIT 1";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);

// Criar novo documento PDF com header/rodapé unificados (inclui logotipo)
$titulo = 'RELATÓRIO DE ENTRADAS DE STOCK (RESUMO) - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Entradas de Stock');
$pdf->AddPage();

// Restante dos dados da Empresa a Faturar
// Cabeçalho da tabela padronizado
$pdf->SetFont('helvetica', '', 9);
$columns = [ ['Descrição', 110, 'L'], ['Quantidade Total', 40, 'C'], ['Valor Total', 40, 'R'] ];
tcpdf_table_header($pdf, $columns);
$tot = 0;

// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT (SELECT nomeproduto FROM produto as g WHERE g.idproduto = e.artigo) as artigos, SUM(qtd) as qtds, (SUM(qtd) *(SELECT preco FROM produto as g WHERE g.idproduto = e.artigo)) as valor  FROM `es_artigos` as e INNER JOIN entrada_stock as en ON en.id = e.es WHERE en.data BETWEEN '$data1' AND '$data2' AND en.serie = '$serie' OR en.user = '$utilizador' GROUP BY artigos";
$rs3 = mysqli_query($db, $sql3);
rowIndex:
while ($dados3 = mysqli_fetch_array($rs3)) {
    $tot = $tot + $dados3['valor'];
    static $i = 0; list($fill, $rgb) = tcpdf_row_fill_toggle($i++);
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
    $pdf->Cell(110, 7, $dados3['artigos'], 1, 0, 'L', $fill);
    $pdf->Cell(40, 7, $dados3['qtds'], 1, 0, 'C', $fill);
    $pdf->Cell(40, 7, number_format($dados3['valor'],2), 1, 1, 'R', $fill);

    tcpdf_should_addpage_and_header($pdf, 260, $columns);
}
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(150, 7, 'Total', 0, 0, 'R');
$pdf->Cell(40, 7, number_format($tot,2), 0, 1, 'R');

$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');


// Output do PDF
$pdf->Output('Relatorio_entrada_stock.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();