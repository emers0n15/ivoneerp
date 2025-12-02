<?php
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit;
}

if($_SESSION['categoriaUsuario'] != "recepcao"){
    header("location:../admin/");
    exit;
}

include '../../conexao/index.php';

/**
 * Verifica se uma tabela existe, mantendo cache local para evitar múltiplas consultas.
 */
function tabelaExiste($db, $tabela) {
    static $cache = [];
    if(isset($cache[$tabela])) {
        return $cache[$tabela];
    }

    $tabela_segura = mysqli_real_escape_string($db, $tabela);
    $sql = "SHOW TABLES LIKE '$tabela_segura'";
    $resultado = mysqli_query($db, $sql);
    $cache[$tabela] = ($resultado && mysqli_num_rows($resultado) > 0);
    return $cache[$tabela];
}

function formatarNumeroDoc($prefixo, $serie, $numero) {
    if(!$numero) {
        return $prefixo;
    }
    $serie = $serie ?: date('Y');
    return sprintf('%s#%s/%s', strtoupper($prefixo), $serie, $numero);
}

function formatarMoeda($valor) {
    return number_format(floatval($valor ?? 0), 2, ',', '.') . ' MT';
}

function somarValores($registos, $campo = 'valor') {
    $total = 0;
    foreach($registos as $item) {
        $total += floatval($item[$campo] ?? 0);
    }
    return $total;
}

function calcularResumoFatura($db, $fatura_id, $valor_total) {
    $totais = [
        'pago' => 0,
        'nc'   => 0,
        'nd'   => 0,
        'dv'   => 0
    ];

    if(tabelaExiste($db, 'pagamentos_recepcao')) {
        $sql = "SELECT COALESCE(SUM(valor_pago), 0) as total 
                FROM pagamentos_recepcao 
                WHERE factura_recepcao_id = $fatura_id 
                   OR (fatura_id = $fatura_id AND factura_recepcao_id IS NULL)";
        $rs = mysqli_query($db, $sql);
        if($rs) {
            $dados = mysqli_fetch_array($rs);
            $totais['pago'] = floatval($dados['total'] ?? 0);
        }
    }

    if(tabelaExiste($db, 'nota_credito_recepcao')) {
        $sql = "SELECT COALESCE(SUM(valor), 0) as total 
                FROM nota_credito_recepcao 
                WHERE factura_recepcao_id = $fatura_id";
        $rs = mysqli_query($db, $sql);
        if($rs) {
            $dados = mysqli_fetch_array($rs);
            $totais['nc'] = floatval($dados['total'] ?? 0);
        }
    }

    if(tabelaExiste($db, 'nota_debito_recepcao')) {
        $sql = "SELECT COALESCE(SUM(valor), 0) as total 
                FROM nota_debito_recepcao 
                WHERE factura_recepcao_id = $fatura_id";
        $rs = mysqli_query($db, $sql);
        if($rs) {
            $dados = mysqli_fetch_array($rs);
            $totais['nd'] = floatval($dados['total'] ?? 0);
        }
    }

    if(tabelaExiste($db, 'devolucao_recepcao')) {
        $sql = "SELECT COALESCE(SUM(valor), 0) as total 
                FROM devolucao_recepcao 
                WHERE factura_recepcao_id = $fatura_id";
        $rs = mysqli_query($db, $sql);
        if($rs) {
            $dados = mysqli_fetch_array($rs);
            $totais['dv'] = floatval($dados['total'] ?? 0);
        }
    }

    $valor_disponivel = max(0, $valor_total + $totais['nd'] - $totais['nc'] - $totais['dv']);

    $status = 'Pendente';
    $classe = 'badge badge-warning';

    if($totais['dv'] > 0 && $totais['dv'] >= $valor_total) {
        $status = 'Devolvida';
        $classe = 'badge badge-secondary';
    } elseif($totais['pago'] >= $valor_disponivel && $valor_disponivel > 0) {
        $status = 'Paga';
        $classe = 'badge badge-success';
    } elseif($totais['pago'] > 0) {
        $status = 'Parcial';
        $classe = 'badge badge-info';
    }

    return [
        'status' => $status,
        'classe' => $classe,
        'total_pago' => $totais['pago'],
        'valor_disponivel' => $valor_disponivel
    ];
}

$faturas = [];
if(tabelaExiste($db, 'factura_recepcao')) {
    $sql = "SELECT f.*, 
                   p.nome, p.apelido, p.numero_processo, 
                   e.nome AS empresa_nome
            FROM factura_recepcao f
            LEFT JOIN pacientes p ON f.paciente = p.id
            LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
            ORDER BY f.dataa DESC, f.id DESC";
    $rs = mysqli_query($db, $sql);
    if($rs) {
        while($linha = mysqli_fetch_assoc($rs)) {
            $linha['detalhes'] = calcularResumoFatura($db, intval($linha['id']), floatval($linha['valor'] ?? 0));
            $faturas[] = $linha;
        }
    }
}

