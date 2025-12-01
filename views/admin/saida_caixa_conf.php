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
                        <h4 class="page-title">Saída de Caixa</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="saida_caixa.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Saídas</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <h4 class="page-title">Nova Saída</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="daos/nova_saida_caixa.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Valor <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" name="valor">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Caixa/Utilizador <span class="text-danger">*</span></label>
                                        <select class="form-control select" name="caixa" id="caixa">
                                            <option>Selecione o caixa/utilizador</option>
                                            <?php 
                                                $sql = "SELECT *, (SELECT nome FROM users as u WHERE u.id = p.usuario) as nome, p.usuario FROM periodo as p WHERE diaperiodo = 'Aberto'";
                                                $rs = mysqli_query($db,$sql);
                                                while ($dados = mysqli_fetch_array($rs)) {
                                            ?>
                                                <option value="<?php echo $dados['idperiodo']; ?>"><?php echo $dados['nome']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="xample">
                                            <thead>
                                                <tr>
                                                    <th>Valor no caixa</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Criar saída</button>
                            </div>
                        </form>
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
       $(function() {
            $('#xample').DataTable();
             $("#caixa").on('change', function() {
                $.ajax({
                    url: 'carregarValorCaixas.php',
                    type: 'GET',
                    data:{
                        caixa: $(this).val()
                    },
                    success: function(data) {
                        $("tbody").html(data);
                    },
                    error: function() {
                        $("tbody").html("<tr><td colspan='13'>Erro ao popular o valor do caixa! Contacte o administrador do sistema.</td></tr>");
                    }
                });
            });
       });
    </script>
</body>


<!-- add-patient24:07-->
</html>
