<?php
// Visualização simples e direta dos artigos em estado crítico
include_once '../../conexao/index.php';
include_once '../../controle/headers/headerprodutos.php';
date_default_timezone_set('Africa/Maputo');

// Definir o período para consulta
$hoje = date('Y-m-d');
$data_inicio = date('Y-m-d', strtotime('-3 months'));

// Consultar produtos em estado crítico diretamente, sem usar DataTable
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
    HAVING stock_atual < consumo_medio_mensal AND consumo_medio_mensal > 0
    ORDER BY (stock_atual / consumo_medio_mensal) ASC
    LIMIT 100
";

$result = $db->query($sql);
$produtos = [];
$erro = "";

if (!$result) {
    $erro = "Erro na consulta: " . $db->error;
} else {
    while ($row = $result->fetch_assoc()) {
        // Calcular dias de cobertura
        $stock_atual = floatval($row['stock_atual']);
        $consumo_medio_mensal = floatval($row['consumo_medio_mensal']);
        $dias_cobertura = ($consumo_medio_mensal > 0) ? round(($stock_atual / $consumo_medio_mensal) * 30) : 0;
        
        $row['dias_cobertura'] = $dias_cobertura;
        $produtos[] = $row;
    }
}

// Registrar a consulta para debug
$debug_info = [
    'sql' => $sql,
    'num_resultados' => count($produtos),
    'erro' => $erro,
    'data_inicio' => $data_inicio,
    'data_fim' => $hoje,
];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artigos em Estado Crítico - Visualização Simples</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .status-critical { background-color: #ffebee !important; }
        .status-warning { background-color: #fff8e1 !important; }
        .status-normal { background-color: #e8f5e9 !important; }
        
        .debug-section {
            background-color: #f5f5f5;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .debug-section pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h2>Artigos em Estado Crítico</h2>
                <p>Mostrando artigos com estoque abaixo do consumo médio mensal</p>
            </div>
            
            <div class="card-body">
                <?php if ($erro): ?>
                    <div class="alert alert-danger">
                        <strong>Erro:</strong> <?= $erro ?>
                    </div>
                <?php elseif (empty($produtos)): ?>
                    <div class="alert alert-info">
                        <strong>Informação:</strong> Não foram encontrados artigos em estado crítico.
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Produto</th>
                                <th>Estoque Atual</th>
                                <th>Consumo Médio Mensal</th>
                                <th>Dias de Cobertura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $produto): 
                                $status_class = '';
                                if ($produto['dias_cobertura'] < 7) {
                                    $status_class = 'status-critical';
                                } elseif ($produto['dias_cobertura'] < 15) {
                                    $status_class = 'status-warning';
                                } else {
                                    $status_class = 'status-normal';
                                }
                            ?>
                                <tr class="<?= $status_class ?>">
                                    <td><?= $produto['idproduto'] ?></td>
                                    <td><?= htmlspecialchars($produto['codbar'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($produto['nomeproduto']) ?></td>
                                    <td><?= number_format($produto['stock_atual'], 0) ?></td>
                                    <td><?= number_format($produto['consumo_medio_mensal'], 2) ?></td>
                                    <td><?= $produto['dias_cobertura'] ?> dias</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
                <div class="mt-3">
                    <a href="consumo_artigos_baixo.php" class="btn btn-primary">Voltar à página principal</a>
                    <button id="btnDetalhes" class="btn btn-info">Mostrar Detalhes Técnicos</button>
                </div>
            </div>
        </div>
        
        <!-- Seção de Debug - Inicialmente oculta -->
        <div id="debugSection" class="debug-section mt-4" style="display: none;">
            <h3>Informações de Diagnóstico</h3>
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    Detalhes da Consulta
                </div>
                <div class="card-body">
                    <h5>Parâmetros</h5>
                    <ul>
                        <li><strong>Data Início:</strong> <?= $data_inicio ?></li>
                        <li><strong>Data Fim:</strong> <?= $hoje ?></li>
                        <li><strong>Resultados encontrados:</strong> <?= count($produtos) ?></li>
                    </ul>

                    <h5>Consulta SQL</h5>
                    <pre><?= htmlspecialchars($sql) ?></pre>
                    
                    <?php if ($erro): ?>
                        <h5>Erro</h5>
                        <pre><?= htmlspecialchars($erro) ?></pre>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mostrar/ocultar seção de debug
            $("#btnDetalhes").click(function() {
                $("#debugSection").toggle();
            });
            
            // Registrar no console informações de diagnóstico
            console.log("Diagnóstico de Artigos Críticos:", <?= json_encode($debug_info) ?>);
        });
    </script>
</body>
</html>
