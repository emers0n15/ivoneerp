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
                        <h4 class="page-title">Entradas de Caixa</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="entrada_caixa_conf.php" class="btn btn-primary btn-rounded float-right" style="margin-left: 10px;"><i class="fa fa-plus"></i> Nova entrada</a>
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
                                        <th>Utilizador</th>
                                        <th>Data</th>
                                        <th class="text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $sql = "SELECT *, (SELECT nome FROM users as u WHERE u.id = p.user) as nome FROM entrada_caixa as p";
                                        $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
                                        while ($dados = mysqli_fetch_array($rs)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $dados['id']; ?></td>
                                            <td><?php echo "EC#".$dados['serie']."/".$dados['id']; ?></td>
                                            <td><?php echo $dados['valor']; ?></td>
                                            <td><?php echo $dados['nome']; ?></td>
                                            <td><?php echo $dados['data']; ?></td>
                                            <td class="text-right">
                                                <a class="" href="ec_pdf.php?id_ec=<?php echo $dados['id']; ?>" target="_blank"><i class="fa fa-print" style="margin-right:10px;"></i> </a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    
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
            $('#example').DataTable();

        });
    </script>
<!-- add-patient24:07-->
</html>
