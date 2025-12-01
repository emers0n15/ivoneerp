<?php
// Relatório de produto em PDF - Otimizado para grandes volumes de dados
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit;
}

// Incluir template TCPDF unificado
require_once 'includes/tcpdf_template.php';
include '../../conexao/index.php';

// Verifica parâmetros
if (!isset($_GET['produto_id']) || !isset($_GET['data_inicio']) || !isset($_GET['data_fim'])) {
    die("Parâmetros insuficientes");
}

// Obter parâmetros
$produto_id = mysqli_real_escape_string($db, $_GET['produto_id']);
$data_inicio = mysqli_real_escape_string($db, $_GET['data_inicio']);
$data_fim = mysqli_real_escape_string($db, $_GET['data_fim']);
$tipo_movimento = isset($_GET['tipo_movimento']) ? mysqli_real_escape_string($db, $_GET['tipo_movimento']) : 'todos';

// Obter informações do produto
$sql_produto = "SELECT nomeproduto, codbar FROM produto WHERE idproduto = '$produto_id'";
$result_produto = mysqli_query($db, $sql_produto);
$produto = mysqli_fetch_assoc($result_produto);
$nome_produto = $produto['nomeproduto'];
$codigo_produto = $produto['codbar'];

// Obter dados da empresa
$sql_empresa = "SELECT * FROM empresa LIMIT 1";
$result_empresa = mysqli_query($db, $sql_empresa);
if (!$result_empresa) {
    die("Erro na consulta da empresa: " . mysqli_error($db));
}
$empresa = mysqli_fetch_assoc($result_empresa);

// Criar novo objeto PDF com tema unificado
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $empresa, 'RELATÓRIO DE MOVIMENTAÇÃO DE PRODUTO');

// Configurações do documento
$pdf->SetCreator('IvoneERP');
$pdf->SetAuthor($empresa['nome']);
$pdf->SetTitle('Relatório de Produto - ' . $nome_produto);
$pdf->SetSubject('Relatório de Movimentação de Produto');
$pdf->SetKeywords('Produto, Relatório, Movimentação, Stock');

// Adicionar primeira página
$pdf->AddPage();

// Obter estatísticas do produto
$sql_stock = "SELECT IFNULL(SUM(quantidade), 0) AS stock_atual 
              FROM stock 
              WHERE produto_id = '$produto_id' AND estado = 'ativo'";
$result_stock = mysqli_query($db, $sql_stock);
$row_stock = mysqli_fetch_assoc($result_stock);
$stock_atual = $row_stock['stock_atual'];

// Calcular estatísticas - Total de entradas no período
$sql_entradas = "SELECT IFNULL(SUM(ea.qtd), 0) AS total_entradas
                FROM es_artigos ea
                JOIN entrada_stock es ON ea.es = es.id
                WHERE ea.artigo = '$produto_id' 
                  AND es.data BETWEEN '$data_inicio' AND '$data_fim'";
$result_entradas = mysqli_query($db, $sql_entradas);
$row_entradas = mysqli_fetch_assoc($result_entradas);
$total_entradas = $row_entradas['total_entradas'];

// Calcular estatísticas - Total de saídas (combinando todas as formas)
// 1. Saídas por entrega
$sql_saidas_entrega = "SELECT IFNULL(SUM(e.qtdentrega), 0) AS total_saidas_entrega,
                               IFNULL(SUM(e.valorentrega), 0) AS valor_vendas_entrega
                       FROM entrega e
                       WHERE e.produtoentrega = '$produto_id' 
                         AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'";
$result_saidas_entrega = mysqli_query($db, $sql_saidas_entrega);
$row_saidas_entrega = mysqli_fetch_assoc($result_saidas_entrega);
$total_saidas_entrega = $row_saidas_entrega['total_saidas_entrega'];
$valor_vendas_entrega = $row_saidas_entrega['valor_vendas_entrega'];

// 2. Saídas por fatura
$sql_saidas_fatura = "SELECT IFNULL(SUM(fa.qtd), 0) AS total_saidas_fatura,
                            IFNULL(SUM(fa.valor), 0) AS valor_vendas_fatura
                     FROM fa_artigos_fact fa
                     JOIN factura f ON fa.factura = f.id
                     WHERE fa.artigo = '$produto_id' 
                       AND f.data BETWEEN '$data_inicio' AND '$data_fim'";
$result_saidas_fatura = mysqli_query($db, $sql_saidas_fatura);
$row_saidas_fatura = mysqli_fetch_assoc($result_saidas_fatura);
$total_saidas_fatura = $row_saidas_fatura['total_saidas_fatura'];
$valor_vendas_fatura = $row_saidas_fatura['valor_vendas_fatura'];

