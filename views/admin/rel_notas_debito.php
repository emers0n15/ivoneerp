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
    <link rel="stylesheet" type="text/css" href="../../css/select2.min.css">
    <script type="text/javascript" src="../../js/select2.min.js"></script>
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
                        <h4 class="page-title">Novo Relatório de Notas de Debito</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="nota_de_debito.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Notas de Debito</a>
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
                        <a id="printa4" class="btn btn-primary btn-rounded float-right"><i class="fa fa-print"></i> Imprimir A4</a><!-- 
                        <a id="processar" class="btn btn-primary btn-rounded float-right"><i class="fa fa-edit"></i> Processar</a> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <h4 class="page-title"></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="daos/novo_cliente.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Data de Inicio <span class="text-danger">*</span></label>
                                        <select class="form-control data1" id="data1">
                                            <option>Selecione a data de inicio</option>
                                            <?php 
                                                $sql = "SELECT data FROM nota_debito;";
                                                $rs = mysqli_query($db,$sql);
                                                while ($dados = mysqli_fetch_array($rs)) {
                                            ?>
                                                <option value="<?php echo $dados['data']; ?>"><?php echo $dados['data']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Data de Término <span class="text-danger">*</span></label>
                                        <select class="form-control data2" id="data2">
                                            <option>Selecione a data de término</option>
                                            <?php 
                                                $sql = "SELECT data FROM nota_debito;";
                                                $rs = mysqli_query($db,$sql);
                                                while ($dados = mysqli_fetch_array($rs)) {
                                            ?>
                                                <option value="<?php echo $dados['data']; ?>"><?php echo $dados['data']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Serie <span class="text-danger">*</span></label>
                                        <select class="form-control serie" id="serie">
                                            <option>Selecione a serie</option>
                                            <?php 
                                                $sql = "SELECT * FROM serie_factura;";
                                                $rs = mysqli_query($db,$sql);
                                                while ($dados = mysqli_fetch_array($rs)) {
                                            ?>
                                                <option value="<?php echo $dados['ano_fiscal']; ?>"><?php echo $dados['ano_fiscal']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="xample">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Série</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>IVA(16%)</th>
                                        <th>Cliente</th>
                                        <th>Prazo</th>
                                        <th>Status</th>
                                        <th>Recibo</th>
                                        <th>N/ Crédito</th>
                                        <th>N/ Débito</th>
                                        <th>Utilizador</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> -->
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
        $(function() {
            $('#xample').DataTable();
            $("#printa4").on('click', function() {
                var cliente = $("#cliente").val();
                var serie = $("#serie").val();
                var data1 = $("#data1").val();
                var data2 = $("#data2").val();
                window.open("printNDs.php?serie="+serie+"&data1="+data1+"&data2="+data2+"", target="_blank");
            });

            $('.utilizador').select2();
            $('.serie').select2();
            $('.data1').select2();
            $('.data2').select2();
        });
    </script>
    
</body>


<!-- add-patient24:07-->
</html>
