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

// Buscar estatísticas
$stats = [];

// Verificar se a tabela pacientes existe
$check_pacientes = "SHOW TABLES LIKE 'pacientes'";
$table_pacientes = mysqli_query($db, $check_pacientes);
$pacientes_table_exists = ($table_pacientes && mysqli_num_rows($table_pacientes) > 0);

// Pacientes hoje
$stats['pacientes_hoje'] = 0;
if($pacientes_table_exists) {
    $sql_hoje = "SELECT COUNT(*) as total FROM pacientes WHERE DATE(data_registo) = CURDATE()";
    $rs_hoje = mysqli_query($db, $sql_hoje);
    if($rs_hoje) {
        $hoje = mysqli_fetch_array($rs_hoje);
        $stats['pacientes_hoje'] = $hoje ? $hoje['total'] : 0;
    }
}

// Verificar se a tabela factura_recepcao existe
$check_factura_recepcao = "SHOW TABLES LIKE 'factura_recepcao'";
$table_factura_recepcao = mysqli_query($db, $check_factura_recepcao);
$factura_recepcao_exists = ($table_factura_recepcao && mysqli_num_rows($table_factura_recepcao) > 0);

// Faturas hoje
$stats['faturas_hoje'] = 0;
if($factura_recepcao_exists) {
    $sql_faturas = "SELECT COUNT(*) as total FROM factura_recepcao WHERE DATE(dataa) = CURDATE()";
} else {
    $sql_faturas = "SELECT COUNT(*) as total FROM faturas_atendimento WHERE DATE(data_atendimento) = CURDATE()";
}
$rs_faturas = mysqli_query($db, $sql_faturas);
if($rs_faturas) {
    $faturas = mysqli_fetch_array($rs_faturas);
    $stats['faturas_hoje'] = $faturas ? $faturas['total'] : 0;
}

// Faturas pendentes (sem pagamento ou pagamento parcial)
$stats['pendentes'] = 0;
if($factura_recepcao_exists) {
    // Contar faturas sem pagamento completo
    $sql_pendentes = "SELECT COUNT(*) as total
                      FROM factura_recepcao f
                      LEFT JOIN (
                          SELECT factura_recepcao_id, SUM(valor_pago) AS total_pago
                          FROM pagamentos_recepcao
                          WHERE factura_recepcao_id IS NOT NULL
                          GROUP BY factura_recepcao_id
                      ) pag ON pag.factura_recepcao_id = f.id
                      WHERE COALESCE(pag.total_pago, 0) < f.valor";
} else {
    $sql_pendentes = "SELECT COUNT(*) as total FROM faturas_atendimento WHERE status = 'pendente'";
}
$rs_pendentes = mysqli_query($db, $sql_pendentes);
if($rs_pendentes) {
    $pendentes = mysqli_fetch_array($rs_pendentes);
    $stats['pendentes'] = $pendentes ? $pendentes['total'] : 0;
}

// Total recebido hoje
$stats['total_recebido'] = 0;
if($factura_recepcao_exists) {
    // Somar pagamentos de hoje relacionados a factura_recepcao
    $sql_total_hoje = "SELECT COALESCE(SUM(p.valor_pago), 0) as total 
                       FROM pagamentos_recepcao p
                       INNER JOIN factura_recepcao f ON p.factura_recepcao_id = f.id
                       WHERE DATE(p.data_pagamento) = CURDATE()";
} else {
    $sql_total_hoje = "SELECT COALESCE(SUM(total), 0) as total FROM faturas_atendimento WHERE status = 'paga' AND DATE(data_atendimento) = CURDATE()";
}
$rs_total_hoje = mysqli_query($db, $sql_total_hoje);
if($rs_total_hoje) {
    $total_hoje = mysqli_fetch_array($rs_total_hoje);
    $stats['total_recebido'] = $total_hoje ? $total_hoje['total'] : 0;
}

// Total pacientes
$stats['total_pacientes'] = 0;
if($pacientes_table_exists) {
    $sql_total_pacientes = "SELECT COUNT(*) as total FROM pacientes WHERE ativo = 1";
    $rs_total_pacientes = mysqli_query($db, $sql_total_pacientes);
    if($rs_total_pacientes) {
        $total_pacientes = mysqli_fetch_array($rs_total_pacientes);
        $stats['total_pacientes'] = $total_pacientes ? $total_pacientes['total'] : 0;
    }
}

