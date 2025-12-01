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
                        <h4 class="page-title">Balanço do Stock</h4>
                    </div>

                </div>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-border table-striped custom-table datatable mb-0">
								<thead>
									<tr>
										<th>#</th>
										<th>Artigo</th>
										<th>Stock</th>
										<th>$ Compra</th>
										<th>$ Venda</th>
										<th>Custo</th>
										<th>Renda</th>
										<th>Lucro</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$sql = "SELECT idproduto, nomeproduto, stock, preco_compra, preco, (stock*preco_compra) AS custo, (stock*preco) AS renda, ((stock*preco) - (stock*preco_compra)) AS lucro  FROM produto";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
											$preco_compra = $dados['preco_compra'];
											$preco = $dados['preco'];
											$custo = $dados['custo'];
											$renda = $dados['renda'];
											$lucro = $dados['lucro'];
									?>
										<tr>
											<td><?php echo $dados['idproduto']; ?></td>
											<td><?php echo $dados['nomeproduto']; ?></td>
											<td><?php echo $dados['stock']; ?></td>
											<td><?php echo "".number_format($preco_compra,2,".",","); ?></td>
											<td><?php echo "".number_format($preco,2,".",","); ?></td>
											<td><?php echo "".number_format($custo,2,".",","); ?></td>
											<td><?php echo "".number_format($renda,2,".",","); ?></td>
											<td><?php echo "".number_format($lucro,2,".",","); ?></td>
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