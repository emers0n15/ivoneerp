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
    
    $userID = $_SESSION['idUsuario'];
    // Limpar serviços temporários do usuário ao carregar a página
    $sql_limpar = "DELETE FROM nd_servicos_temp WHERE user = ?";
    $stmt_limpar = mysqli_prepare($db, $sql_limpar);
    mysqli_stmt_bind_param($stmt_limpar, "i", $userID);
    mysqli_stmt_execute($stmt_limpar);
    
    $fatura_selecionada = isset($_GET['fatura']) ? intval($_GET['fatura']) : null;
    $empresa_selecionada = null;
    $paciente_selecionado = null;
    
    if($fatura_selecionada) {
        // Buscar informações da fatura selecionada
        $check_table = "SHOW TABLES LIKE 'factura_recepcao'";
        $table_exists = mysqli_query($db, $check_table);
        if($table_exists && mysqli_num_rows($table_exists) > 0) {
            $sql_fatura = "SELECT f.*, p.nome, p.apelido, e.nome as empresa_nome 
                          FROM factura_recepcao f 
                          INNER JOIN pacientes p ON f.paciente = p.id 
                          LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
                          WHERE f.id = $fatura_selecionada";
            $rs_fatura = mysqli_query($db, $sql_fatura);
            if($rs_fatura && mysqli_num_rows($rs_fatura) > 0) {
                $fatura_data = mysqli_fetch_array($rs_fatura);
                $empresa_selecionada = $fatura_data['empresa_id'] ?? null;
                $paciente_selecionado = $fatura_data['paciente'] ?? null;
            }
        }
    }
    
    $tabela_precos_id = null;
    $desconto_geral = 0;
    
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
      <title>ND - Nota de Débito (Recepção)</title>
      <link rel="shorcut icon"  href="../../img/config/iCone.png">
      <link rel="stylesheet" href="../bootstrap.css">
      <link rel="stylesheet" href="../bootstrap.min.css">
      <link rel="stylesheet" href="../all.min.css">
      <link href="../datatables.min.css" rel="stylesheet"/>
  
      <style>
          *{ font-family: system-ui; font-size: 10pt; }
          #example tr{ cursor: pointer; }
          body{ background: #eee; }
          .pdt img{ max-width: 100%; width: 60%; }
          .sticky-left { position: -webkit-sticky; position: sticky; top: 0; height: 90vh; }
          .pdt, .lista li{ transition: all .2s ease-in-out; }
          .pdt:hover{ background: #eee; cursor: pointer; border-radius: 15px; }
          .lista li:hover{ background: #fff; cursor: pointer; }
          .sticky-right { position: -webkit-sticky; position: sticky; top: 0; height: 90vh; }
          .sticky-header { position: -webkit-sticky; position: sticky; top: 0; z-index: 1000; }
          #qrcode{ height: 40px; width: 100%; }
          .select2-container--default .select2-selection--single { height: 40px; }
          .select2-container--default .select2-dropdown--below { max-height: 200px; }
          .fatura-info {
              background: #fff3cd;
              padding: 10px;
              border-radius: 5px;
              margin-bottom: 10px;
              border-left: 4px solid #ffc107;
          }
      </style>
    </head>
    <body>
              <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">Criar Nota de Débito</h3>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                          </div>
                          <div class="modal-body">
                              <div class="col-sm-12" style="display:flex; flex-direction:column;justify-content: center;align-items: center;">
                            <h4>Valor da Nota de Débito</h4>
                            <h1 class="ttl"></h1>
                            </div>
                            <br>
                            <div class="row">
                            <div class="col-sm-12">
                              <label class="form-label">Motivo *</label>
                              <textarea class="form-control motivo" id="motivo" rows="3" placeholder="Informe o motivo da nota de débito..." required></textarea>
                            </div>
                            </div>
                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary btn_criar">Criar Nota de Débito</button>
                          </div>
                        </div>
                      </div>
              </div>
              
              <div class="modal fade" id="modalFatura" tabindex="-1" aria-labelledby="modalFaturaLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h3 class="modal-title fs-5" id="modalFaturaLabel">Selecionar Fatura</h3>
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
                                    $sql = "SELECT f.*, p.nome, p.apelido, p.numero_processo, e.nome as empresa_nome
                                            FROM factura_recepcao f 
                                            INNER JOIN pacientes p ON f.paciente = p.id 
                                            LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
                                            ORDER BY f.data DESC LIMIT 50";
                                    $rs = mysqli_query($db, $sql);
                                    while ($dados = mysqli_fetch_array($rs)) {
                                        $fatura_text = "FA#" . $dados['serie'] . "/" . str_pad($dados['n_doc'], 6, '0', STR_PAD_LEFT) . " - " . 
                                                      $dados['nome'] . " " . $dados['apelido'] . 
                                                      ($dados['empresa_nome'] ? " (" . $dados['empresa_nome'] . ")" : "") . 
                                                      " - " . number_format($dados['valor'], 2, ',', '.') . " MT";
                                    ?>
                                      <option value="<?php echo $dados['id'];?>" <?php echo ($fatura_selecionada == $dados['id']) ? 'selected' : ''; ?>><?php echo $fatura_text; ?></option>
                                    <?php
                                    }
                                }
                                ?>
                            </select>
                            </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" onclick="selecionarFatura()">Selecionar</button>
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
                        <?php if($fatura_selecionada): ?>
                            <div class="fatura-info">
                                <strong>Fatura Selecionada:</strong> 
                                <?php 
                                if($fatura_data) {
                                    echo "FA#" . $fatura_data['serie'] . "/" . str_pad($fatura_data['n_doc'], 6, '0', STR_PAD_LEFT) . 
                                         " - " . $fatura_data['nome'] . " " . $fatura_data['apelido'] . 
                                         ($fatura_data['empresa_nome'] ? " (" . $fatura_data['empresa_nome'] . ")" : "");
                                }
                                ?>
                                <button type="button" class="btn btn-sm btn-warning ml-2" data-toggle="modal" data-target="#modalFatura">Alterar Fatura</button>
                            </div>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalFatura">Selecionar Fatura</button>
                        <?php endif; ?>
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
                <!-- Área de Serviços -->
                <div class="col-sm-8 table-responsive" style="background:#fff;display: flex;flex-direction: row;overflow: auto;height:90vh;color: #333;justify-content: center;padding-top: 1%;">
                    <?php if(!$fatura_selecionada): ?>
                        <div style="width: 100%; display: flex; align-items: center; justify-content: center; height: 100%;">
                            <div style="text-align: center;">
                                <h3>Selecione uma fatura para continuar</h3>
                                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalFatura">Selecionar Fatura</button>
                            </div>
                        </div>
                    <?php else: ?>
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
                    <?php endif; ?>
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
                        <div class="col-sm-6" style="display:flex; flex-direction:column;justify-content: center;align-items: center;border-left: 1px solid #fff;cursor:pointer;" data-toggle="modal" data-target="#exampleModal3" <?php echo !$fatura_selecionada ? 'style="opacity: 0.5; pointer-events: none;"' : ''; ?>>
                            <h4>Criar ND</h4>
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
        var faturaSelecionada = <?php echo $fatura_selecionada ? $fatura_selecionada : 'null'; ?>;
        var empresaSelecionada = <?php echo $empresa_selecionada ? $empresa_selecionada : 'null'; ?>;
        
        function selecionarFatura() {
            var faturaId = $('#selectFatura').val();
            if(!faturaId || faturaId == "") {
                alert("Por favor, selecione uma fatura!");
                return;
            }
            window.location.href = '?fatura=' + faturaId;
        }
        
        $(function(){
            if(faturaSelecionada) {
                setInterval(nrpedido, 1000);
                popprodtable();
                showtot();
                qrcodefocus();
            }

            function nrpedido(){
              $.ajax({
                  url: 'nd_recepcao/nrpedido.php',
                  type: 'GET',
                  success: function(data) {
                    $("#nrpedido").html(data);
                  },
                  error: function() {
                    $("#nrpedido").html("Erro ao carregar número da ND!");
                  }
              });
            }
      
            function popprodtable(){
                var url = 'nd_recepcao/popprodtable.php';
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
                    url: 'nd_recepcao/qtd_decrease.php',
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
                    url: 'nd_recepcao/qtd_increase.php',
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
                    url: 'nd_recepcao/qtd_update.php',
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
                var url = 'nd_recepcao/show_tot.php';
                if(empresaSelecionada) {
                    url += '?empresa=' + empresaSelecionada;
                }
                $.ajax({
                    url: url,
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
                var codbar = $(this).val();
                alert("QR Code não disponível para serviços");
            });
            
            // Usar delegação de eventos para funcionar com DataTable
            $(document).on('click', '#example tbody tr', function() {
                if(!faturaSelecionada) {
                    alert("Por favor, selecione uma fatura primeiro!");
                    return;
                }
                var idServico = $(this).attr('data-idservico');
                if(!idServico) return;
                
                $.ajax({
                    type: "POST",
                    url: "nd_recepcao/addservtemp.php",
                    data: { 
                        idServico: idServico,
                        empresa_id: empresaSelecionada,
                        fatura_id: faturaSelecionada
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
                        } else if(response == 4){
                            alert("Erro: As tabelas necessárias não foram criadas. Execute o SQL de criação.");
                        } else {
                            console.log('Resposta inesperada:', response);
                            alert("Resposta inesperada do servidor: " + response);
                        }
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro AJAX:', status, error, xhr.responseText);
                        alert("Erro ao comunicar com o servidor: " + error);
                    }
                });
            });
            
            $(".btn_criar").click(function(){
                criarnota();
            });
            
            function criarnota() {
                if(!faturaSelecionada || faturaSelecionada == null || faturaSelecionada == 'null') {
                    alert("Por favor, selecione uma fatura primeiro!");
                    $('#modalFatura').modal('show');
                    return;
                }
                
                // Verificar se há serviços adicionados
                var totalText = $(".ttl").text().trim();
                var totalValue = parseFloat(totalText.replace(/[^\d,.-]/g, '').replace(',', '.'));
                if(!totalValue || totalValue <= 0 || isNaN(totalValue)) {
                    alert("Por favor, adicione pelo menos um serviço à nota de débito antes de criar!");
                    return;
                }
                
                var motivo = $(".motivo").val();
                if(!motivo || motivo.trim() == "") {
                    alert("Por favor, informe o motivo da nota de débito!");
                    $(".motivo").focus();
                    return;
                }
                
                if (confirm("Tem certeza de que deseja criar a nota de débito?")) {
                    console.log('Enviando dados:', {
                        fatura_id: faturaSelecionada,
                        empresa_id: empresaSelecionada,
                        motivo: motivo
                    });
                    
                    $.ajax({
                        type: "POST",
                        url: "nd_recepcao/criarnota.php",
                        data: {
                          fatura_id: faturaSelecionada,
                          empresa_id: empresaSelecionada || null,
                          motivo: motivo
                        },
                        success: function(data){
                            console.log('Resposta do servidor ND:', data);
                            if (data == 1) {
                              alert("Parâmetros insuficientes! Verifique se:\n- Uma fatura foi selecionada\n- Serviços foram adicionados à nota\n- O motivo foi informado\n- Você está logado no sistema\n\nVerifique o console do navegador (F12) para mais detalhes.");
                            }else if(data == 2){
                              alert('Erro ao criar nota de débito.');
                            }else if(data == 3){
                              alert('Não é possível criar uma nota de débito para um ano diferente do atual!');
                            }else if(data == 4){
                              alert('Erro: As tabelas necessárias não foram criadas!\n\n' +
                                    'Execute o arquivo SQL: views/recepcao/sql/create_documentos_recepcao_tables.sql\n\n' +
                                    'Ou acesse: views/recepcao/sql/verificar_e_criar_tabelas.php para verificar quais tabelas estão faltando.');
                            }else{
                              alert('Nota de débito criada com sucesso! ID: ' + data);
                              location.reload();
                            }
                        },
                        error: function(){
                            alert("Ocorreu um erro com a requisição ajax");
                        }
                    }); 
                }
            }
        
            $('.js-example-basic-single').select2();
            
            $("#cancelar").click(function() {
              if (confirm("Tem certeza de que deseja remover estes items?")){
                $.ajax({
                        url: 'nd_recepcao/removeservall.php',
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
                        url: 'nd_recepcao/removeservtemp.php',
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
            
            if(faturaSelecionada) {
                $('#example').DataTable();
            }
        });
    </script>
    </body>
</html>

