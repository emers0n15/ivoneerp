<?php
session_start();
include '../../conexao/index.php';

$sql1 = "SELECT * FROM empresa";
$rs1 = mysqli_query($db, $sql1);
$dados = mysqli_fetch_array($rs1);
$nome = $dados['nome'];
$nuit = $dados['nuit'];
$endereco = $dados['endereco'];
$provincia = $dados['provincia'];
$pais = $dados['pais'];
$contacto = $dados['contacto'];
$capital_social = $dados['capital_social'];
$email = $dados['email'];

// Template TCPDF unificado
require_once 'includes/tcpdf_template.php';

// Criar novo documento PDF com cabeçalho/rodapé unificados
$titulo = 'Fatura #' . $fatura['numero'];
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle($titulo);
$pdf->AddPage();

// Conteúdo da fatura
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Fatura', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Número da Fatura: ' . $fatura['numero'], 0, 1);
$pdf->Cell(0, 10, 'Data: ' . $fatura['data'], 0, 1);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(50, 10, 'Descrição', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantidade', 1, 0, 'C');
$pdf->Cell(40, 10, 'Preço Unitário', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total', 1, 1, 'C');

// Dados dos itens da fatura (exemplo)
$queryItens = "SELECT * FROM itens_fatura WHERE id_fatura = $idFatura";
$resultItens = $connection->query($queryItens);
while ($item = $resultItens->fetch_assoc()) {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(50, 10, $item['descricao'], 1, 0, 'L');
    $pdf->Cell(30, 10, $item['quantidade'], 1, 0, 'C');
    $pdf->Cell(40, 10, 'R$ ' . number_format($item['preco_unitario'], 2), 1, 0, 'R');
    $pdf->Cell(40, 10, 'R$ ' . number_format($item['total'], 2), 1, 1, 'R');
}

// Output do PDF
$pdf->Output('fatura.pdf', 'I');

// Fechar conexão e liberar recursos
$connection->close();