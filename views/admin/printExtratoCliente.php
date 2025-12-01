<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../../conexao/index.php';

$cliente = $_GET['cliente'];
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

// Obter nome do cliente antes de montar o título
$sqlll = "SELECT CONCAT(nome,' ',apelido) as cl FROM clientes WHERE id = '$cliente'";
$rsl = mysqli_query($db, $sqlll);
$dadosl = mysqli_fetch_array($rsl);
$nome = $dadosl['cl'];

// Criar novo documento PDF com header/rodapé unificados
$titulo = 'EXTRATO DO CLIENTE - ' . $nome . ' - ' . $data1 . ' / ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Extrato de Cliente');
$pdf->AddPage();

// Conteúdo da fatura

// Cabeçalho e dados da empresa são geridos pelo template

// Cabeçalho da tabela do extrato
$pdf->SetFont('helvetica', '', 9);
$columns = [ ['Descrição', 75], ['IVA', 25], ['Débito', 30], ['Crédito', 30], ['Saldo', 30] ];
tcpdf_table_header($pdf, $columns);

// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT SUM(iva) as ivas,SUM(debito) as debitos, SUM(credito) as creditos, SUM(saldo) as saldos FROM `transacoes` as e WHERE data < '$data1' AND serie = '$serie' AND cliente = '$cliente' AND (doc = 'Factura' OR doc = 'Nota de Credito' OR doc = 'Nota de Debito' OR doc = 'Recibo')";
$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
if (mysqli_num_rows($rs3) > 0) {
	$dados3 = mysqli_fetch_array($rs3);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell(75, 6, 'Saldo Anterior', 1, 0, 'L');
    $pdf->Cell(25, 6, $dados3['ivas'], 1, 0, 'R');
    $pdf->Cell(30, 6, $dados3['debitos'], 1, 0, 'R');
    $pdf->Cell(30, 6, $dados3['creditos'], 1, 0, 'R');
    $pdf->Cell(30, 6, $dados3['saldos'], 1, 1, 'R');

	$pdf->SetFont('helvetica', '', 8);
	$sqlx = "SELECT * FROM `transacoes` as e WHERE data BETWEEN '$data1' AND '$data2' AND serie = '$serie' AND cliente = '$cliente' AND (doc = 'Factura' OR doc = 'Nota de Credito' OR doc = 'Nota de Debito' OR doc = 'Recibo')";
	$rsx = mysqli_query($db, $sqlx) or die(mysqli_error($db));

        $rowIndex = 0;
        while ($dadosx = mysqli_fetch_array($rsx)) {
            $pdf->SetFont('helvetica', '', 8);
            list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex++);
            $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
            $pdf->Cell(75, 6, $dadosx['doc']."#".$dadosx['serie'] . "/" . $dadosx['n_doc'], 1, 0, 'L', $fill);
            $pdf->Cell(25, 6, $dadosx['iva'], 1, 0, 'R', $fill);
            $pdf->Cell(30, 6, $dadosx['debito'], 1, 0, 'R', $fill);
            $pdf->Cell(30, 6, $dadosx['credito'], 1, 0, 'R', $fill);
            $pdf->Cell(30, 6, $dadosx['saldo'], 1, 1, 'R', $fill);

            tcpdf_should_addpage_and_header($pdf, 260, $columns);
		}
		$sql5 = "SELECT SUM(iva) as ivas,SUM(debito) as debitos, SUM(credito) as creditos, SUM(saldo) as saldos FROM `transacoes` as e WHERE data BETWEEN '$data1' AND '$data2' AND serie = '$serie' AND cliente = '$cliente' AND (doc = 'Factura' OR doc = 'Nota de Credito' OR doc = 'Nota de Debito' OR doc = 'Recibo')";
		$rs5 = mysqli_query($db, $sql5) or die(mysqli_error($db));
		$dados5 = mysqli_fetch_array($rs5);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(75, 6, 'Saldo Atual', 1, 0, 'L');
        $pdf->Cell(25, 6, $dados5['ivas'], 1, 0, 'R');
        $pdf->Cell(30, 6, $dados5['debitos'], 1, 0, 'R');
        $pdf->Cell(30, 6, $dados5['creditos'], 1, 0, 'R');
        $pdf->Cell(30, 6, $dados5['saldos'], 1, 1, 'R');
}else{
	echo "Ocorreu um erro ao popular os a";
}

// Observação
$pdf->Ln(4);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento processado por computador / iVone ERP', 0, 1, 'L');

// Totais



// Output do PDF

$pdf->Output('Extrato_cliente.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();