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
                        <a href="produtos.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Artigos</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Novo Artigo</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="POST" action="daos/novo_artigo_dao.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nome do Artigo <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="nome_artigo">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Stock Minimo</label>
                                        <input class="form-control" type="number" value="0" min="0" name="stock" step="0.01">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Preço de Compra<span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" min="0" name="preco_compra">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Preço de Unitário<span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" min="0" name="preco">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Código de Barras</label>
                                        <input class="form-control" type="text" name="codbar">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Prefixo</label>
                                        <select class="form-control select" name="prefixo">
                                            <option>Selecione o prefixo</option>
                                                <option value="PART">PART</option>
                                                <option value="SEGURA">SEGURA</option>
                                                <option value="EMPRE">EMPRE</option>
                                                <option value="FARM">FARM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
									<div class="form-group">
										<label>Grupo</label>
										<select class="form-control select" name="grupo">
											<option>Selecione o grupo</option>
                                            <?php 
                                                $sql = "SELECT * FROM grupo_artigos;";
                                                $rs = mysqli_query($db,$sql);
                                                while ($dados = mysqli_fetch_array($rs)) {
                                            ?>
                                                <option value="<?php echo $dados['id']; ?>"><?php echo $dados['descricao']; ?></option>
                                            <?php
                                                }
                                            ?>
										</select>
									</div>
								</div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Família</label>
                                        <select class="form-control" name="familia">
                                            <option>Selecione a família</option>
                                            <?php 
                                                $sql = "SELECT * FROM familia_artigos;";
                                                $rs = mysqli_query($db,$sql);
                                                while ($dados = mysqli_fetch_array($rs)) {
                                            ?>
                                                <option value="<?php echo $dados['id']; ?>"><?php echo $dados['descricao']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>IVA</label>
                                        <select class="form-control" name="iva">
                                            <?php 
                                                $sql = "SELECT * FROM iva;";
                                                $rs = mysqli_query($db,$sql);
                                                while ($dados = mysqli_fetch_array($rs)) {
                                            ?>
                                                <option value="<?php echo $dados['percentagem']; ?>"><?php echo $dados['percentagem']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Estocável</label>
                                        <select class="form-control select" name="stocavel">
                                                <option value="0">Nao</option>
                                                <option value="1">Sim</option>
                                        </select>
                                    </div>
                                </div>
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
