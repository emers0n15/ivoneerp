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
    <style>
        /* Ajusta a tabela para ocupar toda a largura disponível */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table#example {
            width: 100%;
        }
    </style>
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
                        <h4 class="page-title">Clientes</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="novo_cliente.php" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Novo Cliente</a>
                    </div>
                </div>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table id="example" class="display">
								<thead>
									<tr>
										<th>#</th>
										<th>Nome</th>
										<th>Apelido</th>
										<th>Nuit</th>
										<th>Endereco</th>
										<th>Contacto</th>
										<th class="text-right">Ações</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$sql = "SELECT `id`, `nome`, `nuit`, `apelido`, `contacto`, `endereco` FROM clientes";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr>
											<td><?php echo $dados['id']; ?></td>
											<td><?php echo $dados['nome']; ?></td>
											<td><?php echo $dados['apelido']; ?></td>
											<td><?php echo $dados['nuit']; ?></td>
											<td><?php echo $dados['endereco']; ?></td>
											<td><?php echo $dados['contacto']; ?></td>
											<td class="text-right">

															<a  href="editar_clientes.php?id_cliente=<?php echo $dados['id']; ?>" style="margin-right: 15px;"><i class="fa fa-pencil m-r-5"></i> </a>
															<a  href="?id_cliente=<?php echo $dados['id']; ?>"><i class="fa fa-trash-o m-r-5"></i> </a>
											
														
													</div>
												</div>
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
if (isset($_GET['id_cliente'])) {
    $id = $_GET['id_cliente'];
    $sql = "DELETE FROM clientes WHERE id = '$id'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error());
    if ($rs > 0) {
    	echo "<script>alert('Cliente apagado com sucesso!!'); </script>";
    }else{
        echo "<script>alert('Ocorreu um erro ao apagar o cliente!!'); </script>";
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