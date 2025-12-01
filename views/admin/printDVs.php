<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../../conexao/index.php';


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
$titulo = 'RELATÓRIO DE DEVOLUÇÕES - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Devoluções');
$pdf->AddPage();

// Conteúdo da fatura

// Cabeçalho da tabela padronizado
$pdf->SetFont('helvetica', '', 9);
$columns = [ ['#', 12, 'C'], ['Descrição', 40, 'L'], ['Ref. a VD', 35, 'L'], ['Valor', 23, 'R'], ['Modo de Pagamento', 45, 'C'], ['Data de Emissão', 35, 'C'] ];
tcpdf_table_header($pdf, $columns);

// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT DISTINCT(idcliente) as cl FROM `devolucao` as e WHERE data BETWEEN '$data1' AND '$data2'";
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

	$sqlx = "SELECT *, (SELECT CONCAT('VD#', p.serie,'/', p.n_doc) FROM pedido as p WHERE p.idpedido = f.idpedido) as vd FROM devolucao as f WHERE idcliente = '$cl' AND f.data BETWEEN '$data1' AND '$data2' AND serie = '$serie' ORDER BY f.data";
	$rsx = mysqli_query($db, $sqlx) or die(mysqli_error($db));
		        $rowIndex = 0;
        while ($dadosx = mysqli_fetch_array($rsx)) {
            list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex++);
            $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
            $pdf->Cell(12, 6, $dadosx['id'], 1, 0, 'C', $fill);
            $pdf->Cell(40, 6, "DV#".$dadosx['serie']."/".$dadosx['n_doc'], 1, 0, 'L', $fill);
            $pdf->Cell(35, 6, $dadosx['vd'], 1, 0, 'L', $fill);
            $pdf->Cell(23, 6, number_format($dadosx['valor'], 2), 1, 0, 'R', $fill);
            $pdf->Cell(45, 6, $dadosx['modo'], 1, 0, 'C', $fill);
            $pdf->Cell(35, 6, $dadosx['data'], 1, 1, 'C', $fill);

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
$pdf->Output('Relatorio_devolucoes.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();