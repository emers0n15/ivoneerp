<?php 
    session_start();
    if(!isset($_SESSION['idUsuario'])){
      header("location:../../");
    }
    include_once '../../conexao/index.php';

    if (!function_exists('tabelaExiste')) {
        function tabelaExiste(mysqli $con, string $nomeTabela): bool {
            $nomeTabela = mysqli_real_escape_string($con, $nomeTabela);
            $resultado = mysqli_query($con, "SHOW TABLES LIKE '$nomeTabela'");
            return $resultado && mysqli_num_rows($resultado) > 0;
        }
    }
    error_reporting(E_ALL);
    $_SESSION['idUsuario'] = $_SESSION['idUsuario'];
    $_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
    $_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
    
    $userID = $_SESSION['idUsuario'];
    // Limpar serviços temporários do usuário ao carregar a página
    $check_table_dv = "SHOW TABLES LIKE 'dv_servicos_temp'";
    $table_dv_exists = mysqli_query($db, $check_table_dv);
    if($table_dv_exists && mysqli_num_rows($table_dv_exists) > 0) {
        $sql_limpar = "DELETE FROM dv_servicos_temp WHERE user = ?";
        $stmt_limpar = mysqli_prepare($db, $sql_limpar);
        mysqli_stmt_bind_param($stmt_limpar, "i", $userID);
        mysqli_stmt_execute($stmt_limpar);
    }
    
    // ATENÇÃO: DV agora trabalha sobre VDS (venda_dinheiro_servico), não mais sobre factura_recepcao
    $vds_selecionado = isset($_GET['vds']) ? intval($_GET['vds']) : null;
    $empresa_selecionada = null;
    $paciente_selecionado = null;
    $vds_data = null;
    
    if($vds_selecionado) {
        // Buscar informações da VDS selecionada
        $check_table = "SHOW TABLES LIKE 'venda_dinheiro_servico'";
        $table_exists = mysqli_query($db, $check_table);
        if($table_exists && mysqli_num_rows($table_exists) > 0) {
            $sql_vds = "SELECT v.*, p.nome, p.apelido, e.nome as empresa_nome 
                        FROM venda_dinheiro_servico v 
                        INNER JOIN pacientes p ON v.paciente = p.id 
                        LEFT JOIN empresas_seguros e ON v.empresa_id = e.id
                        WHERE v.id = $vds_selecionado";
            $rs_vds = mysqli_query($db, $sql_vds);
            if($rs_vds && mysqli_num_rows($rs_vds) > 0) {
                $vds_data = mysqli_fetch_array($rs_vds);
                $empresa_selecionada = $vds_data['empresa_id'] ?? null;
                $paciente_selecionado = $vds_data['paciente'] ?? null;
            }
        }
    }
