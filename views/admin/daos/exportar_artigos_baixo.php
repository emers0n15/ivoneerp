<?php
// Arquivo para exportar produtos com estoque abaixo do consumo médio mensal em formato CSV/Excel
session_start();
include_once '../../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

// Definir cabeçalhos para download do arquivo
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=artigos_estoque_baixo_' . date('Y-m-d') . '.csv');

// Log para depuração
$log = [];
$log[] = "Iniciando exportação de artigos com estoque baixo";

// Criar o manipulador de saída PHP para escrever diretamente para stdout
$output = fopen('php://output', 'w');

// Escrever cabeçalho UTF-8 BOM para Excel reconhecer caracteres especiais
fputs($output, "\xEF\xBB\xBF");

// Escrever cabeçalhos das colunas
fputcsv($output, [
    'ID',
    'Código de Barras',
    'Descrição',
    'Stock Atual',
    'Consumo Médio Mensal',
    'Percentual',
    'Status'
]);

// Data atual
$hoje = date('Y-m-d');

// Período para cálculo do consumo médio (conforme selecionado pelo usuário ou padrão 3 meses)
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 3;
$data_inicio = date('Y-m-d', strtotime("-$periodo months"));
$data_fim = $hoje;

$log[] = "Período para cálculo: $data_inicio até $data_fim";

// Primeiro verificar se temos dados em sessão de uma execução recente
$usar_sessao = false;
if (isset($_SESSION['produtos_consumo_baixo']) && 
    isset($_SESSION['timestamp_produtos_consumo_baixo']) && 
    (time() - $_SESSION['timestamp_produtos_consumo_baixo']) < 300) { // 5 minutos
    $log[] = "Usando dados da sessão (dados recentes)";
    $usar_sessao = true;
}

if ($usar_sessao && isset($_SESSION['produtos_consumo_baixo'])) {
    // Usar os dados da sessão
    $produtos = $_SESSION['produtos_consumo_baixo'];
    
    foreach ($produtos as $produto) {
        $idproduto = $produto['id'];
        $codigobarra = $produto['codigobarra'] ?? 'N/A';
        $nomeproduto = $produto['nome'];
        $stock_atual = $produto['stock_atual'];
        $consumo_medio_mensal = $produto['consumo_medio'];
        
        // Determinar o status baseado na diferença entre estoque e consumo médio
        $percentual = $consumo_medio_mensal > 0 ? ($stock_atual / $consumo_medio_mensal) * 100 : 0;
        $percentual_formatado = number_format($percentual, 2) . '%';
        
        if ($percentual <= 30) {
            $status = 'Crítico';
        } elseif ($percentual <= 70) {
            $status = 'Baixo';
        } else {
            $status = 'Adequado';
        }
        
        // Escrever linha no CSV
        fputcsv($output, [
            $idproduto,
            $codigobarra,
            $nomeproduto,
            $stock_atual,
            number_format($consumo_medio_mensal, 2),
            $percentual_formatado,
            $status
        ]);
    }
} else {
    // Consultar dados do banco de dados
    $log[] = "Consultando dados do banco de dados";
    
    // Consulta para obter produtos com estoque abaixo do consumo médio
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
                    AND e.datavenda BETWEEN ? AND ?
            ) AS consumo_medio_mensal
        FROM produto p
        LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
        WHERE p.estado = 'ativo'
        GROUP BY p.idproduto
        HAVING stock_atual <= consumo_medio_mensal AND consumo_medio_mensal > 0
        ORDER BY stock_atual / consumo_medio_mensal ASC
    ";
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $data_inicio, $data_fim);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $idproduto = $row['idproduto'];
            $codigobarra = $row['codigobarra'];
            $nomeproduto = $row['nomeproduto'];
            $stock_atual = $row['stock_atual'];
            $consumo_medio_mensal = $row['consumo_medio_mensal'];
            
            // Determinar o status baseado na diferença entre estoque e consumo médio
            $percentual = $consumo_medio_mensal > 0 ? ($stock_atual / $consumo_medio_mensal) * 100 : 0;
            $percentual_formatado = number_format($percentual, 2) . '%';
            
            if ($percentual <= 30) {
                $status = 'Crítico';
            } elseif ($percentual <= 70) {
                $status = 'Baixo';
            } else {
                $status = 'Adequado';
            }
            
            // Escrever linha no CSV
            fputcsv($output, [
                $idproduto,
                $codigobarra,
                $nomeproduto,
                $stock_atual,
                number_format($consumo_medio_mensal, 2),
                $percentual_formatado,
                $status
            ]);
        }
    } catch (Exception $e) {
        // Em caso de erro, escrever mensagem de erro no arquivo
        fputcsv($output, ['Erro ao exportar dados: ' . $e->getMessage()]);
    }
}

// Fechar o manipulador de saída
fclose($output);
exit;
?>
