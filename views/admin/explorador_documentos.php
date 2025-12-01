<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("location:../../");
}

include '../../conexao/index.php';
require_once 'includes/documentos_union.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario'];

function fetchScalar(mysqli $db, string $sql): int
{
    $rs = mysqli_query($db, $sql);
    if ($rs && $row = mysqli_fetch_row($rs)) {
        return (int) $row[0];
    }
    return 0;
}

$stats = [
    'total_docs' => 0,
    'facturas' => 0,
    'vendas' => 0,
    'notas_credito' => 0,
    'notas_debito' => 0,
    'cotacoes' => 0,
    'recibos' => 0,
    'devolucoes' => 0,
    'ordens_compra' => 0
];

$unionSql = getDocumentosUnionSql();
$stats['total_docs'] = fetchScalar($db, "SELECT COUNT(*) FROM ({$unionSql}) AS docs");
$stats['facturas'] = fetchScalar($db, "SELECT COUNT(*) FROM factura");
$stats['vendas'] = fetchScalar($db, "SELECT COUNT(*) FROM pedido");
$stats['notas_credito'] = fetchScalar($db, "SELECT COUNT(*) FROM nota_de_credito");
$stats['notas_debito'] = fetchScalar($db, "SELECT COUNT(*) FROM nota_debito");
$stats['cotacoes'] = fetchScalar($db, "SELECT COUNT(*) FROM cotacao");
$stats['recibos'] = fetchScalar($db, "SELECT COUNT(*) FROM recibo");
$stats['devolucoes'] = fetchScalar($db, "SELECT COUNT(*) FROM devolucao");
$stats['ordens_compra'] = fetchScalar($db, "SELECT COUNT(*) FROM ordem_compra");

