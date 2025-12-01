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

// Usar template unificado com cabeçalho/rodapé (inclui logo)
require_once 'includes/tcpdf_template.php';

// Dados da Empresa
$sql = "SELECT * FROM empresa LIMIT 1";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);

// Criar documento com título padronizado
$titulo = 'RELATÓRIO DE SAÍDAS DE STOCK - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Saídas de Stock');
$pdf->AddPage();

// Cabeçalho da tabela padronizado
$pdf->SetFont('helvetica', '', 9);
$columns = [ ['#', 12, 'C'], ['Descrição', 48, 'L'], ['Solicitante', 60, 'L'], ['Utilizador', 70, 'L'] ];
tcpdf_table_header($pdf, $columns);

// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT *, (SELECT nome FROM users as u WHERE u.id = e.user) as user  FROM `saida_stock` as e WHERE data BETWEEN '$data1' AND '$data2' AND serie = '$serie' OR user = '$utilizador'";
$rs3 = mysqli_query($db, $sql3);
rowIndex:
while ($dados3 = mysqli_fetch_array($rs3)) {
	$id = $dados3['id'];
    static $i = 0; list($fill, $rgb) = tcpdf_row_fill_toggle($i++);
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
    $pdf->Cell(12, 6, $dados3['id'], 1, 0, 'C', $fill);
    $pdf->Cell(48, 6, "SS#".$dados3['serie']."/".$dados3['id'], 1, 0, 'L', $fill);
    $pdf->Cell(60, 6, $dados3['solicitante'], 1, 0, 'L', $fill);
    $pdf->Cell(70, 6, $dados3['user'], 1, 1, 'L', $fill);

	$sql = "SELECT *, (SELECT nomeproduto FROM produto as g WHERE g.idproduto = e.artigo) as artigos FROM ss_artigos as e WHERE ss = '$id'";
	$rs = mysqli_query($db, $sql);
	if (mysqli_num_rows($rs) > 0) {
		// Cabeçalho da sub-tabela
		$pdf->SetFont('helvetica', '', 9);
		$columnsSub = [ ['Artigos', 130, 'L'], ['Quantidade', 60, 'C'] ];
		tcpdf_table_header($pdf, $columnsSub);
		while ($dadosx = mysqli_fetch_array($rs)) {
			$pdf->SetFont('helvetica', '', 8);
			static $j = 0; list($sfill, $srgb) = tcpdf_row_fill_toggle($j++);
			$pdf->SetFillColor($srgb[0], $srgb[1], $srgb[2]);
			$pdf->Cell(130, 6, $dadosx['artigos'], 1, 0, 'L', $sfill);
			$pdf->Cell(60, 6, $dadosx['qtd'], 1, 1, 'C', $sfill);
		}
	}

    tcpdf_should_addpage_and_header($pdf, 260, $columns);
}

// Observação
$pdf->Ln(4);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');

// Output do PDF
$pdf->Output('Relatorio_saida_stock.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();