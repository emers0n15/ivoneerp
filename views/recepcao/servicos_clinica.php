<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

if($_SESSION['categoriaUsuario'] != "recepcao"){
	header("location:../admin/");
	exit;
}

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$empresa_selecionada = isset($_GET['empresa']) ? intval($_GET['empresa']) : null;
$tabela_precos_id = null;

if($empresa_selecionada) {
    // Buscar tabela de preços da empresa
    $sql_empresa = "SELECT tabela_precos_id FROM empresas_seguros WHERE id = $empresa_selecionada";
    $rs_empresa = mysqli_query($db, $sql_empresa);
    if($rs_empresa && mysqli_num_rows($rs_empresa) > 0) {
        $empresa_data = mysqli_fetch_array($rs_empresa);
        $tabela_precos_id = $empresa_data['tabela_precos_id'] ?? null;
        
        // Se não tem tabela, criar uma
        if(!$tabela_precos_id) {
            $sql_criar = "INSERT INTO tabelas_precos (empresa_id, nome, ativo, usuario_criacao) 
                          VALUES ($empresa_selecionada, 'Tabela Padrão', 1, " . $_SESSION['idUsuario'] . ")";
            mysqli_query($db, $sql_criar);
            $tabela_precos_id = mysqli_insert_id($db);
            
            // Atualizar empresa com tabela de preços
            $sql_update = "UPDATE empresas_seguros SET tabela_precos_id = $tabela_precos_id WHERE id = $empresa_selecionada";
            mysqli_query($db, $sql_update);
        }
    }
}

// Buscar preços cadastrados para a empresa selecionada
$precos_empresa = array();
if($tabela_precos_id) {
    $sql_precos = "SELECT servico_id, preco FROM tabela_precos_servicos WHERE tabela_precos_id = $tabela_precos_id";
    $rs_precos = mysqli_query($db, $sql_precos);
    while($preco = mysqli_fetch_array($rs_precos)) {
        $precos_empresa[$preco['servico_id']] = floatval($preco['preco']);
    }
}

