<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../");
}
include_once '../../conexao/index.php';

// Receber os parâmetros da URL
$utilizador = $_GET['utilizador'];
$data1 = $_GET['data1'];
$data2 = $_GET['data2'];
$status = $_GET['status'];

// Template TCPDF unificado
require_once 'includes/tcpdf_template.php';

// Dados da Empresa
$sql = "SELECT * FROM empresa LIMIT 1";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);

// Criar novo documento PDF com header/rodapé unificados
$titulo = 'RELATÓRIO DE CAIXAS - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Caixas');
$pdf->AddPage();

// Cabeçalho da tabela
$pdf->SetFont('helvetica', '', 9);
$columns = [
    ['#', 12], ['Descrição', 38], ['Valor de Abertura', 30], ['Valor de Fecho', 30], ['Data de Abertura', 40], ['Data de Fecho', 40]
];
tcpdf_table_header($pdf, $columns);

// Preparar e executar a consulta SQL
$sql3 = "SELECT * FROM periodo 
         WHERE datafechoperiodo BETWEEN ? AND ? 
           AND diaperiodo LIKE ? 
           AND (usuario = ? OR ? = 10000)";

$stmt = $db->prepare($sql3);
$status = ($status == "Selecione o status") ? '%' : $status;
$stmt->bind_param('sssii', $data1, $data2, $status, $utilizador, $utilizador);
$stmt->execute();
$result = $stmt->get_result();

// Adicionar dados ao PDF
$pdf->SetFont('helvetica', '', 8);
$rowIndex = 0;
while ($dados3 = $result->fetch_assoc()) {
    list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex++);
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
    $pdf->Cell(12, 6, $dados3['idperiodo'], 1, 0, 'C', $fill);
    $pdf->Cell(38, 6, "CX#".$dados3['serie'].'/'.$dados3['idperiodo'], 1, 0, 'L', $fill);
    $pdf->Cell(30, 6, number_format($dados3['aberturaperiodo'], 2, ".", ","), 1, 0, 'R', $fill);
    $pdf->Cell(30, 6, number_format($dados3['fechoperiodo'], 2, ".", ","), 1, 0, 'R', $fill);
    $pdf->Cell(40, 6, $dados3['dataaberturaperiodo'], 1, 0, 'C', $fill);
    $pdf->Cell(40, 6, $dados3['datafechoperiodo'], 1, 1, 'C', $fill);

    tcpdf_should_addpage_and_header($pdf, 260, $columns);
}

// Calcular e exibir o total
$sql4 = "SELECT SUM(fechoperiodo) as total FROM periodo 
         WHERE datafechoperiodo BETWEEN ? AND ? 
           AND diaperiodo LIKE ? 
           AND (usuario = ? OR ? = 10000)";

$stmt4 = $db->prepare($sql4);
$stmt4->bind_param('sssii', $data1, $data2, $status, $utilizador, $utilizador);
$stmt4->execute();
$result4 = $stmt4->get_result();
$dados4 = $result4->fetch_assoc();

// Totais
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(50, 7, "Total", 0, 0, 'L');
$pdf->Cell(30, 7, '', 0, 0);
$pdf->Cell(30, 7, number_format($dados4['total'], 2, ".", ","), 0, 1, 'R');

// Observação
$pdf->Ln(4);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');

// Output do PDF
$pdf->Output('Relatorio_caixa.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();
