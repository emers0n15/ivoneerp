<?php 
session_start();
error_reporting(E_ALL);
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../conexao/index.php';

$id_vd = $_GET['id_vd'];

// Dados da Factura
$sql2 = "SELECT * FROM pedido WHERE idpedido = '$id_vd'";
$rs2 = mysqli_query($db, $sql2);
$dados2 = mysqli_fetch_array($rs2);
$n_doc = $dados2['n_doc'];
$v = $dados2['pagamentopedido'];
$iva = $dados2['iva'];
$desc = $dados2['disconto'];
$v1 = $v + $desc;
$tot = $v + $iva;

function fetch_data($d, $id_v) {
    $output = '';
    
    // Use uma consulta preparada para evitar injeção de SQL
    $sql = "SELECT *,(SELECT nomeproduto FROM produto as p WHERE p.idproduto = e.produtoentrega) as n FROM entrega as e WHERE pedidoentrega = ?";
    
    // Prepara a consulta
    $stmt = mysqli_prepare($d, $sql);
    
    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt) {
        // Associa o valor de $id_vd à consulta
        mysqli_stmt_bind_param($stmt, "s", $id_v);
        
        // Executa a consulta
        mysqli_stmt_execute($stmt);
        
        // Obtém os resultados da consulta
        $result = mysqli_stmt_get_result($stmt);
        
        // Itera pelos resultados
        while ($dados3 = mysqli_fetch_array($result)) {
            $t = $dados3['totalentrega'];
            $iv = $dados3['iva'];
            $cal = $t + $iv;
            $output .= '
                <tr style="border-bottom: 1px solid #666">
                    <td>'.$dados3['identrega'].'</td>
                    <td>'.$dados3['n'].'</td>
                    <td style="text-align: center;">'.$dados3['qtdentrega'].'</td>
                    <td style="text-align: center;">'.number_format($dados3['precoentrega'], 2, ".", ",").'</td>
                    <td style="text-align: center;">'.number_format($dados3['iva'], 2, ".", ",").'</td>
                    <td style="text-align: center;">'.number_format($cal, 2, ".", ",").'</td>
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
$pdf->SetTitle('Venda a Dinheiro #' . $n_doc); // Defina o título do PDF

$pdf->AddPage();

// Dados da Empresa a Faturar (exemplo)
$sql = "SELECT * FROM empresa";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
$img = $dados['img'];

// Dados da Empresa Faturada (exemplo)
$sql1 = "SELECT * FROM clientes WHERE id = (SELECT clientepedido FROM pedido WHERE idpedido = '$id_vd')";
$rs1 = mysqli_query($db, $sql1);
$dados1 = mysqli_fetch_array($rs1);


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
$pdf->Cell(0, 10, 'Venda a Dinheiro #'.$dados2['serie'].'/'.$n_doc, 0, 1);
// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Método de Pagamento', 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, 'Data de Emissão', 'T', 0);

$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(7);
$pdf->Cell(0, 7, $dados2['modo'], 'TB', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, $dados2['data'], 'TB', 0);

$pdf->Ln(8);
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
$content .= fetch_data($db,$id_vd);
$content .='</table>';
$pdf->writeHTML($content);
// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)
$pdf->Cell(0, 7, 'Documento Processado por Computador/ iVone ERP/', 0, 1);
// Totais
$pdf->Ln(40);
$pdf->Cell(145, 5, 'Mercadoria/Serviços', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($v1, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'Desconto Comercial', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($desc, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'IVA (5%)', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($iva, 2). ' Mt', 1, 1, 'R');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(145, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($tot, 2). ' Mt', 1, 1, 'R');
$pdf->Ln(6);

// // Assinaturas
$pdf->SetFont('helvetica', '', 10);
// $pdf->Cell(0, 20, 'Assinaturas', 0, 1);
$pdf->SetX(35);
$pdf->Cell(0, 12, 'Emitido por', 0, 0);
$pdf->SetX(165);
$pdf->Cell(0, 12, 'Cliente', 0, 1);
$pdf->SetX(20);
$pdf->Cell(0, 6, '_____________________________', 0, 0); // Assinatura do recepcionista
$pdf->SetX(145);
$pdf->Cell(0, 6, '_____________________________', 0, 1); // Assinatura do cliente

// Output do PDF
$pdf->Output('Venda_a_dinheiro.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();