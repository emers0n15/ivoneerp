<?php
// Página ultra-simples para listar artigos críticos sem dependências externas
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

// Cabeçalhos
header('Content-Type: text/html; charset=UTF-8');

// Definir o período para consulta
$hoje = date('Y-m-d');
$data_inicio = date('Y-m-d', strtotime('-3 months'));

// Consulta direta, sem usar table ou javascript externo
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

// Executar consulta diretamente
try {
    $result = $db->query($sql);
    $produtos = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }
    }
} catch (Exception $e) {
    $erro = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Artigos em Estado Crítico</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #c00; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f2f2f2; }
        .critical { background-color: #ffebee; }
        .warning { background-color: #fff8e1; }
        .info { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .info-box { background-color: #e8f4fd; border-left: 5px solid #2196F3; }
        .error-box { background-color: #ffebee; border-left: 5px solid #f44336; }
        .backlink { margin: 20px 0; }
        .backlink a { 
            text-decoration: none; 
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
        }
        .sql-box {
            background-color: #f5f5f5;
            padding: 10px;
            border-left: 5px solid #9e9e9e;
            margin: 15px 0;
            white-space: pre-wrap;
            font-family: monospace;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>Artigos em Estado Crítico</h1>
    
    <div class="info info-box">
        <p><strong>Período de análise:</strong> <?= date('d/m/Y', strtotime($data_inicio)) ?> até <?= date('d/m/Y') ?></p>
        <p><strong>Banco de dados:</strong> <?= $db->host_info ?></p>
    </div>
    
    <?php if (isset($erro)): ?>
        <div class="info error-box">
            <p><strong>Erro:</strong> <?= $erro ?></p>
        </div>
    <?php endif; ?>
    
    <h2>Resultados da Consulta</h2>
    <?php if (empty($produtos)): ?>
        <div class="info error-box">
            <p>Nenhum artigo em estado crítico encontrado.</p>
        </div>
    <?php else: ?>
        <p><strong>Total: <?= count($produtos) ?> artigos encontrados</strong></p>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nome do Produto</th>
                    <th>Estoque Atual</th>
                    <th>Consumo Médio Mensal</th>
                    <th>Cobertura (Dias)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): 
                    $stock = floatval($p['stock_atual']);
                    $consumo = floatval($p['consumo_medio_mensal']);
                    $dias_cobertura = ($consumo > 0) ? round(($stock / $consumo) * 30) : 0;
                    $class = ($dias_cobertura < 7) ? 'critical' : (($dias_cobertura < 15) ? 'warning' : '');
                ?>
                    <tr class="<?= $class ?>">
                        <td><?= $p['idproduto'] ?></td>
                        <td><?= htmlspecialchars($p['codbar'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($p['nomeproduto']) ?></td>
                        <td><?= number_format($stock, 0) ?></td>
                        <td><?= number_format($consumo, 2) ?></td>
                        <td><?= $dias_cobertura ?> dias</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <div class="backlink">
        <a href="consumo_artigos_baixo.php">← Voltar à página principal</a>
    </div>
    
    <h3>Consulta SQL Utilizada</h3>
    <div class="sql-box"><?= htmlspecialchars($sql) ?></div>
    
    <script>
        // Script mínimo sem dependências externas
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Total de artigos críticos encontrados: <?= count($produtos) ?>');
        });
    </script>
</body>
</html>
