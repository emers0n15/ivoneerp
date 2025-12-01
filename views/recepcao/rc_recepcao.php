<?php 
    session_start();
    if(!isset($_SESSION['idUsuario'])){
      header("location:../../");
      exit;
    }
    
    try {
        include_once '../../conexao/index.php';
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // Não exibir erros na tela, apenas logar
        
        $_SESSION['idUsuario'] = $_SESSION['idUsuario'];
        $_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
        $_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
        
        $userID = $_SESSION['idUsuario'];
        
        // Limpar faturas temporárias do usuário ao carregar a página
        // Verificar se a tabela existe antes de limpar
        $check_table_temp = "SHOW TABLES LIKE 'rc_faturas_temp_recepcao'";
        $table_temp_exists = mysqli_query($db, $check_table_temp);
        if($table_temp_exists && mysqli_num_rows($table_temp_exists) > 0) {
            $sql_limpar = "DELETE FROM rc_faturas_temp_recepcao WHERE user = ?";
            $stmt_limpar = mysqli_prepare($db, $sql_limpar);
            if($stmt_limpar) {
                mysqli_stmt_bind_param($stmt_limpar, "i", $userID);
                mysqli_stmt_execute($stmt_limpar);
            }
        }
    } catch (Exception $e) {
        error_log("RC: Erro ao inicializar página - " . $e->getMessage());
        die("Erro ao carregar página. Verifique os logs do servidor.");
    }
