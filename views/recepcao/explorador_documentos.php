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
        .doc-card {
            border-radius: 12px;
            color: #fff;
            padding: 18px;
            margin-bottom: 20px;
            min-height: 140px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .doc-card .card-icon {
            font-size: 26px;
            margin-bottom: 8px;
            opacity: 0.9;
        }
        .doc-card .card-value {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }
        .doc-card .card-label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 3px;
        }
        .doc-card small {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            opacity: 0.85;
        }
        .nav-pills.document-tabs .nav-link {
            border-radius: 30px;
            margin-right: 10px;
            padding: 8px 18px;
            font-weight: 600;
            color: #3D5DFF;
            border: 1px solid rgba(61,93,255,0.2);
        }
        .nav-pills.document-tabs .nav-link.active {
            background: #3D5DFF;
            color: #fff;
            box-shadow: 0 8px 20px rgba(61,93,255,0.25);
        }
        .table-documentos {
            width: 100%;
        }
        .table-documentos thead th {
            background: #f5f7fb;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 30px;
        }
        .table-actions .btn {
            margin-right: 6px;
        }
        .detalhe-linha {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #f1f1f1;
            padding: 8px 0;
            font-size: 14px;
        }
        .detalhe-linha span {
            color: #7a7a7a;
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
                <div class="row">
                    <div class="col-sm-8 col-12">
                        <h4 class="page-title">Explorador de Documentos</h4>
                        <p class="text-muted m-b-20">Acompanhe todos os documentos emitidos na recepção em um único ecrã.</p>
                    </div>
                    <div class="col-sm-4 col-12 text-right">
                        <a href="fa_recepcao.php" target="_blank" class="btn btn-primary btn-rounded m-r-5"><i class="fa fa-plus"></i> Nova Fatura</a>
                        <a href="ct_recepcao.php" target="_blank" class="btn btn-outline-primary btn-rounded m-r-5"><i class="fa fa-file"></i> Nova Cotação</a>
                    </div>
                </div>

                <div class="row">
                    <?php foreach($cards as $card): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                            <div class="doc-card" style="background: <?php echo $card['cor']; ?>;">
                                <div class="card-icon"><i class="fa <?php echo $card['icone']; ?>"></i></div>
                                <span class="card-label"><?php echo $card['label']; ?></span>
                                <p class="card-value"><?php echo $card['quantidade']; ?></p>
                                <small>Total: <?php echo formatarMoeda($card['total']); ?></small>
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

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-faturas">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Faturas Emitidas</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-faturas" data-order-column="3">
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
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-eye"></i> Detalhes
                                                            </button>
                                                            <a href="imprimir_recibo.php?id=<?php echo $fatura['id']; ?>" target="_blank" class="btn btn-sm btn-primary">
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
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-vendas" data-order-column="3">
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
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-eye"></i> Detalhes
                                                            </button>
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
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-cotacoes" data-order-column="3">
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
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-eye"></i> Detalhes
                                                            </button>
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
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-nc" data-order-column="4">
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
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-eye"></i> Detalhes
                                                            </button>
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
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-nd" data-order-column="4">
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
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-eye"></i> Detalhes
                                                            </button>
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
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-dv" data-order-column="5">
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
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-eye"></i> Detalhes
                                                            </button>
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
                                    <table class="table table-striped table-bordered table-documentos" id="tabela-rc" data-order-column="5">
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
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary btn-detalhes" data-documento="<?php echo htmlspecialchars(json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-eye"></i> Detalhes
                                                            </button>
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
            $('.table-documentos').each(function() {
                const orderColumn = parseInt($(this).data('order-column'), 10);
                const orderConfig = isNaN(orderColumn) ? [[0, "desc"]] : [[orderColumn, "desc"]];

                $(this).DataTable({
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json",
                        emptyTable: "Nenhum registo encontrado"
                    },
                    order: orderConfig,
                    pageLength: 10,
                    responsive: true
                });
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

