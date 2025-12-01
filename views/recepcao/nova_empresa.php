<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

if($_SESSION['categoriaUsuario'] != "recepcao"){
	header("location:../admin/");
	exit;
}

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
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
                        <h4 class="page-title">Nova Empresa/Seguro</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="empresas.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="POST" action="daos/registar_empresa.php" id="formEmpresa">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Dados da Empresa</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Nome da Empresa/Seguro <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" name="nome" required maxlength="255">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>NUIT</label>
                                                <input class="form-control" type="text" name="nuit" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Contacto</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">+258</span>
                                                    </div>
                                                    <input class="form-control" type="text" name="contacto" placeholder="84 000 0000" maxlength="13">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input class="form-control" type="email" name="email" maxlength="255">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Número do Contrato</label>
                                                <input class="form-control" type="text" name="contrato" maxlength="255">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Data Início do Contrato</label>
                                                <input class="form-control" type="date" name="data_inicio_contrato">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Data Fim do Contrato</label>
                                                <input class="form-control" type="date" name="data_fim_contrato">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Desconto Geral (%)</label>
                                                <input class="form-control" type="number" name="desconto_geral" step="0.01" min="0" max="100" value="0">
                                                <small class="form-text text-muted">Desconto percentual aplicado a todos os serviços</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Endereço</label>
                                                <textarea class="form-control" name="endereco" rows="2" maxlength="500"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Observações</label>
                                                <textarea class="form-control" name="observacoes" rows="3" maxlength="1000"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Registar Empresa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

