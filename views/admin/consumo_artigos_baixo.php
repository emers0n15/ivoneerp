<?php
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
error_reporting(E_ALL);
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
$data = date("Y-m-d H:m:s");

// Verificar se temos informações de produtos com estoque baixo na sessão
$total_produtos_baixos = isset($_SESSION['total_produtos_consumo_baixo']) ? $_SESSION['total_produtos_consumo_baixo'] : 0;
$total_produtos_criticos = isset($_SESSION['total_produtos_criticos']) ? $_SESSION['total_produtos_criticos'] : 0;

// Se não temos dados na sessão ou se os dados são antigos, forçar atualização
$atualizar_automaticamente = false;
if (!isset($_SESSION['timestamp_produtos_consumo_baixo']) || 
    (time() - $_SESSION['timestamp_produtos_consumo_baixo']) > 100) { // 5 minutos
    $atualizar_automaticamente = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include 'includes/head.php'; ?>
    <style type="text/css">
        .table-responsive {
            overflow-x: auto;
        }

        table.dataTable {
            width: 100% !important; /* Garante que a tabela ocupe toda a largura disponível */
        }
        
        .badge-danger {
            background-color: #dc3545;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-success {
            background-color: #28a745;
        }
        
        .dashboard-stats {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        }
        
        .stat-box {
            text-align: center;
            padding: 10px;
            border-radius: 4px;
        }
        
        .stat-box h5 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .stat-box p {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        
        .critical {
            background-color: #ffebee;
            border-left: 4px solid #dc3545;
        }
        
        .warning {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
        }
        
        .info-button {
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
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
                    <div class="col-sm-8 col-6">
                        <h4 class="page-title">Artigos com Estoque Abaixo do Consumo Médio</h4>
                        <?php if ($atualizar_automaticamente): ?>
                        <span id="auto-update-indicator" class="badge badge-info">
                            <i class="fas fa-sync-alt fa-spin"></i> Atualizando dados automaticamente...
                        </span>
                        <?php endif; ?>
                    </div>
                    <!-- <div class="col-sm-4 col-6 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" id="btnAtualizar" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Atualizar Dados</button>
                            <button type="button" id="btnExportar" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar Excel</button>
                        </div>
                    </div> -->
                </div>
                
                <!-- Dashboard Stats -->
                <div class="row dashboard-stats">
                    <div class="col-md-4">
                        <div class="stat-box critical">
                            <h5>Situação Crítica <i class="fa fa-question-circle info-button" data-toggle="tooltip" title="Produtos com estoque menor ou igual a 30% do consumo médio mensal"></i></h5>
                            <p id="criticoCount"><?php echo $total_produtos_criticos; ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-box warning">
                            <h5>Situação de Atenção <i class="fa fa-question-circle info-button" data-toggle="tooltip" title="Produtos com estoque entre 31% e 70% do consumo médio mensal"></i></h5>
                            <p id="atencaoCount">0</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-box">
                            <h5>Total <i class="fa fa-question-circle info-button" data-toggle="tooltip" title="Total de produtos com estoque abaixo do consumo médio mensal"></i></h5>
                            <p id="totalCount"><?php echo $total_produtos_baixos; ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros adicionais -->
                <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Filtrar por Status</label>
                                            <select class="form-control" id="filtroStatus">
                                                <option value="">Todos</option>
                                                <option value="Crítico">Crítico</option>
                                                <option value="Baixo">Baixo</option>
                                                <option value="Adequado">Adequado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Período de Análise</label>
                                            <select class="form-control" id="filtroPeriodo">
                                                <option value="3">Últimos 3 meses</option>
                                                <option value="6">Últimos 6 meses</option>
                                                <option value="12">Último ano</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                
                <div class="row">
                    <div class="col-md-12">
                        <!-- Painel de diagnóstico - Apenas em ambiente de desenvolvimento -->
                        <!-- <div class="card mb-3" style="background-color: #f8f9fc; border-left: 4px solid #4e73df;">
                            <div class="card-body">
                                <h5 class="card-title">Informações de Diagnóstico</h5>
                                <pre id="diagnostico-info" style="background-color: #eaecf4; padding: 10px; max-height: 300px; overflow: auto;">Carregando informações de diagnóstico...</pre>
                                <button type="button" class="btn btn-sm btn-info" id="btnTestarConsulta">Testar Consulta Direta</button>
                                <button type="button" class="btn btn-sm btn-warning" id="btnLimparCache">Limpar Cache da Sessão</button>
                            </div>
                        </div> -->
                        
                        <div class="table-responsive">
                            <table id="example" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Código de Barras</th>
                                        <th>Descrição</th>
                                        <th>Stock Atual</th>
                                        <th>Consumo Médio Mensal</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Será preenchido via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
    <!-- Scripts principais -->
    <script src="../../js/jquery-3.7.1.min.js"></script>
    <script src="assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../js/jquery.dataTables.min.js"></script>
    <script src="../../js/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        console.log("Documento carregado, inicializando componentes...");
        
        // Verificar se tooltip existe antes de chamar
        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        } else {
            console.warn("Plugin tooltip não disponível");
        }
        
        // Definir tabela como variável global para acessá-la em outras funções
        window.tabela = null;
        
        // Verificar se precisamos atualizar os dados automaticamente
        var atualizarAutomaticamente = <?php echo $atualizar_automaticamente ? 'true' : 'false'; ?>;
        console.log("Atualização automática:", atualizarAutomaticamente);
        
        try {
            // Inicializar DataTable
            window.tabela = $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "daos/listar_artigos_consumo_baixo.php", // Voltar para o endpoint server-side
                    "type": "GET",
                    "dataSrc": function(json) {
                        console.log("Dados recebidos do servidor:", json);
                        
                        // Se recebemos um log do servidor, mostrar no console
                        if (json.log) {
                            console.log("Log do servidor:", json.log);
                        }
                        
                        // Resetar contadores
                        var criticoCount = 0;
                        var atencaoCount = 0;
                        
                        // Contar itens por status
                        if (json.data && Array.isArray(json.data)) {
                            json.data.forEach(function(item) {
                                if (item.status && item.status.indexOf('badge-danger') !== -1) {
                                    criticoCount++;
                                } else if (item.status && item.status.indexOf('badge-warning') !== -1) {
                                    atencaoCount++;
                                }
                            });
                        }
                        
                        // Atualizar contadores
                        var totalCount = json.recordsFiltered || 0;
                        
                        $('#criticoCount').text(criticoCount);
                        $('#atencaoCount').text(atencaoCount);
                        $('#totalCount').text(totalCount);
                        
                        // Armazenar no localStorage para uso em notificações
                        localStorage.setItem('quantidadeArtigosBaixo', totalCount);
                        localStorage.setItem('artigosCriticos', criticoCount);
                        
                        console.log("Atualizado contadores: críticos=" + criticoCount + ", atenção=" + atencaoCount + ", total=" + totalCount);
                        
                        return json.data || [];
                    },
                    "error": function(xhr, error, thrown) {
                        console.error("Erro ao carregar dados:", error, thrown);
                        alert("Erro ao carregar dados da tabela. Verifique o console para mais detalhes.");
                    }
                },
                "columns": [
                    { "data": "idproduto" },
                    { "data": "codigobarra" },
                    { "data": "nomeproduto" },
                    { "data": "stock_atual" },
                    { "data": "consumo_medio_mensal" },
                    { "data": "status" },
                    { "data": "acoes" }
                ],
                "scrollX": true,
                "bDestroy": true,
                "bRetrieve": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "Não foram encontrados registros",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "Não há registros disponíveis",
                    "infoFiltered": "(filtrado de _MAX_ registros no total)",
                    "search": "Pesquisar:",
                    "paginate": {
                        "first": "Primeiro",
                        "last": "Último",
                        "next": "Próximo",
                        "previous": "Anterior"
                    },
                    "processing": "Processando..."
                }
            });
            
            console.log("DataTable inicializado com sucesso");
        } catch (e) {
            console.error("Erro ao inicializar DataTable:", e);
        }
        
        // Filtro por status
        $('#filtroStatus').on('change', function() {
            var filtro = $(this).val();
            console.log("Filtro por status alterado para:", filtro);
            
            try {
                // Limpar filtros existentes
                $.fn.dataTable.ext.search.pop();
                
                // Aplicar filtro personalizado
                if (filtro) {
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            if (!filtro) {
                                return true; // Se não tem filtro, mostrar tudo
                            }
                            
                            var status = data[5]; // Índice da coluna status
                            return status.indexOf(filtro) !== -1;
                        }
                    );
                }
                
                // Redesenhar tabela com o filtro
                if (window.tabela) {
                    window.tabela.draw();
                }
            } catch (e) {
                console.error("Erro ao aplicar filtro de status:", e);
            }
        });
        
        // Atualizar período de análise
        $('#filtroPeriodo').on('change', function() {
            var periodo = $(this).val();
            console.log("Período de análise alterado para:", periodo);
            
            try {
                // Recarregar a tabela com o novo período
                if (window.tabela) {
                    window.tabela.ajax.url('daos/listar_artigos_consumo_baixo.php?periodo=' + periodo).load();
                }
            } catch (e) {
                console.error("Erro ao alterar período de análise:", e);
            }
        });
        
        // Botão para atualizar dados
        $('#btnAtualizar').on('click', function() {
            console.log("Botão de atualização clicado");
            atualizarDados();
        });
        
        // Botão para exportar
        $('#btnExportar').on('click', function() {
            console.log("Botão de exportação clicado");
            window.location.href = 'daos/exportar_artigos_baixo.php';
        });
        
        // Se for necessário atualizar automaticamente, fazer isso após inicializar a tabela
        if (atualizarAutomaticamente) {
            console.log("Atualizando dados automaticamente ao carregar a página");
            setTimeout(function() {
                atualizarDados();
            }, 500);
        }
        
        // Funções de diagnóstico
        function atualizarInfoDiagnostico() {
            var info = "--- Informações de Sessão ---\n";
            
            // Via AJAX para ter os valores do servidor
            $.ajax({
                url: 'daos/diagnostico.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.session) {
                        info += "Produtos com estoque baixo na sessão: " + (data.session.total_produtos_consumo_baixo || "Não definido") + "\n";
                        info += "Produtos críticos na sessão: " + (data.session.total_produtos_criticos || "Não definido") + "\n";
                        info += "Timestamp da última atualização: " + (data.session.timestamp ? new Date(data.session.timestamp * 1000).toLocaleString() : "Não definido") + "\n";
                        info += "Dados em cache: " + (data.session.tem_cache ? "Sim" : "Não") + "\n\n";
                    } else {
                        info += "Não foi possível recuperar dados de sessão\n\n";
                    }
                    
                    info += "--- Informações do Banco de Dados ---\n";
                    if (data.db) {
                        info += "Total de produtos ativos: " + data.db.total_produtos + "\n";
                        info += "Produtos com stock: " + data.db.produtos_com_stock + "\n";
                        info += "Produtos com consumo médio: " + data.db.produtos_com_consumo + "\n";
                        info += "Produtos com stock < consumo médio: " + data.db.produtos_baixo_consumo + "\n\n";
                    } else {
                        info += "Não foi possível recuperar dados do banco\n\n";
                    }
                    
                    info += "--- Últimas Consultas SQL ---\n";
                    if (data.queries && data.queries.length) {
                        data.queries.forEach(function(query, index) {
                            info += (index + 1) + ") " + query + "\n";
                        });
                    } else {
                        info += "Nenhuma consulta registrada\n";
                    }
                    
                    $('#diagnostico-info').text(info);
                },
                error: function() {
                    $('#diagnostico-info').text("Erro ao carregar informações de diagnóstico");
                }
            });
        }
        
        // Inicializar informações de diagnóstico
        atualizarInfoDiagnostico();
        
        // Botão para testar consulta direta
        $('#btnTestarConsulta').on('click', function() {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testando...');
            
            $.ajax({
                url: 'daos/testar_consulta_direta.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    alert('Consulta direta realizada! Encontrados ' + data.total + ' produtos com estoque baixo. Verifique o console para mais detalhes.');
                    console.log('Resultado da consulta direta:', data);
                    atualizarInfoDiagnostico();
                },
                error: function(xhr, status, error) {
                    alert('Erro ao realizar consulta direta: ' + error);
                    console.error('Erro na consulta direta:', xhr.responseText);
                },
                complete: function() {
                    $('#btnTestarConsulta').prop('disabled', false).html('Testar Consulta Direta');
                }
            });
        });
        
        // Botão para limpar cache da sessão
        $('#btnLimparCache').on('click', function() {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Limpando...');
            
            $.ajax({
                url: 'daos/limpar_cache_sessao.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    alert('Cache da sessão limpo com sucesso!');
                    atualizarInfoDiagnostico();
                    // Recarregar a página para reinicializar tudo
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function() {
                    alert('Erro ao limpar cache da sessão');
                },
                complete: function() {
                    $('#btnLimparCache').prop('disabled', false).html('Limpar Cache da Sessão');
                }
            });
        });
    });
    
    function atualizarDados() {
        console.log("Iniciando atualização de dados");
        // Mostrar o indicador de atualização
        $('#btnAtualizar').find('i').addClass('fa-spin');
        
        $.ajax({
            url: 'daos/atualizarStockConsumoMedio.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log("Dados atualizados com sucesso:", response);
                
                // Recarregar a tabela
                if (window.tabela) {
                    window.tabela.ajax.reload(null, false);
                    console.log("Tabela recarregada após atualização");
                } else {
                    console.log("Tabela não inicializada ainda, recarregando a página...");
                    location.reload();
                }
                
                // Atualizar contadores
                if (response.quantidade) {
                    $('#totalCount').text(response.quantidade);
                }
                
                // Esconder o indicador de atualização automática
                $('#auto-update-indicator').fadeOut();
            },
            error: function(xhr, status, error) {
                console.error("Erro ao atualizar os dados:", xhr, status, error);
                alert("Erro ao atualizar dados. Verifique o console para mais detalhes.");
            },
            complete: function() {
                // Garantir que o spinner seja removido
                $('#btnAtualizar').find('i').removeClass('fa-spin');
                console.log("Atualização de dados concluída");
            }
        });
    }
</script>
</body>
</html>