// 3. Saídas por saida_stock
$sql_saidas_ss = "SELECT IFNULL(SUM(ssa.qtd), 0) AS total_saidas_ss
                 FROM ss_artigos ssa
                 JOIN saida_stock ss ON ssa.ss = ss.id
                 WHERE ssa.artigo = '$produto_id' 
                   AND ss.data BETWEEN '$data_inicio' AND '$data_fim'";
$result_saidas_ss = mysqli_query($db, $sql_saidas_ss);
$row_saidas_ss = mysqli_fetch_assoc($result_saidas_ss);
$total_saidas_ss = $row_saidas_ss['total_saidas_ss'];

// Somar todas as formas de saída
$total_saidas = $total_saidas_entrega + $total_saidas_fatura + $total_saidas_ss;

// Somar valores de vendas
$total_valor_vendas = $valor_vendas_entrega + $valor_vendas_fatura;

// Calcular stock final
$stock_final = $stock_atual + $total_entradas - $total_saidas;

// Calcular consumo médio mensal
$sql_meses = "SELECT COUNT(DISTINCT DATE_FORMAT(datavenda, '%Y-%m')) AS num_meses
              FROM entrega
              WHERE produtoentrega = '$produto_id' 
              AND datavenda BETWEEN DATE_SUB('$data_inicio', INTERVAL 3 MONTH) AND '$data_fim'";
$result_meses = mysqli_query($db, $sql_meses);
$row_meses = mysqli_fetch_assoc($result_meses);
$num_meses = max(1, $row_meses['num_meses']); // Evitar divisão por zero
$consumo_medio = round($total_saidas / $num_meses, 2);

// Informações de lotes ativos
$sql_lotes = "SELECT 
                lote, 
                prazo, 
                SUM(quantidade) as qtd_lote 
              FROM stock 
              WHERE produto_id = '$produto_id' AND estado = 'ativo' 
              GROUP BY lote, prazo 
              ORDER BY prazo ASC";
$result_lotes = mysqli_query($db, $sql_lotes);

// Adicionar estatísticas ao PDF
$pdf->Ln(12);

// Título do resumo
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(190, 7, 'RESUMO', 0, 1, 'L');
$pdf->Ln(2); // Adicionar espaço entre o título e a tabela
$pdf->SetFont('helvetica', '', 9);

$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(47.5, 6, 'Stock Atual:', 1, 0, 'L', 1);
$pdf->Cell(47.5, 6, number_format($stock_atual, 0, ',', '.'), 1, 0, 'R');
$pdf->Cell(47.5, 6, 'Total de Entradas:', 1, 0, 'L', 1);
$pdf->Cell(47.5, 6, number_format($total_entradas, 0, ',', '.'), 1, 1, 'R');

$pdf->Cell(47.5, 6, 'Total de Saídas:', 1, 0, 'L', 1);
$pdf->Cell(47.5, 6, number_format($total_saidas, 0, ',', '.'), 1, 0, 'R');
$pdf->Cell(47.5, 6, 'Consumo Médio Mensal:', 1, 0, 'L', 1);
$pdf->Cell(47.5, 6, number_format($consumo_medio, 2, ',', '.'), 1, 1, 'R');

// Adicionar linha com valor total de vendas
$pdf->Cell(47.5, 6, 'Valor Total de Vendas:', 1, 0, 'L', 1);
$pdf->Cell(47.5, 6, number_format($total_valor_vendas, 2, ',', '.') . ' MZN', 1, 0, 'R');
$pdf->Cell(47.5, 6, 'Preço Médio por Unidade:', 1, 0, 'L', 1);
$preco_medio = ($total_saidas_entrega + $total_saidas_fatura) > 0 ? $total_valor_vendas / ($total_saidas_entrega + $total_saidas_fatura) : 0;
$pdf->Cell(47.5, 6, number_format($preco_medio, 2, ',', '.') . ' MZN', 1, 1, 'R');

