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
      <title>VD - Venda a Dinheiro</title>
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
    .diva{
      width: 100%;
      padding: 5px 0;
    }
    .diva select{
      width: 100%;
      padding: 5px;
      border: 1px solid #ccc;
      border-right: 7px;
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

    /* NOVOS ESTILOS PARA O CAMPO DE TROCO */
    .valor-pago {
        border: 2px solid #28a745 !important;
        text-align: center;
        font-size: 18px !important;
    }

    .valor-pago:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 5px rgba(0,123,255,.5) !important;
    }

    .troco {
        border: 2px solid #17a2b8 !important;
        text-align: center;
        font-size: 18px !important;
        background-color: #f8f9fa !important;
    }

    /* Destaque para valores */
    .valor-destaque {
        font-size: 24px;
        font-weight: bold;
        color: #28a745;
    }
      </style>
    </head>
    <body>
              <!-- MODAL DE PAGAMENTO ATUALIZADO -->
              <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Detalhes do Pagamento</h1>
                          </div>
                          <div class="modal-body">
                              <div class="col-sm-12" style="display:flex; flex-direction:column;justify-content: center;align-items: center;">
                            <h4>Valor a Pagar</h4>
                            <h1 class="ttl" style="color: #28a745; font-weight: bold;"></h1>
                            </div>
                            
                            <!-- NOVO CAMPO: VALOR PAGO PELO CLIENTE -->
                            <div class="col-sm-12 mt-3">
                                <label class="form-label">Valor Pago pelo Cliente *</label>
                                <input type="number" class="form-control valor-pago" step="0.01" min="0" placeholder="Digite o valor recebido" style="height: 50px; font-size: 16px; font-weight: bold;">
                                <small class="text-muted">Digite o valor que o cliente está pagando</small>
                            </div>
                            
                            <!-- CAMPO TROCO (AUTO CALCULADO) -->
                            <div class="col-sm-12 mt-3">
                                <label class="form-label">Troco</label>
                                <input type="text" class="form-control troco" readonly style="height: 50px; font-size: 18px; font-weight: bold; background-color: #f8f9fa;">
                            </div>
                            
                            <div class="col-sm-12 mt-3">
                                <label class="form-label">Cliente</label>
                                <select class="form-control js-example-basic-single1  cliente" aria-label="Default select example" id="select">
                                    <?php 
                        					$sql = "SELECT * FROM clientes";
                        					$rs = mysqli_query($db, $sql);
                        					while ($dados = mysqli_fetch_array($rs)) {
                        				?>
                        					<option value="<?php echo $dados['id'];?>"><?php echo $dados['nome']." ".$dados['apelido'];?></option>
                        				<?php
                        					}
                        				?>
                                </select>
                            </div>
                            
                            <div class="col-sm-12 mt-3">
                                <label class="form-label">Metodo de Pagamento</label>
                                <select class="form-control modo" aria-label="Default select example" id="select">
                                  <?php 
                                        $sql = "SELECT * FROM metodo_pagamento";
                                        $rs = mysqli_query($db, $sql);
                                        while ($dados = mysqli_fetch_array($rs)) {
                                    ?>
                                        <option value="<?php echo $dados['id'];?>"><?php echo $dados['descricao'];?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                            
                            <input type="hidden" class="form-control valor" step="0.01" id="valorPago" value="999999999999">
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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
                <!-- Primeira Coluna -->
                <div class="col-sm-1 sticky-left" style="padding: 0;">
                    <ul class="list-unstyled text-center lista">
                        <li class="p-2 artigo">
                            <img src="9055226_bxs_dashboard_icon.png">
                            <p>Artigos</p>
                        </li>
                        <li class="p-2 caixa">
                            <img src="2559831_box_media_network_social_icon.png">
                            <p>Caixa</p>
                        </li>
                    </ul>
                </div>
        
                <!-- Segunda Coluna -->
                <div class="col-sm-7 segunda1 table-responsive" style="background:#fff;display: flex;flex-direction: row;overflow: auto;height:90vh;color: #333;justify-content: center;padding-top: 1%;">
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
                                    s.lote,
                                    s.quantidade,
                                    s.prazo,
                                    s.quantidade_inicial
                                FROM produto as p
                                INNER JOIN stock as s ON s.produto_id = p.idproduto 
                                    AND s.prazo = (
                                        SELECT MIN(s2.prazo)
                                        FROM stock as s2
                                        WHERE s2.produto_id = p.idproduto
                                        AND s2.quantidade > 0
                                        AND s2.prazo >= CURDATE()
                                        AND s2.estado = 'ativo'
                                    )
                                ORDER BY p.nomeproduto
                            ";

                            $rs = mysqli_query($db, $sql);

                            while ($dados = mysqli_fetch_array($rs)) {
                                // Calcular porcentagem de estoque restante (apenas se o estoque inicial existir)
                                $percentual_estoque = isset($dados['quantidade_inicial']) && $dados['quantidade_inicial'] > 0
                                    ? ($dados['quantidade'] / $dados['quantidade_inicial']) * 100
                                    : 0;

                                // Verificar se o prazo está a menos de 30 dias da data atual
                                $hoje = date('Y-m-d');
                                $validade_proxima = isset($dados['prazo']) && $dados['prazo'] != null
                                    ? (strtotime($dados['prazo']) - strtotime($hoje)) / (60 * 60 * 24)
                                    : null;

                                // Definir classes de estilo com base nas condições
                                $classe_estoque = '';
                                $classe_validade = '';

                                if ($dados['quantidade'] > 0 && ($percentual_estoque <= 5 || $dados['quantidade'] <= 10)) {
                                    $classe_estoque = 'estoque-baixo';  // classe CSS para estoque baixo
                                }

                                if ($validade_proxima !== null && $validade_proxima <= 30) {
                                    $classe_validade = 'validade-proxima';  // classe CSS para validade próxima
                                }

                                // Combinar as classes
                                $classe_linha = trim($classe_estoque . ' ' . $classe_validade);
                            ?>
                            <tr class="<?php echo $classe_linha; ?>" data-idproduto="<?php echo $dados['idproduto']; ?>">
                                <td><?php echo $dados['idproduto']; ?></td>
                                <td><?php echo $dados['nomeproduto']; ?></td>
                                <td><?php echo $dados['preco']; ?></td>
                                <td><?php echo $dados['lote'] ?: '-'; ?></td> <!-- Mostrar '-' se não houver lote -->
                                <td><?php echo isset($dados['quantidade']) ? $dados['quantidade'] : '0'; ?></td> <!-- Mostrar '0' se não houver stock -->
                                <td><?php echo $dados['prazo'] ?: '-'; ?></td> <!-- Mostrar '-' se não houver prazo -->
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Defina as classes CSS para as cores -->
                    <style>
                        .estoque-baixo {
                            background-color: #ffcccc;  /* Vermelho claro para estoque baixo */
                        }
                        .validade-proxima {
                            background-color: #fff3cd;  /* Amarelo claro para validade próxima */
                        }
                    </style>
                </div>

                <div class="col-sm-7 segunda2" style="padding: 0;display: flex;flex-direction: row;flex-wrap:wrap;overflow: auto;height:90vh;display:none;background: #fff;">
                    <div class="col-sm-12">
                        <h2 style="margin-left: 4%;margin-top: 2%;">Caixa</h2>
                    </div>
                    <div class="col-sm-12" style="padding: 0;display: flex;flex-direction: row;justify-content: space-around;flex-wrap:wrap;">
                        <div class="card col-sm-5 p-2">
                          <div class="card-body">
                            <h5 class="card-title">Valor em caixa</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Abertura</h6>
                            <h2 class="card-title" id="valorAbertura"></h2>
                          </div>
                          <?php 
                            if ($_SESSION['categoriaUsuario'] == "recepcao_mestre" OR $_SESSION['categoriaUsuario'] == "admin" OR $_SESSION['categoriaUsuario'] == "contabilidade") {
                          ?>
                          <div style="display: flex;flex-direction: row;justify-content: space-around;">
                              <input type="number" class="form-control text-center" value="0" style="width: 240px;" id="valorAberturaCaixa">
                              <button class="btn btn-danger" id="btnOpenCaixa"> Abrir</button>
                          </div>
                          
                            <div class="diva">
                            <select id="usar">
                              <option>Selecione o usuario</option>
                              <?php
                                  $sql = "SELECT * FROM users";
                                  $rs = mysqli_query($db, $sql);
                                  while ($dados = mysqli_fetch_array($rs)) {
                              ?>
                                  <option value="<?php echo $dados['id'] ?>"><?php echo $dados['nome'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <div class="msgOpenCaixa">
                          </div>
                          <?php
                            }
                          ?>
                        </div>
                        <div class="card col-sm-5 p-2">
                          <div class="card-body">
                            <h5 class="card-title">Valor em caixa</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Fecho</h6>
                            <h2 class="card-title" id="valorFecho"></h2>
                          </div>
                          <?php 
                            if ($_SESSION['categoriaUsuario'] == "recepcao_mestre" OR $_SESSION['categoriaUsuario'] == "admin" OR $_SESSION['categoriaUsuario'] == "contabilidade") {
                          ?>
                            <div class="diva">
                            <select id="usar">
                              <option>Selecione o usuario</option>
                              <?php
                                  $sql = "SELECT * FROM users";
                                  $rs = mysqli_query($db, $sql);
                                  while ($dados = mysqli_fetch_array($rs)) {
                              ?>
                                  <option value="<?php echo $dados['id'] ?>"><?php echo $dados['nome'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                          
                          <div style="display: flex;flex-direction: row;justify-content: flex-end;color: #fff;">
                              <a class="btn btn-danger printa"> Imprimir</a>
                              <button class="btn btn-danger" style="margin-left: 10px;" id="btnCloseCaixa"> Fechar</button>
                          </div>
                          <div class="msgCloseCaixa">
                          </div>
                          <?php
                            }
                          ?>
                        </div>
                    </div>
                    <div class="col-sm-12 mt-4 poptype">
                        <!-- Image and text -->
                        
                    </div>
                </div>
                <div class="col-sm-7 segunda3" style="padding: 0;display: flex;flex-direction: row;flex-wrap:wrap;overflow: auto;height:96vh;display:none;">
                    
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
        // FUNÇÃO PARA CALCULAR TROCO AUTOMATICAMENTE
        function calcularTroco() {
            var valorTotal = parseFloat($(".ttl").text().replace(',', '.')) || 0;
            var valorPago = parseFloat($(".valor-pago").val()) || 0;
            
            if (valorPago > 0) {
                var troco = valorPago - valorTotal;
                
                if (troco >= 0) {
                    // Troco positivo ou zero
                    $(".troco").val("MT " + troco.toFixed(2).replace('.', ','));
                    $(".troco").css({
                        'color': '#28a745',
                        'font-weight': 'bold'
                    });
                } else {
                    // Valor pago insuficiente
                    $(".troco").val("Falta MT " + Math.abs(troco).toFixed(2).replace('.', ','));
                    $(".troco").css({
                        'color': '#dc3545',
                        'font-weight': 'bold'
                    });
                }
            } else {
                $(".troco").val("");
            }
        }

        // ATUALIZAR TROCO QUANDO O MODAL ABRIR
        $('#exampleModal3').on('shown.bs.modal', function () {
            // Atualizar o total no modal
            showtot();
            
            // Limpar campos
            $(".valor-pago").val("");
            $(".troco").val("");
            
            // Focar no campo de valor pago
            setTimeout(function() {
                $(".valor-pago").focus();
            }, 500);
            
            // Configurar Select2
            $('.js-example-basic-single1').select2({
                dropdownParent: $('#exampleModal3 .modal-body')
            });
        });

        // CALCULAR TROCO EM TEMPO REAL
        $(document).on('input', '.valor-pago', function() {
            calcularTroco();
        });

        // Função definida no escopo global para estar acessível em todo lugar
        function atualizarStockConsumoMedio() {
            console.log("Verificando produtos com estoque abaixo do consumo médio...");
            // Adicionar um timestamp para evitar cache
            var noCache = new Date().getTime();
            
            $.ajax({
                url: "admin/daos/atualizarStockConsumoMedio.php?nocache=" + noCache,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    console.log("Resposta recebida:", response);
                    
                    // Resetar contador de tentativas
                    window.stockCheckRetryCount = 0;
                    
                    if (response.status === 'sucesso') {
                        var quantidade = response.quantidade;
                        
                        if (quantidade > 0) {
                            console.log("Artigos com estoque baixo encontrados:", quantidade);
                            // Armazenar localmente a última verificação
                            localStorage.setItem('ultimaVerificacaoStock', new Date().toISOString());
                            localStorage.setItem('quantidadeArtigosBaixo', quantidade);
                            
                            // Contar produtos críticos (valor absoluto ou percentual)
                            var produtosCriticos = 0;
                            if (response.produtos && Array.isArray(response.produtos)) {
                                produtosCriticos = response.produtos.filter(function(p) {
                                    return p.tipo_alerta === 'absoluto' || p.tipo_alerta === 'percentual';
                                }).length;
                                
                                localStorage.setItem('artigosCriticos', produtosCriticos);
                                console.log("Artigos em estado crítico:", produtosCriticos);
                            }
                            
                            // Sempre mostrar o alerta visual
                            console.log("Mostrando alerta visual");
                            setTimeout(function() {
                                mostrarAlertaVisual(quantidade);
                            }, 500);
                            
                            // FORÇAR envio de notificação - Sem verificação de permissão
                            enviarNotificacaoForcada(quantidade);
                        } else {
                            console.log("Nenhum artigo com estoque abaixo do consumo médio encontrado.");
                        }
                    } else {
                        console.error("Erro na resposta da verificação de estoque:", response.mensagem);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao verificar estoque:", error);
                    
                    // Incrementar contador de tentativas
                    window.stockCheckRetryCount = (window.stockCheckRetryCount || 0) + 1;
                    
                    if (window.stockCheckRetryCount < 3) {
                        console.log("Tentando novamente em 5 segundos... (Tentativa " + window.stockCheckRetryCount + "/3)");
                        setTimeout(atualizarStockConsumoMedio, 5000);
                    } else {
                        console.error("Número máximo de tentativas alcançado. Desistindo.");
                    }
                }
            });
        }
        
        // Nova função para forçar a notificação sem verificações
        function enviarNotificacaoForcada(quantidade) {
            console.log("FORÇANDO NOTIFICAÇÃO para " + quantidade + " produtos");
            
            if (!("Notification" in window)) {
                console.log("Este navegador não suporta notificações");
                return;
            }
            
            // Solicitar permissão e mostrar imediatamente
            if (Notification.permission === "granted") {
                // Já temos permissão, mostrar notificação
                mostrarNotificacaoReal(quantidade);
            } else if (Notification.permission !== "denied") {
                // Solicitar permissão
                Notification.requestPermission().then(function(permission) {
                    if (permission === "granted") {
                        mostrarNotificacaoReal(quantidade);
                    }
                });
            }
        }
        
        // Função que realmente mostra a notificação
        function mostrarNotificacaoReal(quantidade) {
            // Mensagem da notificação
            var mensagem = "Existem " + quantidade + " artigos com estoque abaixo do consumo médio mensal.";
            var titulo = "Artigos com estoque abaixo do consumo médio";
            
            // Verificar produtos críticos
            var artigosCriticos = localStorage.getItem('artigosCriticos');
            if (artigosCriticos && parseInt(artigosCriticos) > 0) {
                titulo = "⚠️ ALERTA: Estoque crítico em " + artigosCriticos + " artigos";
                mensagem = "Há " + artigosCriticos + " artigos com estoque CRÍTICO!";
            }
            
            // URL de destino
            var url = window.location.origin + '/views/admin/consumo_artigos_baixo.php';
            
            try {
                // Criar a notificação de forma simples
                var notification = new Notification(titulo, {
                    body: mensagem,
                    icon: window.location.origin + "/img/config/iCone.png",
                    tag: 'stock-notification',
                    requireInteraction: true
                });
                
                console.log("Notificação criada com sucesso!", notification);
                
                // Evento de clique simplificado
                notification.onclick = function() {
                    console.log("Notificação clicada, redirecionando para", url);
                    window.open(url, '_blank');
                    this.close();
                };
            } catch(e) {
                console.error("Erro ao criar notificação:", e);
            }
        }
        
        // Função para mostrar alerta visual na página
        function mostrarAlertaVisual(quantidade) {
            console.log("Iniciando função mostrarAlertaVisual para " + quantidade + " produtos");
            
            // Verificar se o alerta já existe para evitar duplicidade
            if (document.getElementById('alerta-estoque-baixo')) {
                console.log("Alerta visual já existe, não criando duplicado");
                return;
            }
            
            console.log("Criando alerta visual agora");
            
            // Criar um elemento de alerta mais chamativo e interativo
            var alerta = document.createElement('div');
            alerta.id = 'alerta-estoque-baixo';
            alerta.className = 'alert alert-danger';
            alerta.style.position = 'fixed';
            alerta.style.top = '40px';
            alerta.style.left = '20px';
            alerta.style.zIndex = '9999';
            alerta.style.minWidth = '300px';
            alerta.style.maxWidth = '450px';
            alerta.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
            alerta.style.borderLeft = '5px solid #dc3545';
            alerta.style.padding = '15px';
            alerta.style.display = 'none'; // Começar invisível para animar
            
            // Tentar obter informações adicionais do localStorage
            var mensagem = "Existem " + quantidade + " artigos com estoque abaixo do consumo médio mensal.";
            var tipoAlerta = "Atenção";
            
            try {
                var artigosCriticos = localStorage.getItem('artigosCriticos');
                if (artigosCriticos && parseInt(artigosCriticos) > 0) {
                    mensagem = "Há " + artigosCriticos + " artigos com estoque CRÍTICO!";
                    tipoAlerta = "ALERTA CRÍTICO";
                }
            } catch (e) {
                console.error("Erro ao verificar localStorage:", e);
            }
            
            // Conteúdo do alerta com título e botões
            alerta.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="color: #dc3545; font-size: 16px;">⚠️ ${tipoAlerta}</strong>
                    <button type="button" class="close" style="font-size: 20px; cursor: pointer;">&times;</button>
                </div>
                <p>${mensagem}</p>
                <div style="text-align: right; margin-top: 10px;">
                    <button id="btn-ver-produtos" class="btn btn-sm btn-danger">Verificar Agora</button>
                    <button id="btn-adiar" class="btn btn-sm btn-outline-secondary ml-2">Lembrar Depois</button>
                </div>
            `;
            
            // Adicionar o alerta à página
            document.body.appendChild(alerta);
            
            // Animar o aparecimento do alerta
            setTimeout(function() {
                alerta.style.display = 'block';
                alerta.style.opacity = '0';
                alerta.style.transform = 'translateX(50px)';
                alerta.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(function() {
                    alerta.style.opacity = '1';
                    alerta.style.transform = 'translateX(0)';
                }, 50);
            }, 100);
            
            // Log para confirmar que o alerta foi criado
            console.log("Alerta visual criado e adicionado ao DOM:", alerta);
            
            // Manipulador para fechar o alerta
            alerta.querySelector('.close').addEventListener('click', function() {
                fecharAlertaComAnimacao(alerta);
            });
            
            // Manipulador para o botão "Verificar Agora"
            alerta.querySelector('#btn-ver-produtos').addEventListener('click', function() {
                // URL consistente com a usada nas notificações
                var url = window.location.origin + '/views/admin/consumo_artigos_baixo.php';
                
                try {
                    // Verificar se estamos em um iframe ou na janela principal
                    if (window.top !== window.self) {
                        // Estamos em um iframe
                        window.top.location.href = url;
                    } else {
                        // Abrir em uma nova aba
                        window.open(url, '_blank');
                    }
                } catch(e) {
                    console.error("Erro ao redirecionar:", e);
                    // Tentativa de fallback
                    window.open(url, '_blank');
                }
                
                fecharAlertaComAnimacao(alerta);
            });
            
            // Manipulador para o botão "Lembrar Depois"
            alerta.querySelector('#btn-adiar').addEventListener('click', function() {
                // Programar para mostrar o alerta novamente após 1 hora
                localStorage.setItem('adiarAlertaEstoque', new Date().getTime() + 3600000);
                
                fecharAlertaComAnimacao(alerta);
                console.log("Alerta adiado por 1 hora, voltará às " + new Date(new Date().getTime() + 3600000).toLocaleTimeString());
            });
        }
        
        // Função para fechar o alerta com animação
        function fecharAlertaComAnimacao(elemento) {
            elemento.style.opacity = '0';
            elemento.style.transform = 'translateX(50px)';
            
            setTimeout(function() {
                elemento.remove();
            }, 500);
        }
        
        $(function(){
            // Remover qualquer adiamento de alertas armazenado
            localStorage.removeItem('adiarAlertaEstoque');
            console.log("Valores de adiamento de alerta removidos do localStorage");
            
            // Força a solicitação de permissão logo no carregamento da página
            setTimeout(function() {
                if ("Notification" in window) {
                    console.log("Status atual da permissão de notificação:", Notification.permission);
                    if (Notification.permission !== "granted") {
                        console.log("Solicitando permissão para notificações...");
                        Notification.requestPermission().then(function(permission) {
                            console.log("Permissão de notificação atualizada para:", permission);
                        });
                    }
                }
            }, 1000);
            
            // Sempre verificar o estoque ao carregar a página - executar imediatamente
            console.log("Página carregada - iniciando verificação de estoque imediatamente");
            atualizarStockConsumoMedio();
            
            // NOVIDADE: Forçar notificação direto no carregamento da página
            setTimeout(function() {
                // Quantidade padrão (será atualizada pela verificação de estoque)
                var qtd = 5;
                // Se tivermos dados armazenados, usar esses dados
                if (localStorage.getItem('quantidadeArtigosBaixo')) {
                    qtd = parseInt(localStorage.getItem('quantidadeArtigosBaixo'));
                }
                console.log("FORÇANDO NOTIFICAÇÃO INICIAL com " + qtd + " produtos");
                
                // Criar notificação diretamente, sem verificações adicionais
                if ("Notification" in window) {
                    // Tentar solicitar permissão e mostrar notificação imediatamente
                    if (Notification.permission === "granted") {
                        mostrarNotificacaoGarantida(qtd);
                    } else {
                        // Se não temos permissão, solicitar e tentar mostrar
                        Notification.requestPermission().then(function(permission) {
                            console.log("Permissão atualizada para: " + permission);
                            if (permission === "granted") {
                                mostrarNotificacaoGarantida(qtd);
                            } else {
                                console.log("Permissão negada pelo usuário, mostrando apenas alerta visual");
                                // Garantir que ao menos o alerta visual é mostrado
                                setTimeout(function() {
                                    mostrarAlertaVisual(qtd);
                                }, 500);
                            }
                        });
                    }
                } else {
                    console.log("Este navegador não suporta notificações");
                    // Garantir que ao menos o alerta visual é mostrado
                    setTimeout(function() {
                        mostrarAlertaVisual(qtd);
                    }, 500);
                }
            }, 2000); // Pequeno atraso para garantir que a página tenha carregado completamente
            
            // Configurar verificação periódica a cada 1 hora APENAS se não estivermos na página principal
            if (!window.location.pathname.endsWith('vd.php')) {
                setInterval(function() {
                    console.log("Verificação periódica de estoque...");
                    atualizarStockConsumoMedio();
                }, 3600000); // 1 hora em milissegundos
            }
            
            // Adicionar estilos CSS para garantir que o alerta seja visível em qualquer layout
            var style = document.createElement('style');
            style.innerHTML = `
                #alerta-estoque-baixo {
                    position: fixed !important;
                    z-index: 9999 !important;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
                    border-radius: 4px !important;
                    font-family: Arial, sans-serif !important;
                }
                
                #alerta-estoque-baixo p {
                    margin-bottom: 10px !important;
                }
                
                #alerta-estoque-baixo button.btn {
                    font-size: 12px !important;
                    padding: 4px 10px !important;
                }
                
                #alerta-estoque-baixo .close {
                    background: none !important;
                    border: none !important;
                    padding: 0 !important;
                    color: #999 !important;
                    font-size: 20px !important;
                }
                
                #alerta-estoque-baixo .close:hover {
                    color: #333 !important;
                }
            `;
            document.head.appendChild(style);
            
            $("#btnOpenCaixa").click(function(){
                abrirCaixa();
            });
            $("#btnCloseCaixa").click(function(){
                fecharCaixa();
            });
            $('#exampleModal').on('shown.bs.modal', function () {
                $('#qrcode').focus();
            });
            setInterval(nrpedido, 1000);

			function nrpedido(){
				$.ajax({
						url: 'vds/nrpedido.php',
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
                    url: 'vds/popprodtable.php',
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
                    url: 'vds/qtd_decrease.php',
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
                    url: 'vds/qtd_increase.php',
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
                    url: 'vds/qtd_update.php',
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
                    url: 'vds/show_tot.php',
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
                    url: "vds/addarttempbarcode.php", // Especifique o arquivo PHP desejado
                    data: { codbar: codbar }, // Passa o valor do input como um par0‰9metro
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
                        } else if(response == 7){
                            alert("Artigo fora de estoque ou expirado.");
                        }
                        console.log(response);
                    }
                });
                $(this).val("");
            });
            $("#example tbody tr").click(handleClick);
            function handleClick() {
                var idProduto = this.getAttribute('data-idproduto'); // Obt¨¦m o valor do input dentro da div clicada
                
                $.ajax({
                    type: "POST",
                    url: "vds/addarttemp.php", // Seu arquivo PHP para remover o item
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
                        } else if(response == 7){
                            alert("Artigo fora de estoque ou expirado.");
                        }
                        console.log(response);
                    }
                });
            }
            
            $(".btn_criar").click(function(){
                criarpedido();
            });
            
            //processamento para criar o pedido - VERSÃO ATUALIZADA
            function criarpedido() {
                var valorTotal = parseFloat($(".ttl").text().replace(',', '.')) || 0;
                var valorPago = parseFloat($(".valor-pago").val()) || 0;
                
                // Validar se o valor pago foi informado
                if (!valorPago || valorPago <= 0) {
                    alert("Por favor, informe o valor pago pelo cliente!");
                    $(".valor-pago").focus();
                    return;
                }
                
                // Validar se o valor pago é suficiente
                if (valorPago < valorTotal) {
                    var falta = valorTotal - valorPago;
                    if (!confirm("O valor pago é insuficiente! Falta MT " + falta.toFixed(2).replace('.', ',') + "\nDeseja continuar mesmo assim?")) {
                        $(".valor-pago").focus();
                        return;
                    }
                }
                
                if (confirm("Tem certeza de que deseja efetuar o pagamento?")) {
                    var cliente = $(".cliente").val();
                    var modo = $(".modo").val();
                    var valor = $(".valor-pago").val(); // Usar o valor pago em vez do valor fixo
                    
                    $.ajax({
                        type: "POST",
                        url: "vds/criarpedido.php",
                        data: { 
                            cliente: cliente, 
                            modo: modo, 
                            valor: valor
                        },
                        dataType: "json",
                        success: function(response){
                            if (response.error) {
                                alert(response.error);
                            } else if (response.ot == 1000000000001) {
                                alert("O valor informado é inferior ao valor a pagar, por favor informe novamente o valor.");
                                $(".valor-pago").focus();
                            } else if(response.id) {
                                // Mostrar mensagem de sucesso com informações do troco
                                var mensagem = "Pagamento efetuado com sucesso!";
                                if (valorPago > valorTotal) {
                                    var troco = valorPago - valorTotal;
                                    mensagem += "\nTroco: MT " + troco.toFixed(2).replace('.', ',');
                                }
                                alert(mensagem);
                                
                                window.open("vd_tick.php?id_vd=" + response.id + "");
                                location.reload();
                            } 
                            console.log(response);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Error details: ", textStatus, errorThrown);
                            alert("Ocorreu um erro com a requisicao ajax");
                        }
                    }); 
                }
            }
        
            // Atribua a fun0Š40Š0o ao manipulador de evento de clique
            
            $(".artigo").click(function(){
                $(".segunda1").css({
                    display: "flex"
                });
                qrcodefocus();
                $(".segunda2").css({
                    display: "none"
                });
            });
            $(".caixa").click(function(){
                $(".segunda1").css({
                    display: "none"
                });
                $(".segunda2").css({
                    display: "block"
                });
                showCaixaAbertura();
                showCaixaFecho();
                showtype();
            });
            
            $('#exampleModal1').on('shown.modal', function () {
              $('.js-example-basic-single').select2({
                dropdownParent: $('#exampleModal1 .modal-body') // Defina o pai do dropdown como o corpo do modal
              });
            });
            
            $(".printa").on('click', function() {
                var usar = $("#idperiodo").val();
                window.open("boxpos/print_caixa.php?id="+usar+"", "_blank");
            });
            function abrirCaixa(){
                    $.ajax({
                        url: 'vds/openCaixa.php',
                        type: 'POST',
                        data: {
                            valor: $("#valorAberturaCaixa").val(),
                            usar: $("#usar").val()
                        },
                        success: function(data){
                            $(".msgOpenCaixa").html(data);
                            showCaixaAbertura();
                            showCaixaFecho();
                        },
                        error: function(){
                            $(".msgOpenCaixa").html("<p class='mt-2'>Ocorreu um erro ao popular a mensagem!</p>");
                        }
                    })
            }
            function fecharCaixa(){
                    $.ajax({
                        url: 'vds/closeCaixa.php',
                        type: 'POST',
                        data: {
                          usar: $("#usar").val()
                        },
                        success: function(data){
                            $(".msgCloseCaixa").html(data);
                            showCaixaAbertura();
                            showCaixaFecho();
                        },
                        error: function(){
                            $(".msgCloseCaixa").html("<p class='mt-2'>Ocorreu um erro ao popular a mensagem!</p>");
                        }
                    })
            }
            function showCaixaAbertura(){
                    $.ajax({
                        url: 'vds/showCaixaAbertura.php',
                        type: 'GET',
                        success: function(data){
                            $("#valorAbertura").html(data);
                        },
                        error: function(){
                            $("#valorAbertura").html("<p class='mt-2'>Ocorreu um erro ao popular o valor!</p>");
                        }
                    })
            }
            
            function showtype(){
                    $.ajax({
                        url: 'vds/showtype.php',
                        type: 'GET',
                        success: function(data){
                            $(".poptype").html(data);
                        },
                        error: function(){
                            $(".poptype").html("<p class='mt-2' style='margin-left: 30px;'>Ocorreu um erro ao popular o valor!</p>");
                        }
                    })
            }
            
            function showCaixaFecho(){
                    $.ajax({
                        url: 'vds/showCaixaFecho.php',
                        type: 'GET',
                        success: function(data){
                            $("#valorFecho").html(data.fechoperiodo);
                            // Crie um novo input hidden
                            var novoInput = $('<input>', {
                                type: 'hidden',
                                name: 'idperiodo', // Você pode alterar o nome conforme necessário
                                id: 'idperiodo',
                                value: data.idperiodo
                            });
                    
                            // Adicione o novo input ao DOM, por exemplo, dentro de um contêiner com id "container"
                            $('.msgCloseCaixa').append(novoInput);
                        },
                        error: function(){
                            $("#valorFecho").html("<p class='mt-2'>Ocorreu um erro ao popular o valor!</p>");
                        }
                    })
            }
            
            $("#cancelar").click(function() {
            	if (confirm("Tem certeza de que deseja remover estes items?")){
            		$.ajax({
                        url: 'vds/artremoveall.php', // Seu arquivo PHP para remover o item
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
                        url: 'vds/artremove.php', // Seu arquivo PHP para remover o item
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
            
            // Adicionar botão de verificação manual de estoque para testes
            var verificarBtn = document.createElement('button');
            verificarBtn.id = 'btn-verificar-estoque';
            verificarBtn.className = 'btn btn-sm btn-info';
            verificarBtn.style.position = 'fixed';
            verificarBtn.style.bottom = '10px';
            verificarBtn.style.left = '10px';
            verificarBtn.style.zIndex = '999';
            verificarBtn.innerHTML = '🔍 Verificar Estoque';
            verificarBtn.title = 'Verificar agora produtos com estoque baixo';
            
            // Adicionar badge para mostrar o status da permissão de notificação
            var statusBadge = document.createElement('span');
            statusBadge.id = 'notification-status';
            statusBadge.style.position = 'fixed';
            statusBadge.style.bottom = '50px';
            statusBadge.style.left = '10px';
            statusBadge.style.zIndex = '999';
            statusBadge.style.padding = '2px 8px';
            statusBadge.style.borderRadius = '10px';
            statusBadge.style.fontSize = '12px';
            statusBadge.style.fontWeight = 'bold';
            
            // Função para atualizar o status
            function atualizarStatusNotificacao() {
                if (!("Notification" in window)) {
                    statusBadge.style.backgroundColor = '#dc3545';
                    statusBadge.style.color = 'white';
                    statusBadge.innerHTML = '❌ Notificações não suportadas';
                    return;
                }
                
                switch(Notification.permission) {
                    case 'granted':
                        statusBadge.style.backgroundColor = '#28a745';
                        statusBadge.style.color = 'white';
                        statusBadge.innerHTML = '✅ Notificações permitidas';
                        break;
                    case 'denied':
                        statusBadge.style.backgroundColor = '#dc3545';
                        statusBadge.style.color = 'white';
                        statusBadge.innerHTML = '❌ Notificações bloqueadas';
                        break;
                    default:
                        statusBadge.style.backgroundColor = '#ffc107';
                        statusBadge.style.color = 'black';
                        statusBadge.innerHTML = '⚠️ Permissão não definida';
                }
            }
            
            // Adicionar um botão para solicitar permissão
            var permissaoBtn = document.createElement('button');
            permissaoBtn.id = 'btn-permissao';
            permissaoBtn.className = 'btn btn-sm btn-warning';
            permissaoBtn.style.position = 'fixed';
            permissaoBtn.style.bottom = '10px';
            permissaoBtn.style.left = '130px';
            permissaoBtn.style.zIndex = '999';
            permissaoBtn.innerHTML = '🔔 Permitir Notificações';
            permissaoBtn.title = 'Solicitar permissão para notificações';
            
            permissaoBtn.addEventListener('click', function() {
                if ("Notification" in window) {
                    permissaoBtn.innerHTML = '⏳ Solicitando...';
                    permissaoBtn.disabled = true;
                    
                    Notification.requestPermission().then(function(permission) {
                        console.log("Resposta da solicitação de permissão:", permission);
                        atualizarStatusNotificacao();
                        
                        permissaoBtn.innerHTML = '🔔 Permitir Notificações';
                        permissaoBtn.disabled = false;
                        
                        if (permission === 'granted') {
                            // Mostrar uma notificação de teste
                            var notification = new Notification('Notificação Ativada!', {
                                body: 'As notificações foram ativadas com sucesso!',
                                icon: window.location.origin + "/ivoneerp_v2.0/img/config/iCone.png"
                            });
                            
                            // Fechar após 3 segundos
                            setTimeout(function() {
                                notification.close();
                            }, 3000);
                        }
                    }).catch(function(error) {
                        console.error("Erro ao solicitar permissão:", error);
                        permissaoBtn.innerHTML = '🔔 Permitir Notificações';
                        permissaoBtn.disabled = false;
                    });
                }
            });
            
            // Atualizar status inicial
            atualizarStatusNotificacao();
            
            // Adicionar os elementos à página
            document.body.appendChild(statusBadge);
            document.body.appendChild(permissaoBtn);
            
            verificarBtn.addEventListener('click', function() {
                console.log("Verificação manual de estoque iniciada pelo usuário");
                atualizarStockConsumoMedio();
                
                // Feedback visual
                verificarBtn.innerHTML = '⏳ Verificando...';
                verificarBtn.disabled = true;
                
                setTimeout(function() {
                    verificarBtn.innerHTML = '🔍 Verificar Estoque';
                    verificarBtn.disabled = false;
                }, 3000);
            });
            
            document.body.appendChild(verificarBtn);
            
            // Função para forçar a criação de notificações no carregamento da página
            $(document).ready(function() {
                // Verificar se temos produtos em estoque baixo armazenados
                var quantidadeArtigosBaixo = localStorage.getItem('quantidadeArtigosBaixo');
                if (quantidadeArtigosBaixo && parseInt(quantidadeArtigosBaixo) > 0) {
                    console.log("Encontrados " + quantidadeArtigosBaixo + " artigos com estoque baixo no localStorage, tentando mostrar notificação imediatamente");
                    
                    // Forçar notificação na inicialização da página
                    setTimeout(function() {
                        enviarNotificacaoForcada(parseInt(quantidadeArtigosBaixo));
                    }, 2000);
                }
            });
            
            // Forçar mostrar notificação agora (adicionar botão para testes)
            function adicionarBotaoTeste() {
                var btn = document.createElement('button');
                btn.textContent = 'Testar Notificação';
                btn.style.position = 'fixed';
                btn.style.bottom = '10px';
                btn.style.left = '10px';
                btn.style.zIndex = '9999';
                btn.className = 'btn btn-sm btn-info';
                
                btn.onclick = function() {
                    console.log("Botão de teste clicado - forçando notificação");
                    var quantidade = localStorage.getItem('quantidadeArtigosBaixo') || 5;
                    enviarNotificacaoForcada(parseInt(quantidade));
                };
                
                document.body.appendChild(btn);
            }
            
            // Adicionar botão de teste em ambiente de desenvolvimento
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                setTimeout(adicionarBotaoTeste, 1000);
            }
        });
        
        // Função que GARANTE que a notificação será exibida (sem verificações)
        function mostrarNotificacaoGarantida(quantidade) {
            console.log("mostrarNotificacaoGarantida - exibindo notificação para " + quantidade + " produtos");
            
            // Variáveis básicas
            var titulo = "Artigos com estoque abaixo do consumo médio";
            var mensagem = "Existem " + quantidade + " artigos com estoque abaixo do consumo médio mensal.";
            var url = window.location.origin + '/ivoneerp_v2.0/views/admin/consumo_artigos_baixo.php';
            
            // Verificar informações adicionais no localStorage
            var artigosCriticos = localStorage.getItem('artigosCriticos');
            if (artigosCriticos && parseInt(artigosCriticos) > 0) {
                titulo = "⚠️ ALERTA: Estoque crítico em " + artigosCriticos + " artigos";
                mensagem = "Há " + artigosCriticos + " artigos com estoque CRÍTICO!";
            }
            
            try {
                // Criar notificação sem verificações
                var notification = new Notification(titulo, {
                    body: mensagem,
                    icon: window.location.origin + "/ivoneerp_v2.0/img/config/iCone.png",
                    tag: 'stock-notification',
                    requireInteraction: true, // Importante: manter visível até o usuário interagir
                    vibrate: [200, 100, 200], // Padrão de vibração para dispositivos móveis
                    timestamp: Date.now()
                });
                
                // Garantir que o evento de clique redirecione o usuário
                notification.onclick = function() {
                    console.log("Notificação clicada, redirecionando para:", url);
                    try {
                        // Se estivermos em um iframe, tentar abrir no navegador principal
                        if (window.top !== window.self) {
                            window.top.location.href = url;
                        } else {
                            window.open(url, '_blank');
                        }
                    } catch(e) {
                        console.error("Erro ao redirecionar:", e);
                        window.open(url, '_blank');
                    }
                    
                    this.close();
                };
                
                // Registrar quando a notificação é exibida (alguns navegadores suportam)
                if (notification.onshow) {
                    notification.onshow = function() {
                        console.log("Notificação foi exibida na tela");
                    };
                }
                
                // Registrar quando a notificação é fechada
                notification.onclose = function() {
                    console.log("Notificação foi fechada pelo usuário");
                };
                
                // Registrar erros
                notification.onerror = function(event) {
                    console.error("Erro ao mostrar notificação:", event);
                    // Garantir que ao menos o alerta visual é mostrado
                    mostrarAlertaVisual(quantidade);
                };
                
                console.log("Notificação criada com sucesso:", notification);
            } catch(e) {
                console.error("Erro ao criar notificação:", e);
                // Em caso de erro, garantir que ao menos o alerta visual é mostrado
                mostrarAlertaVisual(quantidade);
            }
        }
    </script>
    </body>
</html>