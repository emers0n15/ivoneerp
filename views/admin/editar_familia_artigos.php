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

if(isset($_GET['id_familia'])){
    $id = $_GET['id_familia'];
    
    $sql = "SELECT * FROM familia_artigos WHERE id = '$id'";
    $rs = mysqli_query($db, $sql);
    if(mysqli_num_rows($rs)>0){
        $dados = mysqli_fetch_array($rs);
    }
}

if(isset($_POST['btn'])){
    $descricao = $_POST['descricao'];
    $ids = $_POST['ids'];
    $sql = "UPDATE familia_artigos SET descricao = '$descricao' WHERE id = '$ids'";
    $rs = mysqli_query($db, $sql);
    if($rs > 0){
        echo "<script>alert('Familia editada com sucesso!!'); </script>";
    	echo "<script>window.location.href='familia_artigos.php'; </script>";
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
                        <h4 class="page-title">Editar Família</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="familia_artigos.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Família</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Editar Família</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Descrição <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="descricao" required="" value="<?php echo $dados['descricao']; ?>">
                                        <input type="hidden" name="ids" value="<?php echo $id; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Criar Família</button>
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