$vendas = [];
if(tabelaExiste($db, 'venda_dinheiro_servico')) {
    $sql = "SELECT v.*, 
                   p.nome, p.apelido, p.numero_processo,
                   e.nome AS empresa_nome
            FROM venda_dinheiro_servico v
            LEFT JOIN pacientes p ON v.paciente = p.id
            LEFT JOIN empresas_seguros e ON v.empresa_id = e.id
            ORDER BY v.dataa DESC, v.id DESC";
    $rs = mysqli_query($db, $sql);
    if($rs) {
        while($linha = mysqli_fetch_assoc($rs)) {
            $vendas[] = $linha;
        }
    }
}

$cotacoes = [];
if(tabelaExiste($db, 'cotacao_recepcao')) {
    $sql = "SELECT c.*,
                   p.nome, p.apelido, p.numero_processo,
                   e.nome AS empresa_nome
            FROM cotacao_recepcao c
            LEFT JOIN pacientes p ON c.paciente = p.id
            LEFT JOIN empresas_seguros e ON c.empresa_id = e.id
            ORDER BY c.dataa DESC, c.id DESC";
    $rs = mysqli_query($db, $sql);
    if($rs) {
        while($linha = mysqli_fetch_assoc($rs)) {
            $cotacoes[] = $linha;
        }
    }
}

$notasCredito = [];
if(tabelaExiste($db, 'nota_credito_recepcao')) {
    $sql = "SELECT nc.*,
                   f.n_doc AS fatura_n_doc,
                   f.serie AS fatura_serie,
                   p.nome, p.apelido, p.numero_processo,
                   e.nome AS empresa_nome
            FROM nota_credito_recepcao nc
            LEFT JOIN factura_recepcao f ON nc.factura_recepcao_id = f.id
            LEFT JOIN pacientes p ON nc.paciente = p.id
            LEFT JOIN empresas_seguros e ON nc.empresa_id = e.id
            ORDER BY nc.dataa DESC, nc.id DESC";
    $rs = mysqli_query($db, $sql);
    if($rs) {
        while($linha = mysqli_fetch_assoc($rs)) {
            $notasCredito[] = $linha;
        }
    }
}

$notasDebito = [];
if(tabelaExiste($db, 'nota_debito_recepcao')) {
    $sql = "SELECT nd.*,
                   f.n_doc AS fatura_n_doc,
                   f.serie AS fatura_serie,
                   p.nome, p.apelido, p.numero_processo,
                   e.nome AS empresa_nome
            FROM nota_debito_recepcao nd
            LEFT JOIN factura_recepcao f ON nd.factura_recepcao_id = f.id
            LEFT JOIN pacientes p ON nd.paciente = p.id
            LEFT JOIN empresas_seguros e ON nd.empresa_id = e.id
            ORDER BY nd.dataa DESC, nd.id DESC";
    $rs = mysqli_query($db, $sql);
    if($rs) {
        while($linha = mysqli_fetch_assoc($rs)) {
            $notasDebito[] = $linha;
        }
    }
}

$devolucoes = [];
if(tabelaExiste($db, 'devolucao_recepcao')) {
    $sql = "SELECT dv.*,
                   f.n_doc AS fatura_n_doc,
                   f.serie AS fatura_serie,
                   p.nome, p.apelido, p.numero_processo,
                   e.nome AS empresa_nome
            FROM devolucao_recepcao dv
            LEFT JOIN factura_recepcao f ON dv.factura_recepcao_id = f.id
            LEFT JOIN pacientes p ON dv.paciente = p.id
            LEFT JOIN empresas_seguros e ON dv.empresa_id = e.id
            ORDER BY dv.dataa DESC, dv.id DESC";
    $rs = mysqli_query($db, $sql);
    if($rs) {
        while($linha = mysqli_fetch_assoc($rs)) {
            $devolucoes[] = $linha;
        }
    }
}

$recibos = [];
if(tabelaExiste($db, 'recibo_recepcao')) {
    $sql = "SELECT rc.*,
                   p.nome, p.apelido, p.numero_processo,
                   e.nome AS empresa_nome
            FROM recibo_recepcao rc
            LEFT JOIN pacientes p ON rc.paciente = p.id
            LEFT JOIN empresas_seguros e ON rc.empresa_id = e.id
            ORDER BY rc.dataa DESC, rc.id DESC";
    $rs = mysqli_query($db, $sql);
    if($rs) {
        while($linha = mysqli_fetch_assoc($rs)) {
            $recibos[] = $linha;
        }
    }
}

