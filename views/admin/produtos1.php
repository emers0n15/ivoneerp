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
                        <h4 class="page-title">Artigos</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="novo_artigo.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Novo Artigo</a>
                    </div>
                </div>
				<div class="row doctor-grid">
                    <?php 
                        $sql = "SELECT * FROM produto";
                        $rs = mysqli_query($db, $sql) or die(mysqli_error());
                        while ($dados  = mysqli_fetch_array($rs)) {
                            $stock = $dados['stock'];
                            $preco = $dados['preco'];
                            $balanco = $stock*$preco;
                    ?>
                        <div class="col-md-4 col-sm-4  col-lg-3">
                        <div class="profile-widget">
                            <div class="doctor-img">
                                <a class="avatar"><img alt="" src="../../img/imageProduto/<?php echo $dados['img'] ?>"></a>
                            </div>
                            <div class="dropdown profile-action">
                                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="editar_artigo.php?id=<?php echo $dados['idproduto']; ?>"><i class="fa fa-pencil m-r-5"></i> Editar</a>
                                    <a class="dropdown-item" href="?id=<?php echo $dados['idproduto']; ?>" data-toggle="modal"><i class="fa fa-trash-o m-r-5"></i> Apagar</a>
                                </div>
                            </div>
                            <h4 class="doctor-name text-ellipsis"><a><?php echo $dados['nomeproduto']; ?></a></h4>
                            <div class="doc-prof">Stock - <?php echo $dados['stock']; ?> - Balanço = <?php echo "".number_format($balanco,2,".",","); ?></div>
                            <div class="user-country">
                                <i class="fa fa-dollar"></i>Compra -  <?php echo $dados['preco_compra']; ?> Venda - <?php echo $dados['preco']; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                         
                </div>
            </div>

        </div>

    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
</body>

<?php 
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM produto WHERE idproduto = '$id'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error());
    if ($rs > 0) {
        echo "<script>alert('Artigo apagado com sucesso!!'); </script>";
        echo "<script>window.location='produtos.php'; </script>";
    }else{
        echo "<script>alert('Ocorreu um erro ao apagar o produto!!'); </script>";
        echo "<script>window.location='produtos.php'; </script>";
    }
}
?>
<!-- doctors23:17-->
</html>