<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit;
}

if($_SESSION['categoriaUsuario'] != "recepcao"){
    header("location:../admin/");
    exit;
}

include '../../conexao/index.php';

// Verificar se a conexão foi estabelecida
if(!isset($db) || !$db){
    die('Erro ao conectar à base de dados.');
}

$tipo = isset($_GET['tipo']) ? strtolower(trim($_GET['tipo'])) : '';
$documento_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$auto_print = isset($_GET['print']);

if(!$tipo || $documento_id <= 0){
    die('Parâmetros inválidos. Tipo: ' . htmlspecialchars($tipo) . ', ID: ' . htmlspecialchars($documento_id));
}

function formatarMoeda($valor){
    return number_format(floatval($valor ?? 0), 2, ',', '.') . ' MT';
}

function formatarData($data){
    if(!$data || $data == '0000-00-00' || $data == '0000-00-00 00:00:00'){
        return '-';
    }
    try {
        return date('d/m/Y', strtotime($data));
    } catch(Exception $e){
        return '-';
    }
}

function obterSomatorio($db, $sql, $param){
    if(!$db || !$sql || !$param){
        return 0;
    }
    $stmt = mysqli_prepare($db, $sql);
    if(!$stmt){
        error_log("Erro ao preparar statement: " . mysqli_error($db));
        return 0;
    }
    mysqli_stmt_bind_param($stmt, "i", $param);
    if(!mysqli_stmt_execute($stmt)){
        error_log("Erro ao executar statement: " . mysqli_error($db));
        mysqli_stmt_close($stmt);
        return 0;
    }
    $rs = mysqli_stmt_get_result($stmt);
    if($rs && mysqli_num_rows($rs) > 0){
        $row = mysqli_fetch_array($rs);
        mysqli_stmt_close($stmt);
        return floatval($row['total'] ?? 0);
    }
    mysqli_stmt_close($stmt);
    return 0;
}

function tabelaExiste($db, $nome_tabela) {
    $check = "SHOW TABLES LIKE '$nome_tabela'";
    $result = mysqli_query($db, $check);
    return ($result && mysqli_num_rows($result) > 0);
}

$documento = null;
$itens = [];
$secundario = [];
$titulo = '';