$cards = [
    'faturas' => [
        'label' => 'Faturas',
        'icone' => 'fa-file-text-o',
        'quantidade' => count($faturas),
        'total' => somarValores($faturas),
        'cor' => '#3D5DFF'
    ],
    'vendas' => [
        'label' => 'Vendas a Dinheiro/Serviço',
        'icone' => 'fa-money',
        'quantidade' => count($vendas),
        'total' => somarValores($vendas),
        'cor' => '#38D0ED'
    ],
    'cotacoes' => [
        'label' => 'Cotações',
        'icone' => 'fa-file-pdf-o',
        'quantidade' => count($cotacoes),
        'total' => somarValores($cotacoes),
        'cor' => '#4CAF50'
    ],
    'nc' => [
        'label' => 'Notas de Crédito',
        'icone' => 'fa-level-down',
        'quantidade' => count($notasCredito),
        'total' => somarValores($notasCredito),
        'cor' => '#FF9800'
    ],
    'nd' => [
        'label' => 'Notas de Débito',
        'icone' => 'fa-level-up',
        'quantidade' => count($notasDebito),
        'total' => somarValores($notasDebito),
        'cor' => '#673AB7'
    ],
    'dv' => [
        'label' => 'Devoluções',
        'icone' => 'fa-repeat',
        'quantidade' => count($devolucoes),
        'total' => somarValores($devolucoes),
        'cor' => '#F44336'
    ],
    'rc' => [
        'label' => 'Recibos',
        'icone' => 'fa-file-text',
        'quantidade' => count($recibos),
        'total' => somarValores($recibos),
        'cor' => '#00BCD4'
    ]
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <link href="../datatables.min.css" rel="stylesheet"/>
    <style>
        :root {
            --primary: #3D5DFF;
            --primary-soft: #EEF1FF;
            --accent: #38D0ED;
            --text-main: #111827;
            --text-muted: #6B7280;
            --border-subtle: #E5E7EB;
            --card-radius: 16px;
        }

        body {
            background: #F3F4F6;
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text-main);
        }

        .page-title {
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 13px;
        }

        .doc-card {
            border-radius: var(--card-radius);
            color: #fff;
            padding: 14px 16px;
            margin-bottom: 18px;
            min-height: 90px;
            box-shadow: 0 14px 40px rgba(15,23,42,0.18);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
        .doc-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(255,255,255,0.25), transparent 60%);
            opacity: 0.8;
        }
        .doc-card-content {
            position: relative;
            z-index: 1;
        }
        .doc-card .card-icon-wrapper {
            position: relative;
            z-index: 1;
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .doc-card .card-icon {
            font-size: 18px;
        }
        .doc-card .card-value {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            line-height: 1.1;
        }
        .doc-card .card-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 3px;
            opacity: 0.9;
        }
        .doc-card small {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            opacity: 0.9;
        }

        .nav-pills.document-tabs {
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 8px;
            margin-bottom: 18px;
        }
        .nav-pills.document-tabs .nav-link {
            border-radius: 999px;
            margin-right: 8px;
            padding: 6px 16px;
            font-weight: 500;
            color: var(--text-muted);
            border: 1px solid transparent;
            background: transparent;
            font-size: 13px;
        }
        .nav-pills.document-tabs .nav-link.active {
            background: #fff;
            color: var(--primary);
            border-color: rgba(61,93,255,0.35);
            box-shadow: 0 6px 18px rgba(15,23,42,0.12);
        }

        .document-filters {
            border-radius: var(--card-radius);
            border: 1px solid var(--border-subtle);
            box-shadow: 0 12px 28px rgba(15,23,42,0.12);
            background: #FFFFFF;
        }
        .filter-card .form-control,
        .filter-card .form-select {
            border-radius: 999px;
            border-color: var(--border-subtle);
            box-shadow: none;
            font-size: 13px;
            padding-left: 14px;
            padding-right: 14px;
            height: 36px;
        }
        .filter-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.10em;
            color: var(--text-muted);
        }

        .table-documentos {
            width: 100%;
            border-radius: 14px;
            overflow: hidden;
        }
        .table-documentos thead th {
            background: #F9FAFB;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-bottom: 1px solid var(--border-subtle);
            color: #6B7280;
        }
        .table-documentos tbody tr:hover {
            background: #F3F4FF;
        }
        .table-documentos tbody td {
            vertical-align: middle;
            font-size: 13px;
        }
        .table-documentos tbody td:nth-child(5),
        .table-documentos tbody td:nth-child(6) {
            white-space: nowrap;
        }

        .badge {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 999px;
        }
        .table-actions .btn {
            margin-right: 4px;
        }

        .detalhe-linha {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #E5E7EB;
            padding: 8px 0;
            font-size: 14px;
        }
        .detalhe-linha span {
            color: var(--text-muted);
        }

        .status-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        .status-chip {
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 600;
            background: var(--primary-soft);
            color: #1D4ED8;
        }
        .status-chip i {
            margin-right: 6px;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn-outline-secondary, .btn-outline-dark, .btn-outline-primary {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .card-title {
            font-weight: 600;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <?php include 'includes/header.php'; ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php include 'includes/side_bar.php'; ?>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row m-b-20">
                    <div class="col-sm-8 col-12">
                        <h4 class="page-title">Explorador de Documentos</h4>
                        <p class="page-subtitle">Visão consolidada de faturas, vendas, cotações, notas e recibos emitidos na recepção.</p>
                    </div>
                    <div class="col-sm-4 col-12 text-right">
                        <a href="fa_recepcao.php" target="_blank" class="btn btn-primary btn-rounded m-r-5"><i class="fa fa-plus"></i> Nova Fatura</a>
                        <a href="ct_recepcao.php" target="_blank" class="btn btn-outline-primary btn-rounded m-r-5"><i class="fa fa-file-text-o"></i> Nova Cotação</a>
                    </div>
                </div>

                <div class="row">
                    <?php foreach($cards as $card): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                            <div class="doc-card" style="background: <?php echo $card['cor']; ?>;">
                                <div class="doc-card-content">
                                    <span class="card-label"><?php echo $card['label']; ?></span>
                                    <p class="card-value"><?php echo $card['quantidade']; ?></p>
                                    <small>Total: <?php echo formatarMoeda($card['total']); ?></small>
                                </div>
                                <div class="card-icon-wrapper">
                                    <i class="fa <?php echo $card['icone']; ?> card-icon"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <ul class="nav nav-pills document-tabs m-b-30" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-faturas" role="tab">Faturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-vendas" role="tab">VDS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-cotacoes" role="tab">Cotações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-nc" role="tab">Notas de Crédito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-nd" role="tab">Notas de Débito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-dv" role="tab">Devoluções</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-rc" role="tab">Recibos</a>
                    </li>
                </ul>

                <div class="card filter-card document-filters m-b-30">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-4 col-sm-12 m-b-15">
                                <label class="filter-label" for="filterSearch">Pesquisa rápida</label>
                                <input type="text" id="filterSearch" class="form-control" placeholder="Paciente, Nº documento, empresa...">
                            </div>
                            <div class="col-md-3 col-sm-6 m-b-15">
                                <label class="filter-label" for="filterDateFrom">Data inicial</label>
                                <input type="date" id="filterDateFrom" class="form-control">
                            </div>
                            <div class="col-md-3 col-sm-6 m-b-15">
                                <label class="filter-label" for="filterDateTo">Data final</label>
                                <input type="date" id="filterDateTo" class="form-control">
                            </div>
                            <div class="col-md-2 col-sm-12 m-b-15 filter-actions">
                                <button class="btn btn-light btn-block" id="filterReset"><i class="fa fa-undo"></i> Limpar</button>
                            </div>
                        </div>
                        <div class="status-legend">
                            <span class="status-chip"><i class="fa fa-circle text-success"></i> Pagas</span>
                            <span class="status-chip"><i class="fa fa-circle text-info"></i> Parciais</span>
                            <span class="status-chip"><i class="fa fa-circle text-warning"></i> Pendentes</span>
                            <span class="status-chip"><i class="fa fa-circle text-secondary"></i> Devolvidas</span>
                        </div>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-faturas">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Faturas Emitidas</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-faturas" data-order-column="3" data-date-column="3">
                                        <thead>
                                            <tr>
                                                <th>Nº Fatura</th>
                                                <th>Paciente</th>
                                                <th>Empresa</th>
                                                <th>Data</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($faturas)): ?>
                                                <tr><td colspan="7" class="text-center">Nenhuma fatura encontrada.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($faturas as $fatura): ?>
                                                    <?php
                                                        $numero = formatarNumeroDoc('FA', $fatura['serie'] ?? date('Y'), $fatura['n_doc'] ?? $fatura['id']);
                                                        $paciente = trim(($fatura['nome'] ?? '') . ' ' . ($fatura['apelido'] ?? ''));
                                                        $processo = $fatura['numero_processo'] ?? '';
                                                        $detalhes = [
                                                            'Documento' => $numero,
                                                            'Paciente' => $paciente,
                                                            'Nº Processo' => $processo,
                                                            'Empresa' => $fatura['empresa_nome'] ?? '-',
                                                            'Valor' => formatarMoeda($fatura['valor'] ?? 0),
                                                            'Total Pago' => formatarMoeda($fatura['detalhes']['total_pago'] ?? 0),
                                                            'Valor Disponível' => formatarMoeda($fatura['detalhes']['valor_disponivel'] ?? 0),
                                                            'Estado' => $fatura['detalhes']['status'] ?? 'Pendente',
                                                            'Data' => $fatura['dataa'] ?? '-'
                                                        ];
                                                    ?>
                                                    <?php $detalhes_json = htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php $detalhes_json = htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php $detalhes_json = htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php $detalhes_json = htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>
                                                    <tr>
                                                        <td><?php echo $numero; ?></td>
                                                        <td>
                                                            <?php echo $paciente ?: '-'; ?>
                                                            <?php if($processo): ?>
                                                                <br><small><?php echo $processo; ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $fatura['empresa_nome'] ?? '-'; ?></td>
                                                        <td><?php echo $fatura['dataa'] ? date('d/m/Y', strtotime($fatura['dataa'])) : '-'; ?></td>
                                                        <td><?php echo formatarMoeda($fatura['valor'] ?? 0); ?></td>
                                                        <td><span class="<?php echo $fatura['detalhes']['classe']; ?>"><?php echo $fatura['detalhes']['status']; ?></span></td>
                                                        <td class="table-actions">
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo $detalhes_json; ?>">
                                                                <i class="fa fa-info-circle"></i> Detalhes
                                                            </button>
                                                            <a href="documento_detalhe.php?tipo=fa&id=<?php echo $fatura['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i> Visualizar
                                                            </a>
                                                            <a href="documento_detalhe.php?tipo=fa&id=<?php echo $fatura['id']; ?>&print=1" target="_blank" class="btn btn-sm btn-outline-dark">
                                                                <i class="fa fa-print"></i> Imprimir
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-vendas">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Vendas a Dinheiro/Serviço</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-vendas" data-order-column="3" data-date-column="3">
                                        <thead>
                                            <tr>
                                                <th>Nº Documento</th>
                                                <th>Paciente</th>
                                                <th>Empresa</th>
                                                <th>Data</th>
                                                <th>Valor</th>
                                                <th>Método</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($vendas)): ?>
                                                <tr><td colspan="7" class="text-center">Nenhuma venda encontrada.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($vendas as $venda): ?>
                                                    <?php
                                                        $numero = formatarNumeroDoc('VDS', $venda['serie'] ?? date('Y'), $venda['n_doc'] ?? $venda['id']);
                                                        $paciente = trim(($venda['nome'] ?? '') . ' ' . ($venda['apelido'] ?? ''));
                                                        $processo = $venda['numero_processo'] ?? '';
                                                        $detalhes = [
                                                            'Documento' => $numero,
                                                            'Paciente' => $paciente,
                                                            'Nº Processo' => $processo,
                                                            'Empresa' => $venda['empresa_nome'] ?? '-',
                                                            'Valor' => formatarMoeda($venda['valor'] ?? 0),
                                                            'Método' => strtoupper($venda['metodo'] ?? '-'),
                                                            'Data' => $venda['dataa'] ?? '-'
                                                        ];
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $numero; ?></td>
                                                        <td>
                                                            <?php echo $paciente ?: '-'; ?>
                                                            <?php if($processo): ?>
                                                                <br><small><?php echo $processo; ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $venda['empresa_nome'] ?? '-'; ?></td>
                                                        <td><?php echo $venda['dataa'] ? date('d/m/Y', strtotime($venda['dataa'])) : '-'; ?></td>
                                                        <td><?php echo formatarMoeda($venda['valor'] ?? 0); ?></td>
                                                        <td><?php echo strtoupper($venda['metodo'] ?? '-'); ?></td>
                                                        <td class="table-actions">
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo $detalhes_json; ?>">
                                                                <i class="fa fa-info-circle"></i> Detalhes
                                                            </button>
                                                            <a href="documento_detalhe.php?tipo=vds&id=<?php echo $venda['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i> Visualizar
                                                            </a>
                                                            <a href="documento_detalhe.php?tipo=vds&id=<?php echo $venda['id']; ?>&print=1" target="_blank" class="btn btn-sm btn-outline-dark">
                                                                <i class="fa fa-print"></i> Imprimir
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-cotacoes">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Cotações</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-cotacoes" data-order-column="3" data-date-column="3">
                                        <thead>
                                            <tr>
                                                <th>Nº Cotação</th>
                                                <th>Paciente</th>
                                                <th>Empresa</th>
                                                <th>Data</th>
                                                <th>Prazo</th>
                                                <th>Valor</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($cotacoes)): ?>
                                                <tr><td colspan="7" class="text-center">Nenhuma cotação encontrada.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($cotacoes as $cotacao): ?>
                                                    <?php
                                                        $numero = formatarNumeroDoc('CT', $cotacao['serie'] ?? date('Y'), $cotacao['n_doc'] ?? $cotacao['id']);
                                                        $paciente = trim(($cotacao['nome'] ?? '') . ' ' . ($cotacao['apelido'] ?? ''));
                                                        $processo = $cotacao['numero_processo'] ?? '';
                                                        $detalhes = [
                                                            'Documento' => $numero,
                                                            'Paciente' => $paciente,
                                                            'Nº Processo' => $processo,
                                                            'Empresa' => $cotacao['empresa_nome'] ?? '-',
                                                            'Valor' => formatarMoeda($cotacao['valor'] ?? 0),
                                                            'Prazo' => $cotacao['prazo'] ? date('d/m/Y', strtotime($cotacao['prazo'])) : '-',
                                                            'Data' => $cotacao['dataa'] ?? '-'
                                                        ];
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $numero; ?></td>
                                                        <td>
                                                            <?php echo $paciente ?: '-'; ?>
                                                            <?php if($processo): ?>
                                                                <br><small><?php echo $processo; ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $cotacao['empresa_nome'] ?? '-'; ?></td>
                                                        <td><?php echo $cotacao['dataa'] ? date('d/m/Y', strtotime($cotacao['dataa'])) : '-'; ?></td>
                                                        <td><?php echo $cotacao['prazo'] ? date('d/m/Y', strtotime($cotacao['prazo'])) : '-'; ?></td>
                                                        <td><?php echo formatarMoeda($cotacao['valor'] ?? 0); ?></td>
                                                        <td class="table-actions">
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo $detalhes_json; ?>">
                                                                <i class="fa fa-info-circle"></i> Detalhes
                                                            </button>
                                                            <a href="documento_detalhe.php?tipo=ct&id=<?php echo $cotacao['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i> Visualizar
                                                            </a>
                                                            <a href="documento_detalhe.php?tipo=ct&id=<?php echo $cotacao['id']; ?>&print=1" target="_blank" class="btn btn-sm btn-outline-dark">
                                                                <i class="fa fa-print"></i> Imprimir
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-nc">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Notas de Crédito</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-nc" data-order-column="4" data-date-column="4">
                                        <thead>
                                            <tr>
                                                <th>Nº Nota</th>
                                                <th>Fatura de Origem</th>
                                                <th>Paciente</th>
                                                <th>Valor</th>
                                                <th>Data</th>
                                                <th>Motivo</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($notasCredito)): ?>
                                                <tr><td colspan="7" class="text-center">Nenhuma nota de crédito encontrada.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($notasCredito as $nota): ?>
                                                    <?php
                                                        $numero = formatarNumeroDoc('NC', $nota['serie'] ?? date('Y'), $nota['n_doc'] ?? $nota['id']);
                                                        $faturaOrigem = $nota['fatura_n_doc'] ? formatarNumeroDoc('FA', $nota['fatura_serie'] ?? date('Y'), $nota['fatura_n_doc']) : '-';
                                                        $paciente = trim(($nota['nome'] ?? '') . ' ' . ($nota['apelido'] ?? ''));
                                                        $processo = $nota['numero_processo'] ?? '';
                                                        $detalhes = [
                                                            'Documento' => $numero,
                                                            'Fatura de Origem' => $faturaOrigem,
                                                            'Paciente' => $paciente,
                                                            'Nº Processo' => $processo,
                                                            'Valor' => formatarMoeda($nota['valor'] ?? 0),
                                                            'Motivo' => $nota['motivo'] ?? '-',
                                                            'Data' => $nota['dataa'] ?? '-'
                                                        ];
                                                    ?>
                                                    <?php $detalhes_json = htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php $detalhes_json = htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php $detalhes_json = htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>
                                                    <tr>
                                                        <td><?php echo $numero; ?></td>
                                                        <td><?php echo $faturaOrigem; ?></td>
                                                        <td>
                                                            <?php echo $paciente ?: '-'; ?>
                                                            <?php if($processo): ?>
                                                                <br><small><?php echo $processo; ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo formatarMoeda($nota['valor'] ?? 0); ?></td>
                                                        <td><?php echo $nota['dataa'] ? date('d/m/Y', strtotime($nota['dataa'])) : '-'; ?></td>
                                                        <td><?php echo $nota['motivo'] ?? '-'; ?></td>
                                                        <td class="table-actions">
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo $detalhes_json; ?>">
                                                                <i class="fa fa-info-circle"></i> Detalhes
                                                            </button>
                                                            <a href="documento_detalhe.php?tipo=nc&id=<?php echo $nota['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i> Visualizar
                                                            </a>
                                                            <a href="documento_detalhe.php?tipo=nc&id=<?php echo $nota['id']; ?>&print=1" target="_blank" class="btn btn-sm btn-outline-dark">
                                                                <i class="fa fa-print"></i> Imprimir
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-nd">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Notas de Débito</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-nd" data-order-column="4" data-date-column="4">
                                        <thead>
                                            <tr>
                                                <th>Nº Nota</th>
                                                <th>Fatura de Origem</th>
                                                <th>Paciente</th>
                                                <th>Valor</th>
                                                <th>Data</th>
                                                <th>Motivo</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($notasDebito)): ?>
                                                <tr><td colspan="7" class="text-center">Nenhuma nota de débito encontrada.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($notasDebito as $nota): ?>
                                                    <?php
                                                        $numero = formatarNumeroDoc('ND', $nota['serie'] ?? date('Y'), $nota['n_doc'] ?? $nota['id']);
                                                        $faturaOrigem = $nota['fatura_n_doc'] ? formatarNumeroDoc('FA', $nota['fatura_serie'] ?? date('Y'), $nota['fatura_n_doc']) : '-';
                                                        $paciente = trim(($nota['nome'] ?? '') . ' ' . ($nota['apelido'] ?? ''));
                                                        $processo = $nota['numero_processo'] ?? '';
                                                        $detalhes = [
                                                            'Documento' => $numero,
                                                            'Fatura de Origem' => $faturaOrigem,
                                                            'Paciente' => $paciente,
                                                            'Nº Processo' => $processo,
                                                            'Valor' => formatarMoeda($nota['valor'] ?? 0),
                                                            'Motivo' => $nota['motivo'] ?? '-',
                                                            'Data' => $nota['dataa'] ?? '-'
                                                        ];
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $numero; ?></td>
                                                        <td><?php echo $faturaOrigem; ?></td>
                                                        <td>
                                                            <?php echo $paciente ?: '-'; ?>
                                                            <?php if($processo): ?>
                                                                <br><small><?php echo $processo; ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo formatarMoeda($nota['valor'] ?? 0); ?></td>
                                                        <td><?php echo $nota['dataa'] ? date('d/m/Y', strtotime($nota['dataa'])) : '-'; ?></td>
                                                        <td><?php echo $nota['motivo'] ?? '-'; ?></td>
                                                        <td class="table-actions">
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo $detalhes_json; ?>">
                                                                <i class="fa fa-info-circle"></i> Detalhes
                                                            </button>
                                                            <a href="documento_detalhe.php?tipo=nd&id=<?php echo $nota['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i> Visualizar
                                                            </a>
                                                            <a href="documento_detalhe.php?tipo=nd&id=<?php echo $nota['id']; ?>&print=1" target="_blank" class="btn btn-sm btn-outline-dark">
                                                                <i class="fa fa-print"></i> Imprimir
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-dv">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Devoluções</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-dv" data-order-column="5" data-date-column="5">
                                        <thead>
                                            <tr>
                                                <th>Nº Documento</th>
                                                <th>Fatura de Origem</th>
                                                <th>Paciente</th>
                                                <th>Valor</th>
                                                <th>Método</th>
                                                <th>Data</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($devolucoes)): ?>
                                                <tr><td colspan="7" class="text-center">Nenhuma devolução encontrada.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($devolucoes as $dv): ?>
                                                    <?php
                                                        $numero = formatarNumeroDoc('DV', $dv['serie'] ?? date('Y'), $dv['n_doc'] ?? $dv['id']);
                                                        $faturaOrigem = $dv['fatura_n_doc'] ? formatarNumeroDoc('FA', $dv['fatura_serie'] ?? date('Y'), $dv['fatura_n_doc']) : '-';
                                                        $paciente = trim(($dv['nome'] ?? '') . ' ' . ($dv['apelido'] ?? ''));
                                                        $processo = $dv['numero_processo'] ?? '';
                                                        $detalhes = [
                                                            'Documento' => $numero,
                                                            'Fatura de Origem' => $faturaOrigem,
                                                            'Paciente' => $paciente,
                                                            'Nº Processo' => $processo,
                                                            'Valor' => formatarMoeda($dv['valor'] ?? 0),
                                                            'Método' => strtoupper($dv['metodo'] ?? '-'),
                                                            'Motivo' => $dv['motivo'] ?? '-',
                                                            'Data' => $dv['dataa'] ?? '-'
                                                        ];
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $numero; ?></td>
                                                        <td><?php echo $faturaOrigem; ?></td>
                                                        <td>
                                                            <?php echo $paciente ?: '-'; ?>
                                                            <?php if($processo): ?>
                                                                <br><small><?php echo $processo; ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo formatarMoeda($dv['valor'] ?? 0); ?></td>
                                                        <td><?php echo strtoupper($dv['metodo'] ?? '-'); ?></td>
                                                        <td><?php echo $dv['dataa'] ? date('d/m/Y', strtotime($dv['dataa'])) : '-'; ?></td>
                                                        <td class="table-actions">
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo $detalhes_json; ?>">
                                                                <i class="fa fa-info-circle"></i> Detalhes
                                                            </button>
                                                            <a href="documento_detalhe.php?tipo=dv&id=<?php echo $dv['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i> Visualizar
                                                            </a>
                                                            <a href="documento_detalhe.php?tipo=dv&id=<?php echo $dv['id']; ?>&print=1" target="_blank" class="btn btn-sm btn-outline-dark">
                                                                <i class="fa fa-print"></i> Imprimir
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-rc">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Recibos</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-rc" data-order-column="5" data-date-column="5">
                                        <thead>
                                            <tr>
                                                <th>Nº Recibo</th>
                                                <th>Paciente</th>
                                                <th>Empresa</th>
                                                <th>Valor</th>
                                                <th>Método</th>
                                                <th>Data</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($recibos)): ?>
                                                <tr><td colspan="7" class="text-center">Nenhum recibo encontrado.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($recibos as $rc): ?>
                                                    <?php
                                                        $numero = formatarNumeroDoc('RC', $rc['serie'] ?? date('Y'), $rc['n_doc'] ?? $rc['id']);
                                                        $paciente = trim(($rc['nome'] ?? '') . ' ' . ($rc['apelido'] ?? ''));
                                                        $processo = $rc['numero_processo'] ?? '';
                                                        $detalhes = [
                                                            'Documento' => $numero,
                                                            'Paciente' => $paciente,
                                                            'Nº Processo' => $processo,
                                                            'Empresa' => $rc['empresa_nome'] ?? '-',
                                                            'Valor' => formatarMoeda($rc['valor'] ?? 0),
                                                            'Método' => strtoupper($rc['metodo'] ?? '-'),
                                                            'Data' => $rc['dataa'] ?? '-'
                                                        ];
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $numero; ?></td>
                                                        <td>
                                                            <?php echo $paciente ?: '-'; ?>
                                                            <?php if($processo): ?>
                                                                <br><small><?php echo $processo; ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $rc['empresa_nome'] ?? '-'; ?></td>
                                                        <td><?php echo formatarMoeda($rc['valor'] ?? 0); ?></td>
                                                        <td><?php echo strtoupper($rc['metodo'] ?? '-'); ?></td>
                                                        <td><?php echo $rc['dataa'] ? date('d/m/Y', strtotime($rc['dataa'])) : '-'; ?></td>
                                                        <td class="table-actions">
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo $detalhes_json; ?>">
                                                                <i class="fa fa-info-circle"></i> Detalhes
                                                            </button>
                                                            <a href="documento_detalhe.php?tipo=rc&id=<?php echo $rc['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i> Visualizar
                                                            </a>
                                                            <a href="documento_detalhe.php?tipo=rc&id=<?php echo $rc['id']; ?>&print=1" target="_blank" class="btn btn-sm btn-outline-dark">
                                                                <i class="fa fa-print"></i> Imprimir
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetalhesDocumento" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-file-text-o"></i> Detalhes do Documento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="documentoDetalhes">
                    <p class="text-muted">Selecione um documento para visualizar os detalhes.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            const dataTables = {};
            const tables = $('.table-documentos');
            let activeTableId = tables.first().attr('id');
            const filterState = {
                text: '',
                dateFrom: null,
                dateTo: null
            };

            function parseDatePtBr(value) {
                if(!value || value === '-' || value.length < 8) return null;
                const parts = value.split('/');
                if(parts.length !== 3) return null;
                const [day, month, year] = parts;
                return new Date(`${year}-${month}-${day}`);
            }

            $.fn.dataTable.ext.search.push(function(settings, data) {
                if(settings.sTableId !== activeTableId) {
                    return true;
                }

                const tableNode = settings.nTable;
                const dateColumnIndex = parseInt($(tableNode).data('date-column'), 10);

                if(!isNaN(dateColumnIndex)) {
                    const dateString = (data[dateColumnIndex] || '').trim();
                    const rowDate = parseDatePtBr(dateString);

                    if(filterState.dateFrom) {
                        const fromDate = new Date(filterState.dateFrom);
                        if(!rowDate || rowDate < fromDate) {
                            return false;
                        }
                    }

                    if(filterState.dateTo) {
                        const toDate = new Date(filterState.dateTo);
                        toDate.setHours(23, 59, 59, 999);
                        if(!rowDate || rowDate > toDate) {
                            return false;
                        }
                    }
                }

                return true;
            });

            tables.each(function() {
                const orderColumn = parseInt($(this).data('order-column'), 10);
                const orderConfig = isNaN(orderColumn) ? [[0, "desc"]] : [[orderColumn, "desc"]];
                const tableId = $(this).attr('id');

                dataTables[tableId] = $(this).DataTable({
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json",
                        emptyTable: "Nenhum registo encontrado"
                    },
                    order: orderConfig,
                    pageLength: 10,
                    responsive: true
                });
            });

            function getActiveTable() {
                return dataTables[activeTableId];
            }

            function applyFilters() {
                const table = getActiveTable();
                if(!table) return;

                table.search(filterState.text).draw();
            }

            $('#filterSearch').on('input', function() {
                filterState.text = $(this).val();
                applyFilters();
            });

            $('#filterDateFrom').on('change', function() {
                filterState.dateFrom = $(this).val() || null;
                applyFilters();
            });

            $('#filterDateTo').on('change', function() {
                filterState.dateTo = $(this).val() || null;
                applyFilters();
            });

            $('#filterReset').on('click', function() {
                filterState.text = '';
                filterState.dateFrom = null;
                filterState.dateTo = null;
                $('#filterSearch').val('');
                $('#filterDateFrom').val('');
                $('#filterDateTo').val('');
                applyFilters();
            });

            $('.document-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const targetTable = $( $(e.target).attr('href') ).find('.table-documentos').attr('id');
                if(targetTable) {
                    activeTableId = targetTable;
                    applyFilters();
                }
            });

            $('.btn-detalhes').on('click', function() {
                const dadosRaw = $(this).attr('data-documento');
                let dados;
                try {
                    dados = JSON.parse(dadosRaw);
                } catch (error) {
                    dados = null;
                }

                const container = $('#documentoDetalhes');
                container.empty();

                if(!dados) {
                    container.append('<p class="text-danger">Não foi possível carregar os detalhes deste documento.</p>');
                } else {
                    Object.keys(dados).forEach(function(chave) {
                        const valor = dados[chave] && dados[chave] !== '' ? dados[chave] : '-';
                        container.append('<div class="detalhe-linha"><span>' + chave + '</span><strong>' + valor + '</strong></div>');
                    });
                }

                $('#modalDetalhesDocumento').modal('show');
            });

        });
    </script>
</body>
</html>

