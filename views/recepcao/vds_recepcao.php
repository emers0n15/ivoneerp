<?php 
    session_start();
    if(!isset($_SESSION['idUsuario'])){
      header("location:../../");
    }
    include_once '../../conexao/index.php';
    error_reporting(E_ALL);
    $_SESSION['idUsuario'] = $_SESSION['idUsuario'];
    $_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
    $_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
    
    $empresa_selecionada = isset($_GET['empresa']) ? intval($_GET['empresa']) : null;
    $tabela_precos_id = null;
    $desconto_geral = 0;
    
    // Limpar TODOS os serviços temporários do usuário ao carregar a página (começar sempre do zero)
    $userID = $_SESSION['idUsuario'];
    $sql_limpar = "DELETE FROM vds_servicos_temp WHERE user = ?";
    $stmt_limpar = mysqli_prepare($db, $sql_limpar);
    mysqli_stmt_bind_param($stmt_limpar, "i", $userID);
    mysqli_stmt_execute($stmt_limpar);
    
    if($empresa_selecionada) {
        $sql_empresa = "SELECT tabela_precos_id, desconto_geral FROM empresas_seguros WHERE id = $empresa_selecionada";
        $rs_empresa = mysqli_query($db, $sql_empresa);
        if($rs_empresa && mysqli_num_rows($rs_empresa) > 0) {
            $empresa_data = mysqli_fetch_array($rs_empresa);
            $tabela_precos_id = $empresa_data['tabela_precos_id'] ?? null;
            $desconto_geral = floatval($empresa_data['desconto_geral'] ?? 0);
        }
    }
