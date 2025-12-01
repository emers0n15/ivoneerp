<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
error_reporting(E_ALL);
include '../../conexao/index.php';
// Data atual no formato YYYY-MM-DD
$today = date('Y-m-d');
$primeiro_dia_mes = date('Y-m-01');
$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
$data = date("Y-m-d H:i:s");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <style>
        /* Ajusta a tabela para ocupar toda a largura disponível */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table#example {
            width: 100%;
        }
        .card-box {
            background-color: #f9f9f9;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            padding: 20px;
            margin-bottom: 20px;
        }
        .stats-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .stats-info .stat-item {
            flex: 0 0 calc(25% - 20px);
            background: #fff;
            padding: 15px;
            margin: 10px;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-item .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2196F3;
        }
        .stat-item .stat-title {
            font-size: 14px;
            color: #555;
        }
        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255,255,255,0.8);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 9999;
        }
        .filtro-section {
            background-color: #f8f9fc;
            border-left: 4px solid #4e73df;
            padding: 15px;
            margin-bottom: 20px;
        }
        /* Melhorias no Select2 */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
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
                    <div class="col-sm-6">
                        <h4 class="page-title">Relatório Completo por Produto</h4>
                    </div>
                    <div class="col-sm-6 text-right" id="divv">
                        <a id="printa4" class="btn btn-primary btn-rounded float-right"><i class="fa fa-print"></i> Imprimir PDF</a>
                        <a id="processar" class="btn btn-success btn-rounded float-right mr-2"><i class="fa fa-search"></i> Processar</a>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="filtro-section">
                            <h5><i class="fa fa-filter"></i> Filtros</h5>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Produto <span class="text-danger">*</span></label>
                                        <select class="form-control select" id="produto" required>
                                            <option value="">Selecione um produto</option>
                                            <?php 
                                                $sql = "SELECT idproduto, nomeproduto, codbar FROM produto ORDER BY nomeproduto";
                                                $rs = mysqli_query($db, $sql);
                                                while ($dados = mysqli_fetch_array($rs)) {
                                                    $codbar = !empty($dados['codbar']) ? "(" . $dados['codbar'] . ")" : "";
                                                    echo "<option value='" . $dados['idproduto'] . "'>" . $dados['nomeproduto'] . " " . $codbar . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Data Inicial <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="data_inicio" value="<?php echo $primeiro_dia_mes; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Data Final <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="data_fim" value="<?php echo $today; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Tipo de Movimento</label>
                                        <select class="form-control" id="tipo_movimento">
                                            <option value="todos">Todos</option>
                                            <option value="entrada">Apenas Entradas</option>
                                            <option value="saida">Apenas Saídas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estatísticas rápidas -->
                <div id="estatisticas" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-box">
                                <h4 id="produto_nome" class="mb-3"></h4>
                                <div class="stats-info">
                                    <div class="stat-item">
                                        <div class="stat-number" id="stock_atual">0</div>
                                        <div class="stat-title">Stock Atual</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number" id="total_entradas">0</div>
                                        <div class="stat-title">Total de Entradas</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number" id="total_saidas">0</div>
                                        <div class="stat-title">Total de Saídas</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number" id="consumo_medio">0</div>
                                        <div class="stat-title">Consumo Médio Mensal</div>
                                    </div>
                                </div>
                                
                                <!-- Informações de Lotes -->
                                <div class="mt-4" id="lotes_container">
                                    <h5>Distribuição por Lotes</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Lote</th>
                                                    <th>Validade</th>
                                                    <th>Quantidade</th>
                                                    <th>% do Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabela_lotes">
                                                <!-- Preenchido via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabela de resultados -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-box">
                            <div class="table-responsive">
                                <table id="example" class="display nowrap table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Tipo</th>
                                            <th>Documento</th>
                                            <th>Nº Documento</th>
                                            <th>Lote</th>
                                            <th>Quantidade</th>
                                            <th>Saldo</th>
                                            <th>Entidade</th>
                                            <th>Obs</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabela_movimentos">
                                        <!-- Preenchido via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="loading">
                    <i class="fa fa-spinner fa-spin fa-2x"></i> Processando...
                </div>
            </div>
        </div>
    </div>
    
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
    
    <!-- Scripts adicionais -->
    <script src="../../js/jquery-3.7.1.min.js"></script>
    <script src="../../js/jquery.dataTables.min.js"></script>
    <script src="../../js/dataTables.bootstrap4.min.js"></script>
    <script src="../../js/moment.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Ativar funcionalidade do menu lateral
            $('.submenu > a').click(function(e) {
                e.preventDefault();
                var submenu = $(this).next('ul');
                var li = $(this).parent('li');
                
                if(submenu.is(':visible')) {
                    submenu.slideUp(200);
                    li.removeClass('active');
                } else {
                    $('.submenu ul').slideUp(200);
                    $('.submenu').removeClass('active');
                    submenu.slideDown(200);
                    li.addClass('active');
                }
            });
            
            // Inicialização do DataTable com configurações otimizadas para grandes volumes
            var table = $('#example').DataTable({
                "ordering": false,
                "pageLength": 25,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json"
                },
                "columnDefs": [{
                    "targets": '_all',
                    "createdCell": function (td, cellData, rowData, row, col) {
                        $(td).css('padding', '8px 5px');
                    }
                }]
            });
            
            // Inicialização do Select2
            $('.select').select2({
                width: '100%',
                placeholder: 'Selecione um produto',
                allowClear: true
            });
            
            // Processar dados
            $('#processar').click(function() {
                var produto_id = $('#produto').val();
                var data_inicio = $('#data_inicio').val();
                var data_fim = $('#data_fim').val();
                var tipo_movimento = $('#tipo_movimento').val();
                
                if (!produto_id || !data_inicio || !data_fim) {
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    return;
                }
                
                // Mostrar indicador de carregamento
                $('.loading').show();
                $('#estatisticas').hide();
                $('#resultados').hide();
                
                // Limpar a tabela
                table.clear().draw();
                
                // Fazer requisição AJAX
                $.ajax({
                    url: 'daos/relatorio_produto_dados.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        produto_id: produto_id,
                        data_inicio: data_inicio,
                        data_fim: data_fim,
                        tipo_movimento: tipo_movimento
                    },
                    success: function(response) {
                        $('.loading').hide();
                        
                        if (response.status === 'success') {
                            // Preencher informações do produto
                            $('#produto_nome').text(response.produto.nomeproduto + ' - Cód: ' + response.produto.codbar);
                            
                            // Preencher estatísticas
                            $('#stock_atual').text(response.estatisticas.stock_atual);
                            $('#total_entradas').text(response.estatisticas.total_entradas);
                            $('#total_saidas').text(response.estatisticas.total_saidas);
                            $('#consumo_medio').text(response.estatisticas.consumo_medio);
                            
                            // Preencher tabela de lotes
                            var lotes = response.estatisticas.lotes;
                            var html_lotes = '';
                            var total_quantidade = 0;
                            
                            // Calcular total de quantidades em lotes
                            for (var i = 0; i < lotes.length; i++) {
                                total_quantidade += parseInt(lotes[i].qtd_lote);
                            }
                            
                            // Criar linhas na tabela de lotes
                            for (var i = 0; i < lotes.length; i++) {
                                var porcentagem = total_quantidade > 0 ? (lotes[i].qtd_lote / total_quantidade * 100).toFixed(2) : 0;
                                var data_formatada = lotes[i].prazo ? new Date(lotes[i].prazo).toLocaleDateString('pt-BR') : 'N/A';
                                
                                html_lotes += '<tr>' +
                                    '<td>' + (lotes[i].lote || 'Sem lote') + '</td>' +
                                    '<td>' + data_formatada + '</td>' +
                                    '<td>' + lotes[i].qtd_lote + '</td>' +
                                    '<td>' + porcentagem + '%</td>' +
                                    '</tr>';
                            }
                            
                            $('#tabela_lotes').html(html_lotes);
                            
                            // Preencher tabela de movimentos
                            for (var i = 0; i < response.movimentos.length; i++) {
                                var m = response.movimentos[i];
                                table.row.add([
                                    m.data,
                                    m.tipo,
                                    m.documento,
                                    m.numero,
                                    m.lote,
                                    m.quantidade,
                                    m.saldo,
                                    m.entidade,
                                    m.observacao
                                ]).draw(false);
                            }
                            
                            // Mostrar resultados
                            $('#estatisticas').show();
                            $('#resultados').show();
                        } else {
                            alert('Erro ao processar os dados: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.loading').hide();
                        console.error("Erro na requisição:", xhr.responseText);
                        
                        try {
                            // Tentar parsear a resposta como JSON
                            var jsonResponse = JSON.parse(xhr.responseText);
                            alert('Erro ao processar os dados: ' + jsonResponse.message);
                        } catch (e) {
                            // Se não for JSON, mostrar mensagem genérica
                            alert('Erro ao processar os dados. Verifique o console para mais detalhes.');
                        }
                    }
                });
            });
            
            // Impressão em PDF
            $('#printa4').click(function() {
                var produto_id = $('#produto').val();
                var data_inicio = $('#data_inicio').val();
                var data_fim = $('#data_fim').val();
                var tipo_movimento = $('#tipo_movimento').val();
                
                if (!produto_id || !data_inicio || !data_fim) {
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    return false;
                }
                
                window.open('relatorio_produto_pdf.php?produto_id=' + produto_id + 
                          '&data_inicio=' + data_inicio + 
                          '&data_fim=' + data_fim + 
                          '&tipo_movimento=' + tipo_movimento, '_blank');
                
                return false;
            });
        });
    </script>
</body>
</html>
