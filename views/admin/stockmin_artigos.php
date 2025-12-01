<?php 
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("location:../../");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table#stockMinTable {
            width: 100%;
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
                <h4 class="page-title">Artigos em Rotura de Stock</h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="stockMinTable" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Artigo</th>
                                        <th>Stock Atual</th>
                                        <th>Lote</th>
                                        <th>Prazo</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#stockMinTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "stockmin_artigoss.php",
                    "type": "GET",
                    "dataSrc": function(json) {
                        if (!json.data) {
                            console.error("Erro ao carregar dados:", json);
                            return [];
                        }
                        return json.data;
                    }
                },
                "columns": [
                    { "data": "idproduto" },
                    { "data": "nomeproduto" },
                    { "data": "quantidade" },
                    { "data": "lote" },
                    { "data": "prazo" }
                ]
            });
        });

    </script>
</body>
</html>
