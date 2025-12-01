<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../../conexao/index.php';

$id_sc = $_GET['id_sc'];

// Template unificado TCPDF
include_once 'includes/tcpdf_template.php';

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

$sqll = "SELECT *, (SELECT nome FROM users as u WHERE u.id = e.user) as utiliza FROM saida_caixa as e WHERE id = '$id_sc'";
$rs1 = mysqli_query($db, $sqll);
$dados1 = mysqli_fetch_array($rs1);
$serie = $dados1['serie'];
$caixote = $dados1['caixa'];

$sqlk = "SELECT (SELECT nome FROM users as u WHERE u.id = p.usuario) as u FROM periodo as p WHERE idperiodo = '$caixote'";
$rsk = mysqli_query($db, $sqlk);
$dadosk = mysqli_fetch_array($rsk);
$homem = $dadosk['u'];

// Conteúdo da fatura

// Dados da Empresa a Faturar
// Criar novo documento PDF com cabeçalho padronizado e logo
$titulo = 'Saída de Caixa SC# ' . $serie . '/' . $id_sc;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $empresa, $titulo);
$pdf->AddPage();

// Tabela
$columns = [
    ['#', 20, 'L'],
    ['Descrição', 60, 'L'],
    ['Valor', 40, 'C'],
    ['Caixa', 40, 'L'],
    ['Utilizador', 30, 'L'],
];
tcpdf_table_header($pdf, $columns);

$pdf->SetFont('helvetica', '', 8);
list($fill, $rgb) = tcpdf_row_fill_toggle(0, false);
if ($fill) { $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]); } else { $pdf->SetFillColor(255,255,255); }
$pdf->Cell(20, 7, $dados1['id'], 1, 0, 'L', $fill);
$pdf->Cell(60, 7, 'SC#'.$dados1['serie'].'/'.$dados1['id'], 1, 0, 'L', $fill);
$pdf->Cell(40, 7, $dados1['valor'], 1, 0, 'C', $fill);
$pdf->Cell(40, 7, $homem, 1, 0, 'L', $fill);
$pdf->Cell(30, 7, $dados1['utiliza'], 1, 0, 'L', $fill);
$pdf->Ln();

// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)
$pdf->Cell(0, 7, 'Documento Processado por Computador/ iVone ERP/', 0, 1);
// Totais


// // Assinaturas
// $pdf->SetFont('helvetica', 'B', 12);
// $pdf->Cell(0, 20, 'Assinaturas', 0, 1);


// Output do PDF
$pdf->Output('Saida_caixa.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();