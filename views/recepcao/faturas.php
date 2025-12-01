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
        .badge-pendente { background-color: #ff9800; }
        .badge-paga { background-color: #4caf50; }
        .badge-cancelada { background-color: #f44336; }
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
                        <h4 class="page-title">Faturas</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="fa_recepcao.php" target="_blank" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Nova Fatura</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Lista de Faturas</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tabelaFaturas" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nº Fatura</th>
                                                <th>Paciente</th>
                                                <th>Empresa/Seguro</th>
                                                <th>Data</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Imprimir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Verificar se a tabela factura_recepcao existe
                                            $check_table = "SHOW TABLES LIKE 'factura_recepcao'";
                                            $table_exists = mysqli_query($db, $check_table);
                                            $use_new_table = ($table_exists && mysqli_num_rows($table_exists) > 0);
                                            
                                            $has_faturas = false;
                                            
                                            if($use_new_table) {
                                                // Usar nova tabela factura_recepcao
                                                $sql = "SELECT f.*, p.nome, p.apelido, p.numero_processo,
                                                        e.nome as empresa_nome
                                                        FROM factura_recepcao f 
                                                        INNER JOIN pacientes p ON f.paciente = p.id 
                                                        LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
                                                        ORDER BY f.data DESC";
                                                
                                                $rs = mysqli_query($db, $sql);
                                                
                                                if($rs) {
                                                    if(mysqli_num_rows($rs) > 0) {
                                                        $has_faturas = true;
                                                        while ($fatura = mysqli_fetch_array($rs)) {
                                                            // Buscar total pago para esta fatura
                                                            $fatura_id = intval($fatura['id']);
                                                            $sql_pag = "SELECT COALESCE(SUM(valor_pago), 0) as total 
                                                                        FROM pagamentos_recepcao 
                                                                        WHERE factura_recepcao_id = $fatura_id 
                                                                        OR (fatura_id = $fatura_id AND factura_recepcao_id IS NULL)";
                                                            $rs_pag = mysqli_query($db, $sql_pag);
                                                            $total_pago = 0;
                                                            if($rs_pag) {
                                                                $pag_data = mysqli_fetch_array($rs_pag);
                                                                $total_pago = floatval($pag_data['total']);
                                                            }
                                                            
                                                            // Calcular totais de NC, ND e DV
                                                            $total_nc = 0;
                                                            $total_nd = 0;
                                                            $total_dv = 0;
                                                            
                                                            $check_nc = "SHOW TABLES LIKE 'nota_credito_recepcao'";
                                                            $table_nc = mysqli_query($db, $check_nc);
                                                            if($table_nc && mysqli_num_rows($table_nc) > 0) {
                                                                $sql_nc = "SELECT COALESCE(SUM(valor), 0) as total FROM nota_credito_recepcao WHERE factura_recepcao_id = $fatura_id";
                                                                $rs_nc = mysqli_query($db, $sql_nc);
                                                                if($rs_nc) {
                                                                    $nc_data = mysqli_fetch_array($rs_nc);
                                                                    $total_nc = floatval($nc_data['total']);
                                                                }
                                                            }
                                                            
                                                            $check_nd = "SHOW TABLES LIKE 'nota_debito_recepcao'";
                                                            $table_nd = mysqli_query($db, $check_nd);
                                                            if($table_nd && mysqli_num_rows($table_nd) > 0) {
                                                                $sql_nd = "SELECT COALESCE(SUM(valor), 0) as total FROM nota_debito_recepcao WHERE factura_recepcao_id = $fatura_id";
                                                                $rs_nd = mysqli_query($db, $sql_nd);
                                                                if($rs_nd) {
                                                                    $nd_data = mysqli_fetch_array($rs_nd);
                                                                    $total_nd = floatval($nd_data['total']);
                                                                }
                                                            }
                                                            
                                                            $check_dv = "SHOW TABLES LIKE 'devolucao_recepcao'";
                                                            $table_dv = mysqli_query($db, $check_dv);
                                                            if($table_dv && mysqli_num_rows($table_dv) > 0) {
                                                                $sql_dv = "SELECT COALESCE(SUM(valor), 0) as total FROM devolucao_recepcao WHERE factura_recepcao_id = $fatura_id";
                                                                $rs_dv = mysqli_query($db, $sql_dv);
                                                                if($rs_dv) {
                                                                    $dv_data = mysqli_fetch_array($rs_dv);
                                                                    $total_dv = floatval($dv_data['total']);
                                                                }
                                                            }
                                                            
                                                            // Calcular valor disponível: valor_original + ND - NC - DV
                                                            $valor_total = floatval($fatura['valor']);
                                                            $valor_disponivel = $valor_total + $total_nd - $total_nc - $total_dv;
                                                            
                                                            // Determinar status baseado em pagamentos e valor disponível
                                                            $status_class = '';
                                                            $status_text = '';
                                                            
                                                            if($total_dv > 0) {
                                                                // Se tem devolução, verificar se foi totalmente devolvida
                                                                if($total_dv >= $valor_total) {
                                                                    $status_class = 'badge-secondary';
                                                                    $status_text = 'Devolvida';
                                                                } elseif($total_pago >= $valor_disponivel) {
                                                                    $status_class = 'badge-success';
                                                                    $status_text = 'Paga';
                                                                } elseif($total_pago > 0) {
                                                                    $status_class = 'badge-info';
                                                                    $status_text = 'Parcial';
                                                                } else {
                                                                    $status_class = 'badge-warning';
                                                                    $status_text = 'Pendente';
                                                                }
                                                            } elseif($total_pago >= $valor_disponivel) {
                                                                $status_class = 'badge-success';
                                                                $status_text = 'Paga';
                                                            } elseif($total_pago > 0) {
                                                                $status_class = 'badge-info';
                                                                $status_text = 'Parcial';
                                                            } else {
                                                                $status_class = 'badge-warning';
                                                                $status_text = 'Pendente';
                                                            }
                                                            
                                                            $numero_fatura = "FA#" . $fatura['serie'] . "/" . $fatura['n_doc'];
                                            ?>
                                                <tr>
                                                    <td data-label="Nº Fatura"><?php echo $numero_fatura; ?></td>
                                                    <td data-label="Paciente"><?php echo $fatura['nome'] . ' ' . $fatura['apelido']; ?><br><small><?php echo $fatura['numero_processo']; ?></small></td>
                                                    <td data-label="Empresa/Seguro"><?php echo $fatura['empresa_nome'] ? $fatura['empresa_nome'] : '-'; ?></td>
                                                    <td data-label="Data"><?php echo date('d/m/Y', strtotime($fatura['dataa'])); ?></td>
                                                    <td data-label="Total"><?php echo number_format($fatura['valor'], 2, ',', '.'); ?> MT</td>
                                                    <td data-label="Status"><span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                                    <td data-label="Ações">
                                                        <a href="imprimir_recibo.php?id=<?php echo $fatura['id']; ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-print"></i> <span class="d-none d-md-inline">Imprimir</span></a>
                                                    </td>
                                                </tr>
                                            <?php
                                                        }
                                                    }
                                                } else {
                                                    // Erro na query - logar para debug
                                                    error_log("Erro ao buscar faturas (factura_recepcao): " . mysqli_error($db));
                                                }
                                            }
                                            
                                            // Se não encontrou na nova tabela ou não existe, tentar tabela antiga
                                            if(!$has_faturas) {
                                                // Fallback para tabela antiga se a nova não existir ou não tiver dados
                                                $sql = "SELECT f.*, p.nome, p.apelido, p.numero_processo 
                                                        FROM faturas_atendimento f 
                                                        INNER JOIN pacientes p ON f.paciente_id = p.id 
                                                        ORDER BY f.data_criacao DESC";
                                                $rs = mysqli_query($db, $sql);
                                                if($rs && mysqli_num_rows($rs) > 0) {
                                                    $has_faturas = true;
                                                    while ($fatura = mysqli_fetch_array($rs)) {
                                                        $status_class = '';
                                                        $status_text = '';
                                                        switch($fatura['status']) {
                                                            case 'pendente':
                                                                $status_class = 'badge-warning';
                                                                $status_text = 'Pendente';
                                                                break;
                                                            case 'parcial':
                                                                $status_class = 'badge-info';
                                                                $status_text = 'Parcial';
                                                                break;
                                                            case 'paga':
                                                                $status_class = 'badge-success';
                                                                $status_text = 'Paga';
                                                                break;
                                                            case 'vencido':
                                                                $status_class = 'badge-secondary';
                                                                $status_text = 'Vencido';
                                                                break;
                                                            case 'cancelada':
                                                                $status_class = 'badge-danger';
                                                                $status_text = 'Cancelada';
                                                                break;
                                                        }
                                            ?>
                                                <tr>
                                                    <td data-label="Nº Fatura"><?php echo $fatura['numero_fatura']; ?></td>
                                                    <td data-label="Paciente"><?php echo $fatura['nome'] . ' ' . $fatura['apelido']; ?><br><small><?php echo $fatura['numero_processo']; ?></small></td>
                                                    <td data-label="Empresa/Seguro">-</td>
                                                    <td data-label="Data"><?php echo date('d/m/Y', strtotime($fatura['data_atendimento'])); ?></td>
                                                    <td data-label="Total"><?php echo number_format($fatura['total'], 2, ',', '.'); ?> MT</td>
                                                    <td data-label="Status"><span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                                    <td data-label="Ações">
                                                        <a href="imprimir_recibo.php?id=<?php echo $fatura['id']; ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-print"></i> <span class="d-none d-md-inline">Imprimir</span></a>
                                                    </td>
                                                </tr>
                                            <?php
                                                    }
                                                }
                                            }
                                            
                                            if(!$has_faturas) {
                                                echo '<tr><td colspan="6" class="text-center">Nenhuma fatura encontrada</td></tr>';
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
            $('#tabelaFaturas').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese.json",
                    "emptyTable": "Nenhuma fatura encontrada"
                },
                "order": [[2, "desc"]]
            });
        });
    </script>
</body>
</html>

