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
    
    $userID = $_SESSION['idUsuario'];
    $sql_limpar = "DELETE FROM ct_servicos_temp WHERE user = ?";
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
      <title>CT - Cotação (Recepção)</title>
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
      </style>
    </head>
    <body>
              <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">Detalhes da Cotação</h3>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                          </div>
                          <div class="modal-body">
                              <div class="col-sm-12" style="display:flex; flex-direction:column;justify-content: center;align-items: center;">
                            <h4>Valor Cotado</h4>
                            <h1 class="ttl"></h1>
                            </div>
                            <br>
                            <div class="row">
                            <div class="col-sm-12">
                              <label class="form-label">Paciente</label><br>
                            <select class="form-select js-example-basic-single1 paciente" aria-label="Default select example" id="selectPaciente" style="width: 100%;">
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
                            </div><br>
                            <div class="row">
                            <div class="col-sm-12">
                              <label class="form-label">Prazo de Validade</label>
                              <input type="date" class="form-control prazo" id="prazo">
                            </div>
                        </div>
                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary btn_criar">Criar Cotação</button>
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
                <div class="col-sm-8 segunda1 table-responsive" style="background:#fff;display: flex;flex-direction: row;overflow: auto;height:90vh;color: #333;justify-content: center;padding-top: 1%;">
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
                            <h4>Criar</h4>
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
                  url: 'ct_recepcao/nrpedido.php',
                  type: 'GET',
                  success: function(data) {
                    $("#nrpedido").html(data);
                  },
                  error: function() {
                    $("#nrpedido").html("Ocorreu um erro ao popular o numero da cotação!");
                  }
              });
            }
      
            popprodtable();
            showtot();
            qrcodefocus();

            function popprodtable(){
                var url = 'ct_recepcao/popprodtable.php';
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
                    url: 'ct_recepcao/qtd_decrease.php',
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
                    url: 'ct_recepcao/qtd_increase.php',
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
                    url: 'ct_recepcao/qtd_update.php',
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
                var url = 'ct_recepcao/show_tot.php';
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
                alert("QR Code não disponível para serviços");
            });
            
            // Função para lidar com clique na linha da tabela (igual ao VDS)
            function handleClick() {
                var idServico = this.getAttribute('data-idservico');
                if(!idServico) {
                    console.log('ID do serviço não encontrado');
                    return;
                }
                
                console.log('Serviço clicado:', idServico, 'Empresa:', empresaSelecionada);
                
                $.ajax({
                    type: "POST",
                    url: "ct_recepcao/addservtemp.php",
                    data: { 
                        idServico: idServico,
                        empresa_id: empresaSelecionada
                    },
                    success: function(response){
                        console.log('Resposta do servidor:', response);
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
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro AJAX:', status, error, xhr.responseText);
                        alert("Erro ao comunicar com o servidor: " + error);
                    }
                });
            }
            
            // Anexar evento de clique ANTES de inicializar o DataTable (igual ao VDS)
            $("#example tbody tr").click(handleClick);
            
            // Também usar delegação de eventos como fallback (funciona mesmo quando DataTable redesenha)
            $(document).on('click', '#example tbody tr', function() {
                handleClick.call(this);
            });
            
            $(".btn_criar").click(function(){
                criarpedido();
            });
            
            function criarpedido() {
                if (confirm("Tem certeza de que deseja criar a cotação?")) {
                    // Usar select2 para obter o valor corretamente
                    var paciente = $('#selectPaciente').val();
                    var pacienteText = $('#selectPaciente option:selected').text();
                    if(!paciente || paciente == "" || paciente == null || paciente == undefined || paciente == "0"){
                        alert("Por favor, selecione um paciente!");
                        $('#selectPaciente').focus();
                        setTimeout(function() {
                            $('#selectPaciente').select2('open');
                        }, 100);
                        return false;
                    }
                    
                    var prazo = $(".prazo").val();
                    if(!prazo || prazo == ""){
                        alert("Por favor, informe o prazo de validade!");
                        $(".prazo").focus();
                        return false;
                    }
                    
                    $.ajax({
                            type: "POST",
                            url: "ct_recepcao/criarpedido.php",
                            data: {
                              paciente: paciente,
                              empresa_id: empresaSelecionada || null,
                              prazo: prazo
                            },
                            success: function(data){
                                if (data == 1) {
                                  alert("Informe o paciente por favor!");
                                }else if(data == 2){
                                  alert('Erro ao criar cotação.');
                                }else if(data == 3){
                                  alert('Não é possível criar uma cotação para um ano diferente do atual!');
                                  window.location.href = 'dashboard.php';
                                }else if(data == 4){
                                  alert('Erro: As tabelas necessárias não foram criadas.');
                                }else{
                                  alert('Cotação criada com sucesso! ID: ' + data);
                                  location.reload();
                                }
                            },
                            error: function(){
                                alert("Ocorreu um erro com a requisição ajax");
                            }
                        }); 
                    }
                }
            }
        
            $('.js-example-basic-single').select2();
            
            // Inicializar select2 do paciente quando o modal abrir
            $('#exampleModal3').on('shown.bs.modal', function () {
                // Sempre reinicializar o select2 do paciente
                if ($('.js-example-basic-single1').hasClass('select2-hidden-accessible')) {
                    $('.js-example-basic-single1').select2('destroy');
                }
                $('.js-example-basic-single1').select2({
                    dropdownParent: $('#exampleModal3 .modal-body'),
                    width: '100%'
                });
                // Limpar seleção ao abrir o modal
                $('#selectPaciente').val('').trigger('change');
            });
            
            function carregarPacientes(empresaId) {
                var url = 'ct_recepcao/buscar_pacientes.php';
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
                        url: 'ct_recepcao/removeservall.php',
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
                        url: 'ct_recepcao/removeservtemp.php',
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
            
            // Inicializar DataTable (o evento já foi anexado acima)
            $('#example').DataTable();
        });
    </script>
    </body>
</html>

