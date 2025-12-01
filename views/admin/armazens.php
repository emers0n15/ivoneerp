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
<html lang="pt-PT">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        /* Ajusta a tabela para ocupar toda a largura disponível */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table#tabelaArmazens {
            width: 100%;
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
                        <h4 class="page-title">Armazéns</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <button class="btn btn-primary btn-rounded float-right ml-2" data-toggle="modal" data-target="#modalCadastrarArmazem"><i class="fa fa-plus"></i> Novo Armazém</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="tabelaArmazens" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Endereço</th>
                                        <th>Telefone</th>
                                        <th>Responsável</th>
                                        <th>Estado</th>
                                        <th class="text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dados serão carregados via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Cadastrar Armazém -->
    <div class="modal fade" id="modalCadastrarArmazem" tabindex="-1" role="dialog" aria-labelledby="modalCadastrarArmazemLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCadastrarArmazemLabel">Cadastrar Novo Armazém</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formCadastrarArmazem">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nome">Nome do Armazém <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="endereco">Endereço</label>
                            <input type="text" class="form-control" id="endereco" name="endereco">
                        </div>
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone">
                        </div>
                        <div class="form-group">
                            <label for="responsavel">Responsável</label>
                            <select class="form-control" id="responsavel" name="responsavel">
                                <option value="">Selecione um responsável</option>
                                <?php
                                $sqlUsuarios = "SELECT id, nome FROM users ORDER BY nome";
                                $resultadoUsuarios = mysqli_query($db, $sqlUsuarios);
                                
                                if ($resultadoUsuarios && mysqli_num_rows($resultadoUsuarios) > 0) {
                                    while ($usuario = mysqli_fetch_assoc($resultadoUsuarios)) {
                                        echo '<option value="' . $usuario['id'] . '">' . $usuario['nome'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Armazém -->
    <div class="modal fade" id="modalEditarArmazem" tabindex="-1" role="dialog" aria-labelledby="modalEditarArmazemLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarArmazemLabel">Editar Armazém</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEditarArmazem">
                    <input type="hidden" id="editId" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editNome">Nome do Armazém <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editNome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="editEndereco">Endereço</label>
                            <input type="text" class="form-control" id="editEndereco" name="endereco">
                        </div>
                        <div class="form-group">
                            <label for="editTelefone">Telefone</label>
                            <input type="text" class="form-control" id="editTelefone" name="telefone">
                        </div>
                        <div class="form-group">
                            <label for="editResponsavel">Responsável</label>
                            <select class="form-control" id="editResponsavel" name="responsavel">
                                <option value="">Selecione um responsável</option>
                                <?php
                                $sqlUsuarios = "SELECT idusuario, nomeUsuario FROM usuario ORDER BY nomeUsuario";
                                $resultadoUsuarios = mysqli_query($db, $sqlUsuarios);
                                
                                if ($resultadoUsuarios && mysqli_num_rows($resultadoUsuarios) > 0) {
                                    mysqli_data_seek($resultadoUsuarios, 0); // Reset the pointer
                                    while ($usuario = mysqli_fetch_assoc($resultadoUsuarios)) {
                                        echo '<option value="' . $usuario['idusuario'] . '">' . $usuario['nomeUsuario'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
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
            // Inicializar DataTable com ServerSide
            $('#tabelaArmazens').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "ajax/listar_armazens.php",
                    "type": "POST"
                },
                "language": {
                    "url": "../../js/dataTables.portuguese.json"
                },
                "columns": [
                    { "data": 0 }, // ID
                    { "data": 1 }, // Nome
                    { "data": 2 }, // Endereço
                    { "data": 3 }, // Telefone
                    { "data": 4 }, // Responsável
                    { "data": 5 }, // Estado
                    { 
                        "data": 6,
                        "orderable": false
                    } // Ações
                ]
            });

            // Cadastrar Armazém
            $('#formCadastrarArmazem').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: 'ajax/armazem_cadastrar.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#modalCadastrarArmazem').modal('hide');
                            $('#formCadastrarArmazem')[0].reset();
                            $('#tabelaArmazens').DataTable().ajax.reload();
                            
                            swal({
                                title: "Sucesso!",
                                text: "Armazém cadastrado com sucesso!",
                                icon: "success",
                                button: "OK"
                            });
                        } else {
                            swal({
                                title: "Erro!",
                                text: response.message || "Ocorreu um erro ao cadastrar o armazém.",
                                icon: "error",
                                button: "OK"
                            });
                        }
                    },
                    error: function() {
                        swal({
                            title: "Erro!",
                            text: "Ocorreu um erro na comunicação com o servidor.",
                            icon: "error",
                            button: "OK"
                        });
                    }
                });
            });

            // Carregar dados para edição
            $(document).on('click', '.editarArmazem', function() {
                var id = $(this).data('id');
                
                $.ajax({
                    url: 'ajax/armazem_obter.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            var armazem = response.data;
                            
                            $('#editId').val(armazem.id);
                            $('#editNome').val(armazem.nome);
                            $('#editEndereco').val(armazem.endereco);
                            $('#editTelefone').val(armazem.telefone);
                            $('#editResponsavel').val(armazem.responsavel);
                            
                            $('#modalEditarArmazem').modal('show');
                        } else {
                            swal({
                                title: "Erro!",
                                text: response.message || "Não foi possível carregar os dados do armazém.",
                                icon: "error",
                                button: "OK"
                            });
                        }
                    },
                    error: function() {
                        swal({
                            title: "Erro!",
                            text: "Ocorreu um erro na comunicação com o servidor.",
                            icon: "error",
                            button: "OK"
                        });
                    }
                });
            });

            // Editar Armazém
            $('#formEditarArmazem').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: 'ajax/armazem_editar.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#modalEditarArmazem').modal('hide');
                            $('#tabelaArmazens').DataTable().ajax.reload();
                            
                            swal({
                                title: "Sucesso!",
                                text: "Armazém atualizado com sucesso!",
                                icon: "success",
                                button: "OK"
                            });
                        } else {
                            swal({
                                title: "Erro!",
                                text: response.message || "Ocorreu um erro ao atualizar o armazém.",
                                icon: "error",
                                button: "OK"
                            });
                        }
                    },
                    error: function() {
                        swal({
                            title: "Erro!",
                            text: "Ocorreu um erro na comunicação com o servidor.",
                            icon: "error",
                            button: "OK"
                        });
                    }
                });
            });

            // Alterar Estado do Armazém
            $(document).on('click', '.alterarEstadoArmazem', function() {
                var id = $(this).data('id');
                var estado = $(this).data('estado');
                var textoConfirmacao = estado === 'ativo' ? 'ativar' : 'inativar';
                
                swal({
                    title: "Tem certeza?",
                    text: "Deseja realmente " + textoConfirmacao + " este armazém?",
                    icon: "warning",
                    buttons: ["Cancelar", "Sim"],
                    dangerMode: estado === 'inativo'
                }).then((willChange) => {
                    if (willChange) {
                        $.ajax({
                            url: 'ajax/armazem_alterar_estado.php',
                            type: 'POST',
                            data: { id: id, estado: estado },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    $('#tabelaArmazens').DataTable().ajax.reload();
                                    
                                    swal({
                                        title: "Sucesso!",
                                        text: "Estado do armazém alterado com sucesso!",
                                        icon: "success",
                                        button: "OK"
                                    });
                                } else {
                                    swal({
                                        title: "Erro!",
                                        text: response.message || "Ocorreu um erro ao alterar o estado do armazém.",
                                        icon: "error",
                                        button: "OK"
                                    });
                                }
                            },
                            error: function() {
                                swal({
                                    title: "Erro!",
                                    text: "Ocorreu um erro na comunicação com o servidor.",
                                    icon: "error",
                                    button: "OK"
                                });
                            }
                        });
                    }
                });
            });

            // Gerenciar stock do armazém
            $(document).on('click', '.gerenciarStockArmazem', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var nome = $(this).data('nome');
                window.location.href = 'armazem_stock.php?id=' + id + '&nome=' + encodeURIComponent(nome);
            });
        });
    </script>
</body>
</html>
