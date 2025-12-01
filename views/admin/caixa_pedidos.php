<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$caixa = $_GET['caixa'];

$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_array($rs);
	$serie = $dados['serie'];
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
                        <h4 class="page-title">Vendas a Dinheiro do Caixa CX#<?php echo $dados['serie'] ?>/<?php echo $caixa ?></h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="rel_caixa.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-navicon"></i> Lista de Caixas</a>
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
										<th>Valor</th>
										<th>Troco</th>
										<th>Desconto</th>
										<th>Metódo de Pagamento</th>
										<th>Cliente</th>
										<th>N<sub>0</sub> da Devolução</th>
										<th>Data</th>
										<th class="text-right">Ações</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$sql = "SELECT *, (SELECT CONCAT(nome,' ',apelido) FROM clientes as g WHERE g.id = e.clientepedido) as cliente, (SELECT nome FROM users as g WHERE g.id = e.userpedido) as users, (SELECT serie FROM devolucao as g WHERE g.id = e.devolucao) as seriee FROM pedido as e WHERE periodo = '$caixa'";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr>
											<td><?php echo $dados['idpedido']; ?></td>
											<td><?php echo "VD#".$dados['serie']."/".$dados['idpedido']; ?></td>
											<td><?php echo $dados['pagamentopedido']; ?></td>
											<td><?php echo $dados['trocopedido']; ?></td>
											<td><?php echo $dados['disconto']; ?></td>
											<td><?php echo $dados['modo']; ?></td>
											<td><?php echo $dados['cliente']; ?></td>
											<td><?php echo "DV#".$dados['seriee']."/".$dados['devolucao']; ?></td>
											<td><?php echo $dados['data']; ?></td>
											<td class="text-right">
												<a href="#" style="margin-right: 15px;"><i class="fa fa-trash-o m-r-5 apaga"></i> </a>
												<a  href="../vd_pdf.php?id_vd=<?php echo $dados['idpedido']; ?>" target="_blank"><i class="fa fa-print m-r-5"></i> </a>
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
    <input type="hidden" id="caixa" value="<?php echo $caixa; ?>">
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
            	var resposta = window.confirm("Deseja apagar o caixa?");
            	if (resposta = true) {
            		var id = $(this).parents("tr").children().first().text();
            		$.ajax({
					url: 'ApagarPedidos.php',
					type: 'GET',
					data: {
						id: id,
						caixa: $("#caixa").val()
					},
					success: function(data) {
						alert("Pedido #"+id+" apagado com sucesso!");
						location.reload();
					}
				});
            	}

            });
        });
    </script>
<!-- patients23:19-->
</html>