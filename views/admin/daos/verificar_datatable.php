<?php
// Arquivo para testar diretamente a funcionalidade do DataTable
session_start();
include_once '../../../conexao/index.php';
header('Content-Type: application/json');
date_default_timezone_set('Africa/Maputo');

// Log de depuração
$log = [];
$log[] = "Iniciando teste direto de DataTable";

try {
    // Data atual
    $hoje = date('Y-m-d');
    $data_inicio = date('Y-m-d', strtotime('-3 months'));

    // Consulta simplificada sem paginação ou parâmetros - apenas para testar
    $sql = "
        SELECT 
            p.idproduto,
            p.codbar,
            p.nomeproduto,
            IFNULL(SUM(s.quantidade), 0) AS stock_atual,
            (
                SELECT 
                    IFNULL(SUM(e.qtdentrega), 0) / 
                    GREATEST(COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')), 1) 
                FROM entrega e 
                WHERE e.produtoentrega = p.idproduto 
                    AND e.datavenda BETWEEN '$data_inicio' AND '$hoje'
            ) AS consumo_medio_mensal
        FROM produto p
        LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
        GROUP BY p.idproduto
        HAVING consumo_medio_mensal > 0
        LIMIT 100
    ";

    $log[] = "Executando consulta: " . preg_replace('/\s+/', ' ', $sql);
    
    $result = $db->query($sql);
    
    if (!$result) {
        $log[] = "ERRO na consulta: " . $db->error;
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Erro ao executar consulta',
            'erro' => $db->error,
            'log' => $log
        ]);
        exit;
    }
    
    // Contar resultados
    $total = $result->num_rows;
    $log[] = "Total de registros encontrados: $total";
    
    // Obter dados
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $idproduto = $row['idproduto'];
        $codigobarra = $row['codigobarra'] ?? 'N/A';
        $nomeproduto = $row['nomeproduto'];
        $stock_atual = $row['stock_atual'];
        $consumo_medio_mensal = $row['consumo_medio_mensal'];
        
        // Determinar o status baseado na diferença entre estoque e consumo médio
        $percentual = $consumo_medio_mensal > 0 ? ($stock_atual / $consumo_medio_mensal) * 100 : 0;
        
        if ($percentual <= 30) {
            $status = '<span class="badge badge-danger">Crítico</span>';
        } elseif ($percentual <= 70) {
            $status = '<span class="badge badge-warning">Baixo</span>';
        } else {
            $status = '<span class="badge badge-success">Adequado</span>';
        }
        
        // Botões de ação
        $acoes = '
            <div class="dropdown dropdown-action">
                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="../produtos.php?action=edit&id='.$idproduto.'"><i class="fa fa-pencil m-r-5"></i> Editar</a>
                    <a class="dropdown-item" href="../consumo_medio.php?id='.$idproduto.'"><i class="fa fa-bar-chart m-r-5"></i> Ver Consumo</a>
                    <a class="dropdown-item" href="../armazem.php?produto_id='.$idproduto.'"><i class="fa fa-cubes m-r-5"></i> Ver Stock</a>
                </div>
            </div>
        ';
        
        $data[] = [
            "idproduto" => $idproduto,
            "codbar" => $codigobarra,
            "nomeproduto" => $nomeproduto,
            "stock_atual" => number_format($stock_atual, 0),
            "consumo_medio_mensal" => number_format($consumo_medio_mensal, 2),
            "status" => $status,
            "acoes" => $acoes
        ];
    }
    
    // Retornar no formato do DataTable
    echo json_encode([
        "draw" => 1,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data,
        "log" => $log
    ]);
    
} catch (Exception $e) {
    $log[] = "Exceção: " . $e->getMessage();
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro inesperado',
        'erro' => $e->getMessage(),
        'log' => $log
    ]);
}
?>
