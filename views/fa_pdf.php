<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../");
}
include_once '../conexao/index.php';

$id_factura = $_GET['id_fatura'];
// Dados da Factura
$sql2 = "SELECT * FROM factura WHERE id = '$id_factura'";
$rs2 = mysqli_query($db, $sql2);
$dados2 = mysqli_fetch_array($rs2);
$n_doc = $dados2['n_doc'];
$v = $dados2['valor'];
$desc = $dados2['disconto'];
$v1 = $v + $desc;
$iva = $dados2['iva'];
$v2 = $v + $iva;

// Template unificado
include_once 'admin/includes/tcpdf_template.php';

// Dados da Empresa
$sql = "SELECT * FROM empresa";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
$empresa = [
    'nome' => $dados['nome'] ?? '',
    'endereco' => $dados['endereco'] ?? '',
    'nuit' => $dados['nuit'] ?? '',
    'contacto' => $dados['contacto'] ?? '',
    'email' => $dados['email'] ?? '',
    'pais' => $dados['pais'] ?? '',
    'provincia' => $dados['provincia'] ?? '',
    'img' => $dados['img'] ?? ''
];

// Dados da Empresa Faturada (exemplo)
$sql1 = "SELECT * FROM clientes WHERE id = (SELECT cliente FROM factura WHERE id = '$id_factura')";
$rs1 = mysqli_query($db, $sql1);
$dados1 = mysqli_fetch_array($rs1);

// Criar documento com cabeçalho padronizado e logotipo
$titulo = 'FACTURA #'.$dados2['serie'].'/'.$n_doc;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $empresa, $titulo);
$pdf->AddPage();

// Metadados
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Factura');

// Dados da Empresa a Faturar
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 7, $dados['endereco'], 0, 1);
$pdf->Cell(0, 7, 'Nuit: ' . $dados['nuit'], 0, 1);
$pdf->Cell(0, 7, 'Contacto: ' . $dados['contacto'], 0, 1);
$pdf->Cell(0, 7, 'E-mail: ' . $dados['email'], 0, 1);
$pdf->Cell(0, 7, $dados['pais'] .' - '. $dados['provincia'], 0, 1);

// Dados da Empresa Faturada
$pdf->SetX(140);
$pdf->Cell(0, 7, 'Cliente:', 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['nome'].' '.$dados1['apelido'], 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['endereco'], 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['nuit'], 0, 1);
$pdf->Ln(7);
if ($dados2['utente'] != NULL) {
    $pdf->SetX(140);
    $pdf->Cell(0, 7, 'A Facturar:', 0, 1);
    $pdf->SetX(140);
    $pdf->Cell(0, 7, $dados2['utente'], 0, 1);
}

// Informações de Pagamento
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Método de Pagamento', 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, 'Condições de Pagamento', 'T', 0);
$pdf->SetX(120);
$pdf->Cell(0, 7, 'Data de Emissão', 'T', 0);
$pdf->SetX(160);
$pdf->Cell(0, 7, 'Prazo de Pagamento', 'T', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(7);
$pdf->Cell(0, 7, $dados2['metodo'], 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, $dados2['condicoes'], 'T', 0);
$pdf->SetX(120);
$pdf->Cell(0, 7, $dados2['data'], 'T', 0);
$pdf->SetX(160);
$pdf->Cell(0, 7, $dados2['prazo'], 'T', 0);

// Códigos de Autorização
$pdf->Ln(8);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Número da Apolice', 'B', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, 'Código de Autorização 1', 'B', 0);
$pdf->SetX(120);
$pdf->Cell(0, 7, 'Código de Autorização 2', 'B', 0);
$pdf->SetX(160);
$pdf->Cell(0, 7, 'Código de Autorização 3', 'B', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(7);
$pdf->Cell(0, 7, $dados2['apolice'], 'B', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, $dados2['codigo1'], 'B', 0);
$pdf->SetX(120);
$pdf->Cell(0, 7, $dados2['codigo2'], 'B', 0);
$pdf->SetX(160);
$pdf->Cell(0, 7, $dados2['codigo3'], 'B', 0);

// Tabela de Itens (cabeçalho azul + zebra)
$columns = [
    ['#', 15, 'L'],
    ['Descrição', 85, 'L'],
    ['Qtd', 20, 'C'],
    ['Preço Unitário', 25, 'C'],
    ['IVA', 15, 'C'],
    ['Total', 30, 'C'],
];
tcpdf_table_header($pdf, $columns);

$stmt = mysqli_prepare($db, "SELECT id, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = f.artigo) as artigo, qtd, preco, iva, total FROM fa_artigos_fact as f WHERE factura = ? ");
mysqli_stmt_bind_param($stmt, "s", $id_factura);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rowIndex = 0;
$pdf->SetFont('helvetica', '', 8);
while ($dados3 = mysqli_fetch_assoc($result)) {
    $t = $dados3['total'] + $dados3['iva'];
    tcpdf_should_addpage_and_header($pdf, 260, $columns);
    list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex, false);
    if ($fill) { $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]); } else { $pdf->SetFillColor(255,255,255); }
    $pdf->Cell(15, 7, $dados3['id'], 1, 0, 'L', $fill);
    $pdf->Cell(85, 7, $dados3['artigo'], 1, 0, 'L', $fill);
    $pdf->Cell(20, 7, $dados3['qtd'], 1, 0, 'C', $fill);
    $pdf->Cell(25, 7, number_format($dados3['preco'], 2, ',', '.'), 1, 0, 'C', $fill);
    $pdf->Cell(15, 7, number_format($dados3['iva'], 2, ',', '.'), 1, 0, 'C', $fill);
    $pdf->Cell(30, 7, number_format($t, 2, ',', '.'), 1, 0, 'C', $fill);
    $pdf->Ln();
    $rowIndex++;
}
mysqli_stmt_close($stmt);

// Totais
$pdf->Ln(4);
$pdf->Cell(145, 5, 'Mercadoria/Serviços', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($v1, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'Desconto Comercial', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($desc, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'IVA', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($iva, 2). ' Mt', 1, 1, 'R');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(145, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($v2, 2). ' Mt', 1, 1, 'R');
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
$pdf->Output('fatura.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();