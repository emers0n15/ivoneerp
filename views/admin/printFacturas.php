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
$titulo = 'RELATÓRIO DE FACTURAS - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Facturas');
$pdf->AddPage();

// O cabeçalho e informações da empresa são geridos pelo template na classe ThemedTCPDF

// Cabeçalho da tabela de Itens da Fatura
$pdf->SetFont('helvetica', '', 9);
$columns = [
    ['#', 10], ['Descrição', 40], ['Valor', 25], ['IVA', 20], ['Total', 30], ['Data de Emissão', 30], ['Data de Pagamento', 35]
];
tcpdf_table_header($pdf, $columns);

// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT DISTINCT(cliente) as cl FROM `factura` as e WHERE data BETWEEN '$data1' AND '$data2'";
$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
while ($dados3 = mysqli_fetch_array($rs3)) {
	$cl = $dados3['cl'];
	$sqlxx = "SELECT e.nome, e.apelido FROM `clientes` as e WHERE id = '$cl'";
	$rsxx = mysqli_query($db, $sqlxx) or die(mysqli_error($db));
	$dadosxx = mysqli_fetch_array($rsxx);
	$pdf->SetFont('helvetica', 'B', 8);
	$pdf->Cell(50, 7, 'Entidade/Fornecedor:', 'T', 0);
	$pdf->Cell(140, 7, $dadosxx['nome']." ".$dadosxx['apelido'], 'T', 0);
	$pdf->Ln(6);
$pdf->SetFont('helvetica', '', 8);
	$sqlx = "SELECT * FROM factura as f WHERE cliente = '$cl' AND f.data BETWEEN '$data1' AND '$data2' AND serie = '$serie' ORDER BY f.data";
	$rsx = mysqli_query($db, $sqlx) or die(mysqli_error($db));
		while ($dadosx = mysqli_fetch_array($rsx)) {
			$ta = $dadosx['valor'] + $dadosx['iva'];
			$pdf->Cell(10, 6, $dadosx['id'], 1, 0, 'C');
			$pdf->Cell(40, 6, "FA#".$dadosx['serie']."/".$dadosx['n_doc'], 1, 0, 'L');
			$pdf->Cell(25, 6, number_format($dadosx['valor'], 2), 1, 0, 'R');
			$pdf->Cell(20, 6, number_format($dadosx['iva'], 2), 1, 0, 'R');
			$pdf->Cell(30, 6, number_format($ta, 2), 1, 0, 'R');
			$pdf->Cell(30, 6, $dadosx['data'], 1, 0, 'C');
			$pdf->Cell(35, 6, $dadosx['prazo'], 1, 1, 'C');

			// Nova página com cabeçalho quando necessário
			tcpdf_should_addpage_and_header($pdf, 260, $columns);
		}
		
}



// Observação final
$pdf->Ln(4);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');

// Output do PDF
$pdf->Output('Relatorio_faturas.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();