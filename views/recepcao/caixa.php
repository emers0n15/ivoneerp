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

$data_hoje = date('Y-m-d');

// Verificar se já existe caixa aberto para hoje
$caixa = null;
$sql_caixa = "SELECT * FROM caixa_recepcao WHERE data = '$data_hoje'";
$rs_caixa = mysqli_query($db, $sql_caixa);
if($rs_caixa) {
	$caixa = mysqli_fetch_array($rs_caixa);
}

// Se não existe, criar
if(!$caixa && $rs_caixa){
	$sql_criar = "INSERT INTO caixa_recepcao (data, status, usuario_abertura, data_abertura) VALUES ('$data_hoje', 'aberto', " . $_SESSION['idUsuario'] . ", NOW())";
	if(mysqli_query($db, $sql_criar)) {
		$rs_caixa = mysqli_query($db, $sql_caixa);
		if($rs_caixa) {
			$caixa = mysqli_fetch_array($rs_caixa);
		}
	}
}

// Se ainda não existe, criar valores padrão
if(!$caixa) {
	$caixa = array(
		'valor_inicial' => 0,
		'total_entradas' => 0,
		'total_dinheiro' => 0,
		'total_mpesa' => 0,
		'total_emola' => 0,
		'total_pos' => 0,
		'total_saidas' => 0,
		'saldo_final' => 0
	);
}

// ========== DADOS GERAIS (TODOS OS DIAS) ==========
// Estatísticas gerais do caixa
$stats_gerais = array(
	'total_dias' => 0,
	'total_geral_recebido' => 0,
	'total_geral_pendente' => 0,
	'total_faturas_geral' => 0,
	'media_diaria' => 0
);

// Total de dias com caixa
$sql_total_dias = "SELECT COUNT(DISTINCT data) as total FROM caixa_recepcao";
$rs_total_dias = mysqli_query($db, $sql_total_dias);
if($rs_total_dias) {
	$total_dias_data = mysqli_fetch_array($rs_total_dias);
	$stats_gerais['total_dias'] = $total_dias_data ? intval($total_dias_data['total']) : 0;
}

// Total geral recebido (todos os pagamentos)
$sql_total_geral = "SELECT COALESCE(SUM(valor_pago), 0) as total FROM pagamentos_recepcao";
$rs_total_geral = mysqli_query($db, $sql_total_geral);
if($rs_total_geral) {
	$total_geral_data = mysqli_fetch_array($rs_total_geral);
	$stats_gerais['total_geral_recebido'] = $total_geral_data ? floatval($total_geral_data['total']) : 0;
}

// Verificar se existe factura_recepcao (novo sistema)
$check_table_new = "SHOW TABLES LIKE 'factura_recepcao'";
$table_new_exists = mysqli_query($db, $check_table_new);
$use_new_table = ($table_new_exists && mysqli_num_rows($table_new_exists) > 0);

// Total geral de faturas (considerando novo e antigo sistema)
$total_faturas_new = 0;
$total_faturas_old = 0;

if($use_new_table) {
	$sql_faturas_new = "SELECT COUNT(*) as total FROM factura_recepcao";
	$rs_faturas_new = mysqli_query($db, $sql_faturas_new);
	if($rs_faturas_new) {
		$faturas_new_data = mysqli_fetch_array($rs_faturas_new);
		$total_faturas_new = $faturas_new_data ? intval($faturas_new_data['total']) : 0;
	}
}

$sql_faturas_old = "SELECT COUNT(*) as total FROM faturas_atendimento";
$rs_faturas_old = mysqli_query($db, $sql_faturas_old);
if($rs_faturas_old) {
	$faturas_old_data = mysqli_fetch_array($rs_faturas_old);
	$total_faturas_old = $faturas_old_data ? intval($faturas_old_data['total']) : 0;
}

$stats_gerais['total_faturas_geral'] = $total_faturas_new + $total_faturas_old;

// Média diária
if($stats_gerais['total_dias'] > 0) {
	$stats_gerais['media_diaria'] = $stats_gerais['total_geral_recebido'] / $stats_gerais['total_dias'];
}

// Métodos de pagamento gerais (todos os dias)
$metodos_gerais = array();
$sql_metodos_gerais = "SELECT 
    metodo_pagamento,
    COALESCE(SUM(valor_pago), 0) as total
    FROM pagamentos_recepcao
    GROUP BY metodo_pagamento
    ORDER BY metodo_pagamento";
$rs_metodos_gerais = mysqli_query($db, $sql_metodos_gerais);
if($rs_metodos_gerais && mysqli_num_rows($rs_metodos_gerais) > 0) {
	while($metodo_geral = mysqli_fetch_array($rs_metodos_gerais)){
		$metodos_gerais[$metodo_geral['metodo_pagamento']] = floatval($metodo_geral['total']);
	}
}

// Pagamentos por dia (últimos 30 dias)
$pagamentos_por_dia = array();
$sql_pagamentos_dia = "SELECT 
    DATE(data_pagamento) as data,
    COALESCE(SUM(valor_pago), 0) as total
    FROM pagamentos_recepcao
    WHERE data_pagamento >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(data_pagamento)
    ORDER BY data";
