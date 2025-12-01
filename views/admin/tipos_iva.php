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
                        <h4 class="page-title">IVA</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <h4 class="page-title">Novo IVA</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="daos/novo_iva.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Percentagem <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" name="percentagem" placeholder="Exemplo 16">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Motivo <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="motivo">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="xample">
                                            <thead>
                                                <tr>
                                                    <th>Percentagem</th>
                                                    <th>Motivo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $sql = "SELECT * FROM iva ORDER BY id DESC";
                                                    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
                                                    if (mysqli_num_rows($rs) > 0) {
                                                        while ($dados = mysqli_fetch_array($rs)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $dados['percentagem'] ?></td>
                                                        <td><?php echo $dados['motivo'] ?></td>
                                                    </tr>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Criar IVA</button>
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
       });
    </script>
</body>


<!-- add-patient24:07-->
</html>
