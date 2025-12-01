!<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

// Verificar se o ID do armazém foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Armazém não especificado'); window.location.href='armazens.php';</script>";
    exit;
}

$armazem_id = intval($_GET['id']);
$armazem_nome = isset($_GET['nome']) ? $_GET['nome'] : 'Armazém';

// Verificar se o armazém existe
$sqlVerificarArmazem = "SELECT id, nome FROM armazem WHERE id = $armazem_id";
$resultadoArmazem = mysqli_query($db, $sqlVerificarArmazem);

if (!$resultadoArmazem || mysqli_num_rows($resultadoArmazem) == 0) {
    echo "<script>alert('Armazém não encontrado'); window.location.href='armazens.php';</script>";
    exit;
}

$armazem = mysqli_fetch_assoc($resultadoArmazem);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        /* Ajusta a tabela para ocupar toda a largura disponível */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table.dataTable {
            width: 100% !important;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-badge.bg-success {
            background-color: #55ce63;
            color: white;
        }
        .status-badge.bg-danger {
            background-color: #f62d51;
            color: white;
        }
        .status-badge.bg-warning {
            background-color: #ffbc34;
            color: white;
        }
        .status-badge.bg-secondary {
            background-color: #757575;
            color: white;
        }
        /* Estilo para as abas */
        .tab-content {
            width: 100%;
        }
        .tab-pane {
            width: 100%;
            min-width: 100%;
        }
        /* Garantir que a tabela ocupe o espaço total */
        .tab-pane .table-responsive {
            width: 100%;
            min-width: 100%;
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
                    <div class="col-sm-8 col-5">
                        <h4 class="page-title">Gestão de Stock - <?php echo htmlspecialchars($armazem['nome']); ?></h4>
                    </div>
                    <div class="col-sm-4 col-7 text-right m-b-20">
                        <button class="btn btn-primary btn-rounded float-right ml-2" data-toggle="modal" data-target="#modalEntradaStock"><i class="fa fa-plus"></i> Nova Entrada</button>
                        <a href="armazem_movimentos.php?id=<?php echo $armazem_id; ?>" class="btn btn-info btn-rounded float-right mr-2"><i class="fa fa-history"></i> Movimentos</a>
                    </div>
                </div>

                <!-- Abas para navegar entre produtos em stock e vencidos/a vencer -->
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs nav-tabs-bottom">
                            <li class="nav-item"><a class="nav-link active" href="#tab_stock" data-toggle="tab">Produtos em Stock</a></li>
                            <li class="nav-item"><a class="nav-link" href="#tab_vencidos" data-toggle="tab">A Vencer / Vencidos</a></li>
                        </ul>
                        
                        <div class="tab-content">
                            <!-- Tab de produtos em stock -->
                            <div class="tab-pane show active" id="tab_stock">
                                <div class="table-responsive">
                                    <table id="tabelaStockArmazem" class="display nowrap table table-striped table-bordered" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produto</th>
                                                <th>Lote</th>
                                                <th>Quantidade</th>
                                                <th>Prazo</th>
                                                <th>Data Entrada</th>
                                                <th>Estado</th>
                                                <th class="text-right">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dados carregados via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab de produtos vencidos ou a vencer -->
                            <div class="tab-pane" id="tab_vencidos">
                                <div class="table-responsive">
                                    <table id="tabelaVencidos" class="display nowrap table table-striped table-bordered" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produto</th>
                                                <th>Lote</th>
                                                <th>Quantidade</th>
                                                <th>Prazo</th>
                                                <th>Dias Restantes</th>
                                                <th>Status</th>
                                                <th class="text-right">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dados carregados via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Entrada de Stock -->
    <div class="modal fade" id="modalEntradaStock" tabindex="-1" role="dialog" aria-labelledby="modalEntradaStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEntradaStockLabel">Nova Entrada de Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEntradaStock">
                    <input type="hidden" name="armazem_id" value="<?php echo $armazem_id; ?>">
                    <input type="hidden" name="action" value="adicionar_stock">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="produto_id">Produto <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="produto_id" name="produto_id" required>
                                        <option value="">Selecione um produto</option>
                                        <?php
                                        $sqlProdutos = "SELECT idproduto, nomeproduto FROM produto";
                                        $resultadoProdutos = mysqli_query($db, $sqlProdutos);
                                        
                                        if ($resultadoProdutos && mysqli_num_rows($resultadoProdutos) > 0) {
                                            while ($produto = mysqli_fetch_assoc($resultadoProdutos)) {
                                                echo '<option value="' . $produto['idproduto'] . '">' . $produto['nomeproduto'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantidade">Quantidade <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lote">Lote <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lote" name="lote" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prazo">Prazo de Validade</label>
                                    <input type="date" class="form-control" id="prazo" name="prazo">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preco_custo">Preço de Custo</label>
                                    <input type="number" step="0.01" class="form-control" id="preco_custo" name="preco_custo" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fornecedor">Fornecedor</label>
                                    <select class="form-control select2" id="fornecedor" name="fornecedor">
                                        <?php
                                        $sqlFornecedores = "SELECT id, nome FROM fornecedor ORDER BY nome";
                                        $resultadoFornecedores = mysqli_query($db, $sqlFornecedores);
                                        
                                        if ($resultadoFornecedores && mysqli_num_rows($resultadoFornecedores) > 0) {
                                            while ($fornecedor = mysqli_fetch_assoc($resultadoFornecedores)) {
                                                echo '<option value="' . $fornecedor['id'] . '">' . $fornecedor['nome'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="observacao">Observação</label>
                            <textarea class="form-control" id="observacao" name="observacao" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnSalvarEntradaStock">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Transferir Stock -->
    <div class="modal fade" id="modalTransferirStock" tabindex="-1" role="dialog" aria-labelledby="modalTransferirStockLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTransferirStockLabel">Transferir Stock para Prateleiras</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formTransferirStock">
                    <input type="hidden" name="armazem_id" value="<?php echo $armazem_id; ?>">
                    <input type="hidden" id="stock_id" name="stock_id">
                    <input type="hidden" id="produto_id_transferir" name="produto_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Produto:</label>
                            <p id="produto_nome_transferir" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Lote:</label>
                            <p id="lote_transferir" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label>Quantidade Disponível:</label>
                            <p id="quantidade_disponivel" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label for="quantidade_transferir">Quantidade a Transferir <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantidade_transferir" name="quantidade" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="observacao_transferir">Observação</label>
                            <textarea class="form-control" id="observacao_transferir" name="observacao" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Transferir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <div class="sidebar-overlay" data-reff=""></div>
    <script src="../../js/jquery-3.7.1.min.js"></script>
    <?php include 'includes/footer_plugins.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- JS -->

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            // $('.select2').select2({
            //     width: '100%'
            // });
            $('#modalEntradaStock').on('shown.bs.modal', function () {
    $(this).find('.select2').select2({
        dropdownParent: $('#modalEntradaStock'), // Importante dentro de modais!
        width: '100%'
    });
});

            
            // Evento de clique alternativo para o botão de salvar
            $('#btnSalvarEntradaStock').on('click', function(e) {
                e.preventDefault();
                console.log('Botão de salvar clicado');
                $('#formEntradaStock').submit();
            });
            
            // Inicializar DataTable para produtos em stock
            $('#tabelaStockArmazem').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "ajax/listar_armazem_stock.php",
                    "type": "POST",
                    "data": function(d) {
                        d.armazem_id = <?php echo $armazem_id; ?>;
                        d.incluir_vencidos = false;
                        console.log("Enviando armazem_id:", d.armazem_id);
                    }
                },
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json"
                },
                "columns": [
                    { "data": "id" },
                    { "data": "produto" },
                    { "data": "lote" },
                    { "data": "quantidade" },
                    { "data": "prazo" },
                    { "data": "data_entrada" },
                    { "data": "estado" },
                    { 
                        "data": "acoes",
                        "orderable": false 
                    }
                ],
                "drawCallback": function(settings) {
                    console.log('DataTable atualizada. Dados recebidos:', settings.json);
                },
                "responsive": true,
                "autoWidth": false,
                "scrollX": true
            });
            
            // Inicializar DataTable para produtos vencidos/a vencer
            $('#tabelaVencidos').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "ajax/listar_armazem_stock.php",
                    "type": "POST",
                    "data": function(d) {
                        d.armazem_id = <?php echo $armazem_id; ?>;
                        d.incluir_vencidos = true;
                    }
                },
                "language": {
                    "url": "../../js/dataTables.portuguese.json"
                },
                "columns": [
                    { "data": "id" },
                    { "data": "produto" },
                    { "data": "lote" },
                    { "data": "quantidade" },
                    { "data": "prazo" },
                    { "data": "dias_restantes" },
                    { "data": "status" },
                    { 
                        "data": "acoes",
                        "orderable": false 
                    }
                ],
                "drawCallback": function(settings) {
                    console.log('DataTable vencidos atualizada. Dados recebidos:', settings.json);
                },
                "responsive": true,
                "autoWidth": false,
                "scrollX": true
            });
            
            // Adicionar evento para redimensionar tabelas quando trocar de aba
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var targetTab = $(e.target).attr("href");
                
                // Ajustar tabelas quando a aba é ativada
                if (targetTab === '#tab_vencidos') {
                    setTimeout(function() {
                        $('#tabelaVencidos').DataTable().columns.adjust().responsive.recalc();
                        console.log("Ajustando tabela de vencidos");
                    }, 10);
                } else if (targetTab === '#tab_stock') {
                    setTimeout(function() {
                        $('#tabelaStockArmazem').DataTable().columns.adjust().responsive.recalc();
                        console.log("Ajustando tabela de stock");
                    }, 10);
                }
            });
            
            // Submeter formulário de entrada de stock
            $('#formEntradaStock').submit(function(e) {
                e.preventDefault();
                
                console.log('Enviando formulário de entrada de stock:', $(this).serialize());
                
                $.ajax({
                    url: 'ajax/armazem_entrada_stock.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('Resposta recebida:', response);
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: response.message
                            });
                            $('#modalEntradaStock').modal('hide');
                            $('#formEntradaStock')[0].reset();
                            $('#tabelaStockArmazem').DataTable().ajax.reload();
                            $('#tabelaVencidos').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro AJAX:', xhr.status, xhr.statusText);
                        console.error('Response text:', xhr.responseText);
                        
                        let mensagemErro = 'Ocorreu um erro ao processar a requisição';
                        if (xhr.responseText) {
                            try {
                                const resposta = JSON.parse(xhr.responseText);
                                if (resposta.message) {
                                    mensagemErro = resposta.message;
                                }
                            } catch (e) {
                                // Se não conseguir fazer parse do JSON, mostra o erro bruto
                                mensagemErro += ': ' + xhr.responseText;
                            }
                        } else {
                            mensagemErro += ': ' + error;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ' + xhr.status,
                            text: mensagemErro,
                            footer: 'Verifique o console para mais detalhes'
                        });
                    }
                });
            });
            
            // Abrir modal para transferir stock
            $(document).on('click', '.transferirStock', function(e) {
                e.preventDefault();
                
                var stock_id = $(this).data('id');
                var produto_id = $(this).data('produto');
                var produto_nome = $(this).data('nome');
                var lote = $(this).data('lote');
                var quantidade = $(this).data('quantidade');
                
                $('#stock_id').val(stock_id);
                $('#produto_id_transferir').val(produto_id);
                $('#produto_nome_transferir').text(produto_nome);
                $('#lote_transferir').text(lote);
                $('#quantidade_disponivel').text(quantidade);
                $('#quantidade_transferir').attr('max', quantidade);
                $('#quantidade_transferir').val('');
                $('#observacao_transferir').val('');
                
                $('#modalTransferirStock').modal('show');
            });
            
            // Submeter formulário de transferência de stock
            $('#formTransferirStock').submit(function(e) {
                e.preventDefault();
                
                var quantidade_disponivel = parseInt($('#quantidade_disponivel').text());
                var quantidade_transferir = parseInt($('#quantidade_transferir').val());
                
                if (quantidade_transferir <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'A quantidade a transferir deve ser maior que zero.'
                    });
                    return;
                }
                
                if (quantidade_transferir > quantidade_disponivel) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'A quantidade a transferir não pode ser maior que a quantidade disponível.'
                    });
                    return;
                }
                
                $.ajax({
                    url: 'ajax/armazem_transferir_stock.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: response.message
                            });
                            $('#modalTransferirStock').modal('hide');
                            $('#formTransferirStock')[0].reset();
                            $('#tabelaStockArmazem').DataTable().ajax.reload();
                            $('#tabelaVencidos').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro AJAX:', xhr, status, error);
                        
                        let mensagemErro = 'Ocorreu um erro ao processar a requisição';
                        if (xhr.responseText) {
                            try {
                                const resposta = JSON.parse(xhr.responseText);
                                if (resposta.message) {
                                    mensagemErro = resposta.message;
                                }
                            } catch (e) {
                                // Se não conseguir fazer parse do JSON, apenas exibe o erro padrão
                                mensagemErro += ': ' + error;
                            }
                        } else {
                            mensagemErro += ': ' + error;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: mensagemErro
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
