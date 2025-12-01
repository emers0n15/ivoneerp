<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../");
}
include_once '../../conexao/index.php';

$id_ec = $_GET['id_ec'];

function fetch_data($d, $id_c) {
    $output = '';
    
    // Use uma consulta preparada para evitar injeção de SQL
    $sql = "SELECT id, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = f.artigo) as artigo, qtd, preco, total FROM ct_artigos_cotados as f WHERE cotacao = ? ";
    
    // Prepara a consulta
    $stmt = mysqli_prepare($d, $sql);
    
    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt) {
        // Associa o valor de $id_vd à consulta
        mysqli_stmt_bind_param($stmt, "s", $id_c);
        
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
                    <td style="text-align: center;">'.number_format($dados3['total'], 2, ".", ",").'</td>
                </tr>
            ';
        }
        
        // Fecha a consulta preparada
        mysqli_stmt_close($stmt);
    }
    
    return $output;
}

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

$sqll = "SELECT *, (SELECT nome FROM users as u WHERE u.id = e.user) as utiliza FROM entrada_caixa as e WHERE id = '$id_ec'";
$rs1 = mysqli_query($db, $sqll);
$dados1 = mysqli_fetch_array($rs1);
$serie = $dados1['serie'];
$caixote = $dados1['caixa'];

$sqlk = "SELECT (SELECT nome FROM users as u WHERE u.id = p.usuario) as u FROM periodo as p WHERE idperiodo = '$caixote'";
$rsk = mysqli_query($db, $sqlk);
$dadosk = mysqli_fetch_array($rsk);
$homem = $dadosk['u'];

// Criar novo documento PDF com cabeçalho padronizado e logo
$titulo = 'Entrada de Caixa EC# ' . $serie . '/' . $id_ec;
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
$pdf->Cell(60, 7, 'EC#'.$dados1['serie'].'/'.$dados1['id'], 1, 0, 'L', $fill);
$pdf->Cell(40, 7, $dados1['valor'], 1, 0, 'C', $fill);
$pdf->Cell(40, 7, $homem, 1, 0, 'L', $fill);
$pdf->Cell(30, 7, $dados1['utiliza'], 1, 0, 'L', $fill);
$pdf->Ln();

// Desenhar uma linha horizontal
$pdf->SetLineWidth(0.1); // Defina a largura da linha
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Especifica as coordenadas (x1, y1, x2, y2)
$pdf->Cell(0, 7, 'Documento Processado por Computador/ iVone ERP/', 0, 1);

// Totais



// Output do PDF
$pdf->Output('Entrada_caixa.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();