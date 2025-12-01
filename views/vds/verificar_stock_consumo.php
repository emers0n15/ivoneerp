<?php
// Arquivo para verificar o estoque em relação ao consumo médio mensal
// Este arquivo deve ser executado sempre que a página principal de vendas for carregada

session_start();
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

// Data atual
$hoje = date('Y-m-d');

// Período para cálculo do consumo médio (últimos 3 meses por padrão)
$data_inicio = date('Y-m-d', strtotime('-3 months'));
$data_fim = $hoje;

// Array para armazenar produtos com estoque baixo
$produtos_baixo_estoque = array();

// Consultar todos os produtos ativos com seus estoques
$sqlProdutos = "
    SELECT 
        p.idproduto,
        p.nomeproduto,
        IFNULL(SUM(s.quantidade), 0) AS stock_atual
    FROM produto p
    LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
    WHERE p.estado = 'ativo'
    GROUP BY p.idproduto
";

$rsProdutos = mysqli_query($db, $sqlProdutos);

// Para cada produto, calcular seu consumo médio e verificar se o estoque está baixo
while ($produto = mysqli_fetch_assoc($rsProdutos)) {
    $produto_id = $produto['idproduto'];
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
    $consumo = mysqli_fetch_assoc($rsConsumo);
    
    // Calcular o consumo médio mensal
    $meses = $consumo['meses'] > 0 ? $consumo['meses'] : 1; // Evitar divisão por zero
    $consumo_medio_mensal = $consumo['total_entregas'] / $meses;
    
    // Verificar se o estoque está abaixo do consumo médio mensal
    if ($stock_atual <= $consumo_medio_mensal && $consumo_medio_mensal > 0) {
        // Adicionar ao array de produtos com estoque baixo
        $produtos_baixo_estoque[] = array(
            'id' => $produto_id,
            'nome' => $produto['nomeproduto'],
            'stock_atual' => $stock_atual,
            'consumo_medio' => $consumo_medio_mensal
        );
    }
}

// Guardar a lista de produtos com estoque baixo na sessão para exibição na interface
$_SESSION['produtos_baixo_estoque'] = $produtos_baixo_estoque;
$_SESSION['total_produtos_baixo_estoque'] = count($produtos_baixo_estoque);

// Fechar conexão
mysqli_close($db);
?>
