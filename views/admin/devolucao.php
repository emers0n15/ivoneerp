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
                        <h4 class="page-title">Devoluções</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="rel_devolucao.php" class="btn btn-primary btn-rounded float-right" style="margin-left: 10px;"><i class="fa fa-file"></i> Criar Relatório</a>
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
                                        <th>Modo de Pagamento</th>
                                        <th>Cliente</th>
                                        <th>Ref. VD</th>
                                        <th>Motivo</th>
                                        <th>Utilizador</th>
                                        <th>Data</th>
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
            var table = $('#example').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "popdv.php", // URL do script PHP que retornará os dados de recibos
                "type": "GET",
                "dataSrc": "data", // Chave onde estão os dados na resposta JSON
                "data": function (d) {
                    // Verifica se o DataTables está enviando o 'draw' corretamente
                    console.log(d); // Para depurar os parâmetros enviados
                    return d;
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "descricao" },
                { "data": "valor" },
                { "data": "modo" },
                { "data": "cliente" },
                { "data": "pedidos" },
                { "data": "motivo" },
                { "data": "usuario" },
                { "data": "datax" },
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
            window.open('../dv_pdf.php?id_dv=' + data.id, '_blank');
        });

        });
    </script>
<!-- add-patient24:07-->
</html>
