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
                        <h4 class="page-title">Artigos da Compra</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <p id="p"></p>
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
										<th>Qtd</th>
										<th># Compra</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$id_compra = $_GET['id_compra'];
										$sql = "SELECT id_artigo, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = i.id_artigo) as artigo, qtd FROM itens_comprados as i WHERE id_compra = '$id_compra'";
										$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
										while ($dados = mysqli_fetch_array($rs)) {
									?>
										<tr> 
											<td><?php echo $dados['id_artigo']; ?></td>
											<td><?php echo $dados['artigo']; ?></td>
											<td class="editavel"><?php echo $dados['qtd']; ?></td>
											<td><?php echo $id_compra; ?></td>
										</tr>
									<?php
										}
									?>
								</tbody>
							</table>
						</div>
					</div>
                </div>
                <div class="row">
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="compras.php" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Finalizar Compra</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
</body>

<script type="text/javascript">
	$(function() {
		contentEditable();
		function contentEditable() {
				$("table tbody tr td.editavel").on('dblclick', function() {
				if ($('td > input').length > 0) {
					return;
				}
				var conteudoOriginal = $(this).text();
				var novoElemento = $('<input/>', {type: 'text', value:conteudoOriginal});
				$(this).html(novoElemento.bind('blur keydown', function(e) {
					var keyCode = e.which;
					var conteudoNovo = $(this).val();
					if (keyCode == 13 && conteudoNovo != "" && conteudoNovo != conteudoOriginal) {
						var objecto = $(this);
						$.ajax({
							url: 'daos/qtd_itens_comprados.php',
							type: 'POST',
							data: {
								id: $(this).parents('tr').children().first().text(),
								id_compra: $(this).parents('tr').children().last().text(),
								valor: conteudoNovo 
							},
							success: function(data) {
								objecto.parent().html(conteudoNovo);
								$("#p").text("");
								$("#p").append(data);
							}
						});
					}
					if (e.type == "blur" || keyCode == 27) {
						$(this).parent().html(conteudoOriginal);
					}
				}));
				$(this).children().select();
			});
			}
	});
</script>
<!-- patients23:19-->
</html>