$recentDocs = [];
$recentQuery = "SELECT * FROM ({$unionSql}) AS docs ORDER BY data_emissao DESC LIMIT 6";
$recentResult = mysqli_query($db, $recentQuery);
if ($recentResult) {
    while ($row = mysqli_fetch_assoc($recentResult)) {
        $recentDocs[] = $row;
    }
}

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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .doc-card {
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        .doc-card h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .doc-card p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .filter-grid .form-group {
            margin-bottom: 0;
        }
        .recent-doc {
            border-bottom: 1px solid #f1f1f1;
            padding: 12px 0;
        }
        .recent-doc:last-child {
            border-bottom: 0;
        }
        .recent-doc strong {
            display: block;
        }
        .recent-doc small {
            color: #888;
        }
        .nav-tabs .nav-link {
            padding: 10px 18px;
        }
        .status-legend span {
            margin-right: 12px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <?php include 'includes/header.php' ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php include 'includes/side_bar.php'; ?>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="page-title">Explorador de Documentos</h4>
                        <p class="text-muted">Acompanhe todos os documentos gerados no sistema em um único lugar.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="doc-card">
                            <p>Total de Documentos</p>
                            <h3><?php echo number_format($stats['total_docs']); ?></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="doc-card">
                            <p>Facturas</p>
                            <h3><?php echo number_format($stats['facturas']); ?></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="doc-card">
                            <p>Vendas a Dinheiro</p>
                            <h3><?php echo number_format($stats['vendas']); ?></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="doc-card">
                            <p>Cotações</p>
                            <h3><?php echo number_format($stats['cotacoes']); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="doc-card">
                            <p>Notas de Crédito</p>
                            <h3><?php echo number_format($stats['notas_credito']); ?></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="doc-card">
                            <p>Notas de Débito</p>
                            <h3><?php echo number_format($stats['notas_debito']); ?></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="doc-card">
                            <p>Recibos</p>
                            <h3><?php echo number_format($stats['recibos']); ?></h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="doc-card">
                            <p>Devoluções</p>
                            <h3><?php echo number_format($stats['devolucoes']); ?></h3>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#visao-geral"><i class="fa fa-table"></i> Visão Geral</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="factura.php"><i class="fa fa-file-text-o"></i> Facturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nota_de_credito.php"><i class="fa fa-file-text"></i> Notas de Crédito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nota_de_debito.php"><i class="fa fa-file-text"></i> Notas de Débito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cotacoes.php"><i class="fa fa-list"></i> Cotações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rel_pedidos.php"><i class="fa fa-shopping-cart"></i> Vendas a Dinheiro</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="visao-geral">
                        <div class="row mt-3">
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Filtros</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row filter-grid">
                                            <div class="col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label>Tipo</label>
                                                    <select class="form-control doc-filter" id="filterTipo">
                                                        <option value="all">Todos</option>
                                                        <?php foreach ($tipoLabels as $key => $label): ?>
                                                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select class="form-control doc-filter" id="filterStatus">
                                                        <option value="all">Todos</option>
                                                        <option value="paga">Paga</option>
                                                        <option value="pendente">Pendente</option>
                                                        <option value="emitido">Emitido</option>
                                                        <option value="aberta">Aberta</option>
                                                        <option value="devolvido">Devolvido</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label>Data Inicial</label>
                                                    <input type="date" class="form-control doc-filter" id="filterDataInicio">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label>Data Final</label>
                                                    <input type="date" class="form-control doc-filter" id="filterDataFim">
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-light" id="btnLimparFiltros"><i class="fa fa-eraser"></i> Limpar Filtros</button>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Documentos Criados</h4>
                                        <div class="status-legend">
                                            <span><span class="badge badge-success">&nbsp;</span> Paga</span>
                                            <span><span class="badge badge-warning">&nbsp;</span> Pendente</span>
                                            <span><span class="badge badge-primary">&nbsp;</span> Emitido</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="documentosTable" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Tipo</th>
                                                        <th>Documento</th>
                                                        <th>Cliente/Fornecedor</th>
                                                        <th>Valor</th>
                                                        <th>Status</th>
                                                        <th>Data</th>
                                                        <th>Utilizador</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Documentos Recentes</h4>
                                    </div>
                                    <div class="card-body">
                                        <?php if (count($recentDocs) > 0): ?>
                                            <?php foreach ($recentDocs as $doc): ?>
                                                <div class="recent-doc">
                                                    <strong><?php echo $doc['documento']; ?></strong>
                                                    <small><?php echo $tipoLabels[$doc['tipo']] ?? strtoupper($doc['tipo']); ?></small>
                                                    <div class="text-muted" style="font-size: 12px;">
                                                        <?php echo $doc['cliente']; ?> • <?php echo date('d/m/Y H:i', strtotime($doc['data_emissao'])); ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">Nenhum documento recente disponível.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Atalhos Rápidos</h4>
                                    </div>
                                    <div class="card-body">
                                        <a href="../facturas.php" target="_blank" class="btn btn-primary btn-block m-b-10"><i class="fa fa-plus"></i> Nova Factura</a>
                                        <a href="../vd.php" target="_blank" class="btn btn-warning btn-block m-b-10"><i class="fa fa-cash-register"></i> Nova VD</a>
                                        <a href="../cotacao.php" target="_blank" class="btn btn-info btn-block"><i class="fa fa-file-o"></i> Nova Cotação</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
    <link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(function () {
            var tabela = $('#documentosTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "ajax/listar_documentos.php",
                    "data": function (d) {
                        d.tipo = $('#filterTipo').val();
                        d.status = $('#filterStatus').val();
                        d.data_inicio = $('#filterDataInicio').val();
                        d.data_fim = $('#filterDataFim').val();
                    }
                },
                "columns": [
                    { "data": "tipo_label" },
                    { "data": "documento" },
                    { "data": "cliente" },
                    { "data": "total_fmt", "className": "text-right" },
                    { "data": "status_badge", "orderable": false, "searchable": false },
                    { "data": "data_fmt" },
                    { "data": "utilizador" },
                    { "data": "acoes", "orderable": false, "searchable": false, "className": "text-center" }
                ],
                "order": [[5, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json"
                }
            });

            $('.doc-filter').on('change', function () {
                tabela.ajax.reload();
            });

            $('#btnLimparFiltros').on('click', function () {
                $('#filterTipo').val('all');
                $('#filterStatus').val('all');
                $('#filterDataInicio').val('');
                $('#filterDataFim').val('');
                tabela.ajax.reload();
            });
        });
    </script>
</body>
</html>