?>
<html>
    <head>
      <meta charset="utf-8">
      <title>DV - Devolução (Recepção)</title>
      <link rel="shorcut icon"  href="../../img/config/iCone.png">
      <link rel="stylesheet" href="../bootstrap.css">
      <link rel="stylesheet" href="../bootstrap.min.css">
      <link rel="stylesheet" href="../all.min.css">
      <link href="../datatables.min.css" rel="stylesheet"/>
  
      <style>
          *{ font-family: system-ui; font-size: 10pt; }
          #example tr{ cursor: default; }
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
          .categoria-header {
              background: #f1f5f9;
              font-weight: 600;
              color: #1f2937;
          }
          .categoria-header td{
              padding: 10px 12px;
              border-top: 2px solid #e2e8f0;
          }
      </style>
    </head>
    <body>
              <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h3 class="modal-title fs-5" id="exampleModalLabel">Criar Devolução</h3>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                          </div>
                          <div class="modal-body">
                              <div class="col-sm-12" style="display:flex; flex-direction:column;justify-content: center;align-items: center;">
                            <h4>Valor da Devolução</h4>
                            <h1 class="ttl"></h1>
                            </div>
                            <br>
                            <div class="row">
                            <div class="col-sm-12">
                              <label class="form-label">Motivo *</label>
                              <textarea class="form-control motivo" id="motivo" rows="3" placeholder="Informe o motivo da devolução..." required></textarea>
                            </div>
                            </div>
                            <br>
                            <div class="row">
                            <div class="col-sm-12">
                              <label class="form-label">Método de Reembolso *</label>
                              <select class="form-control metodo" id="metodo" required>
                                  <option value="">Selecione...</option>
                                  <option value="Dinheiro">Dinheiro</option>
                                  <option value="M-Pesa">M-Pesa</option>
                                  <option value="POS">POS</option>
                                  <option value="Emola">Emola</option>
                                  <option value="Crédito em Conta">Crédito em Conta</option>
                              </select>
                            </div>
                            </div>
                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary btn_criar">Criar Devolução</button>
                          </div>
                        </div>
                      </div>
              </div>
              
              <div class="modal fade" id="modalFatura" tabindex="-1" aria-labelledby="modalFaturaLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h3 class="modal-title fs-5" id="modalFaturaLabel">Selecionar VDS</h3>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                      </div>
                      <div class="modal-body">
                          <div class="col-sm-12">
                            <label class="form-label">VDS *</label>
                            <select class="form-select js-example-basic-single" id="selectFatura" style="width: 100%;">
                                <option value="">Selecione uma VDS...</option>
                                <?php 
                                $check_table = "SHOW TABLES LIKE 'venda_dinheiro_servico'";
                                $table_exists = mysqli_query($db, $check_table);
                                if($table_exists && mysqli_num_rows($table_exists) > 0) {
                                    // DV agora trabalha sobre VDS. Listar apenas VDS com valor disponível > 0 (valor - devoluções anteriores)
                                    $sql = "SELECT dados.*
                                            FROM (
                                                SELECT v.*, 
                                                       p.nome, p.apelido, p.numero_processo, 
                                                       e.nome as empresa_nome,
                                                       (v.valor - COALESCE((SELECT SUM(valor) 
                                                                            FROM devolucao_recepcao dv 
                                                                            WHERE dv.factura_recepcao_id = v.id), 0)) AS valor_disponivel
                                                FROM venda_dinheiro_servico v
                                                INNER JOIN pacientes p ON v.paciente = p.id
                                                LEFT JOIN empresas_seguros e ON v.empresa_id = e.id
                                            ) dados
                                            WHERE dados.valor_disponivel > 0.01
                                            ORDER BY dados.dataa DESC 
                                            LIMIT 50";
                                    $rs = mysqli_query($db, $sql);
                                    while ($dados = mysqli_fetch_array($rs)) {
                                        $vds_text = "VDS#" . $dados['serie'] . "/" . str_pad($dados['n_doc'], 6, '0', STR_PAD_LEFT) . " - " . 
                                                      $dados['nome'] . " " . $dados['apelido'] . 
                                                      ($dados['empresa_nome'] ? " (" . $dados['empresa_nome'] . ")" : "") . 
                                                      " - " . number_format($dados['valor_disponivel'], 2, ',', '.') . " MT";
                                    ?>
                                      <option value="<?php echo $dados['id'];?>" <?php echo ($vds_selecionado == $dados['id']) ? 'selected' : ''; ?>><?php echo $vds_text; ?></option>
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
                        <?php if($vds_selecionado): ?>
                            <div class="fatura-info">
                                <strong>Fatura Selecionada:</strong> 
                                <?php 
                                if($vds_data) {
                                    echo "VDS#" . $vds_data['serie'] . "/" . str_pad($vds_data['n_doc'], 6, '0', STR_PAD_LEFT) . 
                                         " - " . $vds_data['nome'] . " " . $vds_data['apelido'] . 
                                         ($vds_data['empresa_nome'] ? " (" . $vds_data['empresa_nome'] . ")" : "");
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
                <div class="col-sm-8" style="background:#fff;display:flex;flex-direction:column;min-height:70vh;color:#333;padding:15px;">
                    <?php if(!$vds_selecionado): ?>
                        <div style="flex:1;display:flex;align-items:center;justify-content:center;">
                            <div style="text-align: center;">
                                <h3>Selecione uma VDS para continuar</h3>
                                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalFatura">Selecionar VDS</button>
                            </div>
                        </div>
                    <?php else: 
                        // Buscar serviços da VDS selecionada
                        $check_table_serv = "SHOW TABLES LIKE 'vds_servicos_fact'";
                        $table_serv_exists = mysqli_query($db, $check_table_serv);
                        if($table_serv_exists && mysqli_num_rows($table_serv_exists) > 0) {
                            $tem_dv_det = tabelaExiste($db, 'dv_servicos_fact') && tabelaExiste($db, 'devolucao_recepcao');
                            $sub_qtd_devolvida = $tem_dv_det
                                ? "(SELECT COALESCE(SUM(dvf.qtd), 0) 
                                   FROM dv_servicos_fact dvf 
                                   INNER JOIN devolucao_recepcao dv ON dv.id = dvf.devolucao_id 
                                   WHERE dv.factura_recepcao_id = $vds_selecionado AND dvf.servico = vs.servico)"
                                : "0";

                            $sql_servicos = "SELECT 
                                                vs.servico,
                                                s.nome AS servico_nome,
                                                s.categoria,
                                                SUM(vs.qtd) AS qtd_total,
                                                SUM(vs.total) AS total_original,
                                                (CASE WHEN SUM(vs.qtd) > 0 THEN SUM(vs.total)/SUM(vs.qtd) ELSE 0 END) AS preco_unit,
                                                $sub_qtd_devolvida AS qtd_devolvida,
                                                (SUM(vs.qtd) - $sub_qtd_devolvida) AS qtd_disponivel
                                            FROM vds_servicos_fact vs 
                                            INNER JOIN servicos_clinica s ON vs.servico = s.id 
                                            WHERE vs.vds_id = $vds_selecionado
                                            GROUP BY vs.servico, s.nome, s.categoria
                                            HAVING qtd_disponivel > 0
                                            ORDER BY s.categoria, s.nome";
                            $rs_servicos = mysqli_query($db, $sql_servicos);
                    ?>
                    <div class="table-responsive">
                    <table id="tabela-servicos-dv" class="table table-sm table-hover" style="width: 100%;">
                        <thead>
                            <th>#</th>
                            <th>Serviço</th>
                            <th>Categoria</th>
                            <th>Qtd disp.</th>
                            <th>Preço Unit.</th>
                            <th>Total</th>
                        </thead>
                        <tbody>
                            <?php
                            $categoria_atual = null;
                            if($rs_servicos && mysqli_num_rows($rs_servicos) > 0) {
                                while ($servico = mysqli_fetch_array($rs_servicos)) {
                                    $servico_id = $servico['servico'];
                                    $preco_unit = floatval($servico['preco_unit']);
                                    $qtd = intval($servico['qtd_disponivel']);
                                    $total_item = $preco_unit * $qtd;
                                    if($categoria_atual !== $servico['categoria']){
                                        $categoria_atual = $servico['categoria'];
                                        ?>
                                        <tr class="categoria-header">
                                            <td colspan="6"><?php echo htmlspecialchars($categoria_atual ?: 'Sem categoria'); ?></td>
                                        </tr>
                                        <?php
                                    }
                            ?>
                            <tr data-idservico="<?php echo $servico_id; ?>" data-preco="<?php echo $preco_unit; ?>" data-qtd="<?php echo $qtd; ?>">
                                <td><?php echo $servico_id; ?></td>
                                <td><?php echo htmlspecialchars($servico['servico_nome']); ?></td>
                                <td><?php echo htmlspecialchars($servico['categoria'] ?: '-'); ?></td>
                                <td><?php echo $qtd; ?></td>
                                <td><?php echo number_format($preco_unit, 2, ',', '.'); ?> MT</td>
                                <td><?php echo number_format($total_item, 2, ',', '.'); ?> MT</td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">Nenhum serviço encontrado nesta VDS.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                    <?php } else {
                        echo '<div style="width: 100%; display: flex; align-items: center; justify-content: center; height: 100%;"><div style="text-align: center;"><h3>Erro: Tabela de serviços não encontrada.</h3></div></div>';
                    }
                    endif; ?>
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
                        <div class="col-sm-6" style="display:flex; flex-direction:column;justify-content: center;align-items: center;border-left: 1px solid #fff;cursor:pointer;" data-toggle="modal" data-target="#exampleModal3" <?php echo !$vds_selecionado ? 'style="opacity: 0.5; pointer-events: none;"' : ''; ?>>
                            <h4>Criar DV</h4>
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
        // Agora DV trabalha sobre VDS. Esta variável representa o ID da VDS selecionada.
        var vdsSelecionado = <?php echo ($vds_selecionado && $vds_selecionado > 0) ? intval($vds_selecionado) : 'null'; ?>;
        var empresaSelecionada = <?php echo ($empresa_selecionada && $empresa_selecionada > 0) ? intval($empresa_selecionada) : 'null'; ?>;
        
        console.log("vdsSelecionado:", vdsSelecionado, "tipo:", typeof vdsSelecionado);
        console.log("empresaSelecionada:", empresaSelecionada);
        
        function selecionarFatura() {
            var vdsId = $('#selectFatura').val();
            if(!vdsId || vdsId == "") {
                alert("Por favor, selecione uma VDS!");
                return;
            }
            // Recarregar a página com a fatura selecionada
            // Os serviços serão carregados automaticamente quando a página carregar
            window.location.href = '?vds=' + vdsId;
        }
        
        $(function(){
            if(vdsSelecionado) {
                // Carregar automaticamente os serviços da fatura
                carregarServicosFatura();
                setInterval(nrpedido, 1000);
                popprodtable();
                showtot();
                qrcodefocus();
            }
            
            function carregarServicosFatura() {
                if(!vdsSelecionado || vdsSelecionado == null || vdsSelecionado == 'null') {
                    console.error("vdsSelecionado não está definido:", vdsSelecionado);
                    return;
                }
                
                console.log("Carregando serviços da VDS:", vdsSelecionado);
                
                $.ajax({
                    type: "POST",
                    url: "dv_recepcao/carregar_servicos_fatura.php",
                    data: { 
                        fatura_id: vdsSelecionado // aqui o ID representa a VDS
                    },
                    success: function(response){
                        console.log("Resposta do servidor:", response);
                        if(response == 3){
                            popprodtable();
                            showtot();
                        } else if(response == 4){
                            alert("Erro: As tabelas necessárias não foram criadas. Execute o SQL de criação.");
                        } else if(response == 7){
                            alert("Nenhum serviço encontrado nesta VDS.");
                        } else if(response == 10){
                            alert("VDS não encontrada.");
                        } else if(response == 31){
                            alert("Erro ao carregar serviços da fatura.");
                        } else if(response == 40){
                            alert("Parâmetros insuficientes. Verifique se a fatura foi selecionada corretamente.");
                        } else {
                            console.error("Resposta inesperada:", response);
                            alert("Resposta inesperada do servidor: " + response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro AJAX:', status, error, xhr.responseText);
                        alert("Erro ao comunicar com o servidor: " + error);
                    }
                });
            }

            function nrpedido(){
              $.ajax({
                  url: 'dv_recepcao/nrpedido.php',
                  type: 'GET',
                  success: function(data) {
                    $("#nrpedido").html(data);
                  },
                  error: function() {
                        $("#nrpedido").html("Erro ao carregar número da DV!");
                  }
              });
            }
      
            function popprodtable(){
                var url = 'dv_recepcao/popprodtable.php';
                if(empresaSelecionada) {
                    url += '?empresa=' + empresaSelecionada;
                }
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data){
                        $("#poptempart").html(data);
                        $(document).off('click', '.btnremove');
                        
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
            
            function showtot() {
                var url = 'dv_recepcao/show_tot.php';
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
            
            // Remover a possibilidade de clicar nos serviços - eles são carregados automaticamente
            // A tabela é apenas para visualização
            
            $(".btn_criar").click(function(){
                criardevolucao();
            });
            
            function criardevolucao() {
                if(!vdsSelecionado || vdsSelecionado == null || vdsSelecionado == 'null') {
                    alert("Por favor, selecione uma VDS primeiro!");
                    $('#modalFatura').modal('show');
                    return;
                }
                
                var motivo = $(".motivo").val();
                if(!motivo || motivo.trim() == "") {
                    alert("Por favor, informe o motivo da devolução!");
                    $(".motivo").focus();
                    return;
                }
                
                var metodo = $(".metodo").val();
                if(!metodo || metodo == "") {
                    alert("Por favor, selecione o método de reembolso!");
                    $(".metodo").focus();
                    return;
                }
                
                if (confirm("Tem certeza de que deseja criar a devolução?")) {
                    $.ajax({
                        type: "POST",
                        url: "dv_recepcao/criardevolucao.php",
                        data: {
                          fatura_id: vdsSelecionado, // aqui o ID representa a VDS
                          empresa_id: empresaSelecionada,
                          motivo: motivo,
                          metodo: metodo
                        },
                        success: function(data){
                            if (data == 1) {
                              alert("Parâmetros insuficientes! Verifique se:\n- Uma fatura foi selecionada\n- O motivo foi informado\n- O método de reembolso foi selecionado\n- Você está logado no sistema");
                            }else if(data == 2){
                              alert('Erro ao criar devolução.');
                            }else if(data.toString().startsWith('4')){
                              var mensagem = 'Erro: As tabelas necessárias não foram criadas!\n\n';
                              if(data.toString().includes('|')) {
                                  var tabelas_faltando = data.toString().split('|')[1];
                                  mensagem += 'Tabelas faltando: ' + tabelas_faltando + '\n\n';
                              }
                              mensagem += 'Opções:\n' +
                                          '1. Execute o arquivo SQL: views/recepcao/sql/create_documentos_recepcao_tables.sql\n' +
                                          '2. Ou acesse: views/recepcao/sql/criar_tabelas_dv.php para criar automaticamente\n' +
                                          '3. Ou acesse: views/recepcao/sql/verificar_e_criar_tabelas.php para verificar todas as tabelas';
                              alert(mensagem);
                            }else if(data.toString().startsWith('5')){
                              var valor_disponivel = data.toString().split('|')[1] || '0,00';
                              alert('Erro: O valor da devolução excede o valor disponível na fatura!\n\n' +
                                    'Valor disponível: ' + valor_disponivel + ' MT\n\n' +
                                    'Por favor, ajuste os serviços selecionados.');
                            }else if(data.toString().startsWith('6')){
                              var valor_disponivel = data.toString().split('|')[1] || '0,00';
                              alert('Erro: Não há valor disponível para devolução nesta fatura!\n\n' +
                                    'Valor disponível: ' + valor_disponivel + ' MT\n\n' +
                                    'A fatura pode já ter sido totalmente paga ou devolvida.');
                            }else{
                              alert('Devolução criada com sucesso! ID: ' + data);
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
                        url: 'dv_recepcao/removeservall.php',
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
                        url: 'dv_recepcao/removeservtemp.php',
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
            
            function inicializarTabelaServicos() {
                var $tabela = $('#tabela-servicos-dv');
                if(!$tabela.length) {
                    return;
                }

                var possuiServicos = $tabela.find('tbody tr[data-idservico]').length > 0;
                if(!possuiServicos || $tabela.data('datatable-initialized')) {
                    return;
                }

                $tabela.DataTable({
                    pageLength: 15,
                    lengthChange: false,
                    searching: true,
                    ordering: true,
                    info: true,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json",
                        paginate: {
                            previous: "Anterior",
                            next: "Próximo"
                        },
                        emptyTable: "Nenhum serviço disponível",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ serviços",
                        infoEmpty: "Mostrando 0 a 0 de 0 serviços",
                        search: "Pesquisar:",
                        zeroRecords: "Nenhum serviço encontrado"
                    }
                });

                $tabela.data('datatable-initialized', true);
            }

            inicializarTabelaServicos();
            inicializarTabelaServicos();
        });
    </script>
    </body>
</html>

