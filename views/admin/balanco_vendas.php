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
                        <h4 class="page-title">Balanço de Vendas</h4>
                    </div>

                </div>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-border table-striped custom-table datatable mb-0">
								<thead>
									<tr>
										<th>Artigo</th>
										<th>Qtd Vendida</th>
										<th>Valor</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$sql = "SELECT DISTINCT(`produtoentrega`) as PROD, SUM(`qtdentrega`) as qt, SUM(`totalentrega`) as vl FROM `entrega` GROUP BY `produtoentrega`";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr>
											<td><?php echo $dados['PROD']; ?></td>
											<td><?php echo $dados['qt']; ?></td>
											<td><?php echo $dados['vl']; ?></td>
										</tr>
									<?php
										}

										$sql = "SELECT SUM(`totalentrega`) as vl FROM `entrega`";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr>
											<td></td>
											<td></td>
											<td>TOTAL - <?php echo $dados['vl']; ?></td>
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
<!-- patients23:19-->
</html>