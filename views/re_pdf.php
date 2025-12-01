<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../conexao/index.php';

$id_re = $_GET['id_re'];

function fetch_data($d, $id_r) {
    $output = '';
    
    // Use uma consulta preparada para evitar injeção de SQL
    $sql = "SELECT id, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = f.artigo) as artigo, qtd FROM re_artigos as f WHERE re = ? ";
    
    // Prepara a consulta
    $stmt = mysqli_prepare($d, $sql);
    
    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt) {
        // Associa o valor de $id_vd à consulta
        mysqli_stmt_bind_param($stmt, "s", $id_r);
        
        // Executa a consulta
        mysqli_stmt_execute($stmt);
        
        // Obtém os resultados da consulta
        $result = mysqli_stmt_get_result($stmt);
        
        // Itera pelos resultados
        while ($dados3 = mysqli_fetch_array($result)) {
            $output .= '
                <tr>
                    <td>'.$dados3['id'].'</td>
                    <td>'.$dados3['artigo'].'</td>
                    <td style="text-align: center;">'.$dados3['qtd'].'</td>
                </tr>
            ';
        }
        
        // Fecha a consulta preparada
        mysqli_stmt_close($stmt);
    }
    
    return $output;
}

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
$sql1 = "SELECT * FROM sector WHERE id = (SELECT sector FROM requisicao_externa WHERE id = '$id_re')";
$rs1 = mysqli_query($db, $sql1);
$dados1 = mysqli_fetch_array($rs1);

// Dados da Empresa Faturada (exemplo)
$sql4 = "SELECT * FROM fornecedor WHERE id = (SELECT fornecedor FROM requisicao_externa WHERE id = '$id_re')";
$rs4 = mysqli_query($db, $sql4);
$dados4 = mysqli_fetch_array($rs4);

// Dados da Factura
$sql2 = "SELECT * FROM requisicao_externa WHERE id = '$id_re'";
$rs2 = mysqli_query($db, $sql2);
$dados2 = mysqli_fetch_array($rs2);
$n_doc = $dados2['n_doc'];

// Criar documento com cabeçalho padronizado e logotipo
$titulo = 'REQUISIÇÃO EXTERNA #'.$dados2['serie'].'/'.$n_doc;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $empresa, $titulo);
$pdf->AddPage();

// Metadados e bloco informativo
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Requisição Externa');

// Dados da Empresa a Faturar
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 7, $dados['endereco'], 0, 1);
$pdf->Cell(0, 7, 'Nuit: ' . $dados['nuit'], 0, 1);
$pdf->Cell(0, 7, 'Contacto: ' . $dados['contacto'], 0, 1);
$pdf->Cell(0, 7, 'E-mail: ' . $dados['email'], 0, 1);
$pdf->Cell(0, 7, $dados['pais'] .' - '. $dados['provincia'], 0, 1);

// Dados da Empresa Faturada
$pdf->SetX(140);
$pdf->Cell(0, 7, 'Fornecedor:', 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados4['nome'], 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, 'Requisitante:', 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['nome'], 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, 'Responsável: '.$dados1['responsavel'], 0, 1);
$pdf->Ln(7);
$pdf->SetX(140);
$pdf->Cell(0, 7, 'A Requisitar:', 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados2['solicitante'], 0, 1);

// Tabela (cabeçalho azul + zebra)
$columns = [
    ['#', 20, 'L'],
    ['Descrição', 130, 'L'],
    ['Quantidade', 30, 'C'],
];
tcpdf_table_header($pdf, $columns);

$stmt = mysqli_prepare($db, "SELECT id, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = f.artigo) as artigo, qtd FROM re_artigos as f WHERE re = ? ");
mysqli_stmt_bind_param($stmt, "s", $id_re);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rowIndex = 0;
$pdf->SetFont('helvetica', '', 8);
while ($dados3 = mysqli_fetch_assoc($result)) {
    tcpdf_should_addpage_and_header($pdf, 260, $columns);
    list($fill, $rgb) = tcpdf_row_fill_toggle($rowIndex, false);
    if ($fill) { $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]); } else { $pdf->SetFillColor(255,255,255); }
    $pdf->Cell(20, 7, $dados3['id'], 1, 0, 'L', $fill);
    $pdf->Cell(130, 7, $dados3['artigo'], 1, 0, 'L', $fill);
    $pdf->Cell(30, 7, $dados3['qtd'], 1, 0, 'C', $fill);
    $pdf->Ln();
    $rowIndex++;
}
mysqli_stmt_close($stmt);

// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)
$pdf->Cell(0, 7, 'Documento Processado por Computador/ iVone ERP/', 0, 1);

// Totais

// Assinaturas
$pdf->Ln(25);
$pdf->SetX(35);
$pdf->Cell(0, 12, 'Emitente', 0, 0);
$pdf->SetX(165);
$pdf->Cell(0, 12, 'Solicitante', 0, 1);
$pdf->SetX(20);
$pdf->Cell(0, 6, '_____________________________', 0, 0); // Assinatura do recepcionista
$pdf->SetX(145);
$pdf->Cell(0, 6, '_____________________________', 0, 1); // Assinatura do cliente

// Output do PDF
$pdf->Output('Requisicao_externa.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();