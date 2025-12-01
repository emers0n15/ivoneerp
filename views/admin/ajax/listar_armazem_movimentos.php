<?php
session_start();
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

// Obter filtros
$tipo_movimento = isset($_POST['tipo_movimento']) ? mysqli_real_escape_string($db, $_POST['tipo_movimento']) : '';
$data_inicial = isset($_POST['data_inicial']) && !empty($_POST['data_inicial']) ? mysqli_real_escape_string($db, $_POST['data_inicial']) : '';
$data_final = isset($_POST['data_final']) && !empty($_POST['data_final']) ? mysqli_real_escape_string($db, $_POST['data_final']) : '';

// Configuração para DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($db, $_POST['search']['value']) : '';

// Construir consulta base
$sqlBase = "FROM armazem_movimentos AS m
            INNER JOIN produto AS p ON m.produto_id = p.idproduto
            INNER JOIN armazem_stock AS s ON m.stock_id = s.id
            INNER JOIN users AS u ON m.usuario_id = u.id
            WHERE m.armazem_id = $armazem_id";

// Adicionar logs para diagnóstico
error_log("Listando movimentos para armazem_id: $armazem_id");

// Adicionar filtros
if (!empty($tipo_movimento)) {
    $sqlBase .= " AND m.tipo_movimento = '$tipo_movimento'";
}

if (!empty($data_inicial)) {
    $sqlBase .= " AND DATE(m.data_movimento) >= '$data_inicial'";
}

if (!empty($data_final)) {
    $sqlBase .= " AND DATE(m.data_movimento) <= '$data_final'";
}

// Adicionar filtro de busca se fornecido
if (!empty($search)) {
    $sqlBase .= " AND (p.nomeproduto LIKE '%$search%' OR s.lote LIKE '%$search%' OR u.nomeUsuario LIKE '%$search%' OR m.observacao LIKE '%$search%')";
}

// Consulta para contar o total de registros
$sqlCount = "SELECT COUNT(*) as total $sqlBase";
$resultCount = mysqli_query($db, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalRecords = $rowCount['total'];
$totalRecordsFiltered = $totalRecords; // Não usamos filtragem separada aqui

// Consulta principal para obter os dados
$sql = "SELECT 
            m.id, 
            m.data_movimento,
            p.nomeproduto AS produto, 
            s.lote, 
            m.tipo_movimento,
            m.quantidade,
            u.nome AS usuario,
            m.observacao
        $sqlBase
        ORDER BY m.data_movimento DESC
        LIMIT $start, $length";

// Log da consulta para diagnóstico
error_log("SQL de movimentos: $sql");

$resultado = mysqli_query($db, $sql);

if (!$resultado) {
    error_log("Erro na consulta de movimentos: " . mysqli_error($db));
    
    // Tentar simplificar a consulta para diagnóstico
    $sql_simples = "SELECT 
                    m.id, 
                    m.data_movimento,
                    m.tipo_movimento,
                    m.quantidade
                FROM armazem_movimentos AS m 
                WHERE m.armazem_id = $armazem_id
                LIMIT $start, $length";
                
    error_log("Tentando consulta simplificada: $sql_simples");
    $resultado = mysqli_query($db, $sql_simples);
    
    if (!$resultado) {
        error_log("Erro na consulta simplificada: " . mysqli_error($db));
    }
}

$data = [];
if ($resultado && mysqli_num_rows($resultado) > 0) {
    while ($movimento = mysqli_fetch_assoc($resultado)) {
        // Formatação do tipo de movimento
        $tipo_badge = '';
        switch ($movimento['tipo_movimento']) {
            case 'entrada':
                $tipo_badge = '<span class="tipo-movimento entrada">Entrada</span>';
                break;
            case 'saida':
                $tipo_badge = '<span class="tipo-movimento saida">Saída</span>';
                break;
            case 'transferencia':
                $tipo_badge = '<span class="tipo-movimento transferencia">Transferência</span>';
                break;
            default:
                $tipo_badge = $movimento['tipo_movimento'];
        }
        
        $data[] = [
            'id' => $movimento['id'],
            'data' => date('d/m/Y H:i', strtotime($movimento['data_movimento'])),
            'produto' => $movimento['produto'] ?? '',
            'lote' => $movimento['lote'] ?? '',
            'tipo' => $tipo_badge,
            'quantidade' => number_format($movimento['quantidade'], 0, ',', '.'),
            'usuario' => $movimento['usuario'] ?? '',
            'observacao' => $movimento['observacao'] ?? ''
        ];
    }
}

// Formatação da resposta para o DataTables
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFiltered,
    "data" => $data
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>