switch($tipo){
    case 'fa':
        $titulo = 'Fatura de Atendimento';
        if(!tabelaExiste($db, 'factura_recepcao')){
            die('Tabela factura_recepcao não existe na base de dados.');
        }
        $sql = "SELECT f.*, 
                       p.nome AS paciente_nome, p.apelido AS paciente_apelido, p.numero_processo, p.contacto AS paciente_contacto,
                       e.nome AS empresa_nome, e.nuit AS empresa_nuit, e.contacto AS empresa_contacto, e.email AS empresa_email
                FROM factura_recepcao f
                LEFT JOIN pacientes p ON f.paciente = p.id
                LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
<<<<<<< HEAD
                LEFT JOIN users u ON f.usuario = u.id
=======
>>>>>>> 25a0cb3ed134b3fba392f117e5fda8254256a55b
                WHERE f.id = ?";
        
        $stmt = mysqli_prepare($db, $sql);
        if(!$stmt){
            error_log("Erro ao preparar query FA: " . mysqli_error($db));
            die('Erro ao preparar consulta: ' . htmlspecialchars(mysqli_error($db)));
        }
        mysqli_stmt_bind_param($stmt, "i", $documento_id);
        if(!mysqli_stmt_execute($stmt)){
            $error = mysqli_error($db);
            error_log("Erro ao executar query FA: " . $error);
            mysqli_stmt_close($stmt);
            die('Erro ao executar consulta: ' . htmlspecialchars($error));
        }
        $rs = mysqli_stmt_get_result($stmt);
        if(!$rs){
            $error = mysqli_error($db);
            error_log("Erro ao obter resultado FA: " . $error);
            mysqli_stmt_close($stmt);
            die('Erro ao obter resultado: ' . htmlspecialchars($error));
        }
        $documento = mysqli_fetch_assoc($rs);
        mysqli_stmt_close($stmt);
        if($documento){
            $documento['numero_formatado'] = "FA#" . $documento['serie'] . "/" . str_pad($documento['n_doc'], 6, '0', STR_PAD_LEFT);
            $documento['data_formatada'] = formatarData($documento['dataa']);
            $documento['subtotal'] = floatval($documento['valor'] ?? 0) + floatval($documento['disconto'] ?? 0);
            $documento['total'] = floatval($documento['valor'] ?? 0);
            $documento['total_pago'] = obterSomatorio($db, "SELECT COALESCE(SUM(valor_pago),0) as total FROM pagamentos_recepcao WHERE factura_recepcao_id = ?", $documento_id);
            $documento['total_nc'] = obterSomatorio($db, "SELECT COALESCE(SUM(valor),0) as total FROM nota_credito_recepcao WHERE factura_recepcao_id = ?", $documento_id);
            $documento['total_nd'] = obterSomatorio($db, "SELECT COALESCE(SUM(valor),0) as total FROM nota_debito_recepcao WHERE factura_recepcao_id = ?", $documento_id);
            $documento['total_dv'] = obterSomatorio($db, "SELECT COALESCE(SUM(valor),0) as total FROM devolucao_recepcao WHERE factura_recepcao_id = ?", $documento_id);
            $sql_itens = "SELECT s.nome AS servico_nome, fs.qtd, fs.preco, fs.total 
                          FROM fa_servicos_fact_recepcao fs
                          LEFT JOIN servicos_clinica s ON fs.servico = s.id
                          WHERE fs.factura = ?";
            $stmt_itens = mysqli_prepare($db, $sql_itens);
            if($stmt_itens){
                mysqli_stmt_bind_param($stmt_itens, "i", $documento_id);
                mysqli_stmt_execute($stmt_itens);
                $rs_itens = mysqli_stmt_get_result($stmt_itens);
                if($rs_itens){
                    while($row = mysqli_fetch_array($rs_itens)){
                        $itens[] = $row;
                    }
                }
            }
        }
        break;

    case 'vds':
        $titulo = 'Venda a Dinheiro/Serviço';
        $sql = "SELECT v.*, p.nome AS paciente_nome, p.apelido AS paciente_apelido, p.numero_processo,
                       e.nome AS empresa_nome, e.nuit AS empresa_nuit, e.contacto AS empresa_contacto
                FROM venda_dinheiro_servico v
                LEFT JOIN pacientes p ON v.paciente = p.id
                LEFT JOIN empresas_seguros e ON v.empresa_id = e.id
<<<<<<< HEAD
                LEFT JOIN users u ON v.usuario = u.id
=======
>>>>>>> 25a0cb3ed134b3fba392f117e5fda8254256a55b
                WHERE v.id = ?";
        $stmt = mysqli_prepare($db, $sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt, "i", $documento_id);
            mysqli_stmt_execute($stmt);
            $rs = mysqli_stmt_get_result($stmt);
            $documento = mysqli_fetch_array($rs);
            if($documento){
                $documento['numero_formatado'] = "VDS#" . $documento['serie'] . "/" . str_pad($documento['n_doc'], 6, '0', STR_PAD_LEFT);
                $documento['data_formatada'] = formatarData($documento['dataa']);
                $sql_itens = "SELECT s.nome AS servico_nome, vf.qtd, vf.preco, vf.total
                              FROM vds_servicos_fact vf
                              LEFT JOIN servicos_clinica s ON vf.servico = s.id
                              WHERE vf.vds_id = ?";
                $stmt_itens = mysqli_prepare($db, $sql_itens);
                if($stmt_itens){
                    mysqli_stmt_bind_param($stmt_itens, "i", $documento_id);
                    mysqli_stmt_execute($stmt_itens);
                    $rs_itens = mysqli_stmt_get_result($stmt_itens);
                    if($rs_itens){
                        while($row = mysqli_fetch_array($rs_itens)){
                            $itens[] = $row;
                        }
                    }
                }
            }
        }
        break;

    case 'ct':
        $titulo = 'Cotação';
        $sql = "SELECT c.*, p.nome AS paciente_nome, p.apelido AS paciente_apelido, p.numero_processo,
                       e.nome AS empresa_nome, e.nuit AS empresa_nuit, e.contacto AS empresa_contacto
                FROM cotacao_recepcao c
                LEFT JOIN pacientes p ON c.paciente = p.id
                LEFT JOIN empresas_seguros e ON c.empresa_id = e.id
