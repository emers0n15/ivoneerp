<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
error_reporting(E_ALL);
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
                        <h4 class="page-title">Notas de Débito</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <!-- Botão para criar relatório (comentado no seu código original) -->
                        <a href="rel_notas_debito.php" class="btn btn-primary btn-rounded float-right">
                            <i class="fa fa-file"></i> Criar Relatório
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="example" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>IVA(16%)</th>
                                        <th>Cliente</th>
                                        <th>Factura</th>
                                        <th>Motivo</th>
                                        <th>Utilizador</th>
                                        <th class="text-right">Ações</th>
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
                "url": "popnd.php", // URL do script PHP que retornará os dados
                "type": "GET",
                "dataSrc": "data" // A chave onde estão os dados na resposta JSON
            },
            "columns": [
                { "data": "id" },
                { "data": "descricao" },
                { "data": "valor" },
                { "data": "iva" },
                { "data": "cliente" },
                { "data": "factura" },
                { "data": "motivo" },
                { "data": "usuario" },
                {
                    "data": null,
                    "defaultContent": '<a href="#" class="btn btn-primary btn-sm print-action"><i class="fa fa-print"></i></a>',
                    "orderable": false
                }
            ]
        });

        // Ação do botão de visualização
        $('#example').on('click', '.print-action', function() {
            var data = $('#example').DataTable().row($(this).parents('tr')).data();
            window.open('../nd_pdf.php?id_nd=' + data.id, '_blank');
        });

        });
    </script>
<!-- add-patient24:07-->
</html>
