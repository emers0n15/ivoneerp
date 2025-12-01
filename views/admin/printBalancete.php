<?php 
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['idUsuario'])) {
    header("location:../");
}
include_once '../../conexao/index.php';

$data1 = $_GET['data1'];
$data2 = $_GET['data2'];

// Template TCPDF unificado
require_once 'includes/tcpdf_template.php';

// Dados da Empresa
$sql = "SELECT * FROM empresa LIMIT 1";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);

// Criar novo documento PDF com header/rodapé unificados
$titulo = 'RELATÓRIO DE BALANCETE - ' . $data1 . ' a ' . $data2;
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Relatório de Balancete');
$pdf->AddPage();

// Início da Tabela HTML
$pdf->Ln(8);
$pdf->SetFont('helvetica', '', 10);

$html = '
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 10px;
    }
    th {
        background-color: #f2f2f2;
        padding: 5px;
        text-align: left;
        font-weight: bold;
        border-bottom: 1px solid #ddd;
    }
    td {
        padding: 5px;
        border-bottom: 1px solid #ddd;
    }
    .desc { width: 44%; }
    .num { width: 8%; text-align: right; }
</style>
<table border="1" cellpadding="4">
    <thead>
        <tr>
            <th class="desc">Descrição</th>
            <th class="num">OCs</th>
            <th class="num">ESs</th>
            <th class="num">VDs</th>
            <th class="num">FAs</th>
            <th class="num">DVs</th>
            <th class="num">NCs</th>
            <th class="num">NDs</th>
        </tr>
    </thead>
    <tbody>';

// Consulta SQL para obter dados do balancete
$sqlBalancete = "
    SELECT 
        p.idproduto, 
        p.nomeproduto,
        (SELECT IFNULL(SUM(qtd), 0) FROM ordem_compra_artigos WHERE artigo = p.idproduto AND ordem_compra_artigos.data BETWEEN ? AND ?) AS quantidade_comprada,
    (SELECT IFNULL(SUM(qtd), 0) FROM es_artigos WHERE artigo = p.idproduto AND es_artigos.data BETWEEN ? AND ?) AS quantidade_estoque,
    (SELECT IFNULL(SUM(qtdentrega), 0) FROM entrega WHERE produtoentrega = p.idproduto AND entrega.datavenda BETWEEN ? AND ?) AS quantidade_vendida,
    (SELECT IFNULL(SUM(qtd), 0) FROM fa_artigos_fact WHERE artigo = p.idproduto AND fa_artigos_fact.data BETWEEN ? AND ?) AS quantidade_faturada,
    (SELECT IFNULL(SUM(qtd), 0) FROM artigos_devolvidos WHERE produto = p.idproduto AND artigos_devolvidos.data BETWEEN ? AND ?) AS quantidade_devolvida,
    (SELECT IFNULL(SUM(qtd), 0) FROM nc_artigos WHERE artigo = p.idproduto AND nc_artigos.data BETWEEN ? AND ?) AS quantidade_creditada,
    (SELECT IFNULL(SUM(qtd), 0) FROM nd_artigos WHERE artigo = p.idproduto AND nd_artigos.data BETWEEN ? AND ?) AS quantidade_debitada
FROM produto p
GROUP BY p.idproduto
";
// Verifique se a consulta foi preparada corretamente
$stmt = $db->prepare($sqlBalancete);
if (!$stmt) {
    die("Erro ao preparar a consulta SQL: " . $db->error);
}
$stmt->bind_param('ssssssssssssss', $data1, $data2, $data1, $data2, $data1, $data2, $data1, $data2, $data1, $data2, $data1, $data2, $data1, $data2);
$stmt->execute();
$rs = $stmt->get_result();

while ($row = $rs->fetch_assoc()) {
    $html .= '
        <tr>
            <td class="desc">' . $row['nomeproduto'] . '</td>
            <td class="num">' . $row['quantidade_comprada'] . '</td>
            <td class="num">' . $row['quantidade_estoque'] . '</td>
            <td class="num">' . $row['quantidade_vendida'] . '</td>
            <td class="num">' . $row['quantidade_faturada'] . '</td>
            <td class="num">' . $row['quantidade_devolvida'] . '</td>
            <td class="num">' . $row['quantidade_creditada'] . '</td>
            <td class="num">' . $row['quantidade_debitada'] . '</td>
        </tr>';
}

$html .= '
    </tbody>
</table>';

// Escrever a tabela no PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Adicionar nota final
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 7, 'Documento Processado por Computador / iVone ERP /', 0, 1);

// Output do PDF
$pdf->Output('Relatorio_balancete.pdf', 'I');

// Fechar conexão e liberar recursos
$db->close();
?>
