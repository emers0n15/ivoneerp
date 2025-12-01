<?php 
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
            width: 100%;
        }
        .tipo-movimento {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .tipo-movimento.entrada {
            background-color: #55ce63;
            color: white;
        }
        .tipo-movimento.saida {
            background-color: #f62d51;
            color: white;
        }
        .tipo-movimento.transferencia {
            background-color: #ffbc34;
            color: white;
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
                        <h4 class="page-title">Movimentos de Stock - <?php echo htmlspecialchars($armazem['nome']); ?></h4>
                    </div>
                    <div class="col-sm-4 col-7 text-right m-b-20">
                        <a href="armazem_stock.php?id=<?php echo $armazem_id; ?>&nome=<?php echo urlencode($armazem['nome']); ?>" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar para Stock</a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row filter-row mb-3">
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>Tipo de Movimento</label>
                            <select class="form-control" id="filtro_tipo">
                                <option value="">Todos</option>
                                <option value="entrada">Entradas</option>
                                <option value="saida">Saídas</option>
                                <option value="transferencia">Transferências</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>Data Inicial</label>
                            <input type="date" class="form-control" id="filtro_data_inicial">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>Data Final</label>
                            <input type="date" class="form-control" id="filtro_data_final">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-success btn-block" id="btn_filtrar">Filtrar</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="tabelaMovimentos" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Data</th>
                                        <th>Produto</th>
                                        <th>Lote</th>
                                        <th>Tipo de Movimento</th>
                                        <th>Quantidade</th>
                                        <th>Usuário</th>
                                        <th>Observação</th>
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

    <!-- Scripts -->
    <div class="sidebar-overlay" data-reff=""></div>
    <script src="../../js/jquery-3.7.1.min.js"></script>
    <?php include 'includes/footer_plugins.php'; ?>
    <script src="../../js/sweetalert.min.js"></script>
    <link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    
    <script>
        $(function() {
            // Inicializar DataTable
            var tabela = $('#tabelaMovimentos').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "ajax/listar_armazem_movimentos.php",
                    "type": "POST",
                    "data": function(d) {
                        d.armazem_id = <?php echo $armazem_id; ?>;
                        d.tipo_movimento = $('#filtro_tipo').val();
                        d.data_inicial = $('#filtro_data_inicial').val();
                        d.data_final = $('#filtro_data_final').val();
                    },
                    "error": function(xhr, error, thrown) {
                        console.error("Erro na requisição AJAX:", error, thrown);
                        console.log("Resposta do servidor:", xhr.responseText);
                    }
                },
                "language": {
                    "url": "../../js/dataTables.portuguese.json"
                },
                "columns": [
                    { "data": "id" },
                    { "data": "data" },
                    { "data": "produto" },
                    { "data": "lote" },
                    { "data": "tipo" },
                    { "data": "quantidade" },
                    { "data": "usuario" },
                    { "data": "observacao" }
                ],
                "order": [[1, "desc"]], // Ordenar por data decrescente (mais recentes primeiro)
                "drawCallback": function(settings) {
                    console.log("Dados recebidos do servidor:", settings.json);
                }
            });
            
            // Aplicar filtros ao clicar no botão
            $('#btn_filtrar').click(function() {
                tabela.ajax.reload();
            });
        });
    </script>
</body>
</html>
