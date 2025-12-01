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
$id_compra = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="en">



<head>
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <div class="main-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-3" style="margin: 10px auto;">
                        <h4 class="page-title">Relatorio de Compras</h4>
                    </div>
                    <div class="col-sm-3 col-9 text-right m-b-20" style="margin: 10px auto;">
                        <h4 class="page-title">Compra # <?php echo $id_compra; ?></h4>
                    </div>
                </div>
                <br><br>
                <div class="row">
                	<?php
						$sql = "SELECT * FROM compras WHERE id = '$id_compra'";
						$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
						while ($dados = mysqli_fetch_array($rs)) {
					?>
						<div class="col-sm-11 col-3" style="margin: 10px auto;display: flex;flex-direction: row;">
	                        <h5 style="margin-right: 10%;">Descrição: <?php echo $dados['descricao']; ?></h5>
	                        <h5>Custo: <?php echo $dados['valor']; ?></h5>
	                    </div>
	                    <div class="col-sm-11 col-3" style="margin: 10px auto;display: flex;flex-direction: row;">
	                        <h5 style="margin-right: 10%;">Data: <?php echo $dados['data']; ?></h5>
	                        <h5>Horas: <?php echo $dados['hora']; ?></h5>
	                    </div>
					<?php
						}
					?>
                </div>
                <br><br>
				<div class="row">
					<div class="col-md-11" style="margin: 10px auto;">
						<div class="table-responsive">
							<table class="table table-border table-striped custom-table datatable mb-0">
								<thead>
									<tr>
										<th>#</th>
										<th>Artigo</th>
										<th>Qtd</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$sql = "SELECT id_artigo, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = i.id_artigo) as artigo, qtd FROM itens_comprados as i WHERE id_compra = '$id_compra' AND qtd > 0";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr> 
											<td><?php echo $dados['id_artigo']; ?></td>
											<td><?php echo $dados['artigo']; ?></td>
											<td><?php echo $dados['qtd']; ?></td>
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
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
</body>


<!-- patients23:19-->
</html>