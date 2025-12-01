<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$id_ss = $_GET['id_ss'];

$sql = "SELECT *, (SELECT serie FROM saida_stock as e WHERE e.id = es.ss) as serie FROM ss_artigos as es WHERE ss = '$id_ss'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_array($rs);
}
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
                        <h4 class="page-title">Artigos da Saída SS#<?php echo $dados['serie'] ?>/<?php echo $id_ss ?></h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="saida_stock.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Saída</a>
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
										<th>Usúario</th>
										<th>Data</th>
										<th class="text-right">Ações</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$sql = "SELECT *, (SELECT nomeproduto FROM produto as g WHERE g.idproduto = e.artigo) as artigos, (SELECT nome FROM users as g WHERE g.id = e.user) as users FROM ss_artigos as e WHERE ss = '$id_ss'";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr>
											<td><?php echo $dados['id']; ?></td>
											<td><?php echo $dados['artigos']; ?></td>
											<td><?php echo $dados['qtd']; ?></td>
											<td><?php echo $dados['users']; ?></td>
											<td><?php echo $dados['data']; ?></td>
											<td class="text-right">
												<a href="#" style="margin-right: 15px;"><i class="fa fa-trash-o m-r-5 apaga"></i> </a>
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

<link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#example').DataTable();
            $(".apaga").on('click', function() {
            	var resposta = window.confirm("Deseja apagar o artigo?");
            	if (resposta = true) {
            		var id = $(this).parents("tr").children().first().text();
            		$.ajax({
					url: 'ApagarArtigoSaidaStock.php',
					type: 'GET',
					data: {
						id: id
					},
					success: function(data) {
						alert("Artigo #"+id+" apagado com sucesso!");
						location.reload();
					}
				});
            	}

            });
        });
    </script>
<!-- patients23:19-->
</html>