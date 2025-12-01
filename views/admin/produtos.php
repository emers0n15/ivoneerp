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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table#example {
            width: 100%;
        }
        .btn-actions {
            display: flex;
            gap: 5px;
            justify-content: flex-end;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        /* Estilo para o botão de correção de duplicados */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        /* Paleta suave para farmácia no modal de duplicados */
        .modal-duplicates .modal-header {
            background-color: #2e7d32; /* verde farmácia */
            color: hsla(0, 0%, 100%, 1.00);
        }
        .modal-duplicates .table thead {
            background-color: #e8f5e9; /* verde claro */
        }
        .modal-duplicates .badge-dup-count {
            background-color: #ffecb3; /* amarelo suave */
            color: #000000ff;
        }
        .modal-duplicates .badge-stock-total {
            background-color: #ffffffffff; /* teal claro */
            color: #004d40;
        }
        .modal-duplicates .btn-remove-duplicates {
            border-color: #ef9a9a; /* vermelho claro */
            color: #b71c1c;
            background-color: #fff;
        }
        .modal-duplicates .btn-remove-duplicates:hover {
            background-color: #ffebee; /* vermelho muito claro */
            color: #b71c1c;
        }
        .modal-duplicates .btn-run-fix-all {
            background-color: #00796b; /* teal médio */
            border-color: #00796b;
            color: #ffffff;
        }
        .modal-duplicates .btn-run-fix-all:hover {
            background-color: rgba(249, 253, 253, 1);
        }
        /* Paleta e contraste para badges de stock na lista de artigos */
        .badge-stock {
            padding: 0.35rem 0.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
        }
        .badge-stock.bg-success {
            background-color: #2e7d32 !important; /* verde farmácia mais escuro */
            color: #ffffff !important;
        }
        .badge-stock.bg-warning {
            background-color: #fff3cd !important; /* amarelo suave */
            color: #6b4f00 !important; /* texto escuro para contraste */
        }
        .badge-stock.bg-danger {
            background-color: #c62828 !important; /* vermelho mais escuro para contraste */
            color: #ffffff !important;
        }
        .badge-stock.bg-secondary {
            background-color: #e0f2f1 !important; /* teal muito claro para zero/nulo */
            color: #004d40 !important;
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
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Artigos</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="novo_artigo.php" class="btn btn-primary btn-rounded float-right">
                            <i class="fa fa-plus"></i> Novo Artigo
                        </a>
                        <!-- BOTÃO PARA CORRIGIR DUPLICADOS -->
                        <button type="button" class="btn btn-warning btn-rounded float-right m-r-10" data-bs-toggle="modal" data-bs-target="#modalDuplicados">
                            <i class="fa fa-exclamation-triangle"></i> Corrigir Duplicados
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="example" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Artigo</th>
                                        <th>Preço Compra</th>
                                        <th>Preço Venda</th>
                                        <th>%IVA</th>
                                        <th>Stock</th>
                                        <th class="text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Os dados serão preenchidos via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CORRIGIR DUPLICADOS -->
    <div class="modal fade" id="modalDuplicados" tabindex="-1" aria-labelledby="modalDuplicadosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-duplicates">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDuplicadosLabel">
                        <i class="fa fa-exclamation-triangle"></i> Corrigir Artigos Duplicados
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Informação:</strong> Esta ferramenta identifica artigos com nomes duplicados e permite 
                        consolidar o stock ou remover os duplicados.
                    </div>
                    
                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-primary" id="btnProcurarDuplicados">
                            <i class="fa fa-search"></i> Procurar Duplicados
                        </button>
                        <button type="button" class="btn btn-run-fix-all" id="btnCorrigirTodos" style="display: none;">
                            <i class="fa fa-broom"></i> Remover Todos os Duplicados
                        </button>
                    </div>
                    
                    <div id="resultadoDuplicados" class="mt-3" style="display: none;">
                        <h6>Artigos Duplicados Encontrados:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Artigo</th>
                                        <th>Quantidade de Duplicados</th>
                                        <th>Stock Total</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelaDuplicados">
                                    <!-- Os dados serão preenchidos via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div id="loadingDuplicados" class="text-center mt-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Processando...</span>
                        </div>
                        <p>Processando, aguarde...</p>
                    </div>
                    
                    <div id="mensagemResultado" class="mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE CONFIRMAÇÃO PARA APAGAR -->
    <div class="modal fade" id="modalConfirmacao" tabindex="-1" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="modalConfirmacaoLabel">
                        <i class="fa fa-exclamation-triangle"></i> Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja apagar este artigo?</p>
                    <p><strong id="nomeArtigoApagar"></strong></p>
                    <div class="alert alert-warning">
                        <small><i class="fa fa-warning"></i> Atenção: Esta ação não pode ser desfeita!</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarApagar">Sim, Apagar Artigo</button>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
</body>

<link href="../datatables.min.css" rel="stylesheet"/>
<script src="../datatables.min.js"></script>
<script type="text/javascript" src="../js/data-table-act.js"></script>
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function() {
    // Inicializar DataTable
    var table = $('#example').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "popartg.php",
            "type": "GET",
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error("Erro DataTables:", error, thrown);
                alert("Erro ao carregar dados. Verifique o console.");
            }
        },
        "columns": [
            { 
                "data": "idproduto",
                "className": "text-center"
            },
            { 
                "data": "artigo",
                "className": "text-left"
            },
            { 
                "data": "preco_compra",
                "className": "text-right",
                "render": function(data, type, row) {
                    return data ? 'MT ' + parseFloat(data).toFixed(2) : 'MT 0.00';
                }
            },
            { 
                "data": "preco",
                "className": "text-right",
                "render": function(data, type, row) {
                    return data ? 'MT ' + parseFloat(data).toFixed(2) : 'MT 0.00';
                }
            },
            { 
                "data": "iva",
                "className": "text-center",
                "render": function(data, type, row) {
                    return data ? parseFloat(data).toFixed(2) + '%' : '0%';
                }
            },
            { 
                "data": "stock_total",
                "className": "text-center",
                "render": function(data, type, row) {
                    var stock = parseInt(data) || 0;
                    var badgeClass = 'bg-secondary';
                    
                    if (stock > 10) badgeClass = 'bg-success';
                    else if (stock > 0) badgeClass = 'bg-warning';
                    else if (stock === 0) badgeClass = 'bg-danger';
                    
                    return '<span class="badge badge-stock ' + badgeClass + '">' + stock + '</span>';
                }
            },
            {
                "data": null,
                "className": "text-center",
                "render": function(data, type, row) {
                    return `
                        <div class="btn-actions">
                            <a href="editar_artigo.php?id=${data.idproduto}" class="btn btn-primary btn-sm" title="Editar">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <button class="btn btn-danger btn-sm btn-apagar" 
                                    data-id="${data.idproduto}" 
                                    data-nome="${data.artigo}"
                                    title="Apagar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                },
                "orderable": false
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json"
        },
        "pageLength": 25
    });

    // Variável para armazenar o ID do artigo a ser apagado
    var artigoParaApagar = null;

    // Evento para o botão de apagar
    $('#example').on('click', '.btn-apagar', function() {
        var id = $(this).data('id');
        var nome = $(this).data('nome');
        
        artigoParaApagar = id;
        $('#nomeArtigoApagar').text(nome + ' (ID: ' + id + ')');
        $('#modalConfirmacao').modal('show');
    });

    // Confirmar apagar artigo
    $('#btnConfirmarApagar').click(function() {
        if (!artigoParaApagar) return;
        
        $('#modalConfirmacao').modal('hide');
        
        // Mostrar loading
        $('body').append('<div id="loadingGlobal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; display:flex; justify-content:center; align-items:center; color:white; font-size:18px;"><div class="spinner-border"></div> Apagando artigo...</div>');
        
        $.ajax({
            url: 'apagar_artigo.php',
            type: 'POST',
            data: { 
                id: artigoParaApagar,
                acao: 'apagar_artigo'
            },
            dataType: 'json',
            success: function(response) {
                $('#loadingGlobal').remove();
                
                if (response.success) {
                    table.ajax.reload();
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', 'Erro: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                $('#loadingGlobal').remove();
                console.error("Erro AJAX:", error);
                showAlert('danger', 'Erro de comunicação: ' + error);
            }
        });
    });

    // FUNÇÕES PARA CORRIGIR DUPLICADOS
    $('#btnProcurarDuplicados').click(function() {
        $('#loadingDuplicados').show();
        $('#resultadoDuplicados').hide();
        $('#btnCorrigirTodos').hide();
        $('#mensagemResultado').hide();
        
        $.ajax({
            url: 'corrigir_duplicados.php',
            type: 'POST',
            data: { acao: 'listar_duplicados' },
            dataType: 'json',
            success: function(response) {
                $('#loadingDuplicados').hide();
                
                if (response.success) {
                    if (response.data.length > 0) {
                        preencherTabelaDuplicados(response.data);
                        $('#resultadoDuplicados').show();
                        $('#btnCorrigirTodos').show();
                    } else {
                        $('#mensagemResultado').html('<div class="alert alert-success">Nenhum duplicado encontrado!</div>').show();
                    }
                } else {
                    $('#mensagemResultado').html('<div class="alert alert-danger">Erro: ' + response.message + '</div>').show();
                }
            },
            error: function(xhr, status, error) {
                $('#loadingDuplicados').hide();
                $('#mensagemResultado').html('<div class="alert alert-danger">Erro: ' + error + '</div>').show();
            }
        });
    });
    
    // Corrigir todos automaticamente
    $('#btnCorrigirTodos').click(function() {
        if (!confirm('ATENÇÃO: Esta ação irá remover TODOS os artigos duplicados.\n\nO sistema manterá o primeiro registro de cada artigo.\n\nDeseja continuar?')) {
            return;
        }
        
        $('#loadingDuplicados').show();
        $(this).prop('disabled', true);
        
        $.ajax({
            url: 'corrigir_duplicados.php',
            type: 'POST',
            data: { acao: 'corrigir_todos' },
            dataType: 'json',
            success: function(response) {
                $('#loadingDuplicados').hide();
                $('#btnCorrigirTodos').prop('disabled', false);
                
                if (response.success) {
                    $('#mensagemResultado').html(
                        '<div class="alert alert-success">' +
                        '<h5><i class="fa fa-check-circle"></i> Correção Concluída!</h5>' +
                        '<p>' + response.message + '</p>' +
                        '</div>'
                    ).show();
                    
                    table.ajax.reload();
                    $('#btnProcurarDuplicados').click();
                } else {
                    $('#mensagemResultado').html('<div class="alert alert-danger">Erro: ' + response.message + '</div>').show();
                }
            },
            error: function(xhr, status, error) {
                $('#loadingDuplicados').hide();
                $('#btnCorrigirTodos').prop('disabled', false);
                $('#mensagemResultado').html('<div class="alert alert-danger">Erro: ' + error + '</div>').show();
            }
        });
    });
    
    // Função para preencher a tabela com os duplicados
    function preencherTabelaDuplicados(duplicados) {
        var tbody = $('#tabelaDuplicados');
        tbody.empty();
        
        duplicados.forEach(function(item) {
            var idsArray = item.ids.split(',');
            
            var row = '<tr>' +
                '<td><strong>' + item.nomeproduto + '</strong></td>' +
                '<td><span class="badge badge-dup-count">' + item.total + ' duplicados</span></td>' +
                '<td><span class="badge badge-stock-total">' + (item.stock_total || 0) + ' unidades</span></td>' +
                '<td>' +
                '<button class="btn btn-sm btn-remove-duplicates" data-ids="' + item.ids + '" data-nome="' + item.nomeproduto + '">' +
                '<i class="fa fa-trash"></i> Remover Duplicados' +
                '</button>' +
                '</td>' +
                '</tr>';
            
            tbody.append(row);
        });
        
        // Adicionar evento aos botões de remover duplicados
        $('.btn-remove-duplicates').click(function() {
            var ids = $(this).data('ids');
            var nome = $(this).data('nome');
            var idsArray = ids.split(',');
            var idManter = idsArray[0];
            
            if (!confirm('Remover duplicados de "' + nome + '"?\n\nSerá mantido o ID: ' + idManter)) {
                return;
            }
            
            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processando...');
            
            $.ajax({
                url: 'corrigir_duplicados.php',
                type: 'POST',
                data: {
                    acao: 'corrigir_duplicado',
                    nome_produto: nome,
                    ids: ids,
                    id_manter: idManter
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        $('#btnProcurarDuplicados').click();
                        $('#mensagemResultado').html(
                            '<div class="alert alert-success">' +
                            '<i class="fa fa-check-circle"></i> ' + response.message +
                            '</div>'
                        ).show();
                    } else {
                        $('.btn-remove-duplicates').prop('disabled', false).html('<i class="fa fa-trash"></i> Remover Duplicados');
                        $('#mensagemResultado').html('<div class="alert alert-danger">Erro: ' + response.message + '</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('.btn-remove-duplicates').prop('disabled', false).html('<i class="fa fa-trash"></i> Remover Duplicados');
                    $('#mensagemResultado').html('<div class="alert alert-danger">Erro: ' + error + '</div>').show();
                }
            });
        });
    }

    // Função para mostrar alertas
    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';
        
        $('.content').prepend(alertHtml);
        
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>
</html>