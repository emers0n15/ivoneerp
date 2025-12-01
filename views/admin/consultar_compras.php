<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
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
                        <h4 class="page-title">Consultar Compras</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        
                        <a href="compras.php" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Compra</a>
                        <a href="print_compras.php?id=<?php echo $id_compra; ?>" class="btn btn btn-primary btn-rounded float-right" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
                    </div>
                </div>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table id="example">
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
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
    <link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#example').DataTable();

        });
    </script>
</body>


<!-- patients23:19-->
</html>