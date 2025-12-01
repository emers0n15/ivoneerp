<?php
// Arquivo para testar uma consulta direta dos produtos com estoque baixo
session_start();
include_once '../../../conexao/index.php';
header('Content-Type: application/json');
date_default_timezone_set('Africa/Maputo');

// Iniciar array de log
$log = [];
$log[] = "Iniciando consulta direta para diagnóstico";

try {
    // Data atual
    $hoje = date('Y-m-d');

    // Período para cálculo do consumo médio (últimos 3 meses por padrão)
    $data_inicio = date('Y-m-d', strtotime('-3 months'));
    $data_fim = $hoje;

    $log[] = "Período para cálculo: $data_inicio até $data_fim";

    // Lista de produtos com estoque baixo
    $produtos = [];

    // Consulta SQL otimizada para performance
    $sql = "
        SELECT 
            p.idproduto,
            p.codigobarra,
            p.nomeproduto,
            IFNULL(SUM(s.quantidade), 0) AS stock_atual,
            (
                SELECT 
                    IFNULL(SUM(e.qtdentrega), 0) / 
                    GREATEST(COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')), 1) 
                FROM entrega e 
                WHERE e.produtoentrega = p.idproduto 
                    AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'
            ) AS consumo_medio_mensal
        FROM produto p
        LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
        WHERE p.estado = 'ativo'
        GROUP BY p.idproduto
        HAVING stock_atual <= consumo_medio_mensal AND consumo_medio_mensal > 0
        ORDER BY (stock_atual / consumo_medio_mensal) ASC
        LIMIT 50
    ";

    $log[] = "Executando consulta SQL direta: " . preg_replace('/\s+/', ' ', $sql);

    $result = mysqli_query($db, $sql);
    
    if (!$result) {
        $log[] = "ERRO na consulta SQL: " . mysqli_error($db);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro na consulta SQL', 'erro' => mysqli_error($db), 'log' => $log]);
        exit;
    }
    
    $log[] = "Consulta executada com sucesso. Encontradas " . mysqli_num_rows($result) . " linhas.";
    
    // Processar resultados
    while ($row = mysqli_fetch_assoc($result)) {
        $percentual = 0;
        if ($row['consumo_medio_mensal'] > 0) {
            $percentual = ($row['stock_atual'] / $row['consumo_medio_mensal']) * 100;
        }
        
        $produtos[] = [
            'id' => $row['idproduto'],
            'codigo' => $row['codigobarra'],
            'nome' => $row['nomeproduto'],
            'stock_atual' => $row['stock_atual'],
            'consumo_medio' => $row['consumo_medio_mensal'],
            'percentual' => $percentual,
            'tipo_alerta' => $percentual <= 30 ? 'crítico' : 'normal'
        ];
    }
    
    // Retornar resultados
    echo json_encode([
        'status' => 'sucesso',
        'total' => count($produtos),
        'produtos' => $produtos,
        'log' => $log,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    $log[] = "ERRO de exceção: " . $e->getMessage();
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Ocorreu um erro ao executar a consulta direta',
        'erro' => $e->getMessage(),
        'log' => $log
    ]);
}
?>
