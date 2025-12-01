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

if(isset($_GET['id_cliente'])){
    $id = $_GET['id_cliente'];
    $sql = "SELECT * FROM clientes WHERE id = '$id'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    if (mysqli_num_rows($rs) > 0) {
        $dados = mysqli_fetch_array($rs);
    }
}
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
                        <h4 class="page-title">Editar Clientes</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="clientes.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Clientes</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Editar Cliente</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="daos/editar_cliente.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nome <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="nome" required="" value="<?php echo $dados['nome']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Apelido <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="apelido" required="" value="<?php echo $dados['apelido']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nuit <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" value="<?php echo $dados['nuit']; ?>" name="nuit" required="" >
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Contacto <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" value="<?php echo $dados['contacto']; ?>" name="contacto" required="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Endereco <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="endereco" required="" value="<?php echo $dados['endereco']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Desconto % <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" name="desconto" required="" value="<?php echo $dados['desconto']; ?>" >
                                        <input class="form-control" type="hidden" name="id" required="" value="<?php echo $dados['id']; ?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Editar Cliente</button>
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
