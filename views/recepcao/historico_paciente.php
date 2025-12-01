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

if(!isset($_GET['id'])){
	header("location:pacientes.php");
	exit;
}

$paciente_id = intval($_GET['id']);
$sql_paciente = "SELECT * FROM pacientes WHERE id = $paciente_id";
$rs_paciente = mysqli_query($db, $sql_paciente);
$paciente = mysqli_fetch_array($rs_paciente);

if(!$paciente){
	header("location:pacientes.php");
	exit;
}

// Buscar histórico
$sql_historico = "SELECT h.*, f.numero_fatura, f.total, f.status as fatura_status
                  FROM historico_atendimentos h
                  LEFT JOIN faturas_atendimento f ON h.fatura_id = f.id
                  WHERE h.paciente_id = $paciente_id
                  ORDER BY h.data_atendimento DESC, h.data_registo DESC";
$rs_historico = mysqli_query($db, $sql_historico);

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
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
                        <h4 class="page-title">Histórico de Atendimentos</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="pacientes.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Paciente: <?php echo $paciente['nome'] . ' ' . $paciente['apelido']; ?></h4>
                                <p class="text-muted">Nº Processo: <?php echo $paciente['numero_processo']; ?> | Contacto: <?php echo $paciente['contacto']; ?></p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Tipo</th>
                                                <th>Serviços</th>
                                                <th>Fatura</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                                <th>Observações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if(mysqli_num_rows($rs_historico) > 0):
                                                while($historico = mysqli_fetch_array($rs_historico)):
                                                    $status_class = '';
                                                    $status_text = '';
                                                    if($historico['fatura_status']) {
                                                        switch($historico['fatura_status']) {
                                                            case 'pendente':
                                                                $status_class = 'badge-warning';
                                                                $status_text = 'Pendente';
                                                                break;
                                                            case 'paga':
                                                                $status_class = 'badge-success';
                                                                $status_text = 'Paga';
                                                                break;
                                                            case 'cancelada':
                                                                $status_class = 'badge-danger';
                                                                $status_text = 'Cancelada';
                                                                break;
                                                        }
                                                    }
                                            ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($historico['data_atendimento'])); ?></td>
                                                    <td><?php echo $historico['tipo_atendimento']; ?></td>
                                                    <td><?php echo $historico['servicos_realizados']; ?></td>
                                                    <td>
                                                        <?php if($historico['numero_fatura']): ?>
                                                            <a href="detalhes_fatura.php?id=<?php echo $historico['fatura_id']; ?>"><?php echo $historico['numero_fatura']; ?></a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($historico['total']): ?>
                                                            <?php echo number_format($historico['total'], 2, ',', '.'); ?> MT
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($status_text): ?>
                                                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $historico['observacoes']; ?></td>
                                                </tr>
                                            <?php
                                                endwhile;
                                            else:
                                            ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">Nenhum atendimento registrado</td>
                                                </tr>
                                            <?php
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
</body>
</html>

