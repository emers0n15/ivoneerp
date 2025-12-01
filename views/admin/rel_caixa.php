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
                        <h4 class="page-title">Relatório de Caixa</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        
                        <a href="rel_caixa_pedidos.php" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-print"></i> Criar Relatório</a>
                    </div>
                </div>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table id="example" class="display">
								<thead>
									<tr>
										<th>#</th>
										<th>Descrição</th>
										<th>Status</th>
										<th>Valor de Abertura</th>
										<th>Valor de Fecho</th>
										<th>Data de Abertura</th>
										<th>Data de Fecho</th>
										<th>Utilizador</th>
										<th>Ações</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$sql = "SELECT *, (SELECT nome FROM users as g WHERE g.id = p.usuario) as users FROM periodo as p";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr> 
											<td><?php echo $dados['idperiodo']; ?></td>
											<td><?php echo "CX#".$dados['serie']."/".$dados['idperiodo']; ?></td>
											<td><?php echo $dados['diaperiodo']; ?></td>
											<td><?php echo "".number_format($dados['aberturaperiodo'],2,".",","); ?></td>
											<td><?php echo "".number_format($dados['fechoperiodo'],2,".",","); ?></td>
											<td><?php echo $dados['dataaberturaperiodo']; ?></td>
											<td><?php echo $dados['datafechoperiodo']; ?></td>
											<td><?php echo $dados['users']; ?></td>
											<td class="text-right">
												<a href="#" style="margin-right: 15px;"><i class="fa fa-trash-o m-r-5 apaga"></i> </a>
												<a  href="caixa_pedidos.php?caixa=<?php echo $dados['idperiodo']; ?>" style="margin-right: 15px;"><i class="fa fa-eye m-r-5"></i> </a>
										<a  href="../boxpos/print_caixa.php?id=<?php echo $dados['idperiodo']; ?>" target="_blank"><i class="fa fa-print m-r-5"></i> </a> 
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
					url: 'ApagarCaixa.php',
					type: 'GET',
					data: {
						id: id
					},
					success: function(data) {
						alert("Caixa #"+id+" apagado com sucesso!");
						location.reload();
					}
				});
            	}

            });
        });
    </script>
</body>


<!-- patients23:19-->
</html>