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
<html lang="en">



<head>
    <?php include 'includes/head.php'; ?>
    <style type="text/css">
        .table-responsive {
            overflow-x: auto;
        }

        table.dataTable {
            width: 100% !important; /* Garante que a tabela ocupe toda a largura disponível */
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
                        <h4 class="page-title">Consumo Medio</h4>
                    </div>

                </div>
                <style type="text/css">
                    #divv{
                        color: #fff;
                    }
                    #divv a{
                        margin-left: 10px;
                    }
                </style>
                <div class="row">
                    <div class="col-sm-6 col-3">
                        <h4 class="page-title">Preencha todos os campos assinalados com *</h4>
                    </div>
                    <div class="col-sm-6 col-9 text-right" id="divv">
                        <!-- <a id="printexcel" class="btn btn-primary btn-rounded float-right"><i class="fa fa-print"></i> Imprimir Excel</a> -->
                        <a id="printa4" class="btn btn-primary btn-rounded float-right"><i class="fa fa-print"></i> Imprimir A4</a>
                        <a id="processar" class="btn btn-primary btn-rounded float-right"><i class="fa fa-edit"></i> Processar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <h4 class="page-title"></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Data de Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="dt1" class="form-control data1" id="data1">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Data de Término <span class="text-danger">*</span></label>
                                        <input type="date" name="dt2" class="form-control data2" id="data2">
                                    </div>
                                </div>
                                
                            </div>
                            
                        </form>
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
                                        <th>Stock</th>
                                        <th>Consumo Medio Mensal</th>
                                        <th>Recomendacao</th>
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
    <link href="../datatables.min.css" rel="stylesheet"/>
    <script type="text/javascript" src="../../js/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="select2.min.css">
    <script type="text/javascript" src="select2.min.js"></script>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
     $('#example').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "consumo1.php",
            "type": "GET",
            "data": function(d) {
                d.data1 = $("#data1").val();
                d.data2 = $("#data2").val();
            },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "idproduto" },
            { "data": "nomeproduto" },
            { "data": "stock" },
            { "data": "consumo_medio_mensal" },
            { "data": "recomendacao" }
        ],
        "scrollX": true,
        "bDestroy": true,
        "bRetrieve": true
    });

    $("#processar").on('click', function() {
        $('#example').DataTable().ajax.reload();
    });

    $("#printa4").on('click', function() {
        var data1 = $("#data1").val();
        var data2 = $("#data2").val();
        if (data1 && data2) {
            window.open("consumo_medio_pdf.php?data1=" + data1 + "&data2=" + data2, "_blank");
        } else {
            alert("Por favor, selecione ambas as datas.");
        }
    });
});


    </script>
    
</body>

</html>
