<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sessão expirada']);
    exit;
}

header('Content-Type: application/json');

include '../../conexao/index.php';
require_once __DIR__ . '/../includes/documentos_union.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $length = $length > 0 ? $length : 10;
    $searchValue = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';
    $tipoFiltro = $_GET['tipo'] ?? 'all';
    $statusFiltro = $_GET['status'] ?? 'all';
    $dataInicio = $_GET['data_inicio'] ?? null;
    $dataFim = $_GET['data_fim'] ?? null;

    $orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 5;
    $orderDir = isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';

    $columnMap = [
        0 => 'tipo',
        1 => 'documento',
        2 => 'cliente',
        3 => 'total',
        4 => 'status',
        5 => 'data_emissao',
        6 => 'utilizador'
    ];
    $orderColumn = $columnMap[$orderColumnIndex] ?? 'data_emissao';

    $unionSql = getDocumentosUnionSql();
    $baseQuery = "FROM ({$unionSql}) AS documentos";

    $whereParts = [];
    $types = '';
    $params = [];

    if ($tipoFiltro !== 'all' && $tipoFiltro !== '') {
        $whereParts[] = 'documentos.tipo = ?';
        $types .= 's';
        $params[] = $tipoFiltro;
    }

    if ($statusFiltro !== 'all' && $statusFiltro !== '') {
        $whereParts[] = 'documentos.status = ?';
        $types .= 's';
        $params[] = $statusFiltro;
    }

    if (!empty($dataInicio)) {
        $whereParts[] = 'DATE(documentos.data_emissao) >= ?';
        $types .= 's';
        $params[] = $dataInicio;
    }

    if (!empty($dataFim)) {
        $whereParts[] = 'DATE(documentos.data_emissao) <= ?';
        $types .= 's';
        $params[] = $dataFim;
    }

    if ($searchValue !== '') {
        $whereParts[] = '(documentos.documento LIKE ? OR documentos.cliente LIKE ? OR documentos.utilizador LIKE ?)';
        $types .= 'sss';
        $like = '%' . $searchValue . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $whereSql = $whereParts ? ' WHERE ' . implode(' AND ', $whereParts) : '';

    $totalSql = "SELECT COUNT(*) AS total FROM ({$unionSql}) AS documentos";
    $totalResult = $db->query($totalSql);
    $recordsTotal = (int) ($totalResult->fetch_assoc()['total'] ?? 0);

    $recordsFiltered = $recordsTotal;
    if ($whereSql !== '') {
        $filteredSql = "SELECT COUNT(*) AS total {$baseQuery} {$whereSql}";
        $filteredStmt = $db->prepare($filteredSql);
        if ($types !== '') {
            $filteredStmt->bind_param($types, ...$params);
        }
        $filteredStmt->execute();
        $filteredResult = $filteredStmt->get_result();
        $recordsFiltered = (int) ($filteredResult->fetch_assoc()['total'] ?? 0);
        $filteredStmt->close();
    }

    $dataSql = "SELECT * {$baseQuery} {$whereSql} ORDER BY {$orderColumn} {$orderDir} LIMIT ? OFFSET ?";
    $dataTypes = $types . 'ii';
    $dataParams = $params;
    $dataParams[] = $length;
    $dataParams[] = $start;

    $dataStmt = $db->prepare($dataSql);
    if ($types !== '') {
        $dataStmt->bind_param($dataTypes, ...$dataParams);
    } else {
        $dataStmt->bind_param('ii', $length, $start);
    }
    $dataStmt->execute();
    $result = $dataStmt->get_result();

    $tipoLabels = [
        'factura' => 'Factura',
        'nota_credito' => 'Nota de Crédito',
        'nota_debito' => 'Nota de Débito',
        'cotacao' => 'Cotação',
        'recibo' => 'Recibo',
        'venda_dinheiro' => 'Venda a Dinheiro',
        'devolucao' => 'Devolução',
        'ordem_compra' => 'Ordem de Compra'
    ];

    $statusColors = [
        'paga' => 'badge-success',
        'pendente' => 'badge-warning',
        'emitido' => 'badge-primary',
        'aberta' => 'badge-info',
        'devolvido' => 'badge-danger'
    ];

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $tipo = $row['tipo'];
        $status = $row['status'];
        $total = isset($row['total']) ? (float) $row['total'] : 0.0;
        $dataBruta = $row['data_emissao'];
        $dataFormatada = $dataBruta ? date('d/m/Y H:i', strtotime($dataBruta)) : '-';
        $rota = $row['rota'] ?? null;
        $parametro = $row['parametro'] ?? null;

        $link = '';
        if ($rota && $parametro) {
            $href = sprintf('../%s?%s=%d', $rota, $parametro, $row['registro_id']);
            $link = '<a href="' . $href . '" target="_blank" class="btn btn-sm btn-outline-primary" title="Abrir PDF"><i class="fa fa-file-pdf-o"></i></a>';
        }

        $statusClass = $statusColors[$status] ?? 'badge-secondary';
        $rows[] = [
            'tipo_label' => $tipoLabels[$tipo] ?? strtoupper($tipo),
            'documento' => $row['documento'],
            'cliente' => $row['cliente'],
            'total_fmt' => number_format($total, 2, ',', '.'),
            'status_badge' => '<span class="badge ' . $statusClass . '">' . ucfirst($status) . '</span>',
            'data_fmt' => $dataFormatada,
            'utilizador' => $row['utilizador'],
            'acoes' => $link
        ];
    }

    $dataStmt->close();

    echo json_encode([
        'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $rows
    ]);
} catch (Throwable $th) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro ao carregar o explorador de documentos',
        'details' => $th->getMessage()
    ]);
}

