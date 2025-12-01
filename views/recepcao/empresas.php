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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
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
                        <h4 class="page-title">Empresas e Seguros</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="nova_empresa.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Nova Empresa</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Empresas Cadastradas</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tabelaEmpresas" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>NUIT</th>
                                                <th>Contacto</th>
                                                <th>Contrato</th>
                                                <th>Validade</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM empresas_seguros WHERE ativo = 1 ORDER BY nome";
                                            $rs = mysqli_query($db, $sql);
                                            if($rs && mysqli_num_rows($rs) > 0):
                                                while ($empresa = mysqli_fetch_array($rs)) {
                                                    $validade_ok = true;
                                                    $validade_text = '-';
                                                    if($empresa['data_fim_contrato']) {
                                                        $hoje = date('Y-m-d');
                                                        $validade_ok = $empresa['data_fim_contrato'] >= $hoje;
                                                        $validade_text = date('d/m/Y', strtotime($empresa['data_fim_contrato']));
                                                    }
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($empresa['nome']); ?></td>
                                                    <td><?php echo htmlspecialchars($empresa['nuit'] ? $empresa['nuit'] : '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($empresa['contacto'] ? $empresa['contacto'] : '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($empresa['contrato'] ? $empresa['contrato'] : '-'); ?></td>
                                                    <td>
                                                        <?php if($validade_text != '-'): ?>
                                                            <span class="<?php echo $validade_ok ? 'text-success' : 'text-danger'; ?>">
                                                                <?php echo $validade_text; ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <?php echo $validade_text; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($empresa['tabela_precos_id']): ?>
                                                            <span class="badge badge-success">Ativa</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning">Sem Tabela</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="editar_empresa.php?id=<?php echo $empresa['id']; ?>" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                                            <a href="tabela_precos.php?id=<?php echo $empresa['id']; ?>" class="btn btn-sm btn-primary"><i class="fa fa-table"></i> Preços</a>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelaEmpresas').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json"
                },
                "order": [[ 0, "asc" ]]
            });
        });
    </script>
</body>
</html>