// Informações de lotes ativos
if (mysqli_num_rows($result_lotes) > 0) {
    $pdf->Ln(8); // Aumento do espaço entre seções
    
    // Título da seção de lotes
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(190, 7, 'LOTES ATIVOS', 0, 1, 'L');
    $pdf->Ln(2); // Adicionar espaço entre o título e a tabela
    
    // Cabeçalho da tabela de lotes
    tcpdf_table_header($pdf, [
        ['Lote', 60], ['Validade', 40], ['Quantidade', 40], ['Dias até expiração', 50]
    ]);
    
    $pdf->SetFont('helvetica', '', 9);
    
    // Adicionar linhas da tabela de lotes
    while ($row_lote = mysqli_fetch_assoc($result_lotes)) {
        $lote = $row_lote['lote'];
        $prazo = $row_lote['prazo'];
        $qtd_lote = $row_lote['qtd_lote'];
        
        // Calcular dias restantes até a validade
        $hoje = new DateTime();
        $data_validade = new DateTime($prazo);
        $dias_restantes = $hoje->diff($data_validade)->days;
        $situacao_validade = $hoje < $data_validade ? $dias_restantes . ' dias' : 'EXPIRADO';
        
        $pdf->Cell(60, 6, $lote, 1, 0, 'L');
        $pdf->Cell(40, 6, date('d/m/Y', strtotime($prazo)), 1, 0, 'C');
        $pdf->Cell(40, 6, number_format($qtd_lote, 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell(50, 6, $situacao_validade, 1, 1, 'C');
    }
}

// Adicionar espaço adequado antes da seção de detalhamento
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(190, 7, 'DETALHAMENTO DE MOVIMENTAÇÕES', 0, 1, 'L');
$pdf->Ln(2); // Adicionar espaço entre o título e a tabela

// Adicionar cabeçalho da tabela
$columnsMov = [
    ['Data', 20], ['Tipo', 20], ['Doc.', 15], ['Número', 20], ['Lote', 25], ['Quantidade', 20], ['Saldo', 25], ['Entidade', 30], ['Valor', 15]
];
tcpdf_table_header($pdf, $columnsMov);

// Obter dados para o relatório - saldo inicial
$sql_saldo_inicial = "
    SELECT (
        IFNULL((SELECT SUM(ea.qtd) 
                FROM es_artigos ea
                JOIN entrada_stock es ON ea.es = es.id
                WHERE ea.artigo = '$produto_id' 
                  AND es.data < '$data_inicio'), 0) 
        -
        (
            IFNULL((SELECT SUM(e.qtdentrega) 
                    FROM entrega e 
                    WHERE e.produtoentrega = '$produto_id' 
                      AND e.datavenda < '$data_inicio'), 0)
            +
            IFNULL((SELECT SUM(fa.qtd) 
                    FROM fa_artigos_fact fa
                    JOIN factura f ON fa.factura = f.id
                    WHERE fa.artigo = '$produto_id' 
                      AND f.data < '$data_inicio'), 0)
            +
            IFNULL((SELECT SUM(ssa.qtd) 
                    FROM ss_artigos ssa
                    JOIN saida_stock ss ON ssa.ss = ss.id
                    WHERE ssa.artigo = '$produto_id' 
                      AND ss.data < '$data_inicio'), 0)
        )
    ) AS saldo_inicial";
$result_saldo_inicial = mysqli_query($db, $sql_saldo_inicial);
$row_saldo_inicial = mysqli_fetch_assoc($result_saldo_inicial);
$saldo_inicial = $row_saldo_inicial['saldo_inicial'];
$saldo_acumulado = $saldo_inicial;

// Adicionar linha de saldo inicial
$pdf->Cell(20, 6, date('d/m/Y', strtotime($data_inicio)), 1, 0, 'C');
$pdf->Cell(20, 6, 'Saldo', 1, 0, 'C');
$pdf->Cell(15, 6, '-', 1, 0, 'C');
$pdf->Cell(20, 6, '-', 1, 0, 'C');
$pdf->Cell(25, 6, '-', 1, 0, 'C');
$pdf->Cell(20, 6, '-', 1, 0, 'C');
$pdf->Cell(25, 6, number_format($saldo_acumulado, 0, ',', '.'), 1, 0, 'R');
$pdf->Cell(30, 6, '-', 1, 0, 'L');
$pdf->Cell(15, 6, '-', 1, 1, 'R');

// Obter movimentos

// Montar consulta SQL para obter todos os movimentos
$sql_movimentos = "";

// 1. Entradas (entrada_stock + es_artigos)
$sql_movimentos .= "
    SELECT 
        es.data AS data,
        'Entrada' AS tipo,
        'ES' AS documento,
        es.id AS numero,
        IFNULL(es.lote, '-') AS lote,
        ea.qtd AS quantidade,
        '' AS entidade,
        '' AS observacao,
        IFNULL(ea.preco_unit * ea.qtd, 0) AS valor
    FROM es_artigos ea
    JOIN entrada_stock es ON ea.es = es.id
    WHERE ea.artigo = '$produto_id' 
      AND es.data BETWEEN '$data_inicio' AND '$data_fim'";

// 2. Saídas por entrega
$sql_movimentos .= " UNION ALL
    SELECT 
        e.datavenda AS data,
        'Saída' AS tipo,
        'Entrega' AS documento,
        e.id AS numero,
        IFNULL(e.lote, '-') AS lote,
        e.qtdentrega AS quantidade,
        c.nome AS entidade,
        '' AS observacao,
        e.valorentrega AS valor
    FROM entrega e
    LEFT JOIN clientes c ON e.clienteentrega = c.id
    WHERE e.produtoentrega = '$produto_id' 
      AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'";

// 3. Saídas por fatura
$sql_movimentos .= " UNION ALL
    SELECT 
        f.data AS data,
        'Saída' AS tipo,
        'Fatura' AS documento,
        f.id AS numero,
        '-' AS lote,
        fa.qtd AS quantidade,
        c.nome AS entidade,
        '' AS observacao,
        fa.valor AS valor
    FROM fa_artigos_fact fa
    JOIN factura f ON fa.factura = f.id
    LEFT JOIN clientes c ON f.cliente = c.id
    WHERE fa.artigo = '$produto_id' 
      AND f.data BETWEEN '$data_inicio' AND '$data_fim'";

// 4. Saídas por saida_stock
$sql_movimentos .= " UNION ALL
    SELECT 
        ss.data AS data,
        'Saída' AS tipo,
        'SS' AS documento,
        ss.id AS numero,
        '-' AS lote,
        ssa.qtd AS quantidade,
        '' AS entidade,
        ss.descricao AS observacao,
        0 AS valor
    FROM ss_artigos ssa
    JOIN saida_stock ss ON ssa.ss = ss.id
    WHERE ssa.artigo = '$produto_id' 
      AND ss.data BETWEEN '$data_inicio' AND '$data_fim'";

// Ordenar por data
$sql_movimentos .= " ORDER BY data ASC";

$result_movimentos = mysqli_query($db, $sql_movimentos);

// Verificar se há erros de consulta SQL
if (empty($sql_movimentos) || !$result_movimentos) {
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Nenhum movimento encontrado para o período especificado.', 0, 1, 'C');
    
    // Se houver um erro de SQL, mostrar detalhes
    if (!empty($sql_movimentos) && !$result_movimentos) {
        $pdf->Cell(0, 10, 'Erro: ' . mysqli_error($db), 0, 1, 'C');
    }
    
    $pdf->Output('relatorio_produto.pdf', 'I');
    exit();
}

// Adicionar movimentos ao PDF em lotes para otimizar memória
if (!empty($sql_movimentos)) {
    // Número de linhas por lote (ajustar conforme necessário para desempenho)
    $batch_size = 100;
    $offset = 0;
    
    do {
        // Adicionar limite para processar em lotes
        $sql_batch = $sql_movimentos . " LIMIT $offset, $batch_size";
        $result_batch = mysqli_query($db, $sql_batch);
        
        // Verificar se a consulta foi bem-sucedida
        if (!$result_batch) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Erro na consulta SQL: ' . mysqli_error($db), 0, 1, 'C');
            $pdf->Cell(0, 10, 'SQL: ' . $sql_batch, 0, 1, 'L');
            $pdf->Output('erro_relatorio_produto.pdf', 'I');
            exit;
        }
        
        $num_rows = 0;
        
        while ($row = mysqli_fetch_assoc($result_batch)) {
            $num_rows++;
            $quantidade = $row['quantidade'];
            
            // Atualizar saldo
            if ($row['tipo'] == 'Entrada') {
                $saldo_acumulado += $quantidade;
                $quantidade_formatada = '+' . number_format($quantidade, 0, ',', '.');
            } else {
                $saldo_acumulado -= $quantidade;
                $quantidade_formatada = '-' . number_format($quantidade, 0, ',', '.');
            }
            
            // Formatar data
            $data_formatada = date('d/m/Y', strtotime($row['data']));
            
            // Adicionar linha à tabela
            $pdf->Cell(20, 6, $data_formatada, 1, 0, 'C');
            $pdf->Cell(20, 6, $row['tipo'], 1, 0, 'C');
            $pdf->Cell(15, 6, substr($row['documento'], 0, 8), 1, 0, 'C');
            $pdf->Cell(20, 6, substr($row['numero'], 0, 10), 1, 0, 'C');
            $pdf->Cell(25, 6, substr($row['lote'], 0, 15), 1, 0, 'L');
            $pdf->Cell(20, 6, $quantidade_formatada, 1, 0, 'R');
            $pdf->Cell(25, 6, number_format($saldo_acumulado, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(30, 6, substr($row['entidade'], 0, 20), 1, 0, 'L');
            $pdf->Cell(15, 6, number_format($row['valor'], 2, ',', '.') . ' MZN', 1, 1, 'R');
            
            // Verificar se precisa adicionar nova página e recriar cabeçalho
            tcpdf_should_addpage_and_header($pdf, 260, $columnsMov);
        }
        
        $offset += $batch_size;
        
    } while ($num_rows == $batch_size); // Continue até processar todos os registros
}

// Adicionar informações extras ao final
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(190, 5, 'Relatório gerado por: ' . $_SESSION['nomeUsuario'] . ' em ' . date('d/m/Y H:i'), 0, 1, 'L');

// Enviar o PDF para o navegador
$pdf->Output('Relatorio_Produto_' . $produto_id . '.pdf', 'I');
?>
