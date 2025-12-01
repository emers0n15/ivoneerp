<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../conexao/index.php';

$id_nc = $_GET['id_nc'];
$sl = "SELECT SUM(total) as trs, SUM(iva) as ivbs FROM nc_artigos as f WHERE id_nota = '$id_nc'";
$re = mysqli_query($db, $sl);
$dar = mysqli_fetch_array($re);
$trs = $dar['trs'];
$ivbs = $dar['ivbs'];
function fetch_data($d, $id_n) {
    $output = '';
    
    // Use uma consulta preparada para evitar injeção de SQL
    $sql = "SELECT id, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = f.artigo) as artigo, qtd, preco, total, iva FROM nc_artigos as f WHERE id_nota = ? ";
    
    // Prepara a consulta
    $stmt = mysqli_prepare($d, $sql);
    
    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt) {
        // Associa o valor de $id_vd à consulta
        mysqli_stmt_bind_param($stmt, "s", $id_n);
        
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
$pdf->SetTitle('Nota de Crédito #' . $id_nc); // Defina o título do PDF

$pdf->AddPage();

// Dados da Empresa a Faturar (exemplo)
$sql = "SELECT * FROM empresa";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
$img = $dados['img'];

// Dados da Empresa Faturada (exemplo)
$sql1 = "SELECT * FROM clientes WHERE id = (SELECT cliente FROM nota_de_credito WHERE id = '$id_nc')";
$rs1 = mysqli_query($db, $sql1);
$dados1 = mysqli_fetch_array($rs1);

// Dados da Factura
$sql2 = "SELECT *, (SELECT data FROM factura WHERE factura.id = nota_de_credito.id_factura) as dt, (SELECT serie FROM factura WHERE factura.id = nota_de_credito.id_factura) as sr, (SELECT n_doc FROM factura WHERE factura.id = nota_de_credito.id_factura) as nd FROM nota_de_credito WHERE id = '$id_nc'";
$rs2 = mysqli_query($db, $sql2);
$dados2 = mysqli_fetch_array($rs2);
$v2 = $dados2['valor'];
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
// $htmlItens = '
// <img src="../img/'.$img.'" style="margin-top: 10px;width: 250%;">
// ';
// $pdf->writeHTML($htmlItens, true, false, true, false, '');
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
$pdf->SetX(145);
$htmlItens = '
 <table style="text-align: right;"><tr><td>À Facturar</td></tr><tr><td>'.$dados1['nome'].' '.$dados1['apelido'].'</td></tr><tr><td>'.$dados1['endereco'].'</td></tr><tr><td>'.$dados1['nuit'].'</td></tr></table>
 ';
$pdf->writeHTML($htmlItens, true, false, true, false, '');


$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Nota de Crédito #'.$dados2['serie'].'/'.$dados2['n_doc'], 0, 1);
// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Referência a Factura #', 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, 'Data de Emissão da Fatura', 'T', 0);

$pdf->SetX(120);
$pdf->Cell(0, 7, 'Data de Emissão', 'T', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(7);
$pdf->Cell(0, 7, $dados2['sr'].'/'.$dados2['nd'], 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, $dados2['dt'], 'T', 0);

$pdf->SetX(120);
$pdf->Cell(0, 7, $dados2['data'], 'T', 0);
$pdf->Ln(8);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Motivo de Emissão', 'T', 0);
$pdf->Ln(7);
$pdf->SetFont('helvetica', '', 8);
$htmlItens = '
 <table style="text-align: justify;"><tr><td>'.$dados2['motivo'].'</td></tr></table>
 ';
$pdf->writeHTML($htmlItens, true, false, true, false, '');

// Tabela de Itens da Fatura
$pdf->SetFont('helvetica', '', 8);

$content = '';
$content .='
<table cellspacing="0" cellpadding="4">
	<tr>
		<th style="width: 10%;font-weight: bold;">#</th>
        <th style="width: 40%;font-weight: bold;">Descrição</th>
        <th style="width: 10%;font-weight: bold;text-align:center;">Quantidade</th>
        <th style="width: 15%;font-weight: bold;text-align:center;">Preço Unitário</th>
        <th style="width: 10%;font-weight: bold;text-align:center;">IVA(5%)</th>
        <th style="width: 15%;font-weight: bold;text-align:center;">Total</th>
	</tr>
';
$content .= fetch_data($db,$id_nc);
$content .='</table>';
$pdf->writeHTML($content);



// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)
$pdf->Cell(0, 7, 'Documento Processado por Computador/ iVone ERP/', 0, 1);
// Totais

$pdf->Cell(145, 5, 'Mercadoria/Serviços', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($trs, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'IVA', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($ivbs, 2). ' Mt', 1, 1, 'R');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(145, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($trs+$ivbs, 2). ' Mt', 1, 1, 'R');
$pdf->Ln(6);
// Dados Bancários
$pdf->SetFont('helvetica', '', 8);

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
$pdf->Output('nota_de_credito.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();