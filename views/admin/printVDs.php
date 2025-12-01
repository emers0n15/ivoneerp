<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../");
}
include_once '../../conexao/index.php';

$serie = $_GET['serie'];
$data1 = $_GET['data1'];
$data2 = $_GET['data2'];

// Template TCPDF unificado
require_once 'includes/tcpdf_template.php';

// Dados da Empresa
$sql = "SELECT * FROM empresa LIMIT 1";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);

// Criar novo documento PDF com header/rodapé unificados
$titulo = 'RELATÓRIO DE VENDAS A DINHEIRO - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Vendas a Dinheiro');
$pdf->AddPage();

// Cabeçalho da tabela
$pdf->SetFont('helvetica', '', 9);
$columns = [
    ['#', 12], ['Descrição', 38], ['Valor', 22], ['IVA', 18], ['Total', 25], ['Modo de Pagamento', 35], ['Data de Pagamento', 40]
];
tcpdf_table_header($pdf, $columns);

// Totais
$total_pagamento = 0;
$total_iva = 0;
$total_geral = 0;

$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT DISTINCT(clientepedido) as cl FROM `pedido` as e WHERE data BETWEEN '$data1' AND '$data2'";
$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
while ($dados3 = mysqli_fetch_array($rs3)) {
    $cl = $dados3['cl'];
    $sqlxx = "SELECT e.nome, e.apelido FROM `clientes` as e WHERE id = '$cl'";
    $rsxx = mysqli_query($db, $sqlxx) or die(mysqli_error($db));
    $dadosxx = mysqli_fetch_array($rsxx);
    
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(0, 7, 'Entidade/Fornecedor:', 'T', 0);
    $pdf->SetX(45);
    $pdf->Cell(0, 7, $dadosxx['nome']." ".$dadosxx['apelido'], 'T', 0);
    $pdf->Ln(6);

    $pdf->SetFont('helvetica', '', 8);
    $sqlx = "SELECT * FROM pedido as f WHERE clientepedido = '$cl' AND f.data BETWEEN '$data1' AND '$data2' AND serie = '$serie' ORDER BY f.data";
    $rsx = mysqli_query($db, $sqlx) or die(mysqli_error($db));
    
    while ($dadosx = mysqli_fetch_array($rsx)) {
        $tss = $dadosx['pagamentopedido'] + $dadosx['iva'];
        $total_pagamento += $dadosx['pagamentopedido'];
        $total_iva += $dadosx['iva'];
        $total_geral += $tss;

        $pdf->Cell(12, 6, $dadosx['idpedido'], 1, 0, 'C');
        $pdf->Cell(38, 6, "VD#".$dadosx['serie']."/".$dadosx['n_doc'], 1, 0, 'L');
        $pdf->Cell(22, 6, number_format($dadosx['pagamentopedido'], 2), 1, 0, 'R');
        $pdf->Cell(18, 6, number_format($dadosx['iva'], 2), 1, 0, 'R');
        $pdf->Cell(25, 6, number_format($tss, 2), 1, 0, 'R');
        $pdf->Cell(35, 6, $dadosx['modo'], 1, 0, 'C');
        $pdf->Cell(40, 6, $dadosx['data'], 1, 1, 'C');

        // Nova página com cabeçalho quando necessário
        tcpdf_should_addpage_and_header($pdf, 260, $columns);
    }
}

// Totais
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(50, 7, 'Total Pagamento', 1, 0, 'L');
$pdf->Cell(30, 7, number_format($total_pagamento, 2), 1, 0, 'R');
$pdf->Cell(30, 7, 'Total IVA', 1, 0, 'L');
$pdf->Cell(30, 7, number_format($total_iva, 2), 1, 0, 'R');
$pdf->Cell(30, 7, 'Total Geral', 1, 0, 'L');
$pdf->Cell(20, 7, number_format($total_geral, 2), 1, 1, 'R');
$pdf->Ln(7);

// Observação
$pdf->Ln(4);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');

// Output do PDF
$pdf->Output('Relatorio_vendas_dinheiro.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();
?>
