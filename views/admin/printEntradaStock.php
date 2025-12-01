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

// Template TCPDF unificado
require_once 'includes/tcpdf_template.php';

// Dados da Empresa
$sql = "SELECT * FROM empresa LIMIT 1";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);

// Criar novo documento PDF com header/rodapé unificados
$titulo = 'RELATÓRIO DE ENTRADAS DE STOCK - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Entradas de Stock');
$pdf->AddPage();

// Cabeçalho e dados da empresa são geridos pelo template

// Cabeçalho da tabela principal
$pdf->SetFont('helvetica', '', 9);
$columns = [
    ['#', 12], ['Descrição', 48], ['Série', 20], ['Data', 35], ['Utilizador', 55]
];
// Espaço extra para garantir afastamento do título/cabeçalho
$pdf->Ln(2);
tcpdf_table_header($pdf, $columns);

// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT *, (SELECT descricao FROM grupo_artigos as g WHERE g.id = e.grupo) as grupo, (SELECT descricao FROM familia_artigos as f WHERE f.id = e.familia) as familia, (SELECT nome FROM users as u WHERE u.id = e.user) as user  FROM `entrada_stock` as e WHERE data BETWEEN '$data1' AND '$data2' AND serie = '$serie' OR user = '$utilizador'";
$rs3 = mysqli_query($db, $sql3);
$rowIndex = 0;
while ($dados3 = mysqli_fetch_array($rs3)) {
	$id = $dados3['id'];
    list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex++);
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
    $pdf->Cell(12, 6, $dados3['id'], 1, 0, 'C', $fill);
	$pdf->Cell(48, 6, "ES#".$dados3['serie']."/".$dados3['id'], 1, 0, 'L', $fill);
	$pdf->Cell(20, 6, $dados3['serie'], 1, 0, 'C', $fill);
	$pdf->Cell(35, 6, $dados3['data'], 1, 0, 'C', $fill);
	$pdf->Cell(55, 6, $dados3['user'], 1, 1, 'L', $fill);

    tcpdf_should_addpage_and_header($pdf, 260, $columns);

	$sql = "SELECT *, (SELECT nomeproduto FROM produto as g WHERE g.idproduto = e.artigo) as artigos, (qtd *(SELECT preco FROM produto as g WHERE g.idproduto = e.artigo)) as valor FROM es_artigos as e WHERE es = '$id'";
	$rs = mysqli_query($db, $sql);
	if (mysqli_num_rows($rs) > 0) {
        // Cabeçalho da sub-tabela de artigos
        $pdf->SetFont('helvetica', '', 9);
        $columnsSub = [ ['Artigos', 110], ['Quantidade', 40], ['Valor Total', 40] ];
        tcpdf_table_header($pdf, $columnsSub);
        $subRowIndex = 0;
        while ($dadosx = mysqli_fetch_array($rs)) {
            $pdf->SetFont('helvetica', '', 8);
            list($sfill, $srgb) = tcpdf_row_fill_toggle($subRowIndex++);
            $pdf->SetFillColor($srgb[0], $srgb[1], $srgb[2]);
            $pdf->Cell(110, 6, $dadosx['artigos'], 1, 0, 'L', $sfill);
            $pdf->Cell(40, 6, $dadosx['qtd'], 1, 0, 'C', $sfill);
            $pdf->Cell(40, 6, number_format($dadosx['valor'],2), 1, 1, 'R', $sfill);

            tcpdf_should_addpage_and_header($pdf, 260, $columnsSub);
        }
    }
}

// Observação
$pdf->Ln(4);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');

// Totais



// Output do PDF
$pdf->Output('Relatorio_entrada_stock.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();