?>
<html>
    <head>
      <meta charset="utf-8">
      <title>RC - Recibo (Recepção)</title>
      <link rel="shorcut icon"  href="../../img/config/iCone.png">
      <link rel="stylesheet" href="../bootstrap.css">
      <link rel="stylesheet" href="../bootstrap.min.css">
      <link rel="stylesheet" href="../all.min.css">
      <link href="../datatables.min.css" rel="stylesheet"/>
  
      <style>
          *{ font-family: system-ui; font-size: 10pt; }
          body{ background: #eee; }
          .sticky-right { position: -webkit-sticky; position: sticky; top: 0; height: 90vh; }
          .sticky-header { position: -webkit-sticky; position: sticky; top: 0; z-index: 1000; }
          .select2-container--default .select2-selection--single { height: 40px; }
          .select2-container--default .select2-dropdown--below { max-height: 200px; }
          .fatura-item {
              background: #fff;
              padding: 10px;
              margin: 5px 0;
              border-radius: 5px;
              border-left: 4px solid #38D0ED;
          }
          .valor-input {
              width: 120px;
              text-align: right;
          }
      </style>
    </head>
    <body>
              <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">Criar Recibo</h3>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                          </div>
                          <div class="modal-body">
                              <div class="col-sm-12" style="display:flex; flex-direction:column;justify-content: center;align-items: center;">
                            <h4>Valor Total do Recibo</h4>
                            <h1 class="ttl"></h1>
                            </div>
                            <br>
                            <div class="row">
                            <div class="col-sm-12">
                              <label class="form-label">Método de Pagamento *</label>
                              <select class="form-select metodo" id="selectMetodo" style="width: 100%; height: 38px;">
                                  <option value="dinheiro">Dinheiro</option>
                                  <option value="m_pesa">M-Pesa</option>
                                  <option value="emola">Emola</option>
                                  <option value="pos">POS</option>
                                  <option value="transferencia">Transferência</option>
                              </select>
                            </div>
                            </div>
                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary btn_criar">Criar Recibo</button>
                          </div>
                        </div>
                      </div>
              </div>
              
              <div class="modal fade" id="modalFatura" tabindex="-1" aria-labelledby="modalFaturaLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h3 class="modal-title fs-5" id="modalFaturaLabel">Selecionar Faturas</h3>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                      </div>
                      <div class="modal-body">
                          <div class="col-sm-12">
                            <label class="form-label">Fatura *</label>
                            <select class="form-select js-example-basic-single" id="selectFatura" style="width: 100%;">
                                <option value="">Selecione uma fatura...</option>
                                <?php 
                                $check_table = "SHOW TABLES LIKE 'factura_recepcao'";
                                $table_exists = mysqli_query($db, $check_table);
                                if($table_exists && mysqli_num_rows($table_exists) > 0) {
                                    // Verificar se a tabela pagamentos_recepcao existe
                                    $check_table_pag = "SHOW TABLES LIKE 'pagamentos_recepcao'";
                                    $table_pag_exists = mysqli_query($db, $check_table_pag);
                                    
                                    if($table_pag_exists && mysqli_num_rows($table_pag_exists) > 0) {
                                        // Usar subquery para calcular total_pago
                                        $sql = "SELECT f.*, p.nome, p.apelido, p.numero_processo, e.nome as empresa_nome,
                                                COALESCE((SELECT SUM(valor_pago) FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id), 0) as total_pago
                                                FROM factura_recepcao f 
                                                INNER JOIN pacientes p ON f.paciente = p.id 
                                                LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
                                                WHERE f.valor > COALESCE((SELECT SUM(valor_pago) FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id), 0)
                                                ORDER BY f.data DESC LIMIT 100";
                                    } else {
                                        // Se não existe tabela de pagamentos, mostrar todas as faturas
                                        $sql = "SELECT f.*, p.nome, p.apelido, p.numero_processo, e.nome as empresa_nome,
                                                0 as total_pago
                                                FROM factura_recepcao f 
                                                INNER JOIN pacientes p ON f.paciente = p.id 
                                                LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
                                                WHERE f.valor > 0
                                                ORDER BY f.data DESC LIMIT 100";
                                    }
                                    
                                    $rs = mysqli_query($db, $sql);
                                    if($rs) {
                                        $count_faturas = 0;
                                        while ($dados = mysqli_fetch_array($rs)) {
                                            $count_faturas++;
                                            $valor_pendente = floatval($dados['valor']) - floatval($dados['total_pago']);
                                            $fatura_text = "FA#" . $dados['serie'] . "/" . str_pad($dados['n_doc'], 6, '0', STR_PAD_LEFT) . " - " . 
                                                          $dados['nome'] . " " . $dados['apelido'] . 
                                                          ($dados['empresa_nome'] ? " (" . $dados['empresa_nome'] . ")" : "") . 
                                                          " - Pendente: " . number_format($valor_pendente, 2, ',', '.') . " MT";
                                        ?>
                                          <option value="<?php echo $dados['id'];?>" data-valor="<?php echo $valor_pendente; ?>"><?php echo htmlspecialchars($fatura_text); ?></option>
                                        <?php
                                        }
                                        
                                        if($count_faturas == 0) {
                                            echo '<option value="">Nenhuma fatura pendente encontrada</option>';
                                        }
                                    } else {
                                        error_log("RC: Erro ao buscar faturas - " . mysqli_error($db));
                                        echo '<option value="">Erro ao carregar faturas</option>';
                                    }
                                } else {
                                    echo '<option value="">Tabela factura_recepcao não existe. Execute o SQL de criação.</option>';
                                }
                                ?>
                            </select>
                            </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" onclick="adicionarFatura()">Adicionar</button>
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
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalFatura">Adicionar Fatura</button>
                    </div>
                </div>
                <div class="col-sm-4" style="background: #fff;">
                </div>
            </div>
            <div class="row">
                <!-- Área de Faturas -->
                <div class="col-sm-8" style="background:#fff;padding: 20px;height:90vh;overflow:auto;">
                    <h3>Faturas Selecionadas</h3>
                    <div id="poptempart">
                        <p class="text-center text-muted" style="padding:20px;color:#666;">Nenhuma fatura selecionada</p>
                    </div>
                </div>
        
                <div class="col-sm-4 sticky-right" style="padding: 0;background: #eee;">
                    <div class="col-sm-12 text-center p-2" style="display: flex;flex-direction: row;justify-content: center">
                        <h3 id="nrpedido" style="font-size: 14pt;"></h3>
                    </div>
                    <div class="col-sm-12 p-2" style="background:#fff;height:70vh;overflow:auto;">
                        <h4>Resumo</h4>
                        <div id="resumo">
                            
                        </div>
                    </div>
                    <div class="col-sm-12 text-center" style="display:flex; flex-direction:row;justify-content: space-between;background:#38D0ED;height:13vh;color:#fff;">
                        <div id="cancelar" class="col-sm-6" style="display:flex; flex-direction:column;justify-content: center;align-items: center;cursor:pointer;">
                            <h4>Cancelar</h4>
                        </div>
                        <div class="col-sm-6" style="display:flex; flex-direction:column;justify-content: center;align-items: center;border-left: 1px solid #fff;cursor:pointer;" data-toggle="modal" data-target="#exampleModal3">
                            <h4>Criar RC</h4>
                            <h2 class="ttl">0,00 MT</h2>
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
        $(function(){
            setInterval(nrpedido, 1000);
            popprodtable();
            showtot();

            function nrpedido(){
              $.ajax({
                  url: 'rc_recepcao/nrpedido.php',
                  type: 'GET',
                  success: function(data) {
                    $("#nrpedido").html(data);
                  },
                  error: function() {
                    $("#nrpedido").html("Erro ao carregar número do RC!");
                  }
              });
            }
      
            function popprodtable(){
                $.ajax({
                    url: 'rc_recepcao/popprodtable.php',
                    type: 'GET',
                    success: function(data){
                        $("#poptempart").html(data);
                        $(document).off('click', '.btnremove');
                        $(document).off('change', '.valor-fatura');
                        
                        $(document).on('click', '.btnremove', function() {
                            var faturaId = $(this).data("faturaid");
                            btnremover(faturaId);
                        });
                        $(document).on('change', '.valor-fatura', function() {
                            var faturaId = $(this).data("faturaid");
                            var novoValor = $(this).val();
                            updateValor(faturaId, novoValor);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao carregar faturas:", status, error, xhr.responseText);
                        $("#poptempart").html('<p class="text-center text-danger">Erro ao carregar faturas. Verifique o console (F12) para mais detalhes.</p>');
                    }
                });
            }
            
            function updateValor(faturaId, novoValor) {
                $.ajax({
                    url: 'rc_recepcao/update_valor.php',
                    type: 'POST',
                    data: { fatura_id: faturaId, valor: novoValor },
                    success: function(data){
                        popprodtable();
                        showtot();
                    }
                });
            }
            
            function showtot() {
                $.ajax({
                    url: 'rc_recepcao/show_tot.php',
                    type: 'GET',
                    success: function(data){
                        $(".ttl").html(data);
                        showresumo();
                    }
                });
            }
            
            function showresumo() {
                $.ajax({
                    url: 'rc_recepcao/show_resumo.php',
                    type: 'GET',
                    success: function(data){
                        $("#resumo").html(data);
                    }
                });
            }
            
            window.adicionarFatura = function() {
                var faturaId = $('#selectFatura').val();
                if(!faturaId || faturaId == "") {
                    alert("Por favor, selecione uma fatura!");
                    return;
                }
                var valor = $('#selectFatura option:selected').data('valor');
                $.ajax({
                    type: "POST",
                    url: "rc_recepcao/addfaturatemp.php",
                    data: { 
                        fatura_id: faturaId,
                        valor: valor
                    },
                    success: function(response){
                        if(response == 1){
                            alert("Erro ao adicionar fatura!");
                        } else if(response == 2){
                            alert("Fatura já foi adicionada!");
                        } else if(response == 3){
                            $('#modalFatura').modal('hide');
                            $('#selectFatura').val('').trigger('change');
                            popprodtable();
                            showtot();
                        } else if(response == 31){
                            alert("Ocorreu um erro! Contacte o administrador do sistema.");
                        } else if(response == 40){
                            alert("Não foram enviados parâmetros para a devida requisição.");
                        }
                        console.log(response);
                    }
                });
            }
            
            $(".btn_criar").click(function(){
                criarrecibo();
            });
            
            function criarrecibo() {
                var metodo = $(".metodo").val();
                if(!metodo || metodo == "") {
                    alert("Por favor, selecione o método de pagamento!");
                    $(".metodo").focus();
                    return;
                }
                
                if (confirm("Tem certeza de que deseja criar o recibo?")) {
                    $.ajax({
                        type: "POST",
                        url: "rc_recepcao/criarrecibo.php",
                        data: {
                          metodo: metodo
                        },
                        success: function(data){
                            if (data == 1) {
                              alert("Nenhuma fatura selecionada!");
                            }else if(data == 2){
                              alert('Erro ao criar recibo.');
                            }else if(data == 3){
                              alert('Não é possível criar um recibo para um ano diferente do atual!');
                            }else if(data == 4){
                              alert('Erro: As tabelas necessárias não foram criadas.');
                            }else{
                              alert('Recibo criado com sucesso! ID: ' + data);
                              location.reload();
                            }
                        },
                        error: function(){
                            alert("Ocorreu um erro com a requisição ajax");
                        }
                    }); 
                }
            }
        
            // Inicializar select2 quando o modal abrir
            $('#modalFatura').on('shown.bs.modal', function () {
                if ($('#selectFatura').hasClass('select2-hidden-accessible')) {
                    $('#selectFatura').select2('destroy');
                }
                $('#selectFatura').select2({
                    dropdownParent: $('#modalFatura .modal-body'),
                    width: '100%',
                    placeholder: 'Selecione uma fatura...'
                });
            });
            
            // Também inicializar no carregamento da página
            $('.js-example-basic-single').select2();
            
            $("#cancelar").click(function() {
              if (confirm("Tem certeza de que deseja remover todas as faturas?")){
                $.ajax({
                        url: 'rc_recepcao/removeall.php',
                        method: 'POST',
                        success: function(response) {
                            popprodtable();
                            showtot();
                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
              }
            });
            function btnremover(faturaId){
                if (confirm("Tem certeza de que deseja remover esta fatura?")) {
                    $.ajax({
                        url: 'rc_recepcao/removefatura.php',
                        method: 'POST',
                        data: { fatura_id: faturaId },
                        success: function(response) {
                            popprodtable();
                            showtot();
                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            }
        });
    </script>
    </body>
</html>