$rs_pagamentos_dia = mysqli_query($db, $sql_pagamentos_dia);
if($rs_pagamentos_dia && mysqli_num_rows($rs_pagamentos_dia) > 0) {
	while($dia_data = mysqli_fetch_array($rs_pagamentos_dia)){
		$pagamentos_por_dia[$dia_data['data']] = floatval($dia_data['total']);
	}
}

// Status das faturas gerais (considerando novo sistema com NC, ND, DV)
$status_faturas_gerais = array(
	'paga' => array('quantidade' => 0, 'valor' => 0),
	'pendente' => array('quantidade' => 0, 'valor' => 0),
	'parcial' => array('quantidade' => 0, 'valor' => 0),
	'cancelada' => array('quantidade' => 0, 'valor' => 0),
	'devolvida' => array('quantidade' => 0, 'valor' => 0)
);

if($use_new_table) {
	// Usar novo sistema - calcular status dinamicamente
	$sql_faturas_new = "SELECT id, valor FROM factura_recepcao";
	$rs_faturas_new = mysqli_query($db, $sql_faturas_new);
	if($rs_faturas_new) {
		while($fatura = mysqli_fetch_array($rs_faturas_new)) {
			$fatura_id = intval($fatura['id']);
			$valor_total = floatval($fatura['valor']);
			
			// Total pago
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
			
			// Totais de NC, ND, DV
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
			
			$valor_disponivel = $valor_total + $total_nd - $total_nc - $total_dv;
			
			// Determinar status
			if($total_dv > 0 && $total_dv >= $valor_total) {
				$status_faturas_gerais['devolvida']['quantidade']++;
				$status_faturas_gerais['devolvida']['valor'] += $valor_total;
			} elseif($total_pago >= $valor_disponivel) {
				$status_faturas_gerais['paga']['quantidade']++;
				$status_faturas_gerais['paga']['valor'] += $valor_total;
			} elseif($total_pago > 0) {
				$status_faturas_gerais['parcial']['quantidade']++;
				$status_faturas_gerais['parcial']['valor'] += $valor_total;
			} else {
				$status_faturas_gerais['pendente']['quantidade']++;
				$status_faturas_gerais['pendente']['valor'] += $valor_total;
			}
		}
	}
}

// Adicionar faturas do sistema antigo
$sql_status_gerais = "SELECT 
    status,
    COUNT(*) as quantidade,
    COALESCE(SUM(total), 0) as valor_total
    FROM faturas_atendimento
    GROUP BY status";
$rs_status_gerais = mysqli_query($db, $sql_status_gerais);
if($rs_status_gerais && mysqli_num_rows($rs_status_gerais) > 0) {
	while($status_geral_data = mysqli_fetch_array($rs_status_gerais)){
		$status = $status_geral_data['status'];
		if(isset($status_faturas_gerais[$status])) {
			$status_faturas_gerais[$status]['quantidade'] += intval($status_geral_data['quantidade']);
			$status_faturas_gerais[$status]['valor'] += floatval($status_geral_data['valor_total']);
		}
	}
}

// Calcular totais do dia (considerando novo e antigo sistema)
$totais = array('total_faturas' => 0, 'total_recebido' => 0, 'total_pendente' => 0, 'total_devolvido' => 0);

if($use_new_table) {
	// Contar faturas do novo sistema criadas hoje
	$sql_count_new = "SELECT COUNT(*) as total FROM factura_recepcao WHERE DATE(data) = '$data_hoje'";
	$rs_count_new = mysqli_query($db, $sql_count_new);
	if($rs_count_new) {
		$count_data = mysqli_fetch_array($rs_count_new);
		$totais['total_faturas'] += intval($count_data['total']);
	}
	
	// Calcular recebido e pendente do novo sistema
	$sql_faturas_dia = "SELECT id, valor FROM factura_recepcao WHERE DATE(data) = '$data_hoje'";
	$rs_faturas_dia = mysqli_query($db, $sql_faturas_dia);
	if($rs_faturas_dia) {
		while($fatura = mysqli_fetch_array($rs_faturas_dia)) {
			$fatura_id = intval($fatura['id']);
			$valor_total = floatval($fatura['valor']);
			
			// Total pago
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
			
			// Totais de NC, ND, DV
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
			
			$valor_disponivel = $valor_total + $total_nd - $total_nc - $total_dv;
			
			if($total_dv > 0 && $total_dv >= $valor_total) {
				$totais['total_devolvido'] += $valor_total;
			} elseif($total_pago >= $valor_disponivel) {
				$totais['total_recebido'] += $valor_total;
			} else {
				$totais['total_pendente'] += $valor_disponivel - $total_pago;
			}
		}
	}
}