<<<<<<< HEAD
                LEFT JOIN users u ON c.usuario = u.id
=======
>>>>>>> 25a0cb3ed134b3fba392f117e5fda8254256a55b
                WHERE c.id = ?";
        $stmt = mysqli_prepare($db, $sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt, "i", $documento_id);
            mysqli_stmt_execute($stmt);
            $rs = mysqli_stmt_get_result($stmt);
            $documento = mysqli_fetch_array($rs);
            if($documento){
                $documento['numero_formatado'] = "CT#" . $documento['serie'] . "/" . str_pad($documento['n_doc'], 6, '0', STR_PAD_LEFT);
                $documento['data_formatada'] = formatarData($documento['dataa']);
                $documento['prazo_formatado'] = $documento['prazo'] ? formatarData($documento['prazo']) : '-';
                $sql_itens = "SELECT s.nome AS servico_nome, cf.qtd, cf.preco, cf.total
                              FROM ct_servicos_fact cf
                              LEFT JOIN servicos_clinica s ON cf.servico = s.id
                              WHERE cf.cotacao_id = ?";
                $stmt_itens = mysqli_prepare($db, $sql_itens);
                if($stmt_itens){
                    mysqli_stmt_bind_param($stmt_itens, "i", $documento_id);
                    mysqli_stmt_execute($stmt_itens);
                    $rs_itens = mysqli_stmt_get_result($stmt_itens);
                    if($rs_itens){
                        while($row = mysqli_fetch_array($rs_itens)){
                            $itens[] = $row;
                        }
                    }
                }
            }
        }
        break;

    case 'nc':
        $titulo = 'Nota de Crédito';
        $sql = "SELECT nc.*, 
                       f.n_doc AS fatura_n_doc, f.serie AS fatura_serie,
                       p.nome AS paciente_nome, p.apelido AS paciente_apelido, p.numero_processo,
                       e.nome AS empresa_nome, e.nuit AS empresa_nuit
                FROM nota_credito_recepcao nc
                LEFT JOIN factura_recepcao f ON nc.factura_recepcao_id = f.id
                LEFT JOIN pacientes p ON nc.paciente = p.id
                LEFT JOIN empresas_seguros e ON nc.empresa_id = e.id
<<<<<<< HEAD
                LEFT JOIN users u ON nc.usuario = u.id
=======
>>>>>>> 25a0cb3ed134b3fba392f117e5fda8254256a55b
                WHERE nc.id = ?";
        $stmt = mysqli_prepare($db, $sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt, "i", $documento_id);
            mysqli_stmt_execute($stmt);
            $rs = mysqli_stmt_get_result($stmt);
            $documento = mysqli_fetch_array($rs);
            if($documento){
                $documento['numero_formatado'] = "NC#" . $documento['serie'] . "/" . str_pad($documento['n_doc'], 6, '0', STR_PAD_LEFT);
                $documento['data_formatada'] = formatarData($documento['dataa']);
                $documento['fatura_formatada'] = $documento['fatura_n_doc'] ? "FA#" . $documento['fatura_serie'] . "/" . str_pad($documento['fatura_n_doc'], 6, '0', STR_PAD_LEFT) : '-';
                $sql_itens = "SELECT s.nome AS servico_nome, nf.qtd, nf.preco, nf.total
                              FROM nc_servicos_fact nf
                              LEFT JOIN servicos_clinica s ON nf.servico = s.id
                              WHERE nf.nota_credito_id = ?";
                $stmt_itens = mysqli_prepare($db, $sql_itens);
                if($stmt_itens){
                    mysqli_stmt_bind_param($stmt_itens, "i", $documento_id);
                    mysqli_stmt_execute($stmt_itens);
                    $rs_itens = mysqli_stmt_get_result($stmt_itens);
                    if($rs_itens){
                        while($row = mysqli_fetch_array($rs_itens)){
                            $itens[] = $row;
                        }
                    }
                }
            }
        }
        break;

    case 'nd':
        $titulo = 'Nota de Débito';
        $sql = "SELECT nd.*, 
                       f.n_doc AS fatura_n_doc, f.serie AS fatura_serie,
                       p.nome AS paciente_nome, p.apelido AS paciente_apelido, p.numero_processo,
                       e.nome AS empresa_nome, e.nuit AS empresa_nuit
                FROM nota_debito_recepcao nd
                LEFT JOIN factura_recepcao f ON nd.factura_recepcao_id = f.id
                LEFT JOIN pacientes p ON nd.paciente = p.id
                LEFT JOIN empresas_seguros e ON nd.empresa_id = e.id
