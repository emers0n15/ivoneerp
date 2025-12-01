<?php
// Desabilitar o cache para esta página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json; charset=utf-8');

// Arquivo para verificar o estoque em relação ao consumo médio mensal
session_start();
include_once '../../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

// Iniciar log para debug
$log = [];
$log[] = "Iniciando verificação de estoque em relação ao consumo médio";

// Verificar se devemos forçar uma nova verificação (ignorar cache)
$force_check = isset($_POST['force_check']);
$timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : '';

$log[] = "Verificação forçada: " . ($force_check ? 'Sim' : 'Não') . " (timestamp: $timestamp)";

try {
    // Data atual
    $hoje = date('Y-m-d');

    // Período para cálculo do consumo médio (últimos 3 meses por padrão)
    $data_inicio = date('Y-m-d', strtotime('-3 months'));
    $data_fim = $hoje;

    $log[] = "Período para cálculo: $data_inicio até $data_fim";

    // Contadores
    $contador = 0;
    $produtos_criticos = 0;
    
    // Produtos encontrados para detalhar no log
    $produtos_baixos = [];

    // Consultar todos os produtos ativos com seus estoques
    $sqlProdutos = "
        SELECT 
            p.idproduto,
            p.nomeproduto,
            IFNULL(SUM(s.quantidade), 0) AS stock_atual
        FROM produto p
        LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
        GROUP BY p.idproduto
    ";

    $log[] = "Executando consulta de produtos: " . preg_replace('/\s+/', ' ', $sqlProdutos);

    $rsProdutos = mysqli_query($db, $sqlProdutos);

    if (!$rsProdutos) {
        $log[] = "Erro na consulta de produtos: " . mysqli_error($db);
        $resultado = [
            'status' => 'erro',
            'mensagem' => 'Erro ao consultar produtos',
            'erro' => mysqli_error($db),
            'log' => $log
        ];
        echo json_encode($resultado);
        exit;
    }

    $log[] = "Total de produtos encontrados: " . mysqli_num_rows($rsProdutos);

    // Para cada produto, calcular seu consumo médio e verificar se o estoque está baixo
    while ($produto = mysqli_fetch_assoc($rsProdutos)) {
        $produto_id = $produto['idproduto'];
        $nome_produto = $produto['nomeproduto'];
        $stock_atual = $produto['stock_atual'];
        
        // Consultar o consumo do produto no período
        $sqlConsumo = "
            SELECT 
                IFNULL(SUM(e.qtdentrega), 0) AS total_entregas,
                COUNT(DISTINCT DATE_FORMAT(e.datavenda, '%Y-%m')) AS meses
            FROM entrega e
            WHERE e.produtoentrega = $produto_id
                AND e.datavenda BETWEEN '$data_inicio' AND '$data_fim'
        ";
        
        $rsConsumo = mysqli_query($db, $sqlConsumo);
        
        if (!$rsConsumo) {
            $log[] = "Erro na consulta de consumo para produto $produto_id: " . mysqli_error($db);
            continue;
        }
        
        $consumo = mysqli_fetch_assoc($rsConsumo);
        
        // Calcular o consumo médio mensal
        $meses = $consumo['meses'] > 0 ? $consumo['meses'] : 1; // Evitar divisão por zero
        $consumo_medio_mensal = $consumo['total_entregas'] / $meses;

        // Definir limites para notificação
        $percentual_limite = 0.2; // 20% do consumo médio
        $estoque_minimo_absoluto = 5; // Quantia mínima absoluta

        // Verificar se é um produto realmente utilizado (com algum consumo nos últimos 3 meses)
        $tem_consumo = $consumo['total_entregas'] > 0;

        // Mesmo sem consumo recente, produtos com estoque muito baixo (1-2 unidades) devem ser destacados
        $estoque_criticamente_baixo = $stock_atual <= 2;

        // Verificações detalhadas para log
        $criterio1 = $stock_atual <= $consumo_medio_mensal && $consumo_medio_mensal > 0;
        $criterio2 = $stock_atual <= $estoque_minimo_absoluto && $tem_consumo;
        $criterio3 = $consumo_medio_mensal > 0 && $stock_atual <= ($consumo_medio_mensal * $percentual_limite);
        $criterio4 = $estoque_criticamente_baixo; // Novo critério: estoque muito baixo (1-2 unidades)

        // Registrar detalhes do produto para debug
        if ($consumo_medio_mensal > 0 && $stock_atual < $consumo_medio_mensal) {
            $contador++;
            
            // Calcular o percentual do estoque em relação ao consumo médio
            $percentual = ($stock_atual / $consumo_medio_mensal) * 100;
            
            // Determinar se é crítico (menos de 30% do consumo médio)
            if ($percentual <= 30) {
                $produtos_criticos++;
                $tipo_alerta = 'percentual';
            } else {
                $tipo_alerta = 'normal';
            }
            
            // Adicionar produto à lista para uso no log e no frontend
            $produtos_baixos[] = [
                'id' => $produto_id,
                'nome' => $nome_produto,
                'codigobarra' => $produto['codigobarra'] ?? '',
                'stock_atual' => $stock_atual,
                'consumo_medio' => $consumo_medio_mensal,
                'percentual' => $percentual,
                'tipo_alerta' => $tipo_alerta
            ];
            
            if (count($produtos_baixos) <= 10) {
                $log[] = "Produto com estoque baixo: $nome_produto (ID: $produto_id), Stock: $stock_atual, Consumo médio: $consumo_medio_mensal";
            }
        }
    }

    // Armazenar dados em sessão para evitar consultas repetidas
    $_SESSION['produtos_consumo_baixo'] = $produtos_baixos;
    $_SESSION['total_produtos_consumo_baixo'] = $contador;
    $_SESSION['total_produtos_criticos'] = $produtos_criticos;
    $_SESSION['timestamp_produtos_consumo_baixo'] = time(); // Adicionar timestamp para verificar idade dos dados

    $log[] = "Total de produtos com estoque abaixo do consumo médio: $contador";
    $log[] = "Total de produtos em situação crítica: $produtos_criticos";

    // Retornar o resultado como JSON
    $resultado = [
        'status' => 'sucesso',
        'quantidade' => $contador,
        'produtos' => $produtos_baixos,
        'log' => $log
    ];

    echo json_encode($resultado);
} catch (Exception $e) {
    $log[] = "Erro inesperado: " . $e->getMessage();
    $resultado = [
        'status' => 'erro',
        'mensagem' => 'Erro inesperado',
        'erro' => $e->getMessage(),
        'log' => $log
    ];
    echo json_encode($resultado);
}
?>
