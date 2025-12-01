<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../conexao/index.php';

$id_rc = $_GET['id_rc'];

//incluir a biblioteca
include 'admin/biblioteca/tcpdf.php';

// Criar novo documento PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Recibo #' . $id_rc); // Defina o título do PDF

$pdf->AddPage();

// Dados da Empresa a Faturar (exemplo)
$sql = "SELECT * FROM empresa";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
$img = $dados['img'];

// Dados da Empresa Faturada (exemplo)
$sql1 = "SELECT * FROM clientes WHERE id = (SELECT cliente FROM recibo WHERE id = '$id_rc')";
$rs1 = mysqli_query($db, $sql1);
$dados1 = mysqli_fetch_array($rs1);

// Dados da Factura
$sql2 = "SELECT * FROM recibo WHERE id = '$id_rc'";
$rs2 = mysqli_query($db, $sql2);
$dados2 = mysqli_fetch_array($rs2);
$v = $dados2['valor'];
// Conteúdo da fatura


// Dados da Empresa a Faturar
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln(7);
// Definir a posição e o tamanho da imagem
$imageX = 160;   // Posição X da imagem
$imageY = 15;   // Posição Y da imagem
$imageWidth = 35;  // Largura da imagem
$imageHeight = 0;   // Altura da imagem (0 para manter a proporção)

// Inserir a imagem
$pdf->Image('../img/'.$img.'', $imageX, $imageY, $imageWidth, $imageHeight);
$pdf->Cell(0, 7, $dados['nome'], 0, 1);

// // // Incluir o logotipo à direita
// $logoWidth = 30; // Largura do logotipo em milímetros
// $logoHeight = 0; // Altura do logotipo em milímetros (0 para manter a proporção)
// $logoX = $pdf->getPageWidth(100) - $pdf->getRightMargin(0) - $logoWidth; // Posição X calculada para alinhar à direita
// $logoY = $pdf->GetY(0); // Posição Y atual
// $pdf->Image('../img/iCone.png', $logoX, $logoY, $logoWidth, $logoHeight);

// // // Ajustar a posição Y para a próxima linha
// $pdf->SetY($pdf->GetY() + $pdf->getCellHeight(10));





// Restante dos dados da Empresa a Faturar
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 7, $dados['endereco'], 0, 1);
$pdf->Cell(0, 7, 'Nuit: ' . $dados['nuit'], 0, 1);
$pdf->Cell(0, 7, 'Contacto: ' . $dados['contacto'], 0, 1);
$pdf->Cell(0, 7, 'E-mail: ' . $dados['email'], 0, 1);
$pdf->Cell(0, 7, $dados['pais'] .' - '. $dados['provincia'], 0, 1);

// // Dados da Empresa Faturada
$pdf->SetX(140);
$pdf->Cell(0, 7, 'Cliente:', 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['nome'].' '.$dados1['apelido'], 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['endereco'], 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['nuit'], 0, 1);


$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Recibo #'.$dados2['serie'].'/'.$id_rc, 0, 1);
// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Método de Pagamento', 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, 'Data de Emissão', 'T', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(7);
$pdf->Cell(0, 7, $dados2['modo'], 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, $dados2['data'], 'T', 0);

// Tabela de Itens da Fatura
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Ln(8);
$pdf->Cell(0, 7, '#', 'T', 0);
$pdf->SetX(30);
$pdf->Cell(0, 7, 'Referência a Factura #', 'T', 0);
$pdf->SetX(120);
$pdf->Cell(0, 7, 'Valor', 'T', 0);
$pdf->SetX(150);
$pdf->Cell(0, 7, 'IVA (5%)', 'T', 0);
$pdf->SetX(180);
$pdf->Cell(0, 7, 'Total', 'T', 0);
$pdf->Ln(8);
// Loop while para adicionar linhas de itens
$pdf->SetFont('helvetica', '', 8);
$sql3 = "SELECT * FROM rc_fact as f WHERE id_rc = '$id_rc'";
$rs3 = mysqli_query($db, $sql3);
while ($dados3 = mysqli_fetch_array($rs3)) {
	$tt = $dados3['valor'] + $dados3['iva'];
    $pdf->Cell(0, 7, $dados3['id'], 0, 0);
	$pdf->SetX(30);
	$pdf->Cell(0, 7, 'FA#'.$dados2['serie'].'/'.$dados3['factura'], 0, 0);
	$pdf->SetX(120);
	$pdf->Cell(0, 7, number_format($dados3['valor'], 2), 0, 0);
	$pdf->SetX(150);
	$pdf->Cell(0, 7, number_format($dados3['iva'], 2), 0, 0);
	$pdf->SetX(180);
	$pdf->Cell(0, 7, number_format($tt, 2), 0, 0);
	$pdf->Ln(6);
}



// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)
$pdf->Cell(0, 7, 'Documento Processado por Computador/ iVone ERP/', 0, 1);
// Totais

$sql4 = "SELECT SUM(valor) as tot, SUM(iva) as iv FROM rc_fact as f WHERE id_rc = '$id_rc'";
$rs4 = mysqli_query($db, $sql4);
$dados4 = mysqli_fetch_array($rs4);
$tot = $dados4['tot'];
$iv = $dados4['iv'];
$tott = $tot + $iv;

$pdf->Cell(145, 5, 'Mercadoria/Serviços', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($tot, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'IVA', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($iv, 2). ' Mt', 1, 1, 'R');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(145, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($tott, 2). ' Mt', 1, 1, 'R');
$pdf->Ln(6);
// Dados Bancários
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 10, 'Dados Bancários', 0, 1);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 5, 'Banco: '. $dados['banco'], 0, 1);
$pdf->Cell(0, 5, 'Conta: '. $dados['conta'], 0, 1);
$pdf->Cell(0, 5, 'NIB: '. $dados['nib'], 0, 1);

// // Assinaturas
// $pdf->SetFont('helvetica', 'B', 12);
// $pdf->Cell(0, 20, 'Assinaturas', 0, 1);
$pdf->SetX(35);
$pdf->Cell(0, 12, 'Recepcionista', 0, 0);
$pdf->SetX(165);
$pdf->Cell(0, 12, 'Cliente', 0, 1);
$pdf->SetX(20);
$pdf->Cell(0, 6, '_____________________________', 0, 0); // Assinatura do recepcionista
$pdf->SetX(145);
$pdf->Cell(0, 6, '_____________________________', 0, 1); // Assinatura do cliente

// Output do PDF
$pdf->Output('Recibo.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();