<<<<<<< HEAD
                LEFT JOIN users u ON nd.usuario = u.id
=======
>>>>>>> 25a0cb3ed134b3fba392f117e5fda8254256a55b
                WHERE nd.id = ?";
        $stmt = mysqli_prepare($db, $sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt, "i", $documento_id);
            mysqli_stmt_execute($stmt);
            $rs = mysqli_stmt_get_result($stmt);
            $documento = mysqli_fetch_array($rs);
            if($documento){
                $documento['numero_formatado'] = "ND#" . $documento['serie'] . "/" . str_pad($documento['n_doc'], 6, '0', STR_PAD_LEFT);
                $documento['data_formatada'] = formatarData($documento['dataa']);
                $documento['fatura_formatada'] = $documento['fatura_n_doc'] ? "FA#" . $documento['fatura_serie'] . "/" . str_pad($documento['fatura_n_doc'], 6, '0', STR_PAD_LEFT) : '-';
                $sql_itens = "SELECT s.nome AS servico_nome, df.qtd, df.preco, df.total
                              FROM nd_servicos_fact df
                              LEFT JOIN servicos_clinica s ON df.servico = s.id
                              WHERE df.nota_debito_id = ?";
                $stmt_itens = mysqli_prepare($db, $sql_itens);
                if($stmt_itens){
                    mysqli_stmt_bind_param($stmt_itens, "i", $documento_id);
                    mysqli_stmt_execute($stmt_itens);
                    $rs_itens = mysqli_stmt_get_result($stmt_itens);
                    if($rs_itens){
                        while($row = mysqli_fetch_array($rs_itens)){
                            $itens[] = $row;
                        }
                    }
                }
            }
        }
        break;

    case 'dv':
        $titulo = 'Devolução';
        $sql = "SELECT dv.*, 
                       v.n_doc AS vds_n_doc, v.serie AS vds_serie,
                       p.nome AS paciente_nome, p.apelido AS paciente_apelido, p.numero_processo,
                       e.nome AS empresa_nome, e.nuit AS empresa_nuit
                FROM devolucao_recepcao dv
                LEFT JOIN venda_dinheiro_servico v ON dv.factura_recepcao_id = v.id
                LEFT JOIN pacientes p ON dv.paciente = p.id
                LEFT JOIN empresas_seguros e ON dv.empresa_id = e.id
<<<<<<< HEAD
                LEFT JOIN users u ON dv.usuario = u.id
