<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../../conexao/index.php';

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
$titulo = 'CONTAS A RECEBER - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Contas a Receber');
$pdf->AddPage();

// Conteúdo da fatura

// Cabeçalho e dados da empresa são geridos pelo template

// Cabeçalho de relatório controlado pelo template

// Cabeçalho da tabela
$pdf->SetFont('helvetica', '', 9);
$columns = [
    ['#', 12], ['Descrição', 35], ['Valor', 22], ['IVA', 18], ['Data de Emissão', 48], ['Prazo de Pagamento', 55]
];
tcpdf_table_header($pdf, $columns);

// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT DISTINCT(cliente) as cl FROM `factura` as e WHERE data BETWEEN '$data1' AND '$data2' AND statuss = 0";
$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
while ($dados3 = mysqli_fetch_array($rs3)) {
	$cl = $dados3['cl'];
	$sqlxx = "SELECT CONCAT(nome,' ',apelido) as client FROM `clientes` as e WHERE id = '$cl'";
	$rsxx = mysqli_query($db, $sqlxx) or die(mysqli_error($db));
	$dadosxx = mysqli_fetch_array($rsxx);
	$pdf->SetFont('helvetica', 'B', 8);
	$pdf->Cell(0, 7, 'Entidade/Cliente:', 'T', 0);
	$pdf->SetX(35);
	$pdf->Cell(0, 7, $dadosxx['client'], 'T', 0);
	$pdf->Ln(6);
	$pdf->SetFont('helvetica', '', 8);
	$sqlx = "SELECT * FROM factura as f WHERE cliente = '$cl' AND f.statuss = 0 AND f.data BETWEEN '$data1' AND '$data2' ORDER BY f.dataa";
	$rsx = mysqli_query($db, $sqlx) or die(mysqli_error($db));
	$rowIndex = 0;
	while ($dadosx = mysqli_fetch_array($rsx)) {
		list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex++);
		$pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
		$pdf->Cell(12, 6, $dadosx['id'], 1, 0, 'C', $fill);
		$pdf->Cell(35, 6, "FA#".$dadosx['serie']."/".$dadosx['id'], 1, 0, 'L', $fill);
		$pdf->Cell(22, 6, number_format($dadosx['valor'], 2), 1, 0, 'R', $fill);
		$pdf->Cell(18, 6, number_format($dadosx['iva'], 2), 1, 0, 'R', $fill);
		$pdf->Cell(48, 6, $dadosx['data'], 1, 0, 'C', $fill);
		$pdf->Cell(55, 6, $dadosx['prazo'], 1, 1, 'C', $fill);

		tcpdf_should_addpage_and_header($pdf, 260, $columns);
	}
}

$sql5 = "SELECT SUM(valor) as valors FROM factura as f JOIN clientes c ON f.cliente = c.id WHERE f.statuss = 0 AND f.data BETWEEN '$data1' AND '$data2' ORDER BY f.dataa";
$rs5 = mysqli_query($db, $sql5) or die(mysqli_error($db));
$dados5 = mysqli_fetch_array($rs5);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Total por Receber', 'T', 0);
$pdf->SetX(55);
$pdf->Cell(0, 7, $dados5['valors'], 'T', 0);
$pdf->Ln(6);

// Observação
$pdf->Ln(4);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');

// Output do PDF
$pdf->Output('Contas_receber.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();