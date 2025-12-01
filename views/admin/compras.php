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
                        <h4 class="page-title">Compras</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="nova_compra.php" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Nova Compra</a>
                    </div>
                </div>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table id="example">
								<thead>
									<tr>
										<th>#</th>
										<th>Descrição</th>
										<th>Custo em Mt</th>
										<th>Data</th>
										<th>Hora</th>
										<th class="text-right">Ações</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$sql = "SELECT * FROM compras";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr>
											<td><?php echo $dados['id']; ?></td>
											<td><?php echo $dados['descricao']; ?></td>
											<td><?php echo $dados['valor']; ?></td>
											<td><?php echo $dados['data']; ?></td>
											<td><?php echo $dados['hora']; ?></td>
											<td class="text-right">
												
												 <a class="" href="consultar_compras.php?id=<?php echo $dados['id']; ?>" target="_blank"><i class="fa fa-eye" style="margin-right:10px;"></i> </a>
											</td>
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

<?php 
if (isset($_GET['id_compra'])) {
    $id = $_GET['id_compra'];
    $sql = "DELETE FROM itens_comprados WHERE id_compra = '$id'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error());
    if ($rs > 0) {
    	$sql = "DELETE FROM compras WHERE id = '$id'";
    	$rs = mysqli_query($db, $sql) or die(mysqli_error());
    	if ($rs > 0) {
    		echo "<script>alert('Compra apagada com sucesso!!'); </script>";
        	echo "<script>window.location='compras.php'; </script>";
    	}else{
    		echo "<script>alert('Ocorreu um erro ao apagar a compra!!'); </script>";
        	echo "<script>window.location='compras.php'; </script>";
    	}
    }else{
        echo "<script>alert('Ocorreu um erro ao apagar a compra!!'); </script>";
        echo "<script>window.location='compras.php'; </script>";
    }
}
?>

<link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#example').DataTable();

        });
    </script>
<!-- patients23:19-->
</html>