=======
>>>>>>> 25a0cb3ed134b3fba392f117e5fda8254256a55b
                WHERE dv.id = ?";
        $stmt = mysqli_prepare($db, $sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt, "i", $documento_id);
            mysqli_stmt_execute($stmt);
            $rs = mysqli_stmt_get_result($stmt);
            $documento = mysqli_fetch_array($rs);
            if($documento){
                $documento['numero_formatado'] = "DV#" . $documento['serie'] . "/" . str_pad($documento['n_doc'], 6, '0', STR_PAD_LEFT);
                $documento['data_formatada'] = formatarData($documento['dataa']);
                $documento['fatura_formatada'] = $documento['vds_n_doc'] ? "VDS#" . $documento['vds_serie'] . "/" . str_pad($documento['vds_n_doc'], 6, '0', STR_PAD_LEFT) : '-';
                $sql_itens = "SELECT s.nome AS servico_nome, dvf.qtd, dvf.preco, dvf.total
                              FROM dv_servicos_fact dvf
                              LEFT JOIN servicos_clinica s ON dvf.servico = s.id
                              WHERE dvf.devolucao_id = ?";
                $stmt_itens = mysqli_prepare($db, $sql_itens);
                if($stmt_itens){
                    mysqli_stmt_bind_param($stmt_itens, "i", $documento_id);
                    mysqli_stmt_execute($stmt_itens);
                    $rs_itens = mysqli_stmt_get_result($stmt_itens);
                    if($rs_itens){
                        while($row = mysqli_fetch_array($rs_itens)){
                            $itens[] = $row;
                        }
                    }
                }
            }
        }
        break;

    case 'rc':
        $titulo = 'Recibo';
        $sql = "SELECT rc.*, 
                       p.nome AS paciente_nome, p.apelido AS paciente_apelido, p.numero_processo,
                       e.nome AS empresa_nome, e.nuit AS empresa_nuit
                FROM recibo_recepcao rc
                LEFT JOIN pacientes p ON rc.paciente = p.id
                LEFT JOIN empresas_seguros e ON rc.empresa_id = e.id
<<<<<<< HEAD
                LEFT JOIN users u ON rc.usuario = u.id
=======
>>>>>>> 25a0cb3ed134b3fba392f117e5fda8254256a55b
                WHERE rc.id = ?";
        $stmt = mysqli_prepare($db, $sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt, "i", $documento_id);
            mysqli_stmt_execute($stmt);
            $rs = mysqli_stmt_get_result($stmt);
            $documento = mysqli_fetch_array($rs);
            if($documento){
                $documento['numero_formatado'] = "RC#" . $documento['serie'] . "/" . str_pad($documento['n_doc'], 6, '0', STR_PAD_LEFT);
                $documento['data_formatada'] = formatarData($documento['dataa']);
                $sql_faturas = "SELECT rf.*, f.serie AS fatura_serie, f.n_doc AS fatura_n_doc
                                FROM recibo_factura_recepcao rf
                                LEFT JOIN factura_recepcao f ON rf.factura_recepcao_id = f.id
                                WHERE rf.recibo_id = ?";
                $stmt_fat = mysqli_prepare($db, $sql_faturas);
                if($stmt_fat){
                    mysqli_stmt_bind_param($stmt_fat, "i", $documento_id);
                    mysqli_stmt_execute($stmt_fat);
                    $rs_fat = mysqli_stmt_get_result($stmt_fat);
                    if($rs_fat){
                        while($row = mysqli_fetch_array($rs_fat)){
                            $secundario[] = $row;
                        }
                    }
                }
            }
        }
        break;

    default:
        die('Tipo de documento não suportado: ' . htmlspecialchars($tipo));
}

