<?php 
    session_start();
    if(!isset($_SESSION['idUsuario'])){
    	header("location:../");
    }
    include_once '../conexao/index.php';
    error_reporting(E_ALL);
    $_SESSION['idUsuario'] = $_SESSION['idUsuario'];
    $_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
    $_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
?>
<html>
    <head>
      <meta charset="utf-8">
      <title>RC - Recibo</title>
      <link rel="shorcut icon"  href="../img/config/iCone.png">
      <link rel="stylesheet" href="bootstrap.css">
      <link rel="stylesheet" href="bootstrap.min.css">
      <link rel="stylesheet" href="all.min.css">
      <!-- Adicione o Select2 ao seu projeto -->
    <link href="datatables.min.css" rel="stylesheet"/>
	
      <style>
          *{
              font-family: system-ui;
              font-size: 10pt;
              
          }
          #example tr{
				cursor: pointer;
			}
          body{
              background: #eee;
          }
          .pdt img{
              max-width: 100%;
              width: 60%;
          }
          
          .sticky-left {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            height: 90vh;
        }
        .pdt, .lista li{
            transition: all .2s ease-in-out;
        }
        .pdt:hover{
            background: #eee;
            cursor: pointer;
            border-radius: 15px;
        }
        
        .lista li:hover{
            background: #fff;
            cursor: pointer;
        }

        /* Adiciona um estilo para a coluna fixa Ã  direita */
        .sticky-right {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            height: 90vh;
        }
        .sticky-header {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 1000; 
    }
    
    #valorPago{
        height: 70px; /* Defina a altura desejada em pixels */
    }
    #qrcode{
        height: 40px; /* Defina a altura desejada em pixels */
        width: 100%;
    }
    
    .select2-container--default .select2-selection--single {
        height: 40px; /* Ajuste a altura conforme necess¨¢rio */
    }
    
    /* Seletor espec¨ªfico para a lista suspensa do Select2 */
    .select2-container--default .select2-dropdown--below {
        max-height: 200px; /* Ajuste a altura m¨¢xima da lista suspensa conforme necess¨¢rio */
    }
      </style>
    </head>
    <body>
              <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">Detalhes do Recibo</h3>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                          </div>
                          <div class="modal-body">
                              <div class="col-sm-12" style="display:flex; flex-direction:column;justify-content: center;align-items: center;">
                            <h4>Valor a Pagar</h4>
                            <h1 class="ttl"></h1>
                            </div>
                            <label class="form-label">Cliente</label>
                            <input type="text" class="form-control cliente" id="cliente">
                            <input type="hidden" class="form-control idp" id="idp">
                            <label class="form-label">Metodo de Pagamento</label><br>
                            <select class="form-select metodo" aria-label="Default select example" id="select" style="width: 100%;padding: 5px 0;">
                              <option value="Cartao de Credito">Cartão de Credito</option>
                              <option value="Transferencia Bancaria">Transferência Bancária</option>
                              <option value="Numerario" selected>Númerario</option>
                              <option value="Mpesa">Mpesa</option>
                              <option value="Emola">Emola</option>
                              <option value="Mkesh">Mkesh</option>
                              <option value="Conta Movel">Conta Móvel</option>
                            </select>
                         
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary btn_criar">Efetuar Pagamento</button>
                          </div>
                        </div>
                      </div>
              </div>

        <div class="container-fluid">
            <div class="row sticky-header">
                <div class="col-sm-1 text-center p-1" style="background: #31B3D0;color: #fff;">
                    <img src="iConewhite.png" style="max-width: 100%; width: 40%;">
                </div>
                <div class="col-sm-11" style="background: #38D0ED;color:#fff;display:flex;flex-direction:row;justify-content:space-between;">

                </div>
            </div>
            <div class="row">
                <!-- Primeira Coluna -->
        
                <!-- Segunda Coluna -->
                <div class="col-sm-8 segunda1 table-responsive" style="background:#fff;display: flex;flex-direction: row;overflow: auto;height:90vh;color: #333;justify-content: center;padding-top: 1%;">
                    <table id="example" style="height: 86%;width: 100%;">
                    	<thead>
                    		<th>#</th>
                    		<th>Descricao</th>
                            <th>Cliente</th>
                    		<th>Valor</th>
                            <th>Iva</th>
                            <th>Total</th>
                    		<th>Condicoes de Pagamento</th>
                    	</thead>
                    	<tbody>
                    		<?php
							    $sql = "SELECT *, (SELECT CONCAT(nome,' ',apelido) FROM clientes as c WHERE c.id = p.cliente) as nms FROM factura as p WHERE recibo = 0 AND nota_credito = 0";
							    $rs = mysqli_query($db, $sql);
							    while ($dados = mysqli_fetch_array($rs)) {
							?>
							    <tr data-idfatura="<?php echo $dados['id']; ?>">
							        <td><?php echo $dados['id'] ?></td>
							        <td><?php echo "FA ".$dados['serie']."/".$dados['n_doc']; ?></td>
							        <td><?php echo $dados['nms'] ?></td>
							        <td><?php echo $dados['valor'] ?></td>
                                    <td><?php echo $dados['iva'] ?></td>
                                    <td><?php echo ($dados['valor']+ $dados['iva']); ?></td>
                                    <td><?php echo $dados['condicoes'] ?></td>
							    </tr>
							<?php } ?>
                    	</tbody>
                    </table>
                </div>

        
                <!-- Terceira Coluna -->
                <div class="col-sm-4 sticky-right" style="padding: 0;background: #eee;">
                    <div class="col-sm-12 text-center p-2" style="display: flex;flex-direction: row;justify-content: center">
                        <h3 id="nrpedido" style="font-size: 14pt;"></h3>
                    </div>
                    <div class="col-sm-12 p-2" style="background:#fff;height:70vh;overflow:auto;" id="poptempart">
                        
                    </div>
                    <div class="col-sm-12 text-center" style="display:flex; flex-direction:row;justify-content: space-between;background:#38D0ED;height:13vh;color:#fff;">
                        <div id="cancelar" class="col-sm-6" style="display:flex; flex-direction:column;justify-content: center;align-items: center;cursor:pointer;">
                            <h4>Cancelar</h4>
                        </div>
                        <div class="col-sm-6" style="display:flex; flex-direction:column;justify-content: center;align-items: center;border-left: 1px solid #fff;cursor:pointer;" data-toggle="modal" data-target="#exampleModal3">
                            <h4>Pagar</h4>
                            <h2 class="ttl"></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>






    <script src="jquery-3.5.1.js"></script>
    <script src="bootstrap.js"></script>
    <script src="bootstrap.min.js"></script>
    <link rel="stylesheet" href="select2.min.css">
    <script src="select2.min.js"></script>
    <script src="datatables.min.js"></script>
    <script>
        $(function(){
            setInterval(nrpedido, 1000);

			function nrpedido(){
				$.ajax({
						url: 'rcbs/nrpedido.php',
						type: 'GET',
						success: function(data) {
							$("#nrpedido").html(data);
						},
						error: function() {
							$("#nrpedido").html("Ocorreu um erro ao popular o numero do pedido!");
						}
				});
			}
			
            popprodtable();
            showtot();
            qrcodefocus();

            function popprodtable(){
                $.ajax({
                    url: 'rcbs/popprodtable.php',
                    type: 'GET',
                    success: function(data){
                        $("#poptempart").html(data);
                        $(".btnremove").click(function() {
                            var fatura = $(this).data("fatura");
                            btnremover(fatura);
                        });
                    }
                });
            }
            
            function showtot() {
                $.ajax({
                    url: 'rcbs/show_tot.php',
                    type: 'GET',
                    success: function(data){
                        $(".ttl").html(data);
                    }
                });
            }
            function qrcodefocus() {
                $("#qrcode").focus();
            }
            $("#example tbody tr").click(handleClick);
            function handleClick() {
                var idfatura = this.getAttribute('data-idfatura'); // Obt¨¦m o valor do input dentro da div clicada
                $.ajax({
                    type: "POST",
                    url: "rcbs/addarttemp.php", // Especifique o arquivo PHP desejado
                    data: { idfatura: idfatura }, // Passa o valor do input como um par0‰9metro
                    success: function(response){
                        popprodtable();
                        showtot();
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "rcbs/addtempinfo.php", // Especifique o arquivo PHP desejado
                    data: { idfatura: idfatura }, // Passa o valor do input como um par0‰9metro
                    success: function(response){
                        var dados = JSON.parse(response);
                        $('.cliente').val(dados.cliente).trigger('change');
                    }
                });

            }
            
            $(".btn_criar").click(function(){
                criarrecibo();
            });
            //processamento para criar o pedido
            function criarrecibo() {
                if (confirm("Tem certeza de que deseja efetuar o pagamento?")) {
                    var cliente = $(".cliente").val();
                    var metodo = $(".metodo").val();
                    if(cliente == ""){
                        alert("Informe algum cliente!");
                    }else{
                        $.ajax({
                            type: "POST",
                            url: "rcbs/criarrecibo.php", // Especifique o arquivo PHP desejado
                            data: { cliente: cliente, metodo: metodo}, // Passa o valor do input como um par0‰9metro
                            success: function(response){
                                if(response == 450000000000000000000){
                                    alert("Nao e possivel criar uma devolucao com a serie inferior ou superior ao ano corrente.");
                                }else if(response == 400000000000000000000){
                                    alert("Algo Falhou");
                                }else{
                                    window.open("rc_pdf.php?id_rc="+response+"");
								    location.reload();
                                }
                                console.log(response);
                            },
                            error: function(){
                                alert("Ocorreu um erro com a requisicao ajax");
                            }
                        }); 
                    }
                }
            }
            
            $("#cancelar").click(function() {
            	if (confirm("Tem certeza de que deseja remover este item?")){
            		$.ajax({
                        url: 'rcbs/artremoveall.php', // Seu arquivo PHP para remover o item
                        method: 'POST',
                        success: function(response) {
                            popprodtable();
                            showtot();
                            qrcodefocus();
                            console.log(response); // Exemplo de sa¨ªda de resposta do servidor
                        },
                        error: function(xhr, status, error) {
                            // Lidar com erros de requisi0Š40Š0o Ajax, se necess¨¢rio
                            console.error(xhr.responseText);
                        }
                    });
            	}
            });
            function btnremover(fatura){
                // Exiba uma caixa de di¨¢logo de confirma0Š40Š0o
                if (confirm("Tem certeza de que deseja remover este item?")) {
                    // Obtenha o ID do produto associado a este bot0Š0o
                    var fatura = fatura;
                    
                    // Enviar requisi0Š40Š0o Ajax para remover o item
                    $.ajax({
                        url: 'rcbs/artremove.php', // Seu arquivo PHP para remover o item
                        method: 'POST',
                        data: { fatura: fatura },
                        success: function(response) {
                            popprodtable();
                            showtot();
                        },
                        error: function(xhr, status, error) {
                            // Lidar com erros de requisi0Š40Š0o Ajax, se necess¨¢rio
                            console.error(xhr.responseText);
                        }
                    });
                }
            }
            
            $('#example').DataTable();
        });
    </script>
    </body>
</html>