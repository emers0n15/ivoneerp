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
$img = $dados['img'];

// Criar novo documento PDF com header/rodapé unificados
$titulo = 'RELATÓRIO DE NOTAS DE CRÉDITO - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Notas de Crédito');
$pdf->AddPage();

// Cabeçalho e dados da empresa são geridos pelo template

// Cabeçalho da tabela
$pdf->SetFont('helvetica', '', 9);
$columns = [
    ['#', 12], ['Descrição', 40], ['Valor', 25], ['Motivo', 73], ['Data', 40]
];
tcpdf_table_header($pdf, $columns);

// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT DISTINCT(cliente) as cl FROM `nota_de_credito` as e WHERE data BETWEEN '$data1' AND '$data2'";
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
    $sqlx = "SELECT * FROM nota_de_credito as f WHERE cliente = '$cl' AND f.data BETWEEN '$data1' AND '$data2' AND serie = '$serie' ORDER BY f.data";
    $rsx = mysqli_query($db, $sqlx) or die(mysqli_error($db));
    $rowIndex = 0;
    while ($dadosx = mysqli_fetch_array($rsx)) {
        list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex++);
        $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
        $pdf->Cell(12, 6, $dadosx['id'], 1, 0, 'C', $fill);
        $pdf->Cell(40, 6, "NC#".$dadosx['serie']."/".$dadosx['n_doc'], 1, 0, 'L', $fill);
        $pdf->Cell(25, 6, number_format($dadosx['valor'], 2), 1, 0, 'R', $fill);
        $pdf->Cell(73, 6, substr($dadosx['motivo'], 0, 40), 1, 0, 'L', $fill);
        $pdf->Cell(40, 6, $dadosx['data'], 1, 1, 'C', $fill);

        tcpdf_should_addpage_and_header($pdf, 260, $columns);
    }
}

// Observação
$pdf->Ln(4);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');

// Totais

// // Assinaturas
// $pdf->SetFont('helvetica', 'B', 12);
// $pdf->Cell(0, 20, 'Assinaturas', 0, 1);

// Output do PDF
$pdf->Output('Relatorio_notas_credito.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();