if(!$documento || empty($documento['id'])){
    http_response_code(404);
    error_log("Documento não encontrado: Tipo=$tipo, ID=$documento_id");
    
    // Tentar verificar se o documento existe diretamente
    $exists = false;
    if($tipo == 'fa' && tabelaExiste($db, 'factura_recepcao')){
        $check_sql = "SELECT id FROM factura_recepcao WHERE id = " . intval($documento_id);
        $check_rs = mysqli_query($db, $check_sql);
        $exists = ($check_rs && mysqli_num_rows($check_rs) > 0);
    }
    
    if($exists){
        die('Documento encontrado na base de dados mas houve erro ao carregar os dados. Tipo: ' . htmlspecialchars($tipo) . ', ID: ' . htmlspecialchars($documento_id) . '. Verifique os logs do servidor.');
    } else {
        die('Documento não encontrado na base de dados. Tipo: ' . htmlspecialchars($tipo) . ', ID: ' . htmlspecialchars($documento_id));
    }
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($titulo); ?> - <?php echo htmlspecialchars($documento['numero_formatado'] ?? ''); ?></title>
    <link rel="stylesheet" href="../bootstrap.min.css">
    <style>
        body { background: #f5f7fb; font-family: "Inter", Arial, sans-serif; padding: 30px; color: #1f2d3d; }
        .doc-card { background: #fff; border-radius: 16px; padding: 25px; box-shadow: 0 20px 35px rgba(15,23,42,0.08); margin-bottom: 25px; }
        .doc-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .doc-header h2 { margin: 0; font-weight: 700; font-size: 24px; color: #0f172a; }
        .doc-meta { display: flex; flex-wrap: wrap; gap: 18px; font-size: 14px; color: #475569; }
        .badge-doc { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 999px; font-size: 13px; background: #eef2ff; color: #312e81; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table thead th { text-transform: uppercase; letter-spacing: 0.05em; font-size: 12px; background: #f8fafc; padding: 12px; border-bottom: 1px solid #e2e8f0; }
        table tbody td { padding: 12px; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-top: 18px; }
        .info-item { background: #f8fafc; border-radius: 12px; padding: 16px; }
        .info-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 6px; }
        .info-value { font-weight: 600; font-size: 15px; color: #1e293b; }
        .total-card { text-align: right; margin-top: 20px; }
        .total-card h3 { margin: 0; font-size: 16px; color: #475569; }
        .total-card p { font-size: 24px; font-weight: 700; color: #111827; margin: 6px 0 0; }
        .action-bar { display: flex; gap: 12px; justify-content: flex-end; margin-bottom: 20px; flex-wrap: wrap; }
        .section-title { font-weight: 600; font-size: 16px; margin-bottom: 10px; color: #0f172a; }
        @media print {
            body { padding: 0; background: #fff; }
            .action-bar { display: none; }
            .doc-card { box-shadow: none; margin-bottom: 10px; }
        }
    </style>
</head>
<body>
    <div class="action-bar">
        <a href="javascript:window.close();" class="btn btn-light border">Fechar</a>
        <button class="btn btn-primary" onclick="window.print();"><i class="fa fa-print"></i> Imprimir</button>
    </div>

    <div class="doc-card">
        <div class="doc-header">
            <div>
                <h2><?php echo htmlspecialchars($titulo); ?></h2>
                <div class="badge-doc"><?php echo htmlspecialchars($documento['numero_formatado'] ?? ''); ?></div>
            </div>
            <div class="doc-meta">
                <span><strong>Data:</strong> <?php echo htmlspecialchars($documento['data_formatada'] ?? '-'); ?></span>
                <?php if(!empty($documento['usuario_nome'])): ?>
                    <span><strong>Utilizador:</strong> <?php echo htmlspecialchars($documento['usuario_nome']); ?></span>
                <?php endif; ?>
                <?php if(isset($documento['metodo'])): ?>
                    <span><strong>Método:</strong> <?php echo htmlspecialchars(strtoupper($documento['metodo'])); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="info-grid">
            <?php if(!empty($documento['paciente_nome']) || !empty($documento['paciente_apelido'])): ?>
                <div class="info-item">
                    <div class="info-label">Paciente</div>
                    <div class="info-value">
                        <?php echo htmlspecialchars(trim(($documento['paciente_nome'] ?? '') . ' ' . ($documento['paciente_apelido'] ?? ''))); ?>
                    </div>
                    <?php if(!empty($documento['numero_processo'])): ?>
                        <small>Proc: <?php echo htmlspecialchars($documento['numero_processo']); ?></small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if(!empty($documento['empresa_nome'])): ?>
                <div class="info-item">
                    <div class="info-label">Empresa/Seguro</div>
                    <div class="info-value"><?php echo htmlspecialchars($documento['empresa_nome']); ?></div>
                    <?php if(!empty($documento['empresa_nuit'])): ?>
                        <small>NUIT: <?php echo htmlspecialchars($documento['empresa_nuit']); ?></small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if(!empty($documento['fatura_formatada'])): ?>
                <div class="info-item">
                    <div class="info-label"><?php echo ($tipo === 'dv') ? 'VDS de Origem' : (($tipo === 'nc' || $tipo === 'nd') ? 'Fatura de Origem' : 'Documento de origem'); ?></div>
                    <div class="info-value"><?php echo htmlspecialchars($documento['fatura_formatada']); ?></div>
                </div>
            <?php endif; ?>
            <?php if(!empty($documento['motivo'])): ?>
                <div class="info-item">
                    <div class="info-label">Motivo</div>
                    <div class="info-value" style="white-space: pre-wrap;"><?php echo htmlspecialchars($documento['motivo']); ?></div>
                </div>
            <?php endif; ?>
            <?php if(!empty($documento['metodo'])): ?>
                <div class="info-item">
                    <div class="info-label">Método</div>
                    <div class="info-value"><?php echo htmlspecialchars(strtoupper($documento['metodo'])); ?></div>
                </div>
            <?php endif; ?>
            <?php if(!empty($documento['prazo_formatado'])): ?>
                <div class="info-item">
                    <div class="info-label">Prazo</div>
                    <div class="info-value"><?php echo htmlspecialchars($documento['prazo_formatado']); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(in_array($tipo, ['fa','vds','ct','nc','nd','dv']) && !empty($itens)): ?>
        <div class="doc-card">
            <div class="section-title">Itens / Serviços</div>
            <table>
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th style="width: 80px;">Qtd.</th>
                        <th style="width: 140px;">Preço</th>
                        <th style="width: 140px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($itens as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['servico_nome'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item['qtd'] ?? $item['quantidade'] ?? 1); ?></td>
                            <td><?php echo formatarMoeda($item['preco'] ?? 0); ?></td>
                            <td><?php echo formatarMoeda($item['total'] ?? 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-card">
                <h3>Total do Documento</h3>
                <p><?php echo formatarMoeda($documento['valor'] ?? $documento['total'] ?? 0); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if($tipo === 'fa'): ?>
        <div class="doc-card">
            <div class="section-title">Resumo Financeiro</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Subtotal</div>
                    <div class="info-value"><?php echo formatarMoeda($documento['subtotal'] ?? 0); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Desconto</div>
                    <div class="info-value"><?php echo formatarMoeda($documento['disconto'] ?? 0); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Pago</div>
                    <div class="info-value"><?php echo formatarMoeda($documento['total_pago'] ?? 0); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Notas de Crédito</div>
                    <div class="info-value text-danger"><?php echo formatarMoeda($documento['total_nc'] ?? 0); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Notas de Débito</div>
                    <div class="info-value text-success"><?php echo formatarMoeda($documento['total_nd'] ?? 0); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Devoluções</div>
                    <div class="info-value text-warning"><?php echo formatarMoeda($documento['total_dv'] ?? 0); ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if($tipo === 'rc' && !empty($secundario)): ?>
        <div class="doc-card">
            <div class="section-title">Faturas associadas</div>
            <table>
                <thead>
                    <tr>
                        <th>Fatura</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($secundario as $fat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars("FA#" . ($fat['fatura_serie'] ?? '') . "/" . str_pad(($fat['fatura_n_doc'] ?? 0), 6, '0', STR_PAD_LEFT)); ?></td>
                            <td><?php echo formatarMoeda($fat['valor'] ?? 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-card">
                <h3>Total Recebido</h3>
                <p><?php echo formatarMoeda($documento['valor'] ?? 0); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if($tipo === 'dv' && empty($itens)): ?>
        <div class="doc-card">
            <div class="section-title">Detalhes</div>
            <p>Não existem itens associados a esta devolução.</p>
        </div>
    <?php endif; ?>

    <?php if($auto_print): ?>
        <script>
            window.onload = function(){
                window.print();
            };
        </script>
    <?php endif; ?>
</body>
</html>
