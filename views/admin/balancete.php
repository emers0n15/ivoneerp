<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
$data = date("Y-m-d H:m:s");
?>
<!DOCTYPE html>
<html lang="pt">



<head>
    <?php include 'includes/head.php'; ?>
    <style>
        /* Ajusta a tabela para ocupar toda a largura disponível */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table#example {
            width: 100%;
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
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Balancete</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="rel_balancete.php" class="btn btn-primary btn-rounded float-right" style="margin-left: 10px;"><i class="fa fa-file"></i> Criar Relatório</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="example" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Artigo</th>
                                        <th>Compras</th>
                                        <th>Entradas de Stock</th>
                                        <th>VDs</th>
                                        <th>FAs</th>
                                        <th>DVs</th>
                                        <th>NCs</th>
                                        <th>NDs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
</body>

<link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "popbal.php",
                    "type": "GET"
                },
                "columns": [
                    { "data": "idproduto" },
                    { "data": "nomeproduto" },
                    { "data": "quantidade_comprada" },
                    { "data": "quantidade_estoque" },
                    { "data": "quantidade_vendida" },
                    { "data": "quantidade_faturada" },
                    { "data": "quantidade_devolvida" },
                    { "data": "quantidade_creditada" },
                    { "data": "quantidade_debitada" }
                ]
            });
        });
    </script>
<!-- add-patient24:07-->
</html>
