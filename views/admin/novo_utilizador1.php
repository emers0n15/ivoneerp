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
                        <h4 class="page-title">Utilizadores</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="produtos.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Utilizadores</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Novo Utilizador</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="daos/novo_utilizador_dao.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nome <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="nome">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Usúario <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="user">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Senha <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="pass">
                                    </div>
                                </div>
             
								<div class="col-sm-6">
									<div class="form-group">
										<label>Categoria</label>
										<select class="form-control select" name="categoria">
											<option value="admin">Administrador</option>
                                            <option value="recepcao">Recepção</option>
											<option value="armazem">Armazém</option>
                                            <option value="contabilidade">Contabilidade</option>
										</select>
									</div>
								</div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Criar Utilizador</button>
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
