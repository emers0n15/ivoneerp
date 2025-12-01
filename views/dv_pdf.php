<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../conexao/index.php';

$id_dv = $_GET['id_dv'];
$sqll = "SELECT * FROM devolucao WHERE id = '$id_dv'";
$rsv = mysqli_query($db, $sqll);
$dadoss = mysqli_fetch_array($rsv);
$n_doc = $dadoss['n_doc'];
$pedir = $dadoss['idpedido'];
// Dados da Empresa a Faturar (exemplo)
$sqlq = "SELECT serie, n_doc FROM pedido WHERE idpedido = '$pedir'";
$rsq = mysqli_query($db, $sqlq);
$dadosq = mysqli_fetch_array($rsq);
$seriea = $dadosq['serie'];
$n_docs = $dadosq['n_doc'];


function fetch_data($d, $id_d) {
    $output = '';
    
    // Use uma consulta preparada para evitar injeção de SQL
    $sql = "SELECT id, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = f.produto) as artigo, qtd, preco,iva, total FROM artigos_devolvidos as f WHERE devolucao = ? ";
    
    // Prepara a consulta
    $stmt = mysqli_prepare($d, $sql);
    
    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt) {
        // Associa o valor de $id_vd à consulta
        mysqli_stmt_bind_param($stmt, "s", $id_d);
        
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
$pdf->SetTitle('Devolução #' . $n_doc); // Defina o título do PDF

$pdf->AddPage();

// Dados da Empresa a Faturar (exemplo)
$sql = "SELECT * FROM empresa";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
$img = $dados['img'];

// Dados da Empresa Faturada (exemplo)
$sql1 = "SELECT * FROM clientes WHERE id = (SELECT idcliente FROM devolucao WHERE id = '$id_dv')";
$rs1 = mysqli_query($db, $sql1);
$dados1 = mysqli_fetch_array($rs1);

// Conteúdo da fatura
// Dados da Factura
$sql8 = "SELECT SUM(iva) as iv, SUM(total) as ts FROM artigos_devolvidos as f WHERE devolucao = '$id_dv'";
$rs8 = mysqli_query($db, $sql8);
$dados8 = mysqli_fetch_array($rs8);
$iv = $dados8['iv'];
$ts = $dados8['ts'];
$v = $iv + $ts;

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
$pdf->Cell(0, 10, 'Nota de Devolução #'.$dadoss['serie'].'/'.$n_doc, 0, 1);
// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)


$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 7, 'Data de Emissão', 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, 'Referência a VD #', 'T', 0);
$pdf->Ln(7);
$pdf->Cell(0, 7, $dadoss['data'], 'T', 0);
$pdf->SetX(60);
$pdf->Cell(0, 7, $seriea.'/'.$n_docs, 'T', 0);
$pdf->Ln(8);
$pdf->Cell(0, 7, 'Motivo de Devolução', 'T', 0);
$pdf->Ln(8);
$htmlItens = '
 <table style="text-align: justify;"><tr><td>'.$dadoss['motivo'].'</td></tr></table>
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
$content .= fetch_data($db,$id_dv);
$content .='</table>';
$pdf->writeHTML($content);



// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)
$pdf->Cell(0, 7, 'Documento Processado por Computador/ iVone ERP/', 0, 1);
// Totais

$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(145, 5, 'Mercadoria/Serviços', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($ts, 2). ' Mt', 1, 1, 'R');
$pdf->Cell(145, 5, 'IVA (5%)', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($iv, 2). ' Mt', 1, 1, 'R');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(145, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(38, 5, number_format($v, 2). ' Mt', 1, 1, 'R');
$pdf->Ln(6);
// Dados Bancários


// // Assinaturas
$pdf->SetFont('helvetica', '', 8);
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
$pdf->Output('Devolucao.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();