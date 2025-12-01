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
            <?php 
                if ($_SESSION['categoriaUsuario'] != "armazem") {
            ?>
            <div class="content">
            	<div class="row">
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
							<span class="dash-widget-bg1"><i class="fa fa-ticket" aria-hidden="true"></i></span>
							<div class="dash-widget-info text-right">
								
								<?php 
                                    $sql = "SELECT count(id) as t FROM factura WHERE statuss = 0";
                                    $rs = mysqli_query($db,$sql);
                                    while ($dados = mysqli_fetch_array($rs)) {
                                ?>
                                    <h3><?php echo $dados['t']; ?></h3>
                                <?php
                                    }
                                ?>
								<span class="widget-title1">Facturas Pendentes <i class="fa fa-check" aria-hidden="true"></i></span>
							</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg2"><i class="fa fa-ticket"></i></span>
                            <div class="dash-widget-info text-right">
                                <?php 
                                    $sql = "SELECT count(id) as tttt FROM recibo";
                                    $rs = mysqli_query($db,$sql);
                                    while ($dados = mysqli_fetch_array($rs)) {
                                ?>
                                    <h3><?php echo $dados['tttt']; ?></h3>
                                <?php
                                    }
                                ?>
                                <span class="widget-title2">Recibos <i class="fa fa-check" aria-hidden="true"></i></span>
                                <span class="trend-badge trend-up"><i class="fa fa-arrow-up"></i> +0.8%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg3"><i class="fa fa-ticket" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right">
                                <?php 
                                    $sql = "SELECT count(id) as ttt FROM nota_de_credito";
                                    $rs = mysqli_query($db,$sql);
                                    while ($dados = mysqli_fetch_array($rs)) {
                                ?>
                                    <h3><?php echo $dados['ttt']; ?></h3>
                                <?php
                                    }
                                ?>
                                <span class="widget-title3">Notas de Crédito <i class="fa fa-check" aria-hidden="true"></i></span>
                                <span class="trend-badge trend-down"><i class="fa fa-arrow-down"></i> -0.4%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg4"><i class="fa fa-ticket" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right">
                                <?php 
                                    $sql4 = "SELECT count(idpedido) as tt FROM pedido";
                                    $rs4 = mysqli_query($db,$sql4);
                                    while ($dados4 = mysqli_fetch_array($rs4)) {
                                ?>
                                    <h3><?php echo $dados4['tt']; ?></h3>
                                <?php
                                    }
                                ?>
                                <span class="widget-title4">Vendas a Dinheiro <i class="fa fa-check" aria-hidden="true"></i></span>
                                <span class="trend-badge trend-up"><i class="fa fa-arrow-up"></i> +2.1%</span>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="row">
					<div class="col-12 col-md-6 col-lg-6 col-xl-6">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title d-inline-block">Transações Recentes</h4>
							</div>
							<div class="card-body p-0">
								<div class="table-responsive">
									<table class="table mb-0">
										<thead class="d-none">
											<tr>
												<th>Patient Name</th>
												<th>Doctor Name</th>
												<th>Timing</th>
											</tr>
										</thead>
										<tbody class="transacao">
											
											
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-12 col-md-10 col-lg-10 col-xl-6">
                        <div class="card member-panel">
							<div class="card-header bg-white">
								<h4 class="card-title mb-0">Caixas</h4>
							</div>
                            <div class="card-body">
                                <ul class="contact-list" id="caixas">
                                    
  
                                </ul>
                            </div>
                   
                        </div>
                    </div>
				</div>
            </div>
            <?php
                }else if ($_SESSION['categoriaUsuario'] == "armazem") {
            ?>
                <div class="content">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                            <div class="dash-widget">
                                <span class="dash-widget-bg1"><i class="fa fa-ticket" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    
                                    <?php 
                                        $sql = "SELECT count(id) as t FROM entrada_stock";
                                        $rs = mysqli_query($db,$sql);
                                        while ($dados = mysqli_fetch_array($rs)) {
                                    ?>
                                        <h3><?php echo $dados['t']; ?></h3>
                                    <?php
                                        }
                                    ?>
                                    <span class="widget-title1">Entrada de Stock <i class="fa fa-check" aria-hidden="true"></i></span>
                                    <span class="trend-badge trend-up"><i class="fa fa-arrow-up"></i> +1.2%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                            <div class="dash-widget">
                                <span class="dash-widget-bg2"><i class="fa fa-ticket"></i></span>
                                <div class="dash-widget-info text-right">
                                    <?php 
                                        $sql = "SELECT count(id) as tttt FROM saida_stock";
                                        $rs = mysqli_query($db,$sql);
                                        while ($dados = mysqli_fetch_array($rs)) {
                                    ?>
                                        <h3><?php echo $dados['tttt']; ?></h3>
                                    <?php
                                        }
                                    ?>
                                    <span class="widget-title2">Saida de Stock <i class="fa fa-check" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                            <div class="dash-widget">
                                <span class="dash-widget-bg3"><i class="fa fa-ticket" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    <?php 
                                        $sql = "SELECT count(id) as ttt FROM requisicao_interna";
                                        $rs = mysqli_query($db,$sql);
                                        while ($dados = mysqli_fetch_array($rs)) {
                                    ?>
                                        <h3><?php echo $dados['ttt']; ?></h3>
                                    <?php
                                        }
                                    ?>
                                    <span class="widget-title3">Requisição Interna <i class="fa fa-check" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                            <div class="dash-widget">
                                <span class="dash-widget-bg4"><i class="fa fa-ticket" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    <?php 
                                        $sql4 = "SELECT count(id) as tt FROM requisicao_externa";
                                        $rs4 = mysqli_query($db,$sql4);
                                        while ($dados4 = mysqli_fetch_array($rs4)) {
                                    ?>
                                        <h3><?php echo $dados4['tt']; ?></h3>
                                    <?php
                                        }
                                    ?>
                                    <span class="widget-title4">Requisição Externa <i class="fa fa-check" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            <?php 
                }
            ?>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
    <script type="text/javascript">
    	$(function() {
    		setInterval(atualizarProdutosMaisVendidos, 1000);
    		setInterval(atualizarVendasRecentes, 1000);
    		setInterval(atualizarTotalDiario, 1000);
            atualizarStockMin();
    		function atualizarProdutosMaisVendidos() {
    			$.ajax({
    				url: 'daos/atualizarProdutosMaisVendidos.php',
    				type: 'GET',
    				success: function(data){
    					$(".tb1").html(data);
    				},
    				error: function(){
    					$(".tb1").html("<tr><td colspan='4'>Erro ao popular os produtos mais vendidos!</td></tr>")
    				}
    			});
    		}
            function atualizarStockMin() {
    			$.ajax({
    				url: 'daos/atualizarStockMin.php',
    				type: 'GET',
    				success: function(data){
    					if (data != 0) {
					      // Código para exibir a notificação quando o registro for encontrado
					      if ("Notification" in window) {
					        Notification.requestPermission().then(function (permission) {
					          if (permission === "granted") {
					            var notification = new Notification("Artigos em rotura", {
					              body: "Sao "+data+" artigos em rotura",
					            });

					            notification.onclick = function () {
					              window.location.href = "stockmin_artigos.php";
					            };
					          }
					        });
					      }
					    } else {
					      // Código para tratar quando o registro não for encontrado
					    }
    				},
    				error: function(){
    					alert("Erro ao popular os artigos com stock min!")
    				}
    			});
    		}
    		function atualizarVendasRecentes() {
    			$.ajax({
    				url: 'daos/atualizarVendasRecentes.php',
    				type: 'GET',
    				success: function(data){
    					$(".tb2").html(data);
    				},
    				error: function(){
    					$(".tb2").html("<tr><td colspan='4'>Erro ao popular as vendas mais recentes!</td></tr>")
    				}
    			});
    		}
    		function atualizarTotalDiario() {
    			$.ajax({
    				url: 'daos/atualizarTotalDiario.php',
    				type: 'GET',
    				success: function(data){
    					$(".ac").html(data);
    				},
    				error: function(){
    					$(".ac").html("Erro ao popular o total diario!")
    				}
    			});
    		}

    		setInterval(atualizartransacoes, 1000);

    		function atualizartransacoes() {
    			$.ajax({
    				url: 'daos/atualizartransacoes.php',
    				type: 'GET',
    				success: function(data){
    					$(".transacao").html(data);
    				},
    				error: function(){
    					$(".transacao").html("<tr><td colspan='4'>Erro ao popular as transacoes!</td></tr>")
    				}
    			});
    		}

    		setInterval(atualizarcaixas, 1000);

    		function atualizarcaixas() {
    			$.ajax({
    				url: 'daos/atualizarcaixas.php',
    				type: 'GET',
    				success: function(data){
    					$("#caixas").html(data);
    				},
    				error: function(){
    					$("#caixas").html("<li>Erro ao popular os caixas!</li>")
    				}
    			});
    		}
    				
    	});
    </script>
</body>



</html>