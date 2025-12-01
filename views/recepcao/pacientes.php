<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

// Verificar se o usuário tem permissão de recepção
if($_SESSION['categoriaUsuario'] != "recepcao"){
	header("location:../admin/");
	exit;
}

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table#tabelaPacientes {
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
                <?php
                // Verificar se a tabela existe (para mostrar aviso no topo)
                $check_table_top = "SHOW TABLES LIKE 'pacientes'";
                $table_exists_top = mysqli_query($db, $check_table_top);
                $pacientes_table_exists_top = ($table_exists_top && mysqli_num_rows($table_exists_top) > 0);
                
                if (!$pacientes_table_exists_top):
                ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Atenção:</strong> A tabela de pacientes ainda não foi criada. 
                    <a href="verificar_tabelas.php" class="alert-link">Clique aqui para verificar e criar as tabelas necessárias</a>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Pacientes</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="novo_paciente.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Cadastrar Paciente</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Pacientes Cadastrados</h4>
                                    </div>
                            <div class="card-body">
                                <div class="row m-b-20">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="pesquisaPaciente" class="form-control" placeholder="Pesquisar por nome, apelido, número de processo, documento ou contacto...">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tabelaPacientes" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nº Processo</th>
                                                <th>Nome</th>
                                                <th>Apelido</th>
                                                <th>Empresa/Seguro</th>
                                                <th>Documento</th>
                                                <th>Contacto</th>
                                                <th>Data Registo</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Verificar se a tabela existe
                                            $check_table = "SHOW TABLES LIKE 'pacientes'";
                                            $table_exists = mysqli_query($db, $check_table);
                                            
                                            if ($table_exists && mysqli_num_rows($table_exists) > 0) {
                                                $sql = "SELECT p.*, e.nome as empresa_nome 
                                                        FROM pacientes p 
                                                        LEFT JOIN empresas_seguros e ON p.empresa_id = e.id 
                                                        WHERE p.ativo = 1 ORDER BY p.data_registo DESC";
                                                $rs = mysqli_query($db, $sql);
                                                
                                                if($rs && mysqli_num_rows($rs) > 0):
                                                    while ($paciente = mysqli_fetch_array($rs)) {
                                                    $doc = '';
                                                    if($paciente['documento_tipo'] && $paciente['documento_numero']) {
                                                        $doc = $paciente['documento_tipo'] . ': ' . $paciente['documento_numero'];
                                                    } elseif($paciente['documento_numero']) {
                                                        $doc = $paciente['documento_numero'];
                                                    } else {
                                                        $doc = '-';
                                                    }
                                            ?>
                                                    <tr>
                                                        <td data-label="Nº Processo"><?php echo htmlspecialchars($paciente['numero_processo']); ?></td>
                                                        <td data-label="Nome"><?php echo htmlspecialchars($paciente['nome']); ?></td>
                                                        <td data-label="Apelido"><?php echo htmlspecialchars($paciente['apelido']); ?></td>
                                                        <td data-label="Empresa/Seguro">
                                                            <?php if($paciente['empresa_nome']): ?>
                                                                <span class="badge badge-info"><?php echo htmlspecialchars($paciente['empresa_nome']); ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted">Particular</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td data-label="Documento"><?php echo htmlspecialchars($doc); ?></td>
                                                        <td data-label="Contacto"><?php echo htmlspecialchars($paciente['contacto'] ? $paciente['contacto'] : '-'); ?></td>
                                                        <td data-label="Data Registo"><?php echo $paciente['data_registo'] ? date('d/m/Y', strtotime($paciente['data_registo'])) : '-'; ?></td>
                                                        <td data-label="Ações">
                                                            <div class="btn-group" role="group">
                                                                <a href="editar_paciente.php?id=<?php echo $paciente['id']; ?>" class="btn btn-sm btn-info"><i class="fa fa-edit"></i> <span class="d-none d-md-inline">Editar</span></a>
                                                                <a href="historico_paciente.php?id=<?php echo $paciente['id']; ?>" class="btn btn-sm btn-success"><i class="fa fa-history"></i> <span class="d-none d-md-inline">Histórico</span></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            <?php
                                                    }
                                                endif;
                                            } else {
                                                // Tabela não existe
                                                echo '<tr><td colspan="8" class="text-center text-warning">';
                                                echo '<i class="fa fa-exclamation-triangle"></i> ';
                                                echo 'As tabelas do módulo de recepção ainda não foram criadas. ';
                                                echo '<a href="verificar_tabelas.php">Clique aqui para verificar e criar as tabelas necessárias</a>';
                                                echo '</td></tr>';
                                            }
                                            ?>
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
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
    <link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#tabelaPacientes').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json",
                    "emptyTable": "Nenhum paciente encontrado"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 7 }
                ],
                "order": [[6, "desc"]],
                "searching": true,
                "paging": true,
                "info": true
            });
            
            // Pesquisa em tempo real
            $('#pesquisaPaciente').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
</body>
</html>