// Adicionar faturas do sistema antigo
$sql_totais = "SELECT 
    COUNT(*) as total_faturas,
    COALESCE(SUM(CASE WHEN status = 'paga' THEN total ELSE 0 END), 0) as total_recebido,
    COALESCE(SUM(CASE WHEN status = 'pendente' THEN total ELSE 0 END), 0) as total_pendente
    FROM faturas_atendimento 
    WHERE DATE(data_atendimento) = '$data_hoje'";
$rs_totais = mysqli_query($db, $sql_totais);
if($rs_totais) {
	$totais_old = mysqli_fetch_array($rs_totais);
	if($totais_old) {
		$totais['total_faturas'] += intval($totais_old['total_faturas']);
		$totais['total_recebido'] += floatval($totais_old['total_recebido']);
		$totais['total_pendente'] += floatval($totais_old['total_pendente']);
	}
}

// Totais por método de pagamento - Filtrar pela data do pagamento, não do atendimento
$metodos = array();
$sql_metodos = "SELECT 
    metodo_pagamento,
    COALESCE(SUM(valor_pago), 0) as total
    FROM pagamentos_recepcao
    WHERE DATE(data_pagamento) = '$data_hoje'
    GROUP BY metodo_pagamento
    ORDER BY metodo_pagamento";
$rs_metodos = mysqli_query($db, $sql_metodos);
if($rs_metodos && mysqli_num_rows($rs_metodos) > 0) {
	while($metodo = mysqli_fetch_array($rs_metodos)){
		$metodos[$metodo['metodo_pagamento']] = floatval($metodo['total']);
	}
}

// Dados para gráfico de linha - Pagamentos por hora do dia
$pagamentos_por_hora = array();
for($h = 0; $h < 24; $h++) {
	$pagamentos_por_hora[$h] = 0;
}
$sql_horas = "SELECT 
    HOUR(data_pagamento) as hora,
    COALESCE(SUM(valor_pago), 0) as total
    FROM pagamentos_recepcao
    WHERE DATE(data_pagamento) = '$data_hoje'
    GROUP BY HOUR(data_pagamento)
    ORDER BY hora";
$rs_horas = mysqli_query($db, $sql_horas);
if($rs_horas && mysqli_num_rows($rs_horas) > 0) {
	while($hora_data = mysqli_fetch_array($rs_horas)){
		$pagamentos_por_hora[intval($hora_data['hora'])] = floatval($hora_data['total']);
	}
}

// Dados para gráfico de barras - Comparação de status (considerando novo sistema)
$status_faturas = array(
	'paga' => array('quantidade' => 0, 'valor' => 0),
	'pendente' => array('quantidade' => 0, 'valor' => 0),
	'parcial' => array('quantidade' => 0, 'valor' => 0),
	'cancelada' => array('quantidade' => 0, 'valor' => 0),
	'devolvida' => array('quantidade' => 0, 'valor' => 0)
);

if($use_new_table) {
	// Calcular status do novo sistema para hoje
	$sql_faturas_dia = "SELECT id, valor FROM factura_recepcao WHERE DATE(data) = '$data_hoje'";
	$rs_faturas_dia = mysqli_query($db, $sql_faturas_dia);
	if($rs_faturas_dia) {
		while($fatura = mysqli_fetch_array($rs_faturas_dia)) {
			$fatura_id = intval($fatura['id']);
			$valor_total = floatval($fatura['valor']);
			
			// Total pago
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
			
			// Totais de NC, ND, DV
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
			
			$valor_disponivel = $valor_total + $total_nd - $total_nc - $total_dv;
			
			// Determinar status
			if($total_dv > 0 && $total_dv >= $valor_total) {
				$status_faturas['devolvida']['quantidade']++;
				$status_faturas['devolvida']['valor'] += $valor_total;
			} elseif($total_pago >= $valor_disponivel) {
				$status_faturas['paga']['quantidade']++;
				$status_faturas['paga']['valor'] += $valor_total;
			} elseif($total_pago > 0) {
				$status_faturas['parcial']['quantidade']++;
				$status_faturas['parcial']['valor'] += $valor_total;
			} else {
				$status_faturas['pendente']['quantidade']++;
				$status_faturas['pendente']['valor'] += $valor_total;
			}
		}
	}
}

// Adicionar faturas do sistema antigo
$sql_status = "SELECT 
    status,
    COUNT(*) as quantidade,
    COALESCE(SUM(total), 0) as valor_total
    FROM faturas_atendimento
    WHERE DATE(data_atendimento) = '$data_hoje'
    GROUP BY status";
