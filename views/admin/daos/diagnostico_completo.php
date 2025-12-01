<?php
// Script para diagnóstico completo de produtos em estado crítico
header('Content-Type: text/html; charset=utf-8');
include_once '../../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

// Definir o período para consulta
$hoje = date('Y-m-d');
$data_inicio = date('Y-m-d', strtotime('-3 months'));

// Obter produtos em estado crítico
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
    HAVING stock_atual <= consumo_medio_mensal AND consumo_medio_mensal > 0
    ORDER BY (stock_atual / consumo_medio_mensal) ASC
    LIMIT 100
";

$result = $db->query($sql);

if (!$result) {
    $erro = "ERRO ao executar consulta: " . $db->error;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico Completo - Artigos em Estado Crítico</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .info-box {
            background-color: #e9f7fe;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
        }
        .warning-box {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
        }
        .error-box {
            background-color: #fde8e8;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .critical {
            background-color: #ffebee;
        }
        .warning {
            background-color: #fff8e1;
        }
        .normal {
            background-color: #e8f5e9;
        }
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-critical {
            background-color: #e74c3c;
        }
        .status-warning {
            background-color: #f39c12;
        }
        .status-normal {
            background-color: #2ecc71;
        }
        .buttons {
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        pre {
            background-color: #f5f5f5;
            padding: 15px;
            overflow-x: auto;
            border-radius: 5px;
            font-family: Consolas, monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Diagnóstico Completo - Artigos em Estado Crítico</h1>
        
        <div class="info-box">
            <h3>Informações do Sistema</h3>
            <p><strong>Data/Hora:</strong> <?= date('d/m/Y H:i:s') ?></p>
            <p><strong>Período de Análise:</strong> <?= date('d/m/Y', strtotime($data_inicio)) ?> até <?= date('d/m/Y', strtotime($hoje)) ?></p>
            <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
            <p><strong>MySQL Version:</strong> <?= $db->server_info ?></p>
        </div>

        <?php if (isset($erro)): ?>
            <div class="error-box">
                <h3>Erro na Consulta</h3>
                <p><?= $erro ?></p>
            </div>
        <?php else: ?>
            <div class="info-box">
                <h3>Resultado da Consulta</h3>
                <p><strong>Total de artigos em estado crítico encontrados:</strong> <?= $result->num_rows ?></p>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <h2>Lista de Artigos em Estado Crítico</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nome do Produto</th>
                            <th>Estoque Atual</th>
                            <th>Consumo Médio Mensal</th>
                            <th>Cobertura (Dias)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): 
                            $stock_atual = floatval($row['stock_atual']);
                            $consumo_medio_mensal = floatval($row['consumo_medio_mensal']);
                            $cobertura_dias = ($consumo_medio_mensal > 0) ? round(($stock_atual / $consumo_medio_mensal) * 30) : 0;
                            
                            $row_class = '';
                            $status_class = '';
                            
                            if ($cobertura_dias < 7) {
                                $row_class = 'critical';
                                $status_class = 'status-critical';
                            } elseif ($cobertura_dias < 15) {
                                $row_class = 'warning';
                                $status_class = 'status-warning';
                            } else {
                                $row_class = 'normal';
                                $status_class = 'status-normal';
                            }
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td><span class="status-indicator <?= $status_class ?>"></span></td>
                            <td><?= $row['idproduto'] ?></td>
                            <td><?= htmlspecialchars($row['codbar'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['nomeproduto']) ?></td>
                            <td><?= number_format($stock_atual, 0) ?></td>
                            <td><?= number_format($consumo_medio_mensal, 2) ?></td>
                            <td><?= $cobertura_dias ?> dias</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="warning-box">
                    <h3>Nenhum artigo em estado crítico encontrado</h3>
                    <p>Não foram encontrados artigos com estoque abaixo do consumo médio mensal.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="../consumo_artigos_baixo.php" class="btn">Voltar para a Página Principal</a>
            <a href="verificar_dados.php" class="btn">Ver Diagnóstico Técnico</a>
        </div>

        <div class="info-box">
            <h3>Consulta SQL Utilizada</h3>
            <pre><?= htmlspecialchars($sql) ?></pre>
        </div>
    </div>
</body>
</html>
