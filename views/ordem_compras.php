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
      <title>OC - Ordem de Compra</title>
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
                      <div class="modal-dialog modal-md">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">Detalhes da Compra</h3>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                          </div>
                          <div class="modal-body">
                              <div class="col-sm-12" style="display:flex; flex-direction:column;justify-content: center;align-items: center;">
                            <h4>Valor</h4>
                            <h1 class="ttl"></h1>
                            </div>
                            <br>
                            <div class="row">
                            <div class="col-sm-12">
                              <label class="form-label">Fornecedor</label><br>
                            <select class="form-select js-example-basic-single1  fornecedor" aria-label="Default select example" id="select" style="width: 100%;">
                                <?php 
                          $sql = "SELECT * FROM fornecedor";
                          $rs = mysqli_query($db, $sql);
                          while ($dados = mysqli_fetch_array($rs)) {
                        ?>
                          <option value="<?php echo $dados['id'];?>"><?php echo $dados['nome'];?></option>
                        <?php
                          }
                        ?>
                            </select>
                            </div>
                            </div>
                            <br>
                            <div class="row">
                            <div class="col-sm-12">
                              <label class="form-label">Prazo</label>
                              <input type="date" class="form-control prazo" id="prazo">
                            </div>
                            </div>
                            <br>
                            <div class="row">
                            <div class="col-sm-12">
                            <label class="form-label">Metodo de Pagamento</label>
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
                        </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary btn_criar">Efetuar Compra</button>
                          </div>
                        </div>
                      </div>
              </div>

        <div class="container-fluid">
            <div class="row sticky-header">
                <div class="col-sm-1 text-center p-1" style="background: #31B3D0;color: #fff;">
                    <img src="iConewhite.png" style="max-width: 100%; width: 40%;">
                </div>
                <div class="col-sm-7" style="background: #38D0ED;color:#fff;display:flex;flex-direction:row;justify-content:space-between;">

                </div>
                <div class="col-sm-4" style="background: #fff;">
                    <ul class="list-unstyled mt-2 listou" style="display:flex;flex-direction:row;justify-content:flex-end;width: 100%;">
                        <li class="col-sm-12">
                              <input type="text" class="form-control" placeholder="QR Code aqui" id="qrcode">
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">

        
                <!-- Segunda Coluna -->
                <div class="col-sm-8 segunda1 table-responsive" style="background:#fff;display: flex;flex-direction: row;overflow: auto;height:90vh;color: #333;justify-content: center;padding-top: 1%;">
                    <table id="example" style="height: 86%;width: 100%;">
                      <thead>
                            <th>#</th>
                            <th>Descricao</th>
                            <th>Preco</th>
                            <th>Lote</th>
                            <th>Stock Disponivel</th>
                            <th>Validade</th>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "
                                SELECT 
                                    p.idproduto, 
                                    p.nomeproduto, 
                                    p.preco, 
                                    (SELECT descricao FROM grupo_artigos as g WHERE g.id = p.grupo) as g,
                                    MIN(s.lote) as lote,  -- pega o lote com menor prazo
                                    MIN(s.quantidade) as quantidade,  -- pode ser ajustado de acordo com sua necessidade
                                    MIN(s.prazo) as prazo,  -- pega o prazo mais próximo
                                    MIN(s.quantidade_inicial) as quantidade_inicial  -- se necessário
                                FROM produto as p
                                JOIN stock as s ON s.produto_id = p.idproduto
                                WHERE s.prazo >= CURDATE()
                                GROUP BY p.idproduto, p.nomeproduto, p.preco, g;

                            ";
                            $rs = mysqli_query($db, $sql);

                            while ($dados = mysqli_fetch_array($rs)) {
                                // Calcular porcentagem de estoque restante
                                $percentual_estoque = ($dados['quantidade'] / $dados['quantidade_inicial']) * 100;

                                // Verificar se o prazo está a menos de 30 dias da data atual
                                $hoje = date('Y-m-d');
                                $validade_proxima = (strtotime($dados['prazo']) - strtotime($hoje)) / (60 * 60 * 24);  // diferença em dias

                                // Definir classes de estilo com base nas condições
                                $classe_estoque = '';
                                $classe_validade = '';

                                if ($percentual_estoque <= 5 || $dados['quantidade'] <= 10) {
                                    $classe_estoque = 'estoque-baixo';  // classe CSS para estoque baixo
                                }
                                
                                if ($validade_proxima <= 30) {
                                    $classe_validade = 'validade-proxima';  // classe CSS para validade próxima
                                }

                                // Combinar as classes
                                $classe_linha = trim($classe_estoque . ' ' . $classe_validade);
                            ?>
                            <tr class="<?php echo $classe_linha; ?>" data-idproduto="<?php echo $dados['idproduto']; ?>">
                                <td><?php echo $dados['idproduto'] ?></td>
                                <td><?php echo $dados['nomeproduto'] ?></td>
                                <td><?php echo $dados['preco'] ?></td>
                                <td><?php echo $dados['lote'] ?></td>
                                <td><?php echo $dados['quantidade'] ?></td>
                                <td><?php echo $dados['prazo'] ?></td>
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
                            <h4>Finalizar</h4>
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
    <script src="boxpos/scripts.js"></script>
    <script>
        $(function(){
            setInterval(nrpedido, 1000);

            function nrpedido(){
              $.ajax({
                  url: 'ocs/nrpedido.php',
                  type: 'GET',
                  success: function(data) {
                    $("#nrpedido").html(data);
                  },
                  error: function() {
                    $("#nrpedido").html("Ocorreu um erro ao popular o numero da fatura!");
                  }
              });
            }
      
            popprodtable();
            showtot();
            qrcodefocus();

            function popprodtable(){
                $.ajax({
                    url: 'ocs/popprodtable.php',
                    type: 'GET',
                    success: function(data){
                        $("#poptempart").html(data);
                        $(".btn_diminuir").click(function() {
                            var idProduto = $(this).data("idproduto");
                            decreaseQuantity(idProduto);
                        });
                        $(".btn_aumentar").click(function() {
                            var idProduto = $(this).data("idproduto");
                            increaseQuantity(idProduto);
                        });
                        $(".qtd").change(function() {
                            var idProduto = $(this).data("idproduto");
                            var novoValor = $(this).val();
                            updateQuantity(idProduto, novoValor);
                        });
                        $(".btnremove").click(function() {
                            var idProduto = $(this).data("idproduto");
                            btnremover(idProduto);
                        });
                    }
                });
            }
            
            function decreaseQuantity(idProduto) {
                $.ajax({
                    url: 'ocs/qtd_decrease.php',
                    type: 'POST',
                    data: {
                            id: idProduto
                    },
                    success: function(data){
                        popprodtable();
                        showtot();
                        qrcodefocus()
                    }
                });
            }
            
            function increaseQuantity(idProduto) {
                $.ajax({
                    url: 'ocs/qtd_increase.php',
                    type: 'POST',
                    data: {
                            id: idProduto
                    },
                    success: function(data){
                        popprodtable();
                        showtot();
                        qrcodefocus()
                    }
                });
            }
            
            function updateQuantity(idProduto, novoValor) {
                $.ajax({
                    url: 'ocs/qtd_update.php',
                    type: 'POST',
                    data: {
                            id: idProduto,
                            novoValor: novoValor
                    },
                    success: function(data){
                        popprodtable();
                        showtot();
                        qrcodefocus()
                    }
                });
            }
            
            function showtot() {
                $.ajax({
                    url: 'ocs/show_tot.php',
                    type: 'GET',
                    success: function(data){
                        $(".ttl").html(data);
                    }
                });
            }
            function qrcodefocus() {
                $("#qrcode").focus();
            }
            
            $("#qrcode").on('change', function(){
                var codbar = $(this).val(); // Obt¨¦m o valor do input dentro da div clicada
                $.ajax({
                    type: "POST",
                    url: "ocs/addarttempbarcode.php", // Especifique o arquivo PHP desejado
                    data: { codbar: codbar }, // Passa o valor do input como um par0‰9metro
                    success: function(response){
                        if(response == 1){
                            alert("O stock deste artigo e inferior ou igual a quantidade solicitada.");
                        } else if(response == 2){
                            alert("Nao foi encontrado nenhum produto com este id");
                        } else if(response == 3){
                            popprodtable();
                            showtot();
                            location.reload();
                        } else if(response == 31){
                            alert("Ocorreu um erro ao adicionar o artigo! Contacte o administrador do sistema.");
                        } else if(response == 40){
                            alert("Nao foram enviados parametros para a devida requisicao.");
                        } else if(response == 10){
                            alert("Nao foi possivel encontrar um artigo com este id.");
                        }
                        console.log(response);
                    }
                });
            
            });
            $("#example tbody tr").click(handleClick);
            function handleClick() {
                var idProduto = this.getAttribute('data-idproduto'); // Obt¨¦m o valor do input dentro da div clicada
                $.ajax({
                    type: "POST",
                    url: "ocs/addarttemp.php", // Especifique o arquivo PHP desejado
                    data: { idProduto: idProduto }, // Passa o valor do input como um par0‰9metro
                    success: function(response){
                        if(response == 1){
                            alert("O caixa encontra-se fechado para este usuario! Abra o caixa na seccao caixa no menu lateral esquerdo.");
                        } else if(response == 2){
                            alert("O stock deste artigo e inferior ou igual a quantidade solicitada");
                        } else if(response == 3){
                            popprodtable();
                            showtot();
                            qrcodefocus();
                        } else if(response == 31){
                            alert("Ocorreu um erro ao adicionar o artigo! Contacte o administrador do sistema.");
                        } else if(response == 40){
                            alert("Nao foram enviados parametros para a devida requisicao.");
                        } else if(response == 10){
                            alert("Nao foi possivel encontrar um artigo com este id.");
                        }
                        console.log(response);
                    }
                });
            }
            
            $(".btn_criar").click(function(){
                criarcotacao();
            });
            //processamento para criar o pedido
            function criarcotacao() {
                if (confirm("Tem certeza de que deseja efetuar o pagamento?")) {
                    var fornecedor = $(".fornecedor").val();
                    if(fornecedor == ""){
                        alert("Informe algum fornecedor!");
                    }else{
                       //alert(cliente+":cliente modo:"+modo+" valor:"+valor+" mesa:"+mesa);
                        $.ajax({
                            type: "POST",
                            url: "ocs/criarordem.php", // Especifique o arquivo PHP desejado
                            data: {
                              fornecedor: fornecedor,
                              prazo: $(".prazo").val(),
                              metodo: $(".metodo").val()
                            },
                            success: function(data){
                                if(data == 2000000000000000000000000){
                                  alert('Nao e possivel criar um documento para uma serie anterior ou posterior! Por favor atualize o numero de serie para o ano atual.');
                                }else{
                                  window.open("oc_pdf.php?id_oc="+data+"");
                                  location.reload();
                                }
                            },
                            error: function(){
                                alert("Ocorreu um erro com a requisicao ajax");
                            }
                        }); 
                    }
                }
            }
        
            // Atribua a fun0Š40Š0o ao manipulador de evento de clique
            

            
            $('.js-example-basic-single').select2();
            $('.js-example-basic-single1').select2();
            
            $("#cancelar").click(function() {
              if (confirm("Tem certeza de que deseja remover estes items?")){
                $.ajax({
                        url: 'ocs/artremoveall.php', // Seu arquivo PHP para remover o item
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
            function btnremover(idProduto){
                // Exiba uma caixa de di¨¢logo de confirma0Š40Š0o
                if (confirm("Tem certeza de que deseja remover este item?")) {
                    // Obtenha o ID do produto associado a este bot0Š0o
                    var idProduto = idProduto;
                    
                    // Enviar requisi0Š40Š0o Ajax para remover o item
                    $.ajax({
                        url: 'ocs/artremove.php', // Seu arquivo PHP para remover o item
                        method: 'POST',
                        data: { idProduto: idProduto },
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
            }
            
            $('#example').DataTable();
        });
    </script>
    </body>
</html>