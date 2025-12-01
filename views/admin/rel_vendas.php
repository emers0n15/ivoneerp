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
<html lang="pt">



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
                        <h4 class="page-title">Relatório de Vendas</h4>
                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h6 class="page-title">Selecione o periodo de verificação</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="GET" action="relatorio/vendas.php" target="_blank">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Data de Inicio<span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" name="data_inicio">
                                    </div>
                                </div>
             
								<div class="col-sm-6">
									<div class="form-group">
										<label>Horas de Inicio</label>
										<input class="form-control" type="time" name="hora_inicio">
									</div>
								</div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Data de Termino<span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" name="data_final">
                                    </div>
                                </div>
             
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Horas de Termino</label>
                                        <input class="form-control" type="time" name="hora_final">
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Criar Relatório</button>
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
