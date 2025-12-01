<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit();
}

error_reporting(E_ALL);
ini_set("display_errors", 1);
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

// Lógica do inventário (mantida do seu código original)
if (isset($_GET['id'])) {
    $dia = date("d");
    $mes = date("m");

    // Atualizar inventário com dados do estoque agrupados por lotes e prazos
    $sqll = "SELECT p.idproduto, SUM(s.quantidade) AS stock_atual FROM produto p 
             JOIN stock s ON p.idproduto = s.produto_id 
             GROUP BY p.idproduto";
    $rss = mysqli_query($db, $sqll) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rss)) {
        $prt = $dados['idproduto'];
        $stck = $dados['stock_atual'];
        $sql = "SELECT artigo FROM inventario WHERE artigo = '$prt'";
        $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
        if (mysqli_num_rows($rs) > 0) {
            $sql = "UPDATE inventario SET `$dia` = '$stck' WHERE artigo = '$prt'";
            $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
        } else {
            $sql = "INSERT INTO inventario SET artigo = '".$dados['idproduto']."', `$dia` = '".$dados['stock_atual']."'";
            $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
        }
    }
    echo "<script>alert('Inventario criado com sucesso!'); </script>";
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
                        <h4 class="page-title">Stock Atual</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="?id=inventario" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Novo Inventário</a>
                        <a href="exportar_inventario.php" target="_blank" class="btn btn-secondary btn-rounded float-right m-r-10">
                            <i class="fa fa-file-pdf-o"></i> Exportar para PDF
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
                                        <th>Artigo</th>
                                        <th>Stock Min</th>
                                        <th>Stock Atual</th>
                                        <th>Lote</th>
                                        <th>Prazo</th>
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
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "popartgs.php",
                    "type": "GET",
                    "dataSrc": "data" 
                },
                "columns": [
                    { "data": "idproduto" },
                    { "data": "artigo" },
                    { "data": "stock_min" },
                    { "data": "stock" },
                    { "data": "lote" },
                    { "data": "datas" }
                ]
            });
        });
    </script>
</body>
</html>