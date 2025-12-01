<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../../conexao/index.php';

// Definir a codificação UTF-8 para garantir que os caracteres especiais sejam tratados corretamente
mysqli_set_charset($db, "utf8");

if (!isset($_SESSION['idUsuario'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Verificar se o ID do armazém foi fornecido
if (!isset($_POST['armazem_id']) || empty($_POST['armazem_id'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'ID do armazém não fornecido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$armazem_id = intval($_POST['armazem_id']);
$incluir_vencidos = isset($_POST['incluir_vencidos']) ? filter_var($_POST['incluir_vencidos'], FILTER_VALIDATE_BOOLEAN) : false;

// Configuração para DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = mysqli_real_escape_string($db, $_POST['search']['value']);
$search = str_replace(['%', '_'], ['\%', '\_'], $search); // evitar conflitos em LIKE


// Construir consulta base
$sqlBase = "FROM armazem_stock AS s
            INNER JOIN produto AS p ON s.produto_id = p.idproduto
            WHERE s.armazem_id = $armazem_id";

// Debug de SQL
error_log("Verificando problemas na consulta - armazem_id: $armazem_id");

// Adicionar filtro de busca se fornecido
if (!empty($search)) {
    $sqlBase .= " AND (p.nomeproduto LIKE '%$search%' OR s.lote LIKE '%$search%')";
}

// Adicionar condição para filtrar produtos vencidos ou a vencer
$hoje = date('Y-m-d');
if ($incluir_vencidos) {
    // Incluir apenas produtos com prazo de validade e que estejam vencidos ou a vencer em 30 dias
    $data_limite = date('Y-m-d', strtotime('+120 days'));
    $sqlBase .= " AND s.prazo IS NOT NULL AND s.prazo <= '$data_limite'";
} else {
    // Na aba normal, mostrar todos os produtos, incluindo os sem prazo
    // Removemos a restrição de prazo para mostrar todos os produtos em estoque
}

// Consulta para contar o total de registros
$sqlCount = "SELECT COUNT(*) as total $sqlBase";
$resultCount = mysqli_query($db, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalRecords = $rowCount['total'];
$totalRecordsFiltered = $totalRecords; // Não usamos filtragem separada aqui

// Consulta principal para obter os dados
$sql = "SELECT 
            s.id, 
            p.nomeproduto AS produto, 
            s.lote, 
            s.quantidade, 
            s.prazo, 
            s.data_entrada,
            s.estado,
            s.produto_id,
            p.codbar AS codigo,
            CASE 
                WHEN s.prazo IS NULL THEN NULL
                ELSE DATEDIFF(s.prazo, '$hoje')
            END AS dias_restantes
        $sqlBase
        ORDER BY ";

// Se estamos na aba de vencidos, ordenar por prazo (mais próximos primeiro)
if ($incluir_vencidos) {
    $sql .= "s.prazo ASC";
} else {
    $sql .= "s.data_entrada DESC";
}

// Adicionar debugging para mostrar a consulta SQL completa
error_log("SQL completo: $sql");

$sql .= " LIMIT $start, $length";

$resultado = mysqli_query($db, $sql);
    
if (!$resultado) {
    error_log("Erro na consulta SQL: " . mysqli_error($db));
    error_log("SQL executado: " . $sql);
    
    // Tentar uma consulta mais simples para diagnosticar
    $sqlSimples = "SELECT s.id, p.nomeproduto AS produto, s.lote, s.quantidade, s.estado
                 FROM armazem_stock AS s
                 INNER JOIN produto AS p ON s.produto_id = p.idproduto
                 WHERE s.armazem_id = $armazem_id
                 LIMIT $start, $length";
    
    error_log("Tentando consulta simplificada: $sqlSimples");
    $resultadoSimples = mysqli_query($db, $sqlSimples);
    
    if ($resultadoSimples) {
        error_log("Consulta simplificada funcionou! Registros: " . mysqli_num_rows($resultadoSimples));
        $resultado = $resultadoSimples;
    }
}

$data = [];
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $total_registros = mysqli_num_rows($resultado);
    error_log("Total de registros encontrados: $total_registros");
    
    while ($stock = mysqli_fetch_assoc($resultado)) {
        error_log("Processando item de stock ID: " . $stock['id']);
        $item = [];
        
        // Campos comuns para ambas as abas
        $item['id'] = $stock['id'] ?? '';
        $item['produto'] = $stock['produto'] ?? '';
        $item['lote'] = $stock['lote'] ?? '';
        $item['quantidade'] = $stock['quantidade'] ?? 0;
        
        // Formatação do prazo (usando operador de coalescência nula para evitar erros)
        $item['prazo'] = isset($stock['prazo']) && $stock['prazo'] ? date('d/m/Y', strtotime($stock['prazo'])) : 'N/A';
        
        // Data de entrada formatada
        $item['data_entrada'] = isset($stock['data_entrada']) && $stock['data_entrada'] ? 
            date('d/m/Y H:i', strtotime($stock['data_entrada'])) : date('d/m/Y H:i');
        
        // Estado formatado com badge
        $estado = $stock['estado'] ?? 'ativo';
        $estado_badge = $estado == 'ativo' ? 
            '<span class="status-badge bg-success">Ativo</span>' : 
            '<span class="status-badge bg-danger">Inativo</span>';
        $item['estado'] = $estado_badge;
        
        // Código de barras
        $item['codigo'] = $stock['codigo'] ?? '';
        
        // Campos específicos para a aba de vencidos
        if ($incluir_vencidos) {
            $dias_restantes = $stock['dias_restantes'] ?? null;
            $item['dias_restantes'] = $dias_restantes !== null ? $dias_restantes : 'N/A';
            
            // Status baseado nos dias restantes
            if ($dias_restantes === null) {
                $item['status'] = '<span class="status-badge bg-secondary">Sem prazo</span>';
            } elseif ($dias_restantes < 0) {
                $item['status'] = '<span class="status-badge bg-danger">Vencido há ' . abs($dias_restantes) . ' dias</span>';
            } elseif ($dias_restantes == 0) {
                $item['status'] = '<span class="status-badge bg-danger">Vence hoje</span>';
            } elseif ($dias_restantes <= 7) {
                $item['status'] = '<span class="status-badge bg-warning">Vence em ' . $dias_restantes . ' dias</span>';
            } else {
                $item['status'] = '<span class="status-badge bg-success">Vence em ' . $dias_restantes . ' dias</span>';
            }
        }
        
        // Ações
        $acoes = '<div class="dropdown dropdown-action">
                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">';
        
        if ($stock['quantidade'] > 0) {
            $acoes .= '<a class="dropdown-item transferirStock" href="#" data-id="' . $stock['id'] . '" data-produto="' . $stock['produto_id'] . '" data-nome="' . $stock['produto'] . '" data-lote="' . $stock['lote'] . '" data-quantidade="' . $stock['quantidade'] . '"><i class="fa fa-exchange m-r-5"></i> Transferir para Prateleiras</a>';
        }
        
        $acoes .= '</div></div>';
        
        $item['acoes'] = $acoes;
        
        $data[] = $item;
    }
}

// Formatação da resposta para o DataTables
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFiltered,
    "data" => $data
];

// Log para verificar se a resposta está sendo construída corretamente
error_log("Resposta DataTables: recordsTotal=$totalRecords, recordsFiltered=$totalRecordsFiltered, itens=" . count($data));

// Saída como JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>