// Buscar categorias da base de dados (nada hardcoded!)
$sql_categorias = "SELECT * FROM categorias_servicos WHERE ativo = 1 ORDER BY nome";
$rs_categorias = mysqli_query($db, $sql_categorias);
$categorias_lista = array();
if($rs_categorias && mysqli_num_rows($rs_categorias) > 0) {
    while($cat = mysqli_fetch_array($rs_categorias)) {
        $categorias_lista[] = $cat;
    }
}
// Se não houver categorias cadastradas, usar as padrão apenas para exibição inicial
// (mas idealmente o usuário deve cadastrar categorias na BD primeiro)
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
                        <h4 class="page-title">Serviços/Procedimentos Clínicos</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <button type="button" class="btn btn-primary btn-rounded float-right" data-toggle="modal" data-target="#modalNovoServico">
                            <i class="fa fa-plus"></i> Novo Serviço
                        </button>
                    </div>
                </div>
                
                <div class="row m-b-20">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><strong>Empresa/Seguradora:</strong></label>
                                        <select class="form-control" id="selectEmpresa" onchange="carregarPrecosEmpresa(this.value)">
                                            <option value="">-- Todas as Empresas (Preço Padrão) --</option>
                                            <?php 
                                            $sql_empresas = "SELECT * FROM empresas_seguros WHERE ativo = 1 ORDER BY nome";
                                            $rs_empresas = mysqli_query($db, $sql_empresas);
                                            while ($empresa = mysqli_fetch_array($rs_empresas)) {
                                                $selected = ($empresa_selecionada == $empresa['id']) ? 'selected' : '';
                                                echo '<option value="' . $empresa['id'] . '" ' . $selected . '>' . htmlspecialchars($empresa['nome']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <small class="form-text text-muted">Selecione uma empresa para configurar os preços específicos daquela seguradora</small>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if($empresa_selecionada): ?>
                                        <div class="alert alert-info" style="margin-top: 30px;">
                                            <strong>Configurando preços para empresa selecionada.</strong> Clique nos preços para editá-los e depois clique em "Salvar Preços" abaixo.
                                        </div>
                                        <?php else: ?>
                                        <div class="alert alert-warning" style="margin-top: 30px;">
                                            <strong>Selecione uma empresa</strong> para configurar os preços específicos daquela seguradora.
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>
                
                <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Serviços Cadastrados</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tabelaServicos" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Nome</th>
                                                <th>Categoria</th>
                                                <th>Preço Padrão</th>
                                                <?php if($empresa_selecionada): ?>
                                                <th>Preço da Empresa (Editar)</th>
                                                <?php endif; ?>
                                                <th>Descrição</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyServicos">
                                            <?php
                                            // Se empresa selecionada, mostrar apenas ativos. Senão, mostrar todos
                                            if($empresa_selecionada) {
                                                $sql = "SELECT * FROM servicos_clinica WHERE ativo = 1 ORDER BY categoria, nome";
                                            } else {
                                            $sql = "SELECT * FROM servicos_clinica ORDER BY categoria, nome";
                                            }
                                            $rs = mysqli_query($db, $sql);
                                            if($rs && mysqli_num_rows($rs) > 0):
                                                while ($servico = mysqli_fetch_array($rs)) {
                                                    $preco_empresa = isset($precos_empresa[$servico['id']]) ? $precos_empresa[$servico['id']] : $servico['preco'];
                                            ?>
                                                <tr data-servico-id="<?php echo $servico['id']; ?>">
                                                    <td><?php echo htmlspecialchars($servico['codigo']); ?></td>
                                                    <td><?php echo htmlspecialchars($servico['nome']); ?></td>
                                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($servico['categoria']); ?></span></td>
                                                    <td><?php echo number_format($servico['preco'], 2, ',', '.'); ?> MT</td>
                                                    <?php if($empresa_selecionada): ?>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control preco-empresa" 
                                                               data-servico-id="<?php echo $servico['id']; ?>"
                                                               value="<?php echo number_format($preco_empresa, 2, '.', ''); ?>" 
                                                               step="0.01" 
                                                               min="0" 
                                                               style="width: 150px; display: inline-block;">
                                                        <span class="text-muted">MT</span>
                                                    </td>
                                                    <?php endif; ?>
                                                    <td><?php echo htmlspecialchars($servico['descricao'] ? $servico['descricao'] : '-'); ?></td>
                                                    <td>
                                                        <?php if($servico['ativo']): ?>
                                                            <span class="badge badge-success">Ativo</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Inativo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-info" onclick="editarServico(<?php echo htmlspecialchars(json_encode($servico)); ?>)">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <a href="daos/excluir_servico.php?id=<?php echo $servico['id']; ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Deseja realmente excluir este serviço?')">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php
                                                }
                                            endif;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 text-right">
                                    <button type="button" id="btnNextServicos" class="btn btn-outline-primary btn-sm">Próximo</button>
                                </div>
                            </div>
                            <?php if($empresa_selecionada): ?>
                            <div class="card-footer">
                                <button type="button" class="btn btn-success btn-lg" onclick="salvarPrecosEmpresa()">
                                    <i class="fa fa-save"></i> Salvar Preços da Empresa
                                </button>
                                <span id="mensagemSalvar" class="ml-3"></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Novo Serviço -->
    <div class="modal fade" id="modalNovoServico" tabindex="-1" role="dialog" aria-labelledby="modalNovoServicoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoServicoLabel">Novo Serviço/Procedimento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="daos/salvar_servico.php">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Código <span class="text-danger">*</span></label>
                                    <input type="text" name="codigo" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Categoria <span class="text-danger">*</span></label>
                                    <select name="categoria" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <?php 
                                        // Buscar categorias da base de dados (nada hardcoded!)
                                        foreach($categorias_lista as $cat) {
                                            echo '<option value="' . htmlspecialchars($cat['nome']) . '">' . htmlspecialchars($cat['nome']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <small class="form-text text-muted">Se não houver categorias, cadastre em "Categorias de Serviços"</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Nome do Serviço <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Preço Padrão (MT) <span class="text-danger">*</span></label>
                                    <input type="number" name="preco" class="form-control" step="0.01" min="0" required>
                                    <small class="form-text text-muted">Este é o preço base. Empresas podem ter preços diferentes.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="ativo" class="form-control">
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="btn_criar" class="btn btn-primary">Salvar Serviço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Serviço -->
    <div class="modal fade" id="modalEditarServico" tabindex="-1" role="dialog" aria-labelledby="modalEditarServicoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarServicoLabel">Editar Serviço/Procedimento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="daos/salvar_servico.php">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Código <span class="text-danger">*</span></label>
                                    <input type="text" name="codigo" id="edit_codigo" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Categoria <span class="text-danger">*</span></label>
                                    <select name="categoria" id="edit_categoria" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <?php 
                                        // Buscar categorias da base de dados (nada hardcoded!)
                                        foreach($categorias_lista as $cat) {
                                            echo '<option value="' . htmlspecialchars($cat['nome']) . '">' . htmlspecialchars($cat['nome']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <small class="form-text text-muted">Se não houver categorias, cadastre em "Categorias de Serviços"</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Nome do Serviço <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="edit_nome" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea name="descricao" id="edit_descricao" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Preço Padrão (MT) <span class="text-danger">*</span></label>
                                    <input type="number" name="preco" id="edit_preco" class="form-control" step="0.01" min="0" required>
                                    <small class="form-text text-muted">Este é o preço base. Empresas podem ter preços diferentes.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="ativo" id="edit_ativo" class="form-control">
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="btn_editar" class="btn btn-primary">Atualizar Serviço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
    <!-- jQuery já é carregado pelo includes/footer.php; remover duplicado -->>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        var dataTable;
        
        $(document).ready(function() {
            var savedLen = parseInt(localStorage.getItem('servicos_page_len')) || 10;
            dataTable = $('#tabelaServicos').DataTable({
                dom: 'lfrtip',
                order: [[ 2, "asc" ], [ 1, "asc" ]],
                pageLength: savedLen,
                lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
                pagingType: 'simple',
                stateSave: true,
                lengthChange: true,
                responsive: true,
                language: {
                    paginate: { previous: "Anterior", next: "Próximo" },
                    emptyTable: "Nenhum serviço cadastrado",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ serviços",
                    infoEmpty: "Mostrando 0 a 0 de 0 serviços",
                    infoFiltered: "(filtrado de _MAX_ serviços)",
                    search: "Pesquisar:",
                    zeroRecords: "Nenhum serviço encontrado com esses filtros"
                }
            });
            $('#tabelaServicos').on('length.dt', function(e, settings, len){ localStorage.setItem('servicos_page_len', len); });
            $('#btnNextServicos').on('click', function(){ if(dataTable){ dataTable.page('next').draw('page'); }});
        });
        
        function editarServico(servico) {
            $('#edit_id').val(servico.id);
            $('#edit_codigo').val(servico.codigo);
            $('#edit_nome').val(servico.nome);
            $('#edit_descricao').val(servico.descricao);
            $('#edit_preco').val(servico.preco);
            $('#edit_categoria').val(servico.categoria);
            $('#edit_ativo').val(servico.ativo);
            $('#modalEditarServico').modal('show');
        }
        
        function carregarPrecosEmpresa(empresaId) {
            if(!empresaId || empresaId == '') {
                window.location.href = 'servicos_clinica.php';
                return;
            }
            
            window.location.href = 'servicos_clinica.php?empresa=' + empresaId;
        }
        
        function salvarPrecosEmpresa() {
            var empresaId = $('#selectEmpresa').val();
            if(!empresaId || empresaId == '') {
                alert('Por favor, selecione uma empresa primeiro!');
                return;
            }
            
            // Coletar todos os preços editados
            var precos = {};
            $('.preco-empresa').each(function() {
                var servicoId = $(this).data('servico-id');
                var preco = parseFloat($(this).val()) || 0;
                if(servicoId && preco > 0) {
                    precos[servicoId] = preco;
                }
            });
            
            if(Object.keys(precos).length == 0) {
                alert('Nenhum preço foi alterado!');
                return;
            }
            
            // Mostrar loading
            $('#mensagemSalvar').html('<i class="fa fa-spinner fa-spin"></i> Salvando...');
            
            $.ajax({
                type: 'POST',
                url: 'daos/salvar_precos_servicos.php',
                data: {
                    empresa_id: empresaId,
                    precos: JSON.stringify(precos)
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#mensagemSalvar').html('<span class="text-success"><i class="fa fa-check"></i> ' + response.mensagem + '</span>');
                        setTimeout(function() {
                            $('#mensagemSalvar').html('');
                        }, 3000);
                    } else {
                        $('#mensagemSalvar').html('<span class="text-danger"><i class="fa fa-times"></i> Erro ao salvar: ' + (response.erro || 'Erro desconhecido') + '</span>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#mensagemSalvar').html('<span class="text-danger"><i class="fa fa-times"></i> Erro ao salvar: ' + error + '</span>');
                }
            });
        }
    </script>
</body>
</html>