?>
<html>
    <head>
      <meta charset="utf-8">
      <title>VDS - Venda a Dinheiro/Serviço (Recepção)</title>
      <link rel="shorcut icon"  href="../../img/config/iCone.png">
      <link rel="stylesheet" href="../bootstrap.css">
      <link rel="stylesheet" href="../bootstrap.min.css">
      <link rel="stylesheet" href="../all.min.css">
      <link href="../datatables.min.css" rel="stylesheet"/>
  
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
        height: 70px;
    }
    #qrcode{
        height: 40px;
        width: 100%;
    }
    
    .select2-container--default .select2-selection--single {
        height: 40px;
    }
    
    .select2-container--default .select2-dropdown--below {
        max-height: 200px;
    }
    
    /* Estilos para o modal de pagamento */
    .valor-pago:focus {
        border-color: #007bff;
        box-shadow: 0 0 3px rgba(0,123,255,.3);
    }
    .lista li {
        transition: none;
    }
    .lista li:hover {
        background: #f0f0f0;
    }
    .lista li.active {
        background: #e0f7fa;
        border-left: 5px solid #38D0ED;
    }
    .lista li.active p {
        color: #333 !important;
        font-weight: bold;
    }
    .lista li.active img {
        opacity: 1;
    }
      </style>
    </head>
    <body>
              <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header" style="background: #38D0ED; color: #fff; padding: 10px 15px;">
                            <h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold; font-size: 16px; margin: 0;">Detalhes do Pagamento</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 1; font-size: 24px; font-weight: bold; background: rgba(255,255,255,0.2); border: none; width: 30px; height: 30px; border-radius: 3px; cursor: pointer;">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body" style="padding: 15px;">
                              <!-- Valor a Pagar -->
                              <div class="col-sm-12 mb-3" style="text-align: center; background: #f8f9fa; padding: 15px; border-radius: 5px;">
                                <label class="form-label" style="font-size: 12px; color: #666; margin: 0 0 5px 0; display: block;">Valor a Pagar</label>
                                <h3 class="ttl" style="color: #28a745; font-weight: bold; font-size: 28px; margin: 0;">0,00 MT</h3>
                              </div>
                              
                              <!-- Valor Pago e Troco em linha -->
                              <div class="row mb-3">
                                <div class="col-sm-6">
                                  <label class="form-label" style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">Valor Pago *</label>
                                  <input type="number" step="0.01" class="form-control valor-pago" id="valorPago" placeholder="0.00" required style="height: 38px; font-size: 14px; text-align: center;">
                                </div>
                                <div class="col-sm-6">
                                  <label class="form-label" style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">Troco</label>
                                  <input type="text" class="form-control troco" id="troco" readonly style="height: 38px; font-size: 14px; text-align: center; background-color: #f8f9fa;">
                                </div>
                              </div>
                              
                              <!-- Paciente -->
                              <div class="col-sm-12 mb-3">
                                <label class="form-label" style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">Paciente *</label>
                                <select class="form-select js-example-basic-single1 paciente" aria-label="Default select example" id="selectPaciente" style="width: 100%; height: 38px;">
                                    <option value="">Selecione um paciente...</option>
                                    <?php 
                                    if($empresa_selecionada) {
                                        $sql = "SELECT * FROM pacientes WHERE empresa_id = $empresa_selecionada AND ativo = 1 ORDER BY nome, apelido";
                                    } else {
                                        $sql = "SELECT * FROM pacientes WHERE ativo = 1 ORDER BY nome, apelido";
                                    }
                                    $rs = mysqli_query($db, $sql);
                                    while ($dados = mysqli_fetch_array($rs)) {
                                    ?>
                                      <option value="<?php echo $dados['id'];?>"><?php echo $dados['nome']." ".$dados['apelido'];?> (<?php echo $dados['numero_processo'];?>)</option>
                                    <?php
                                    }
                                    ?>
                                </select>
                              </div>
                              
                              <!-- Método de Pagamento -->
                              <div class="col-sm-12 mb-2">
                                <label class="form-label" style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">Método de Pagamento *</label>
                                <select class="form-select metodo" aria-label="Default select example" id="selectMetodo" style="width: 100%; height: 38px;">
                                  <option value="dinheiro">Dinheiro</option>
                                  <option value="m_pesa">M-Pesa</option>
                                  <option value="emola">Emola</option>
                                  <option value="pos">POS</option>
                                  <option value="transferencia">Transferência</option>
                                </select>
                              </div>
                            
                          </div>
                          <div class="modal-footer" style="padding: 10px 15px; border-top: 1px solid #dee2e6;">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="padding: 6px 15px; font-size: 13px;">Fechar</button>
                            <button type="button" class="btn btn-success btn-sm btn_criar" style="padding: 6px 20px; font-size: 13px; font-weight: bold;">Efetuar Pagamento</button>
                          </div>
                        </div>
                      </div>
              </div>

        <div class="container-fluid">
            <div class="row sticky-header">
                <div class="col-sm-1 text-center p-1" style="background: #31B3D0;color: #fff;">
                    <img src="../iConewhite.png" style="max-width: 100%; width: 40%;">
                </div>
                <div class="col-sm-7" style="background: #38D0ED;color:#fff;display:flex;flex-direction:row;justify-content:space-between;align-items:center;padding:10px;">
                    <div style="flex:1;">
                        <label style="color:#fff;font-weight:bold;margin-bottom:5px;display:block;">Empresa/Seguro:</label>
                        <select class="form-select js-example-basic-single" id="selectEmpresa" style="width:100%;" onchange="if(confirm('Ao mudar de empresa, todos os serviços selecionados serão removidos. Deseja continuar?')) { window.location.href='?empresa='+this.value; } else { this.value = '<?php echo $empresa_selecionada ? $empresa_selecionada : ""; ?>'; $(this).trigger('change'); }">
                            <option value="">-- Selecione --</option>
                            <?php 
                            $sql_empresas = "SELECT * FROM empresas_seguros WHERE ativo = 1 ORDER BY nome";
                            $rs_empresas = mysqli_query($db, $sql_empresas);
                            while ($empresa = mysqli_fetch_array($rs_empresas)) {
                                $selected = ($empresa_selecionada == $empresa['id']) ? 'selected' : '';
                                echo '<option value="' . $empresa['id'] . '" ' . $selected . '>' . htmlspecialchars($empresa['nome']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
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
                <!-- Sidebar Esquerda -->
                <div class="col-sm-1 sticky-left" style="padding: 0; background: #fff; border-right: 2px solid #dee2e6;">
                    <ul class="list-unstyled text-center lista" style="margin: 0; padding: 0;">
                        <li class="p-3 servicos" style="cursor: pointer; border-bottom: 2px solid #eee; background: #fff;">
                            <img src="../9055226_bxs_dashboard_icon.png" style="width: 60px; height: 60px; display: block; margin: 0 auto 10px;">
                            <p style="margin: 0; font-size: 13px; font-weight: bold; color: #333;">Serviços</p>
                        </li>
                        <li class="p-3 caixa" style="cursor: pointer; background: #fff;">
                            <img src="../2559831_box_media_network_social_icon.png" style="width: 60px; height: 60px; display: block; margin: 0 auto 10px;">
                            <p style="margin: 0; font-size: 13px; font-weight: bold; color: #333;">Caixa</p>
                        </li>
                    </ul>
                </div>
        
                <!-- Área de Serviços -->
                <div class="col-sm-7 segunda1 table-responsive" style="background:#fff;display: flex;flex-direction: row;overflow: auto;height:90vh;color: #333;justify-content: center;padding-top: 1%;">
                    <table id="example" style="height: 86%;width: 100%;">
                        <thead>
                            <th>#</th>
                            <th>Serviço</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM servicos_clinica WHERE ativo = 1 ORDER BY categoria, nome";
                            $rs = mysqli_query($db, $sql);

                            while ($dados = mysqli_fetch_array($rs)) {
                                $servico_id = $dados['id'];
                                $preco_final = floatval($dados['preco']);
                                
                                if($tabela_precos_id) {
                                    $sql_preco = "SELECT preco, desconto_percentual FROM tabela_precos_servicos 
                                                 WHERE tabela_precos_id = $tabela_precos_id AND servico_id = $servico_id";
                                    $rs_preco = mysqli_query($db, $sql_preco);
                                    if($rs_preco && mysqli_num_rows($rs_preco) > 0) {
                                        $preco_data = mysqli_fetch_array($rs_preco);
                                        $preco_final = floatval($preco_data['preco']);
                                        if(isset($preco_data['desconto_percentual']) && $preco_data['desconto_percentual'] > 0) {
                                            $preco_final = $preco_final * (1 - floatval($preco_data['desconto_percentual']) / 100);
                                        }
                                    } else {
                                        if($desconto_geral > 0) {
                                            $preco_final = $preco_final * (1 - $desconto_geral / 100);
                                        }
                                    }
                                }
                            ?>
                            <tr data-idservico="<?php echo $servico_id; ?>">
                                <td><?php echo $servico_id; ?></td>
                                <td><?php echo $dados['nome']; ?></td>
                                <td><?php echo $dados['categoria'] ?: '-'; ?></td>
                                <td><?php echo number_format($preco_final, 2, ',', '.'); ?> MT</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Área de Caixa -->
                <div class="col-sm-7 segunda2" style="padding: 8px;display: flex;flex-direction: column;overflow: hidden;height:90vh;display:none;background: #fff;">
                    <div class="col-sm-12 mb-2" style="background: #38D0ED; padding: 8px; border-radius: 3px;">
                        <h2 style="margin: 0; font-size: 18px; font-weight: bold; color: #fff;">Gestão de Caixa - <?php echo date('d/m/Y'); ?></h2>
                    </div>
                    <div class="col-sm-12" style="padding: 0;display: flex;flex-direction: row;justify-content: space-around;flex-wrap:wrap; margin-bottom: 8px;">
                        <div class="card col-sm-5 p-2" style="border: 1px solid #e0e0e0; border-radius: 3px; background: #fff;">
                          <div class="card-body" style="padding: 8px;">
                            <h5 class="card-title" style="font-size: 12px; color: #666; margin: 0 0 5px 0;">Valor em Caixa - Abertura</h5>
                            <div id="valorAbertura" style="color: #28a745; font-weight: bold; font-size: 24px; margin: 5px 0; min-height: 30px;">0,00 MT</div>
                          </div>
                          <div style="display: flex;flex-direction: column; margin-top: 10px; gap: 8px;">
                              <label style="font-size: 11px; color: #666; margin: 0;">Valor Inicial:</label>
                              <input type="number" step="0.01" class="form-control text-center" value="0" style="height: 40px; font-size: 16px; font-weight: bold;" id="valorAberturaCaixa" placeholder="0.00">
                              <label style="font-size: 11px; color: #666; margin: 0;">Usuário:</label>
                              <select id="usuarioAbertura" class="form-control" style="height: 40px; font-size: 14px;">
                                  <option value="">Selecione o usuário</option>
                                  <?php
                                  $sql_users = "SELECT * FROM users ORDER BY nome";
                                  $rs_users = mysqli_query($db, $sql_users);
                                  if($rs_users && mysqli_num_rows($rs_users) > 0) {
                                      while($user = mysqli_fetch_array($rs_users)) {
                                          echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['nome']) . '</option>';
                                      }
                                  }
                                  ?>
                              </select>
                              <button class="btn btn-success" id="btnOpenCaixa" style="height: 45px; font-size: 15px; font-weight: bold; width: 100%;"> Abrir Caixa</button>
                          </div>
                          <div class="msgOpenCaixa" style="margin-top: 5px; padding: 3px; min-height: 18px; font-size: 11px;">
                          </div>
                        </div>
                        <div class="card col-sm-5 p-2" style="border: 1px solid #e0e0e0; border-radius: 3px; background: #fff;">
                          <div class="card-body" style="padding: 8px;">
                            <h5 class="card-title" style="font-size: 12px; color: #666; margin: 0 0 5px 0;">Valor em Caixa - Fecho</h5>
                            <div id="valorFecho" style="color: #dc3545; font-weight: bold; font-size: 24px; margin: 5px 0; min-height: 30px;">0,00 MT</div>
                          </div>
                          <div style="display: flex;flex-direction: column; margin-top: 10px; gap: 8px;">
                              <label style="font-size: 11px; color: #666; margin: 0;">Usuário:</label>
                              <select id="usuarioFechamento" class="form-control" style="height: 40px; font-size: 14px;">
                                  <option value="">Selecione o usuário</option>
                                  <?php
                                  $sql_users = "SELECT * FROM users ORDER BY nome";
                                  $rs_users = mysqli_query($db, $sql_users);
                                  if($rs_users && mysqli_num_rows($rs_users) > 0) {
                                      while($user = mysqli_fetch_array($rs_users)) {
                                          echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['nome']) . '</option>';
                                      }
                                  }
                                  ?>
                              </select>
                              <div style="display: flex;flex-direction: row;justify-content: space-between;gap: 8px;">
                                  <a class="btn btn-info printa" style="height: 45px; font-size: 15px; font-weight: bold; padding: 0 15px; flex: 1; display: flex; align-items: center; justify-content: center;"> 📄 Imprimir</a>
                                  <button class="btn btn-danger" style="height: 45px; font-size: 15px; font-weight: bold; padding: 0 15px; flex: 1; display: flex; align-items: center; justify-content: center;" id="btnCloseCaixa"> 🔒 Fechar Caixa</button>
                              </div>
                          </div>
                          <div class="msgCloseCaixa" style="margin-top: 5px; padding: 3px; min-height: 18px; font-size: 11px;">
                          </div>
                        </div>
                    </div>
                    <div class="col-sm-12 poptype" style="padding: 0 8px; flex: 1; overflow-y: auto;">
                        <h4 style="margin-bottom: 5px; font-weight: bold; color: #333; font-size: 13px; border-bottom: 1px solid #38D0ED; padding-bottom: 3px;">Totais por Método de Pagamento</h4>
                        <!-- Totais por método de pagamento -->
                    </div>
                </div>
        
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

    <script src="../jquery-3.5.1.js"></script>
    <script src="../bootstrap.js"></script>
    <script src="../bootstrap.min.js"></script>
    <link rel="stylesheet" href="../select2.min.css">
    <script src="../select2.min.js"></script>
    <script src="../datatables.min.js"></script>
    <script>
        var empresaSelecionada = <?php echo $empresa_selecionada ? $empresa_selecionada : 'null'; ?>;
        
        $(function(){
            setInterval(nrpedido, 1000);

            function nrpedido(){
              $.ajax({
                  url: 'vds_recepcao/nrpedido.php',
                  type: 'GET',
                  success: function(data) {
                    $("#nrpedido").html(data);
                  },
                  error: function() {
                    $("#nrpedido").html("Ocorreu um erro ao popular o numero da venda!");
                  }
              });
            }
      
            popprodtable();
            showtot();
            qrcodefocus();

            function popprodtable(){
                var url = 'vds_recepcao/popprodtable.php';
                if(empresaSelecionada) {
                    url += '?empresa=' + empresaSelecionada;
                }
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data){
                        $("#poptempart").html(data);
                        $(document).off('click', '.btn_diminuir');
                        $(document).off('click', '.btn_aumentar');
                        $(document).off('click', '.btnremove');
                        $(document).off('change', '.qtd');
                        
                        $(document).on('click', '.btn_diminuir', function() {
                            var idServico = $(this).data("idservico");
                            decreaseQuantity(idServico);
                        });
                        $(document).on('click', '.btn_aumentar', function() {
                            var idServico = $(this).data("idservico");
                            increaseQuantity(idServico);
                        });
                        $(document).on('change', '.qtd', function() {
                            var idServico = $(this).data("idservico");
                            var novoValor = $(this).val();
                            updateQuantity(idServico, novoValor);
                        });
                        $(document).on('click', '.btnremove', function() {
                            var idServico = $(this).data("idservico");
                            btnremover(idServico);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao carregar serviços:", status, error);
                        $("#poptempart").html('<p class="text-center text-danger">Erro ao carregar serviços</p>');
                    }
                });
            }
            
            function decreaseQuantity(idServico) {
                $.ajax({
                    url: 'vds_recepcao/qtd_decrease.php',
                    type: 'POST',
                    data: { id: idServico },
                    success: function(data){
                        popprodtable();
                        showtot();
                        qrcodefocus()
                    }
                });
            }
            
            function increaseQuantity(idServico) {
                $.ajax({
                    url: 'vds_recepcao/qtd_increase.php',
                    type: 'POST',
                    data: { id: idServico },
                    success: function(data){
                        popprodtable();
                        showtot();
                        qrcodefocus()
                    }
                });
            }
            
            function updateQuantity(idServico, novoValor) {
                $.ajax({
                    url: 'vds_recepcao/qtd_update.php',
                    type: 'POST',
                    data: { id: idServico, novoValor: novoValor },
                    success: function(data){
                        popprodtable();
                        showtot();
                        qrcodefocus()
                    }
                });
            }
            
            function showtot() {
                var url = 'vds_recepcao/show_tot.php';
                if(empresaSelecionada) {
                    url += '?empresa=' + empresaSelecionada;
                }
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data){
                        $(".ttl").html(data);
                        // Atualizar também no modal se estiver aberto
                        if($('#exampleModal3').hasClass('show')) {
                            calcularTroco();
                        }
                    }
                });
            }
            function qrcodefocus() {
                $("#qrcode").focus();
            }
            
            $("#qrcode").on('change', function(){
                var codbar = $(this).val();
                alert("QR Code não disponível para serviços");
            });
            
            $("#example tbody tr").click(handleClick);
            function handleClick() {
                var idServico = this.getAttribute('data-idservico');
                $.ajax({
                    type: "POST",
                    url: "vds_recepcao/addservtemp.php",
                    data: { 
                        idServico: idServico,
                        empresa_id: empresaSelecionada
                    },
                    success: function(response){
                        if(response == 1){
                            alert("Erro ao adicionar serviço!");
                        } else if(response == 2){
                            alert("Erro ao adicionar serviço");
                        } else if(response == 3){
                            popprodtable();
                            showtot();
                            qrcodefocus();
                        } else if(response == 31){
                            alert("Ocorreu um erro ao adicionar o serviço! Contacte o administrador do sistema.");
                        } else if(response == 40){
                            alert("Não foram enviados parâmetros para a devida requisição.");
                        } else if(response == 10){
                            alert("Não foi possível encontrar um serviço com este id.");
                        }else if(response == 7){
                            alert("Serviço não disponível.");
                        }
                        console.log(response);
                    }
                });
            }
            
            $(".btn_criar").click(function(){
                criarpedido();
            });
            
            function criarpedido() {
                var valorTotal = parseFloat($(".ttl").text().replace(/\./g, '').replace(',', '.')) || 0;
                var valorPago = parseFloat($(".valor-pago").val()) || 0;
                
                if (!valorPago || valorPago <= 0) {
                    alert("Por favor, informe o valor pago pelo paciente!");
                    $(".valor-pago").focus();
                    return;
                }
                
                if (valorPago < valorTotal) {
                    var falta = valorTotal - valorPago;
                    if (!confirm("O valor pago é insuficiente! Falta MT " + falta.toFixed(2).replace('.', ',') + "\nDeseja continuar mesmo assim?")) {
                        $(".valor-pago").focus();
                        return;
                    }
                }
                
                if (confirm("Tem certeza de que deseja efetuar o pagamento?")) {
                    var paciente = $("#selectPaciente").val();
                    if(!paciente || paciente == "" || paciente == null){
                        alert("Por favor, selecione um paciente!");
                        $("#selectPaciente").focus();
                        return false;
                    }else{
                        $.ajax({
                            type: "POST",
                            url: "vds_recepcao/criarpedido.php",
                            data: {
                              paciente: paciente,
                              empresa_id: empresaSelecionada,
                              metodo: $(".metodo").val(),
                              valor: valorPago
                            },
                            dataType: "json",
                            success: function(response){
                                if (response.error) {
                                    alert(response.error);
                                } else if (response.ot == 1000000000001) {
                                    alert("O valor informado é inferior ao valor a pagar, por favor informe novamente o valor.");
                                    $(".valor-pago").focus();
                                } else if(response.id) {
                                    var mensagem = "Pagamento efetuado com sucesso!";
                                    if (valorPago > valorTotal) {
                                        var troco = valorPago - valorTotal;
                                        mensagem += "\nTroco: MT " + troco.toFixed(2).replace('.', ',');
                                    }
                                    alert(mensagem);
                                    location.reload();
                                } 
                                console.log(response);
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                alert("Ocorreu um erro: " + textStatus);
                            }
                        }); 
                    }
                }
            }
        
            $('.js-example-basic-single').select2();
            $('.js-example-basic-single1').select2();
            
            function carregarPacientes(empresaId) {
                var url = 'vds_recepcao/buscar_pacientes.php';
                if(empresaId && empresaId != '') {
                    url += '?empresa_id=' + empresaId;
                }
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#selectPaciente').select2('destroy');
                        $('#selectPaciente').empty().append('<option value="">Selecione um paciente...</option>');
                        
                        if(data && Array.isArray(data) && data.length > 0) {
                            $.each(data, function(index, paciente) {
                                var nomeCompleto = paciente.nome + ' ' + paciente.apelido;
                                var texto = nomeCompleto + ' (' + paciente.numero_processo + ')';
                                $('#selectPaciente').append('<option value="' + paciente.id + '">' + texto + '</option>');
                            });
                        }
                        
                        $('#selectPaciente').select2();
                        $('#selectPaciente').val('').trigger('change');
                    },
                    error: function() {
                        console.error('Erro ao carregar pacientes');
                    }
                });
            }
            
            if(empresaSelecionada) {
                carregarPacientes(empresaSelecionada);
            }
            
            $("#cancelar").click(function() {
              if (confirm("Tem certeza de que deseja remover estes items?")){
                $.ajax({
                        url: 'vds_recepcao/removeservall.php',
                        method: 'POST',
                        success: function(response) {
                            popprodtable();
                            showtot();
                            qrcodefocus();
                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
              }
            });
            function btnremover(idServico){
                if (confirm("Tem certeza de que deseja remover este item?")) {
                    $.ajax({
                        url: 'vds_recepcao/removeservtemp.php',
                        method: 'POST',
                        data: { idServico: idServico },
                        success: function(response) {
                            popprodtable();
                            showtot();
                            qrcodefocus();
                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            }
            
            $('#example').DataTable();
            
            // FUNÇÃO PARA CALCULAR TROCO AUTOMATICAMENTE
            function calcularTroco() {
                // Pegar valor total do modal
                var totalText = $('#exampleModal3 .ttl').text() || '0,00 MT';
                var valorTotal = parseFloat(totalText.replace(/\./g, '').replace(',', '.').replace(' MT', '').trim()) || 0;
                var valorPago = parseFloat($(".valor-pago").val()) || 0;
                
                if (valorPago > 0) {
                    var troco = valorPago - valorTotal;
                    
                    if (troco >= 0) {
                        $("#troco").val(troco.toFixed(2).replace('.', ',') + " MT");
                    } else {
                        $("#troco").val("Falta " + Math.abs(troco).toFixed(2).replace('.', ',') + " MT");
                    }
                } else {
                    $("#troco").val("");
                }
            }
            
            // ATUALIZAR TROCO QUANDO O MODAL ABRIR
            $('#exampleModal3').on('shown.bs.modal', function () {
                // Atualizar total no modal (usar o valor do botão Pagar, não duplicar)
                var totalAtual = $(".sticky-right .ttl").text();
                if(totalAtual && totalAtual.trim() !== '' && totalAtual !== '0,00 MT') {
                    $('#exampleModal3 .ttl').text(totalAtual);
                } else {
                    showtot();
                    setTimeout(function() {
                        var novoTotal = $(".sticky-right .ttl").text();
                        $('#exampleModal3 .ttl').text(novoTotal);
                    }, 300);
                }
                
                $(".valor-pago").val("");
                $("#troco").val("");
                setTimeout(function() {
                    $(".valor-pago").focus();
                }, 500);
                $('.js-example-basic-single1').select2({
                    dropdownParent: $('#exampleModal3 .modal-body')
                });
            });
            
            
            // CALCULAR TROCO EM TEMPO REAL
            $(document).on('input', '.valor-pago', function() {
                calcularTroco();
            });
            
            // ALTERNAR ENTRE SERVIÇOS E CAIXA
            $(".servicos").click(function() {
                $(".segunda1").show();
                $(".segunda2").hide();
                $(".servicos").addClass("active");
                $(".caixa").removeClass("active");
            });
            
            $(".caixa").click(function() {
                $(".segunda1").hide();
                $(".segunda2").show();
                $(".caixa").addClass("active");
                $(".servicos").removeClass("active");
                showCaixaAbertura();
                showCaixaFecho();
                showtype();
            });
            
            // Inicializar com serviços ativo
            $(".servicos").addClass("active");
            
            // FUNÇÕES DO CAIXA
            function showCaixaAbertura(){
                $.ajax({
                    url: 'vds_recepcao/showCaixaAbertura.php',
                    type: 'GET',
                    success: function(data){
                        $("#valorAbertura").html(data);
                    },
                    error: function(){
                        $("#valorAbertura").html("0,00 MT");
                    }
                });
            }
            
            function showCaixaFecho(){
                $.ajax({
                    url: 'vds_recepcao/showCaixaFecho.php',
                    type: 'GET',
                    success: function(data){
                        $("#valorFecho").html(data);
                    },
                    error: function(){
                        $("#valorFecho").html("0,00 MT");
                    }
                });
            }
            
            function showtype(){
                $.ajax({
                    url: 'vds_recepcao/showtype.php',
                    type: 'GET',
                    success: function(data){
                        $(".poptype").html(data);
                    },
                    error: function(){
                        $(".poptype").html("<p style='margin: 10px 0; font-size: 12px; color: #dc3545;'>Erro ao carregar totais!</p>");
                    }
                });
            }
            
            // Atualizar valores do caixa a cada 5 segundos quando estiver na aba de caixa
            setInterval(function() {
                if($(".segunda2").is(":visible")) {
                    showCaixaAbertura();
                    showCaixaFecho();
                    showtype();
                }
            }, 5000);
            
            // ABRIR CAIXA
            $("#btnOpenCaixa").click(function(){
                var valor = $("#valorAberturaCaixa").val();
                var usuario = $("#usuarioAbertura").val();
                
                if(!usuario || usuario === '') {
                    alert('Por favor, selecione o usuário que vai abrir o caixa!');
                    return;
                }
                
                if(!valor || parseFloat(valor) <= 0) {
                    alert('Por favor, informe um valor inicial válido!');
                    return;
                }
                var valor = $("#valorAberturaCaixa").val();
                if(!valor || valor <= 0) {
                    alert("Por favor, informe o valor de abertura do caixa!");
                    return;
                }
                $.ajax({
                    url: 'vds_recepcao/openCaixa.php',
                    type: 'POST',
                    data: { valor: valor, usar: usuario },
                    success: function(data){
                        $(".msgOpenCaixa").html(data);
                        showCaixaAbertura();
                        showCaixaFecho();
                        $("#valorAberturaCaixa").val(0);
                        $("#usuarioAbertura").val('');
                    }
                });
            });
            
            // FECHAR CAIXA
            $("#btnCloseCaixa").click(function(){
                var usuario = $("#usuarioFechamento").val();
                
                if(!usuario || usuario === '') {
                    alert('Por favor, selecione o usuário que vai fechar o caixa!');
                    return;
                }
                
                if(confirm("Tem certeza de que deseja fechar o caixa?")) {
                    $.ajax({
                        url: 'vds_recepcao/closeCaixa.php',
                        type: 'POST',
                        data: { usar: usuario },
                        success: function(data){
                            $(".msgCloseCaixa").html(data);
                            showCaixaAbertura();
                            showCaixaFecho();
                            showtype();
                            $("#usuarioFechamento").val('');
                        }
                    });
                }
            });
            
            // IMPRIMIR CAIXA
            $(".printa").on('click', function() {
                var data_hoje = '<?php echo date('Y-m-d'); ?>';
                window.open("vds_recepcao/print_caixa.php?data=" + data_hoje, "_blank");
            });
        });
    </script>
    </body>
</html>


