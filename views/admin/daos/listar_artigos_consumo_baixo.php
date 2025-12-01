<?php
// Arquivo para listar produtos com estoque abaixo do consumo médio mensal
session_start();
include_once '../../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

// Log de depuração
$log = [];
$log[] = "Iniciando listar_artigos_consumo_baixo.php";

// Configurar cabeçalho para JSON
header('Content-Type: application/json');

// Parâmetros de paginação e pesquisa
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;

$log[] = "Parâmetros: start=$start, length=$length, orderColumnIndex=$orderColumnIndex, orderDir=$orderDir, search=$searchValue, draw=$draw";

// Mapear os nomes das colunas para o banco de dados
$columns = [
    0 => 'p.idproduto',
    1 => 'p.codbar',
    2 => 'p.nomeproduto',
    3 => 'stock_atual',
    4 => 'consumo_medio_mensal',
    5 => 'status'
];

$orderColumn = $columns[$orderColumnIndex];

// Data atual
$hoje = date('Y-m-d');

// Período para cálculo do consumo médio (últimos 3 meses por padrão)
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 3;
$data_inicio = date('Y-m-d', strtotime("-$periodo months"));
$data_fim = $hoje;

$log[] = "Período para cálculo: $data_inicio até $data_fim (Últimos $periodo meses)";

// Primeiro verificar se temos dados em sessão de uma execução recente
$usar_sessao = false;
if (isset($_SESSION['produtos_consumo_baixo']) && 
    isset($_SESSION['timestamp_produtos_consumo_baixo']) && 
    (time() - $_SESSION['timestamp_produtos_consumo_baixo']) < 300) { // 5 minutos
    $log[] = "Usando dados da sessão (dados recentes)";
    $usar_sessao = true;
}

// Consultar total de registros sem filtro
$totalRecords = 0;
$totalFiltered = 0;
$data = [];

if ($usar_sessao && isset($_SESSION['produtos_consumo_baixo'])) {
    // Usar os dados da sessão
    $produtos_session = $_SESSION['produtos_consumo_baixo'];
    $totalRecords = count($produtos_session);
    
    // Aplicar filtro de pesquisa nos dados da sessão
    $produtos_filtrados = [];
    if (!empty($searchValue)) {
        foreach ($produtos_session as $produto) {
            if (stripos($produto['nome'], $searchValue) !== false ||
                stripos($produto['id'], $searchValue) !== false) {
                $produtos_filtrados[] = $produto;
            }
        }
    } else {
        $produtos_filtrados = $produtos_session;
    }
    
    $totalFiltered = count($produtos_filtrados);
    
    // Aplicar ordenação
    usort($produtos_filtrados, function($a, $b) use ($orderColumn, $orderDir) {
        $coluna = str_replace('p.', '', $orderColumn);
        $coluna = ($coluna == 'idproduto') ? 'id' : 
                 (($coluna == 'nomeproduto') ? 'nome' : $coluna);
        
        $va = $a[$coluna] ?? 0;
        $vb = $b[$coluna] ?? 0;
        
        if ($va == $vb) return 0;
        
        $comparacao = ($va < $vb) ? -1 : 1;
        return ($orderDir === 'asc') ? $comparacao : -$comparacao;
    });
    
    // Aplicar paginação
    $produtos_paginados = array_slice($produtos_filtrados, $start, $length);
    
    // Preparar os dados para DataTables
    foreach ($produtos_paginados as $row) {
        $idproduto = $row['id'];
        $codigobarra = $row['codbar'] ?? 'N/A';
        $nomeproduto = $row['nome'];
        $stock_atual = $row['stock_atual'];
        $consumo_medio_mensal = $row['consumo_medio'];
        
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
            "codigobarra" => $codigobarra,
            "nomeproduto" => $nomeproduto,
            "stock_atual" => number_format($stock_atual, 0),
            "consumo_medio_mensal" => number_format($consumo_medio_mensal, 0),
            "status" => $status,
            "acoes" => $acoes
        ];
    }
} else {
    // Consultar dados do banco de dados
    $log[] = "Consultando dados do banco de dados";
    
    // Consultar total de registros sem filtro
    $sqlCount = "SELECT COUNT(DISTINCT p.idproduto) as total FROM produto p";
    $resultCount = $db->query($sqlCount);
    $totalRecords = $resultCount->fetch_assoc()['total'];
    
    // Consulta para obter produtos com estoque abaixo do consumo médio
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
                    AND e.datavenda BETWEEN ? AND ?
            ) AS consumo_medio_mensal
        FROM produto p
        LEFT JOIN stock s ON p.idproduto = s.produto_id AND s.estado = 'ativo'
        " . (!empty($searchValue) ? "AND (p.nomeproduto LIKE ? OR p.codbar LIKE ?)" : "") . "
        GROUP BY p.idproduto
        HAVING stock_atual <= consumo_medio_mensal AND consumo_medio_mensal > 0
        ORDER BY $orderColumn $orderDir
        LIMIT ?, ?
    ";
    
    try {
        $stmt = $db->prepare($sql);
        
        if (!empty($searchValue)) {
            $searchValue = '%' . $searchValue . '%';
            $stmt->bind_param("ssssii", $data_inicio, $data_fim, $searchValue, $searchValue, $start, $length);
        } else {
            $stmt->bind_param("ssii", $data_inicio, $data_fim, $start, $length);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Consultar total de registros com filtro
        $sqlFilteredCount = "
            SELECT COUNT(*) as total FROM (
                SELECT 
                    p.idproduto,
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
                " . (!empty($searchValue) ? "AND (p.nomeproduto LIKE ? OR p.codbar LIKE ?)" : "") . "
                GROUP BY p.idproduto
                HAVING stock_atual <= consumo_medio_mensal AND consumo_medio_mensal > 0
            ) as filtered
        ";
        
        $stmt = $db->prepare($sqlFilteredCount);
        
        if (!empty($searchValue)) {
            $stmt->bind_param("ssss", $data_inicio, $data_fim, $searchValue, $searchValue);
        } else {
            $stmt->bind_param("ss", $data_inicio, $data_fim);
        }
        
        $stmt->execute();
        $filteredResult = $stmt->get_result();
        $totalFiltered = $filteredResult->fetch_assoc()['total'];
        
        // Preparar os dados para DataTables
        while ($row = $result->fetch_assoc()) {
            $idproduto = $row['idproduto'];
            $codigobarra = $row['codbar'];
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
                        <a class="dropdown-item" href="../consumo_medio.php?id='.$idproduto.'"><i class="fa fa-bar-chart m-r-5"></i> Ver Consumo</a>
                        <a class="dropdown-item" href="../armazem.php?produto_id='.$idproduto.'"><i class="fa fa-cubes m-r-5"></i> Ver Stock</a>
                    </div>
                </div>
            ';
            
            $data[] = [
                "idproduto" => $idproduto,
                "codigobarra" => $codigobarra,
                "nomeproduto" => $nomeproduto,
                "stock_atual" => number_format($stock_atual, 0),
                "consumo_medio_mensal" => number_format($consumo_medio_mensal, 0),
                "status" => $status,
                "acoes" => $acoes
            ];
        }
    } catch (Exception $e) {
        $log[] = "Erro na consulta: " . $e->getMessage();
        // Em caso de erro, retornar resposta vazia
        $data = [];
        $totalFiltered = 0;
    }
}

// Responder com dados no formato JSON
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data,
    "log" => $log
];

echo json_encode($response);
?>
