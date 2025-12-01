<?php
// Configuração e conexão com o banco de dados
error_reporting(E_ALL);
include '../../../conexao/index.php';

// Verificar se a sessão está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializar resposta
$response = [
    'status' => 'error',
    'message' => 'Erro ao processar a requisição',
    'data' => []
];

// Desativar a exibição de erros (para garantir que apenas JSON seja retornado)
ini_set('display_errors', 0);
error_reporting(0);

// Obter parâmetros da requisição
$produto_id = isset($_POST['produto_id']) ? $_POST['produto_id'] : '';
$data_inicio = isset($_POST['data_inicio']) ? $_POST['data_inicio'] : '';
$data_fim = isset($_POST['data_fim']) ? $_POST['data_fim'] : '';
$tipo_movimento = isset($_POST['tipo_movimento']) ? $_POST['tipo_movimento'] : 'todos';

// Transformar datas para formato SQL (YYYY-MM-DD)
$data_inicio = date('Y-m-d', strtotime($data_inicio));
$data_fim = date('Y-m-d', strtotime($data_fim));

if (empty($produto_id) || empty($data_inicio) || empty($data_fim)) {
    $response['message'] = 'Parâmetros inválidos';
    echo json_encode($response);
    exit;
}

// Tentar executar todo o código em um bloco try-catch para capturar erros
try {
    // 1. Obter dados do produto
    $sql_produto = "SELECT idproduto, codbar, nomeproduto FROM produto WHERE idproduto = $produto_id LIMIT 1";
    $result_produto = mysqli_query($db, $sql_produto);

    if (!$result_produto || mysqli_num_rows($result_produto) == 0) {
        $response['message'] = 'Produto não encontrado';
        echo json_encode($response);
        exit;
    }

    $produto = mysqli_fetch_assoc($result_produto);
    $response['produto'] = $produto;

    // 2. Calcular total de entradas no período
    $sql_entradas = "SELECT IFNULL(SUM(ea.qtd), 0) AS total_entradas
                    FROM es_artigos ea
                    JOIN entrada_stock es ON ea.es = es.id
                    WHERE ea.artigo = '$produto_id' 
                      AND es.data BETWEEN '$data_inicio' AND '$data_fim'";
    $result_entradas = mysqli_query($db, $sql_entradas);
    $row_entradas = mysqli_fetch_assoc($result_entradas);
    $total_entradas = $row_entradas['total_entradas'];

    // 3. Calcular total de saídas no período (combinando todas as formas de saídas)
    // 3.1 Saídas por entrega
    $sql_saidas_entrega = "SELECT IFNULL(SUM(e.qtdentrega), 0) AS total_saidas_entrega
                           FROM entrega e
                           WHERE e.produtoentrega = '$produto_id' 
                             AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'";
    $result_saidas_entrega = mysqli_query($db, $sql_saidas_entrega);
    $row_saidas_entrega = mysqli_fetch_assoc($result_saidas_entrega);
    $total_saidas_entrega = $row_saidas_entrega['total_saidas_entrega'];

    // 3.2 Saídas por fatura (fa_artigos_fact)
    $sql_saidas_fatura = "SELECT IFNULL(SUM(fa.qtd), 0) AS total_saidas_fatura
                          FROM fa_artigos_fact fa
                          JOIN factura f ON fa.factura = f.id
                          WHERE fa.artigo = '$produto_id' 
                            AND f.data BETWEEN '$data_inicio' AND '$data_fim'";
    $result_saidas_fatura = mysqli_query($db, $sql_saidas_fatura);
    $row_saidas_fatura = mysqli_fetch_assoc($result_saidas_fatura);
    $total_saidas_fatura = $row_saidas_fatura['total_saidas_fatura'];

    // 3.3 Saídas por saida_stock (ss_artigos)
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

    // 4. Calcular stock atual e consumo médio
    $stock_atual = 0;
    $consumo_medio = 0;

    // Consultar stock atual
    $sql_stock = "SELECT IFNULL(SUM(quantidade), 0) AS stock_atual 
                 FROM stock 
                 WHERE produto_id = '$produto_id' AND estado = 'ativo'";
    $result_stock = mysqli_query($db, $sql_stock);
    $row_stock = mysqli_fetch_assoc($result_stock);
    $stock_atual = $row_stock['stock_atual'];

    // Calcular consumo médio (saídas dividido pelo número de dias no período)
    $data_inicio_obj = new DateTime($data_inicio);
    $data_fim_obj = new DateTime($data_fim);
    $diff = $data_inicio_obj->diff($data_fim_obj);
    $dias_periodo = $diff->days + 1; // +1 para incluir o dia final
    $consumo_medio = $dias_periodo > 0 ? $total_saidas / $dias_periodo : 0;

    // Informações detalhadas sobre lotes ativos
    $sql_lotes = "SELECT 
                  lote, 
                  prazo, 
                  SUM(quantidade) as qtd_lote 
                  FROM stock 
                  WHERE produto_id = '$produto_id' AND estado = 'ativo' 
                  GROUP BY lote, prazo 
                  ORDER BY prazo ASC";
    $result_lotes = mysqli_query($db, $sql_lotes);
    $lotes_info = [];
    while ($row_lote = mysqli_fetch_assoc($result_lotes)) {
        $lotes_info[] = $row_lote;
    }

    // Adicionar estatísticas à resposta
    $response['estatisticas'] = [
        'stock_atual' => number_format($stock_atual, 0, ',', '.'),
        'total_entradas' => number_format($total_entradas, 0, ',', '.'),
        'total_saidas' => number_format($total_saidas, 0, ',', '.'),
        'consumo_medio' => number_format($consumo_medio, 2, ',', '.'),
        'lotes' => $lotes_info
    ];

    // 5. Obter movimentos (entradas e saídas)
    $movimentos = [];
    $saldo_acumulado = 0;

    // Primeiro, determinar o saldo inicial antes do período selecionado
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
    $saldo_acumulado = $row_saldo_inicial['saldo_inicial'];

    // Adicionar registro de saldo inicial
    $movimentos[] = [
        'data' => date('d/m/Y', strtotime($data_inicio)),
        'tipo' => 'Saldo Inicial',
        'documento' => '-',
        'numero' => '-',
        'lote' => '-',
        'quantidade' => '-',
        'saldo' => number_format($saldo_acumulado, 0, ',', '.'),
        'entidade' => '-',
        'observacao' => 'Saldo anterior a ' . date('d/m/Y', strtotime($data_inicio))
    ];

    // Subconsulta para entradas
    $sql_entradas_mov = "";
    if ($tipo_movimento == 'todos' || $tipo_movimento == 'entrada') {
        $sql_entradas_mov = "
            SELECT 
                es.data AS data,
                'Entrada' AS tipo,
                'ES' AS documento,
                es.n_doc AS numero,
                IFNULL(es.lote, '-') AS lote,
                ea.qtd AS quantidade,
                '' AS entidade,
                '' AS observacao
            FROM es_artigos ea
            JOIN entrada_stock es ON ea.es = es.id
            WHERE ea.artigo = '$produto_id' 
              AND es.data BETWEEN '$data_inicio' AND '$data_fim'
        ";
    }

    // Subconsulta para saídas (combinando todas as formas)
    $sql_saidas_mov = "";
    if ($tipo_movimento == 'todos' || $tipo_movimento == 'saida') {
        // 1. Saídas por entrega
        $sql_saidas_mov = "
            SELECT 
                e.datavenda AS data,
                'Saída (Entrega)' AS tipo,
                'EN' AS documento,
                e.pedidoentrega AS numero,
                IFNULL(e.lote, '-') AS lote,
                e.qtdentrega AS quantidade,
                c.nome AS entidade,
                '' AS observacao
            FROM entrega e
            LEFT JOIN cliente c ON e.clienteentrega = c.idcliente
            WHERE e.produtoentrega = '$produto_id' 
              AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'
        ";
        
        // 2. Saídas por fatura
        $sql_saidas_mov .= " UNION
            SELECT 
                f.data AS data,
                'Saída (Fatura)' AS tipo,
                'FA' AS documento,
                f.n_doc AS numero,
                '-' AS lote,
                fa.qtd AS quantidade,
                c.nome AS entidade,
                '' AS observacao
            FROM fa_artigos_fact fa
            JOIN factura f ON fa.factura = f.id
            LEFT JOIN cliente c ON f.cliente = c.idcliente
            WHERE fa.artigo = '$produto_id' 
              AND f.data BETWEEN '$data_inicio' AND '$data_fim'
        ";
        
        // 3. Saídas por saida_stock
        $sql_saidas_mov .= " UNION
            SELECT 
                ss.data AS data,
                'Saída (Stock)' AS tipo,
                'SS' AS documento,
                ss.n_doc AS numero,
                IFNULL(ss.lote, '-') AS lote,
                ssa.qtd AS quantidade,
                '' AS entidade,
                ss.descricao AS observacao
            FROM ss_artigos ssa
            JOIN saida_stock ss ON ssa.ss = ss.id
            WHERE ssa.artigo = '$produto_id' 
              AND ss.data BETWEEN '$data_inicio' AND '$data_fim'
        ";
    }

    // Combinar consultas de entradas e saídas se necessário
    $sql_movimentos = "";
    if (!empty($sql_entradas_mov) && !empty($sql_saidas_mov)) {
        $sql_movimentos = "$sql_entradas_mov UNION $sql_saidas_mov ORDER BY data ASC";
    } else if (!empty($sql_entradas_mov)) {
        $sql_movimentos = "$sql_entradas_mov ORDER BY data ASC";
    } else if (!empty($sql_saidas_mov)) {
        $sql_movimentos = "$sql_saidas_mov ORDER BY data ASC";
    }

    // Executar consulta de movimentos se houver
    if (!empty($sql_movimentos)) {
        $result_movimentos = mysqli_query($db, $sql_movimentos);
        
        if ($result_movimentos) {
            while ($row = mysqli_fetch_assoc($result_movimentos)) {
                $quantidade = $row['quantidade'];
                $tipo = $row['tipo'];
                
                // Atualizar saldo acumulado
                if (strpos($tipo, 'Entrada') !== false) {
                    $saldo_acumulado += $quantidade;
                } else if (strpos($tipo, 'Saída') !== false) {
                    $saldo_acumulado -= $quantidade;
                }
                
                // Formatar data
                $data_formatada = date('d/m/Y', strtotime($row['data']));
                
                // Adicionar movimento
                $movimentos[] = [
                    'data' => $data_formatada,
                    'tipo' => $tipo,
                    'documento' => $row['documento'],
                    'numero' => $row['numero'],
                    'lote' => $row['lote'],
                    'quantidade' => number_format($quantidade, 0, ',', '.'),
                    'saldo' => number_format($saldo_acumulado, 0, ',', '.'),
                    'entidade' => $row['entidade'],
                    'observacao' => $row['observacao']
                ];
            }
        }
    }

    // Adicionar movimentos à resposta
    $response['movimentos'] = $movimentos;

    // Retornar resposta como JSON
    $response['status'] = 'success';
    $response['message'] = 'Dados obtidos com sucesso';
    echo json_encode($response);
} catch (Exception $e) {
    $response['message'] = 'Erro ao processar a requisição: ' . $e->getMessage();
    echo json_encode($response);
}
?>
