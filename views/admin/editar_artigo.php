<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
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
                        <a href="produtos.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Artigos</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Editar Artigo</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="POST" action="daos/editar_artigo_dao.php" enctype="multipart/form-data">
                            <div class="row">
                                <?php 
                                    $id = $_GET['id'];
                                    $sql = "SELECT * FROM produto WHERE idproduto = '$id'";
                                    $rs = mysqli_query($db, $sql) or die(mysqli_error());
                                    while ($dados  = mysqli_fetch_array($rs)) {
                                ?>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nome do Artigo <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="nome_artigo" value="<?php echo $dados['nomeproduto']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Stock Minimo</label>
                                        <input class="form-control" type="number" min="0" step="0.01" name="stock" value="<?php echo $dados['stock_min']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Preço de Compra<span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" min="0" name="preco_compra" value="<?php echo $dados['preco_compra']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Preço de Venda<span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" min="0" name="preco" value="<?php echo $dados['preco']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Código de Barras</label>
                                        <input class="form-control" type="text" name="codbar" value="<?php echo $dados['codbar']; ?>">
                                    </div>
                                </div>
		                        <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Estocável</label>
                                        <input class="form-control" type="number" name="stocavel" value="<?php echo $dados['stocavel']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" type="hidden" name="id" value="<?php echo $id; ?>">
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Criar Artigo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
</body>


<!-- add-patient24:07-->
</html>