// Faturas pagas hoje
$stats['pagas_hoje'] = 0;
if($factura_recepcao_exists) {
    // Contar faturas pagas hoje (com pagamento completo)
    $sql_pagas = "SELECT COUNT(*) as total FROM (
                    SELECT f.id, SUM(p.valor_pago) AS total_pago, MAX(f.valor) AS valor_total
                    FROM factura_recepcao f
                    INNER JOIN pagamentos_recepcao p ON p.factura_recepcao_id = f.id
                    WHERE DATE(p.data_pagamento) = CURDATE()
                    GROUP BY f.id
                    HAVING total_pago >= valor_total
                  ) pagas";
    $rs_pagas = mysqli_query($db, $sql_pagas);
    if($rs_pagas) {
        $dados_pagas = mysqli_fetch_assoc($rs_pagas);
        $stats['pagas_hoje'] = $dados_pagas ? intval($dados_pagas['total']) : 0;
    }
} else {
    $sql_pagas = "SELECT COUNT(*) as total FROM faturas_atendimento WHERE status = 'paga' AND DATE(data_atendimento) = CURDATE()";
    $rs_pagas = mysqli_query($db, $sql_pagas);
    if($rs_pagas) {
        $pagas = mysqli_fetch_array($rs_pagas);
        $stats['pagas_hoje'] = $pagas ? $pagas['total'] : 0;
    }
}
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
                <!-- Header da Recepção -->
                <div class="dashboard-header">
                    <div>
                        <h2 class="dashboard-title">Recepção</h2>
                        <p class="dashboard-subtitle">Gerencie e acompanhe suas atividades com facilidade</p>
                    </div>
                    <div class="dashboard-actions">
                        <a href="fa_recepcao.php" target="_blank" class="btn-dashboard-primary">
                            <i class="fa fa-plus"></i> Nova Fatura
                        </a>
                        <a href="novo_paciente.php" class="btn-dashboard-secondary">
                            <i class="fa fa-user-plus"></i> Novo Paciente
                        </a>
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="stats-grid">
                    <div class="stat-card stat-card-1">
                        <div class="stat-card-icon">
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="stat-card-content">
                            <h3 class="stat-card-value"><?php echo $stats['pacientes_hoje']; ?></h3>
                            <p class="stat-card-label">Pacientes Hoje</p>
                            <span class="stat-card-badge badge-up">
                                <i class="fa fa-arrow-up"></i> Hoje
                            </span>
                        </div>
                    </div>

                    <div class="stat-card stat-card-2">
                        <div class="stat-card-icon">
                            <i class="fa fa-file-text"></i>
                        </div>
                        <div class="stat-card-content">
                            <h3 class="stat-card-value"><?php echo $stats['faturas_hoje']; ?></h3>
                            <p class="stat-card-label">Faturas Hoje</p>
                            <span class="stat-card-badge badge-up">
                                <i class="fa fa-arrow-up"></i> Hoje
                            </span>
                        </div>
                    </div>

                    <div class="stat-card stat-card-3">
                        <div class="stat-card-icon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <div class="stat-card-content">
                            <h3 class="stat-card-value"><?php echo $stats['pendentes']; ?></h3>
                            <p class="stat-card-label">Faturas Pendentes</p>
                            <span class="stat-card-badge badge-warning">
                                <i class="fa fa-exclamation-circle"></i> Pendentes
                            </span>
                        </div>
                    </div>

                    <div class="stat-card stat-card-4">
                        <div class="stat-card-icon">
                            <i class="fa fa-money"></i>
                        </div>
                        <div class="stat-card-content">
                            <h3 class="stat-card-value"><?php echo number_format($stats['total_recebido'], 0, ',', '.'); ?> MT</h3>
                            <p class="stat-card-label">Total Recebido Hoje</p>
                            <span class="stat-card-badge badge-success">
                                <i class="fa fa-check-circle"></i> Recebido
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Seção de Conteúdo Principal -->
                <div class="dashboard-content-grid">
                    <!-- Ações Rápidas -->
                    <div class="dashboard-card">
                        <div class="card-header-modern">
                            <h4 class="card-title-modern">Ações Rápidas</h4>
                        </div>
                        <div class="quick-actions-grid">
                            <a href="fa_recepcao.php" target="_blank" class="quick-action-item">
                                <div class="quick-action-icon icon-purple">
                                    <i class="fa fa-plus"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h5>Nova Fatura</h5>
                                    <p>Criar fatura de atendimento</p>
                                </div>
                                <i class="fa fa-chevron-right quick-action-arrow"></i>
                            </a>

                            <a href="novo_paciente.php" class="quick-action-item">
                                <div class="quick-action-icon icon-blue">
                                    <i class="fa fa-user-plus"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h5>Novo Paciente</h5>
                                    <p>Cadastrar novo paciente</p>
                                </div>
                                <i class="fa fa-chevron-right quick-action-arrow"></i>
                            </a>

                            <a href="pacientes.php" class="quick-action-item">
                                <div class="quick-action-icon icon-green">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h5>Ver Pacientes</h5>
                                    <p>Lista de todos os pacientes</p>
                                </div>
                                <i class="fa fa-chevron-right quick-action-arrow"></i>
                            </a>

                            <a href="faturas.php" class="quick-action-item">
                                <div class="quick-action-icon icon-orange">
                                    <i class="fa fa-file-text"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h5>Ver Faturas</h5>
                                    <p>Consultar faturas criadas</p>
                                </div>
                                <i class="fa fa-chevron-right quick-action-arrow"></i>
                            </a>

                            <a href="caixa.php" class="quick-action-item">
                                <div class="quick-action-icon icon-teal">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h5>Caixa do Dia</h5>
                                    <p>Relatório financeiro diário</p>
                                </div>
                                <i class="fa fa-chevron-right quick-action-arrow"></i>
                            </a>

                            <a href="nova_empresa.php" class="quick-action-item">
                                <div class="quick-action-icon icon-pink">
                                    <i class="fa fa-building"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h5>Nova Empresa</h5>
                                    <p>Cadastrar empresa/seguro</p>
                                </div>
                                <i class="fa fa-chevron-right quick-action-arrow"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Histórico de Caixa -->
                    <div class="dashboard-card">
                        <div class="card-header-modern">
                            <h4 class="card-title-modern">Histórico de Caixa</h4>
                            <a href="caixa.php" class="view-all-link">Ver detalhes <i class="fa fa-arrow-right"></i></a>
                        </div>
                        <div class="table-responsive">
                            <?php
                            // Verificar se a tabela caixa_recepcao existe
                            $check_caixa = "SHOW TABLES LIKE 'caixa_recepcao'";
                            $table_caixa = mysqli_query($db, $check_caixa);
                            $caixa_exists = ($table_caixa && mysqli_num_rows($table_caixa) > 0);
                            
                            if($caixa_exists):
                                // Buscar histórico de abertura e fechamento do caixa
                                $sql_historico = "SELECT c.*, 
                                                 u1.nome as usuario_abertura_nome,
                                                 u2.nome as usuario_fechamento_nome
                                                 FROM caixa_recepcao c
                                                 LEFT JOIN users u1 ON c.usuario_abertura = u1.id
                                                 LEFT JOIN users u2 ON c.usuario_fechamento = u2.id
                                                 ORDER BY c.data DESC, c.data_abertura DESC
                                                 LIMIT 10";
                                $rs_historico = mysqli_query($db, $sql_historico);
                                if($rs_historico && mysqli_num_rows($rs_historico) > 0):
                            ?>
                                <table class="table-modern">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Status</th>
                                            <th>Valor Inicial</th>
                                            <th>Total Entradas</th>
                                            <th>Saldo Final</th>
                                            <th>Usuário Abertura</th>
                                            <th>Usuário Fechamento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($hist = mysqli_fetch_array($rs_historico)): 
                                            $status_class = $hist['status'] == 'aberto' ? 'status-warning' : 'status-success';
                                            $status_text = $hist['status'] == 'aberto' ? 'Aberto' : 'Fechado';
                                            $data_formatada = date('d/m/Y', strtotime($hist['data']));
                                            $hora_abertura = $hist['data_abertura'] ? date('H:i', strtotime($hist['data_abertura'])) : '-';
                                            $hora_fechamento = $hist['data_fechamento'] ? date('H:i', strtotime($hist['data_fechamento'])) : '-';
                                        ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo $data_formatada; ?></strong><br>
                                                    <small class="text-muted">
                                                        <?php if($hora_abertura != '-'): ?>
                                                            Abertura: <?php echo $hora_abertura; ?>
                                                        <?php endif; ?>
                                                        <?php if($hora_fechamento != '-'): ?>
                                                            | Fechamento: <?php echo $hora_fechamento; ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </td>
                                                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                                <td><strong><?php echo number_format($hist['valor_inicial'], 2, ',', '.'); ?> MT</strong></td>
                                                <td><?php echo number_format($hist['total_entradas'], 2, ',', '.'); ?> MT</td>
                                                <td><strong><?php echo number_format($hist['saldo_final'] ?: ($hist['valor_inicial'] + $hist['total_entradas']), 2, ',', '.'); ?> MT</strong></td>
                                                <td>
                                                    <?php if($hist['usuario_abertura_nome']): ?>
                                                        <i class="fa fa-user text-primary"></i> <?php echo htmlspecialchars($hist['usuario_abertura_nome']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($hist['usuario_fechamento_nome']): ?>
                                                        <i class="fa fa-user text-danger"></i> <?php echo htmlspecialchars($hist['usuario_fechamento_nome']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fa fa-money"></i>
                                    <p>Nenhum registro de caixa encontrado</p>
                                </div>
                            <?php 
                                endif;
                            else:
                            ?>
                                <div class="empty-state">
                                    <i class="fa fa-money"></i>
                                    <p>Tabela de caixa não encontrada</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Resumo Estatístico -->
                    <div class="dashboard-card">
                        <div class="card-header-modern">
                            <h4 class="card-title-modern">Resumo Geral</h4>
                        </div>
                        <div class="summary-stats">
                            <div class="summary-item">
                                <div class="summary-icon icon-purple-light">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="summary-content">
                                    <h4><?php echo $stats['total_pacientes']; ?></h4>
                                    <p>Total de Pacientes</p>
                                </div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-icon icon-green-light">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                                <div class="summary-content">
                                    <h4><?php echo $stats['pagas_hoje']; ?></h4>
                                    <p>Faturas Pagas Hoje</p>
                                </div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-icon icon-orange-light">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                                <div class="summary-content">
                                    <h4><?php echo $stats['pendentes']; ?></h4>
                                    <p>Aguardando Pagamento</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuração de Ano Fiscal -->
                <div class="dashboard-card" id="ano-fiscal">
                    <div class="card-header-modern">
                        <h4 class="card-title-modern">Ano Fiscal</h4>
                        <span class="card-subtitle-modern">Atualize o ano fiscal para criar faturas</span>
                    </div>
                    <div class="ano-fiscal-section">
                        <?php
                        // Buscar ano fiscal atual
                        $sql_serie = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
                        $rs_serie = mysqli_query($db, $sql_serie);
                        $ano_atual = date('Y');
                        $serie_atual = $ano_atual;
                        if($rs_serie && mysqli_num_rows($rs_serie) > 0) {
                            $dados_serie = mysqli_fetch_array($rs_serie);
                            $serie_atual = $dados_serie['serie'] ?? $ano_atual;
                        }
                        $ano_corresponde = ($serie_atual == $ano_atual);
                        ?>
                        <div class="ano-fiscal-info <?php echo $ano_corresponde ? 'ano-ok' : 'ano-alert'; ?>">
                            <div class="ano-fiscal-status">
                                <i class="fa <?php echo $ano_corresponde ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                                <div>
                                    <h5>Ano Fiscal Atual: <strong><?php echo $serie_atual; ?></strong></h5>
                                    <p>Ano do Sistema: <strong><?php echo $ano_atual; ?></strong></p>
                                    <?php if(!$ano_corresponde): ?>
                                        <p class="alert-message">
                                            <i class="fa fa-warning"></i> 
                                            O ano fiscal não corresponde ao ano atual. Atualize para criar faturas.
                                        </p>
                                    <?php else: ?>
                                        <p class="success-message">
                                            <i class="fa fa-check"></i> 
                                            Ano fiscal configurado corretamente.
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="daos/atualizar_ano_fiscal.php" class="ano-fiscal-form">
                            <div class="form-row-ano">
                                <div class="form-group-ano">
                                    <label>Novo Ano Fiscal <span class="text-danger">*</span></label>
                                    <input class="form-control" type="number" name="ano" value="<?php echo $ano_atual; ?>" min="2020" max="2100" required>
                                    <small class="form-text">Digite o ano fiscal que deseja configurar</small>
                                </div>
                                <div class="form-group-ano">
                                    <label>&nbsp;</label>
                                    <button class="btn-dashboard-primary" type="submit" name="btn">
                                        <i class="fa fa-save"></i> Atualizar Ano Fiscal
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="ano-fiscal-history">
                            <h6>Histórico de Anos Fiscais</h6>
                            <div class="history-list">
                                <?php
                                $sql_historico = "SELECT * FROM serie_factura ORDER BY id DESC LIMIT 5";
                                $rs_historico = mysqli_query($db, $sql_historico);
                                if($rs_historico && mysqli_num_rows($rs_historico) > 0):
                                    while($hist = mysqli_fetch_array($rs_historico)):
                                ?>
                                    <div class="history-item <?php echo $hist['ano_fiscal'] == $serie_atual ? 'active' : ''; ?>">
                                        <i class="fa fa-calendar"></i>
                                        <span><?php echo $hist['ano_fiscal']; ?></span>
                                        <?php if($hist['ano_fiscal'] == $serie_atual): ?>
                                            <span class="badge-active">Atual</span>
                                        <?php endif; ?>
                                    </div>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <p class="no-history">Nenhum ano fiscal registrado</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faturas Recentes -->
                <div class="dashboard-card">
                    <div class="card-header-modern">
                        <h4 class="card-title-modern">Faturas Recentes</h4>
                        <a href="faturas.php" class="view-all-link">Ver todas <i class="fa fa-arrow-right"></i></a>
                    </div>
                    <div class="table-responsive">
                        <?php
                        if($factura_recepcao_exists) {
                            $sql_recentes = "SELECT f.*, p.nome, p.apelido,
                                            COALESCE((SELECT SUM(valor_pago) FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id), 0) as total_pago
                                            FROM factura_recepcao f 
                                            INNER JOIN pacientes p ON f.paciente = p.id 
                                            ORDER BY f.data DESC 
                                            LIMIT 5";
                        } else {
                            $sql_recentes = "SELECT f.*, p.nome, p.apelido 
                                            FROM faturas_atendimento f 
                                            INNER JOIN pacientes p ON f.paciente_id = p.id 
                                            ORDER BY f.data_criacao DESC 
                                            LIMIT 5";
                        }
                        $rs_recentes = mysqli_query($db, $sql_recentes);
                        if($rs_recentes && mysqli_num_rows($rs_recentes) > 0):
                        ?>
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Nº Fatura</th>
                                        <th>Paciente</th>
                                        <th>Data</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($recente = mysqli_fetch_array($rs_recentes)): 
                                        if($factura_recepcao_exists) {
                                            $total_pago = floatval($recente['total_pago']);
                                            $valor_total = floatval($recente['valor']);
                                            if($total_pago >= $valor_total) {
                                                $status_class = 'status-success';
                                                $status_text = 'Paga';
                                            } elseif($total_pago > 0) {
                                                $status_class = 'status-info';
                                                $status_text = 'Parcial';
                                            } else {
                                                $status_class = 'status-warning';
                                                $status_text = 'Pendente';
                                            }
                                            $numero_fatura = "FA#" . $recente['serie'] . "/" . $recente['n_doc'];
                                            $data_fatura = $recente['dataa'];
                                            $valor_fatura = $recente['valor'];
                                        } else {
                                            $status_class = $recente['status'] == 'paga' ? 'status-success' : 
                                                           ($recente['status'] == 'pendente' ? 'status-warning' : 'status-danger');
                                            $status_text = $recente['status'] == 'paga' ? 'Paga' : 
                                                          ($recente['status'] == 'pendente' ? 'Pendente' : 'Cancelada');
                                            $numero_fatura = $recente['numero_fatura'];
                                            $data_fatura = $recente['data_atendimento'];
                                            $valor_fatura = $recente['total'];
                                        }
                                    ?>
                                        <tr>
                                            <td><strong><?php echo $numero_fatura; ?></strong></td>
                                            <td><?php echo $recente['nome'] . ' ' . $recente['apelido']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($data_fatura)); ?></td>
                                            <td><strong><?php echo number_format($valor_fatura, 2, ',', '.'); ?> MT</strong></td>
                                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                            <td>
                                                <a href="detalhes_fatura.php?id=<?php echo $recente['id']; ?>" class="btn-action" title="Ver detalhes">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa fa-file-text-o"></i>
                                <p>Nenhuma fatura registrada ainda</p>
                                <a href="fa_recepcao.php" target="_blank" class="btn-dashboard-primary">Criar primeira fatura</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