$rs_status = mysqli_query($db, $sql_status);
if($rs_status && mysqli_num_rows($rs_status) > 0) {
	while($status_data = mysqli_fetch_array($rs_status)){
		$status = $status_data['status'];
		if(isset($status_faturas[$status])) {
			$status_faturas[$status]['quantidade'] += intval($status_data['quantidade']);
			$status_faturas[$status]['valor'] += floatval($status_data['valor_total']);
		}
	}
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
        .card canvas {
            max-height: 300px;
        }
        @media (max-width: 768px) {
            .card canvas {
                max-height: 250px;
            }
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
                        <h4 class="page-title">Caixa</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="dashboard.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                
                <!-- Abas -->
                <div class="row mb-3">
                    <div class="col-12">
                        <ul class="nav nav-tabs" id="caixaTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="caixa-dia-tab" data-toggle="tab" href="#caixa-dia" role="tab" aria-controls="caixa-dia" aria-selected="true">
                                    <i class="fa fa-calendar-day"></i> Caixa do Dia
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="caixa-geral-tab" data-toggle="tab" href="#caixa-geral" role="tab" aria-controls="caixa-geral" aria-selected="false">
                                    <i class="fa fa-chart-line"></i> Caixa Geral
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="tab-content" id="caixaTabsContent">
                    <!-- Aba: Caixa do Dia -->
                    <div class="tab-pane fade show active" id="caixa-dia" role="tabpanel" aria-labelledby="caixa-dia-tab">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3><?php echo $totais['total_faturas']; ?></h3>
                                <p class="text-muted">Total de Faturas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="text-success"><?php echo number_format($totais['total_recebido'], 2, ',', '.'); ?> MT</h3>
                                <p class="text-muted">Total Recebido</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="text-warning"><?php echo number_format($totais['total_pendente'], 2, ',', '.'); ?> MT</h3>
                                <p class="text-muted">Total Pendente</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <?php 
                                // Calcular saídas (devoluções) do dia
                                $total_saidas_hoje = 0;
                                $check_dv = "SHOW TABLES LIKE 'devolucao_recepcao'";
                                $table_dv = mysqli_query($db, $check_dv);
                                if($table_dv && mysqli_num_rows($table_dv) > 0) {
                                    $sql_saidas = "SELECT COALESCE(SUM(valor), 0) as total 
                                                   FROM devolucao_recepcao 
                                                   WHERE DATE(data) = '$data_hoje' 
                                                   AND metodo_reembolso = 'dinheiro'";
                                    $rs_saidas = mysqli_query($db, $sql_saidas);
                                    if($rs_saidas) {
                                        $saidas_data = mysqli_fetch_array($rs_saidas);
                                        $total_saidas_hoje = floatval($saidas_data['total']);
                                    }
                                }
                                $saldo_final = $caixa['valor_inicial'] + $totais['total_recebido'] - $total_saidas_hoje;
                                ?>
                                <h3 class="text-info"><?php echo number_format($saldo_final, 2, ',', '.'); ?> MT</h3>
                                <p class="text-muted">Saldo Final</p>
                                <?php if($total_saidas_hoje > 0): ?>
                                    <small class="text-danger">(-<?php echo number_format($total_saidas_hoje, 2, ',', '.'); ?> MT em devoluções)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Pagamentos por Método</h4>
                            </div>
                            <div class="card-body">
                                <?php 
                                $total_metodos = 0;
                                foreach($metodos as $valor) {
                                    $total_metodos += $valor;
                                }
                                if($total_metodos == 0): ?>
                                    <p class="text-muted text-center">Nenhum pagamento registrado hoje</p>
                                <?php else: ?>
                                    <table class="table">
                                        <tr>
                                            <td><strong>Dinheiro</strong></td>
                                            <td class="text-right"><?php echo number_format(isset($metodos['dinheiro']) ? $metodos['dinheiro'] : 0, 2, ',', '.'); ?> MT</td>
                                        </tr>
                                        <tr>
                                            <td><strong>M-Pesa</strong></td>
                                            <td class="text-right"><?php echo number_format(isset($metodos['m-pesa']) ? $metodos['m-pesa'] : 0, 2, ',', '.'); ?> MT</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Emola</strong></td>
                                            <td class="text-right"><?php echo number_format(isset($metodos['emola']) ? $metodos['emola'] : 0, 2, ',', '.'); ?> MT</td>
                                        </tr>
                                        <tr>
                                            <td><strong>POS</strong></td>
                                            <td class="text-right"><?php echo number_format(isset($metodos['pos']) ? $metodos['pos'] : 0, 2, ',', '.'); ?> MT</td>
                                        </tr>
                                        <?php if(isset($metodos['transferencia'])): ?>
                                        <tr>
                                            <td><strong>Transferência</strong></td>
                                            <td class="text-right"><?php echo number_format($metodos['transferencia'], 2, ',', '.'); ?> MT</td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if(isset($metodos['fatura_empresa'])): ?>
                                        <tr>
                                            <td><strong>Fatura Empresa</strong></td>
                                            <td class="text-right"><?php echo number_format($metodos['fatura_empresa'], 2, ',', '.'); ?> MT</td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr class="table-info">
                                            <td><strong>TOTAL</strong></td>
                                            <td class="text-right"><strong><?php echo number_format($total_metodos, 2, ',', '.'); ?> MT</strong></td>
                                        </tr>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Faturas do Dia</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nº</th>
                                                <th>Paciente</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $faturas_listadas = 0;
                                            
                                            // Listar faturas do novo sistema
                                            if($use_new_table) {
                                                $sql_faturas_new = "SELECT f.*, p.nome, p.apelido 
                                                                   FROM factura_recepcao f 
                                                                   INNER JOIN pacientes p ON f.paciente = p.id 
                                                                   WHERE DATE(f.data) = '$data_hoje'
                                                                   ORDER BY f.data DESC
                                                                   LIMIT 10";
                                                $rs_faturas_new = mysqli_query($db, $sql_faturas_new);
                                                if($rs_faturas_new):
                                                    while($fatura = mysqli_fetch_array($rs_faturas_new)):
                                                        $fatura_id = intval($fatura['id']);
                                                        $valor_total = floatval($fatura['valor']);
                                                        
                                                        // Calcular status
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
                                                        
                                                        $valor_disponivel = $valor_total + $total_nd - $total_nc - $total_dv;
                                                        
                                                        if($total_dv > 0 && $total_dv >= $valor_total) {
                                                            $status_text = 'Devolvida';
                                                            $status_class = 'badge-secondary';
                                                        } elseif($total_pago >= $valor_disponivel) {
                                                            $status_text = 'Paga';
                                                            $status_class = 'badge-success';
                                                        } elseif($total_pago > 0) {
                                                            $status_text = 'Parcial';
                                                            $status_class = 'badge-info';
                                                        } else {
                                                            $status_text = 'Pendente';
                                                            $status_class = 'badge-warning';
                                                        }
                                                        
                                                        $numero_fatura = "FA#" . $fatura['serie'] . "/" . $fatura['n_doc'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $numero_fatura; ?></td>
                                                    <td><?php echo $fatura['nome'] . ' ' . $fatura['apelido']; ?></td>
                                                    <td><?php echo number_format($valor_total, 2, ',', '.'); ?> MT</td>
                                                    <td><span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                                </tr>
                                            <?php 
                                                        $faturas_listadas++;
                                                    endwhile;
                                                endif;
                                            }
                                            
                                            // Listar faturas do sistema antigo se ainda houver espaço
                                            if($faturas_listadas < 10) {
                                                $limite_restante = 10 - $faturas_listadas;
                                                $sql_faturas_old = "SELECT f.*, p.nome, p.apelido 
                                                                   FROM faturas_atendimento f 
                                                                   INNER JOIN pacientes p ON f.paciente_id = p.id 
                                                                   WHERE DATE(f.data_atendimento) = '$data_hoje'
                                                                   ORDER BY f.data_criacao DESC
                                                                   LIMIT $limite_restante";
                                                $rs_faturas_old = mysqli_query($db, $sql_faturas_old);
                                                if($rs_faturas_old):
                                                    while($fatura_dia = mysqli_fetch_array($rs_faturas_old)):
                                                        $status_class = $fatura_dia['status'] == 'paga' ? 'badge-success' : ($fatura_dia['status'] == 'pendente' ? 'badge-warning' : 'badge-danger');
                                            ?>
                                                <tr>
                                                    <td><?php echo $fatura_dia['numero_fatura']; ?></td>
                                                    <td><?php echo $fatura_dia['nome'] . ' ' . $fatura_dia['apelido']; ?></td>
                                                    <td><?php echo number_format($fatura_dia['total'], 2, ',', '.'); ?> MT</td>
                                                    <td><span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($fatura_dia['status']); ?></span></td>
                                                </tr>
                                            <?php 
                                                    endwhile;
                                                endif;
                                            }
                                            
                                            if($faturas_listadas == 0):
                                            ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Nenhuma fatura encontrada</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seção de Gráficos Estatísticos -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Distribuição por Método de Pagamento</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoPizzaMetodos" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Pagamentos ao Longo do Dia</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoLinhaHoras" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Status das Faturas - Comparação</h4>
                            </div>
                            <div class="card-body" style="position: relative; height: 350px;">
                                <canvas id="graficoBarrasStatus"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                    
                    <!-- Aba: Caixa Geral -->
                    <div class="tab-pane fade" id="caixa-geral" role="tabpanel" aria-labelledby="caixa-geral-tab">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h3><?php echo $stats_gerais['total_dias']; ?></h3>
                                        <p class="text-muted">Total de Dias</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h3 class="text-success"><?php echo number_format($stats_gerais['total_geral_recebido'], 2, ',', '.'); ?> MT</h3>
                                        <p class="text-muted">Total Geral Recebido</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h3><?php echo $stats_gerais['total_faturas_geral']; ?></h3>
                                        <p class="text-muted">Total de Faturas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h3 class="text-info"><?php echo number_format($stats_gerais['media_diaria'], 2, ',', '.'); ?> MT</h3>
                                        <p class="text-muted">Média Diária</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Métodos de Pagamento - Geral</h4>
                                    </div>
                                    <div class="card-body">
                                        <?php 
                                        $total_metodos_gerais = 0;
                                        foreach($metodos_gerais as $valor) {
                                            $total_metodos_gerais += $valor;
                                        }
                                        if($total_metodos_gerais == 0): ?>
                                            <p class="text-muted text-center">Nenhum pagamento registrado</p>
                                        <?php else: ?>
                                            <table class="table">
                                                <tr>
                                                    <td><strong>Dinheiro</strong></td>
                                                    <td class="text-right"><?php echo number_format(isset($metodos_gerais['dinheiro']) ? $metodos_gerais['dinheiro'] : 0, 2, ',', '.'); ?> MT</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>M-Pesa</strong></td>
                                                    <td class="text-right"><?php echo number_format(isset($metodos_gerais['m-pesa']) ? $metodos_gerais['m-pesa'] : 0, 2, ',', '.'); ?> MT</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Emola</strong></td>
                                                    <td class="text-right"><?php echo number_format(isset($metodos_gerais['emola']) ? $metodos_gerais['emola'] : 0, 2, ',', '.'); ?> MT</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>POS</strong></td>
                                                    <td class="text-right"><?php echo number_format(isset($metodos_gerais['pos']) ? $metodos_gerais['pos'] : 0, 2, ',', '.'); ?> MT</td>
                                                </tr>
                                                <?php if(isset($metodos_gerais['transferencia'])): ?>
                                                <tr>
                                                    <td><strong>Transferência</strong></td>
                                                    <td class="text-right"><?php echo number_format($metodos_gerais['transferencia'], 2, ',', '.'); ?> MT</td>
                                                </tr>
                                                <?php endif; ?>
                                                <tr class="table-info">
                                                    <td><strong>TOTAL</strong></td>
                                                    <td class="text-right"><strong><?php echo number_format($total_metodos_gerais, 2, ',', '.'); ?> MT</strong></td>
                                                </tr>
                                            </table>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Status das Faturas - Geral</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <tr>
                                                <td><strong>Pagas</strong></td>
                                                <td class="text-right"><?php echo $status_faturas_gerais['paga']['quantidade']; ?> faturas</td>
                                                <td class="text-right"><?php echo number_format($status_faturas_gerais['paga']['valor'], 2, ',', '.'); ?> MT</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pendentes</strong></td>
                                                <td class="text-right"><?php echo $status_faturas_gerais['pendente']['quantidade']; ?> faturas</td>
                                                <td class="text-right"><?php echo number_format($status_faturas_gerais['pendente']['valor'], 2, ',', '.'); ?> MT</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Parciais</strong></td>
                                                <td class="text-right"><?php echo $status_faturas_gerais['parcial']['quantidade']; ?> faturas</td>
                                                <td class="text-right"><?php echo number_format($status_faturas_gerais['parcial']['valor'], 2, ',', '.'); ?> MT</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Canceladas</strong></td>
                                                <td class="text-right"><?php echo $status_faturas_gerais['cancelada']['quantidade']; ?> faturas</td>
                                                <td class="text-right"><?php echo number_format($status_faturas_gerais['cancelada']['valor'], 2, ',', '.'); ?> MT</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Devolvidas</strong></td>
                                                <td class="text-right"><?php echo $status_faturas_gerais['devolvida']['quantidade']; ?> faturas</td>
                                                <td class="text-right"><?php echo number_format($status_faturas_gerais['devolvida']['valor'], 2, ',', '.'); ?> MT</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gráficos Gerais -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Distribuição por Método - Geral</h4>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="graficoPizzaMetodosGeral" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Pagamentos por Dia (Últimos 30 dias)</h4>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="graficoLinhaDias" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Status das Faturas - Comparação Geral</h4>
                                    </div>
                                    <div class="card-body" style="position: relative; height: 350px;">
                                        <canvas id="graficoBarrasStatusGeral"></canvas>
                                    </div>
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
    
    <script>
        // Preparar dados para os gráficos
        var metodosPagamento = <?php echo json_encode($metodos); ?>;
        var pagamentosPorHora = <?php echo json_encode(array_values($pagamentos_por_hora)); ?>;
        var statusFaturas = <?php echo json_encode($status_faturas); ?>;
        
        // Cores para os gráficos
        var coresMetodos = {
            'dinheiro': '#28a745',
            'm-pesa': '#17a2b8',
            'emola': '#ffc107',
            'pos': '#dc3545',
            'transferencia': '#6f42c1',
            'fatura_empresa': '#fd7e14'
        };
        
        var coresStatus = {
            'paga': '#28a745',
            'pendente': '#ffc107',
            'parcial': '#17a2b8',
            'cancelada': '#dc3545',
            'devolvida': '#6c757d'
        };
        
        // Gráfico de Pizza - Métodos de Pagamento
        var ctxPizza = document.getElementById('graficoPizzaMetodos');
        if (ctxPizza) {
            var labelsPizza = [];
            var dadosPizza = [];
            var coresPizza = [];
            
            var nomesMetodos = {
                'dinheiro': 'Dinheiro',
                'm-pesa': 'M-Pesa',
                'emola': 'Emola',
                'pos': 'POS',
                'transferencia': 'Transferência',
                'fatura_empresa': 'Fatura Empresa'
            };
            
            for (var metodo in metodosPagamento) {
                if (metodosPagamento[metodo] > 0) {
                    labelsPizza.push(nomesMetodos[metodo] || metodo);
                    dadosPizza.push(metodosPagamento[metodo]);
                    coresPizza.push(coresMetodos[metodo] || '#6c757d');
                }
            }
            
            if (dadosPizza.length > 0) {
                new Chart(ctxPizza, {
                    type: 'pie',
                    data: {
                        labels: labelsPizza,
                        datasets: [{
                            data: dadosPizza,
                            backgroundColor: coresPizza,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.parsed || 0;
                                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        var percentage = ((value / total) * 100).toFixed(1);
                                        return label + ': ' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) + ' MT (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                ctxPizza.parentElement.innerHTML = '<p class="text-muted text-center p-4">Nenhum dado disponível para exibir</p>';
            }
        }
        
        // Gráfico de Linha - Pagamentos por Hora
        var ctxLinha = document.getElementById('graficoLinhaHoras');
        if (ctxLinha) {
            var labelsHoras = [];
            for (var h = 0; h < 24; h++) {
                labelsHoras.push(h + 'h');
            }
            
            var temDados = pagamentosPorHora.some(function(val) { return val > 0; });
            
            if (temDados) {
                new Chart(ctxLinha, {
                    type: 'line',
                    data: {
                        labels: labelsHoras,
                        datasets: [{
                            label: 'Valor Recebido (MT)',
                            data: pagamentosPorHora,
                            borderColor: '#3D5DFF',
                            backgroundColor: 'rgba(61, 93, 255, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#3D5DFF',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Valor: ' + context.parsed.y.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) + ' MT';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('pt-BR') + ' MT';
                                    }
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } else {
                ctxLinha.parentElement.innerHTML = '<p class="text-muted text-center p-4">Nenhum pagamento registrado hoje</p>';
            }
        }
        
        // Gráfico de Barras - Status das Faturas
        var ctxBarras = document.getElementById('graficoBarrasStatus');
        if (ctxBarras) {
            var labelsStatus = ['Pagas', 'Pendentes', 'Parciais', 'Canceladas', 'Devolvidas'];
            var dadosQuantidade = [];
            var dadosValor = [];
            var coresBarras = [];
            
            var ordemStatus = ['paga', 'pendente', 'parcial', 'cancelada', 'devolvida'];
            ordemStatus.forEach(function(status) {
                if (statusFaturas[status]) {
                    dadosQuantidade.push(statusFaturas[status].quantidade || 0);
                    dadosValor.push(statusFaturas[status].valor || 0);
                    coresBarras.push(coresStatus[status] || '#6c757d');
                } else {
                    dadosQuantidade.push(0);
                    dadosValor.push(0);
                    coresBarras.push(coresStatus[status] || '#6c757d');
                }
            });
            
            var temDadosStatus = dadosQuantidade.some(function(val) { return val > 0; }) || dadosValor.some(function(val) { return val > 0; });
            
            if (temDadosStatus) {
                // Criar gráfico de barras agrupadas
                new Chart(ctxBarras, {
                    type: 'bar',
                    data: {
                        labels: labelsStatus,
                        datasets: [
                            {
                                label: 'Quantidade de Faturas',
                                data: dadosQuantidade,
                                backgroundColor: coresBarras.map(function(cor) {
                                    return cor + 'CC'; // 80% de opacidade
                                }),
                                borderColor: coresBarras,
                                borderWidth: 2
                            },
                            {
                                label: 'Valor Total (MT)',
                                data: dadosValor,
                                backgroundColor: 'rgba(61, 93, 255, 0.6)',
                                borderColor: '#3D5DFF',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.parsed.y;
                                        if (context.datasetIndex === 0) {
                                            return label + ': ' + value + ' fatura(s)';
                                        } else {
                                            return label + ': ' + value.toLocaleString('pt-BR', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }) + ' MT';
                                        }
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        if (Number.isInteger(value)) {
                                            return value;
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Quantidade / Valor (MT)',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } else {
                ctxBarras.parentElement.innerHTML = '<p class="text-muted text-center p-4">Nenhuma fatura encontrada hoje</p>';
            }
        }
        
        // ========== GRÁFICOS GERAIS ==========
        var metodosPagamentoGeral = <?php echo json_encode($metodos_gerais); ?>;
        var pagamentosPorDia = <?php echo json_encode($pagamentos_por_dia); ?>;
        var statusFaturasGeral = <?php echo json_encode($status_faturas_gerais); ?>;
        
        // Gráfico de Pizza - Métodos de Pagamento Geral
        var ctxPizzaGeral = document.getElementById('graficoPizzaMetodosGeral');
        if (ctxPizzaGeral) {
            var labelsPizzaGeral = [];
            var dadosPizzaGeral = [];
            var coresPizzaGeral = [];
            
            var nomesMetodos = {
                'dinheiro': 'Dinheiro',
                'm-pesa': 'M-Pesa',
                'emola': 'Emola',
                'pos': 'POS',
                'transferencia': 'Transferência',
                'fatura_empresa': 'Fatura Empresa'
            };
            
            for (var metodo in metodosPagamentoGeral) {
                if (metodosPagamentoGeral[metodo] > 0) {
                    labelsPizzaGeral.push(nomesMetodos[metodo] || metodo);
                    dadosPizzaGeral.push(metodosPagamentoGeral[metodo]);
                    coresPizzaGeral.push(coresMetodos[metodo] || '#6c757d');
                }
            }
            
            if (dadosPizzaGeral.length > 0) {
                new Chart(ctxPizzaGeral, {
                    type: 'pie',
                    data: {
                        labels: labelsPizzaGeral,
                        datasets: [{
                            data: dadosPizzaGeral,
                            backgroundColor: coresPizzaGeral,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.parsed || 0;
                                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        var percentage = ((value / total) * 100).toFixed(1);
                                        return label + ': ' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) + ' MT (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                ctxPizzaGeral.parentElement.innerHTML = '<p class="text-muted text-center p-4">Nenhum dado disponível</p>';
            }
        }
        
        // Gráfico de Linha - Pagamentos por Dia
        var ctxLinhaDias = document.getElementById('graficoLinhaDias');
        if (ctxLinhaDias) {
            var labelsDias = [];
            var dadosDias = [];
            
            // Ordenar por data
            var diasOrdenados = Object.keys(pagamentosPorDia).sort();
            diasOrdenados.forEach(function(data) {
                labelsDias.push(new Date(data).toLocaleDateString('pt-BR', {day: '2-digit', month: '2-digit'}));
                dadosDias.push(pagamentosPorDia[data]);
            });
            
            var temDadosDias = dadosDias.some(function(val) { return val > 0; });
            
            if (temDadosDias) {
                new Chart(ctxLinhaDias, {
                    type: 'line',
                    data: {
                        labels: labelsDias,
                        datasets: [{
                            label: 'Valor Recebido (MT)',
                            data: dadosDias,
                            borderColor: '#3D5DFF',
                            backgroundColor: 'rgba(61, 93, 255, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: true, position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Valor: ' + context.parsed.y.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }) + ' MT';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('pt-BR') + ' MT';
                                    }
                                },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } else {
                ctxLinhaDias.parentElement.innerHTML = '<p class="text-muted text-center p-4">Nenhum pagamento registrado</p>';
            }
        }
        
        // Gráfico de Barras - Status das Faturas Geral
        var ctxBarrasGeral = document.getElementById('graficoBarrasStatusGeral');
        if (ctxBarrasGeral) {
            var labelsStatusGeral = ['Pagas', 'Pendentes', 'Parciais', 'Canceladas', 'Devolvidas'];
            var dadosQuantidadeGeral = [];
            var dadosValorGeral = [];
            var coresBarrasGeral = [];
            
            var ordemStatus = ['paga', 'pendente', 'parcial', 'cancelada', 'devolvida'];
            ordemStatus.forEach(function(status) {
                if (statusFaturasGeral[status]) {
                    dadosQuantidadeGeral.push(statusFaturasGeral[status].quantidade || 0);
                    dadosValorGeral.push(statusFaturasGeral[status].valor || 0);
                    coresBarrasGeral.push(coresStatus[status] || '#6c757d');
                } else {
                    dadosQuantidadeGeral.push(0);
                    dadosValorGeral.push(0);
                    coresBarrasGeral.push(coresStatus[status] || '#6c757d');
                }
            });
            
            var temDadosStatusGeral = dadosQuantidadeGeral.some(function(val) { return val > 0; }) || dadosValorGeral.some(function(val) { return val > 0; });
            
            if (temDadosStatusGeral) {
                new Chart(ctxBarrasGeral, {
                    type: 'bar',
                    data: {
                        labels: labelsStatusGeral,
                        datasets: [
                            {
                                label: 'Quantidade de Faturas',
                                data: dadosQuantidadeGeral,
                                backgroundColor: coresBarrasGeral.map(function(cor) { return cor + 'CC'; }),
                                borderColor: coresBarrasGeral,
                                borderWidth: 2
                            },
                            {
                                label: 'Valor Total (MT)',
                                data: dadosValorGeral,
                                backgroundColor: 'rgba(61, 93, 255, 0.6)',
                                borderColor: '#3D5DFF',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { padding: 15, font: { size: 12 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.parsed.y;
                                        if (context.datasetIndex === 0) {
                                            return label + ': ' + value + ' fatura(s)';
                                        } else {
                                            return label + ': ' + value.toLocaleString('pt-BR', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }) + ' MT';
                                        }
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        if (Number.isInteger(value)) {
                                            return value;
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Quantidade / Valor (MT)',
                                    font: { size: 12, weight: 'bold' }
                                },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } else {
                ctxBarrasGeral.parentElement.innerHTML = '<p class="text-muted text-center p-4">Nenhuma fatura encontrada</p>';
            }
        }
    </script>
</body>
</html>

