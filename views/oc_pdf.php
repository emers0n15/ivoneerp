<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../conexao/index.php';

$id_oc = $_GET['id_oc'];

function fetch_data($d, $id_o) {
    $output = '';
    
    // Use uma consulta preparada para evitar injeção de SQL
    $sql = "SELECT id, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = f.artigo) as artigo, qtd, preco, total,iva FROM ordem_compra_artigos as f WHERE ordem_compra = ? ";
    
    // Prepara a consulta
    $stmt = mysqli_prepare($d, $sql);
    
    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt) {
        // Associa o valor de $id_vd à consulta
        mysqli_stmt_bind_param($stmt, "s", $id_o);
        
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
                    <td style="text-align: center;">'.number_format($dados3['preco'], 2, ".", ",").'</td>
                    <td style="text-align: center;">'.number_format($dados3['iva'], 2, ".", ",").'</td>
                    <td style="text-align: center;">'.number_format($dados3['total']+$dados3['iva'], 2, ".", ",").'</td>
                </tr>
            ';
        }
        
        // Fecha a consulta preparada
        mysqli_stmt_close($stmt);
    }
    
    return $output;
}

//incluir a biblioteca
include 'admin/biblioteca/tcpdf.php';

// Criar novo documento PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Ordem de Compra #' . $id_oc); // Defina o título do PDF

$pdf->AddPage();

// Dados da Empresa a Faturar (exemplo)
$sql = "SELECT * FROM empresa";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
$img = $dados['img'];

// Dados da Empresa Faturada (exemplo)
$sql1 = "SELECT * FROM fornecedor WHERE id = (SELECT fornecedor FROM ordem_compra WHERE id = '$id_oc')";
$rs1 = mysqli_query($db, $sql1);
$dados1 = mysqli_fetch_array($rs1);

// Dados da Factura
$sql2 = "SELECT * FROM ordem_compra WHERE id = '$id_oc'";
$rs2 = mysqli_query($db, $sql2);
$dados2 = mysqli_fetch_array($rs2);
$v = $dados2['valor'];
$n_doc = $dados2['n_doc'];
$iva = $dados2['iva'];
$desc = $dados2['desconto'];
$tr = $v + $desc;
$v2 = $v + $iva;

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
$pdf->Cell(0, 7, 'Fornecedor:', 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['nome'], 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['endereco'], 0, 1);
$pdf->SetX(140);
$pdf->Cell(0, 7, $dados1['nuit'], 0, 1);
$pdf->Ln(7);


$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Ordem de Compra #'.$dados2['serie'].'/'.$n_doc, 0, 1);
// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Método de Pagamento', 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, 'Data de Emissão', 'T', 0);
$pdf->SetX(120);
$pdf->Cell(0, 7, 'Prazo de Pagamento', 'T', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(7);
$pdf->Cell(0, 7, $dados2['modo'], 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, $dados2['data'], 'T', 0);
$pdf->SetX(120);
$pdf->Cell(0, 7, $dados2['prazo'], 'T', 0);



// Tabela de Itens da Fatura
$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(8);
$content = '';
$content .='
<table cellspacing="0" cellpadding="4">
	<tr>
		<th style="width: 10%;font-weight: bold;">#</th>
		<th style="width: 40%;font-weight: bold;">Descrição</th>
		<th style="width: 10%;font-weight: bold;text-align:center;">Quantidade</th>
		<th style="width: 15%;font-weight: bold;text-align:center;">Preço Unitário</th>
        <th style="width: 10%;font-weight: bold;text-align:center;">IVA</th>
		<th style="width: 15%;font-weight: bold;text-align:center;">Total</th>
	</tr>
';
$content .= fetch_data($db,$id_oc);
$content .='</table>';
$pdf->writeHTML($content);



// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)
$pdf->Cell(0, 7, 'Documento Processado por Computador/ iVone ERP/', 0, 1);
// Totais

$pdf->Cell(145, 5, 'Mercadoria/Serviços', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($tr, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'Desconto Comercial', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($desc, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'IVA', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($iva, 2). ' Mt', 1, 1, 'R');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(145, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($v2, 2). ' Mt', 1, 1, 'R');
$pdf->Ln(6);
// Dados Bancários
$pdf->SetFont('helvetica', '', 8);


$pdf->SetX(35);
$pdf->Cell(0, 12, 'Recepcionista', 0, 0);
$pdf->SetX(160);
$pdf->Cell(0, 12, 'Fornecedor', 0, 1);
$pdf->SetX(20);
$pdf->Cell(0, 6, '_____________________________', 0, 0); // Assinatura do recepcionista
$pdf->SetX(140);
$pdf->Cell(0, 6, '_____________________________', 0, 1); // Assinatura do cliente

// Output do PDF
$pdf->Output('ordem_compra.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();