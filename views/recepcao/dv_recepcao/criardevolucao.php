<?php
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;
$fatura_id = isset($_POST['fatura_id']) && $_POST['fatura_id'] != '' && $_POST['fatura_id'] != 'null' ? intval($_POST['fatura_id']) : null;
$empresa_id = isset($_POST['empresa_id']) && $_POST['empresa_id'] != '' && $_POST['empresa_id'] != 'null' ? intval($_POST['empresa_id']) : null;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
$metodo = isset($_POST['metodo']) ? trim($_POST['metodo']) : '';

if(!$fatura_id || $fatura_id <= 0) {
    error_log("DV: fatura_id inválido - " . ($_POST['fatura_id'] ?? 'não definido'));
    echo 1;
    exit;
}

if(!$userID || $userID <= 0) {
    error_log("DV: userID inválido - " . ($userID ?? 'não definido'));
    echo 1;
    exit;
}

if(!$motivo || $motivo == '') {
    error_log("DV: motivo vazio");
    echo 1;
    exit;
}

if(!$metodo || $metodo == '') {
    error_log("DV: metodo vazio");
    echo 1;
    exit;
}

// Verificar se todas as tabelas necessárias existem
$tabelas_necessarias = [
    'factura_recepcao',
    'devolucao_recepcao',
    'dv_servicos_temp',
    'dv_servicos_fact'
];

$tabelas_faltando = [];
foreach($tabelas_necessarias as $tabela) {
    $check_table = "SHOW TABLES LIKE '$tabela'";
    $table_exists = mysqli_query($db, $check_table);
    if(!$table_exists || mysqli_num_rows($table_exists) == 0) {
        $tabelas_faltando[] = $tabela;
    }
}

if(!empty($tabelas_faltando)) {
    $tabelas_faltando_str = implode(', ', $tabelas_faltando);
    error_log("DV: Tabelas faltando: " . $tabelas_faltando_str);
    // Retornar código de erro com informações sobre quais tabelas faltam
    echo "4|" . $tabelas_faltando_str;
    exit;
}

$sql_fatura = "SELECT * FROM factura_recepcao WHERE id = ?";
$stmt_fatura = mysqli_prepare($db, $sql_fatura);
mysqli_stmt_bind_param($stmt_fatura, "i", $fatura_id);
mysqli_stmt_execute($stmt_fatura);
$rs_fatura = mysqli_stmt_get_result($stmt_fatura);

if(!$rs_fatura || mysqli_num_rows($rs_fatura) == 0) {
    echo 1;
    exit;
}

$fatura_data = mysqli_fetch_array($rs_fatura);
$paciente_id = intval($fatura_data['paciente']);
$empresa_id_db = $empresa_id ? intval($empresa_id) : (isset($fatura_data['empresa_id']) ? intval($fatura_data['empresa_id']) : null);

// Buscar serviços da tabela temporária
if($empresa_id_db && $empresa_id_db > 0) {
    $sql_temp = "SELECT * FROM dv_servicos_temp WHERE user = ? AND empresa_id = ? AND factura_recepcao_id = ?";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "iii", $userID, $empresa_id_db, $fatura_id);
} else {
    $sql_temp = "SELECT * FROM dv_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0) AND factura_recepcao_id = ?";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $fatura_id);
}
mysqli_stmt_execute($stmt);
$rs_temp = mysqli_stmt_get_result($stmt);

if(!$rs_temp || mysqli_num_rows($rs_temp) == 0) {
    echo 1;
    exit;
}

// Calcular total da devolução
$total = 0;
while($servico_temp = mysqli_fetch_array($rs_temp)) {
    $total += floatval($servico_temp['total']);
}

if($total <= 0) {
    echo 1;
    exit;
}

// Calcular valor disponível da fatura (considerando pagamentos, NC, ND e outras devoluções)
$valor_fatura_original = floatval($fatura_data['valor']);

// Total pago
$sql_total_pago = "SELECT COALESCE(SUM(valor_pago), 0) as total_pago 
                   FROM pagamentos_recepcao 
                   WHERE factura_recepcao_id = ? 
                   OR (fatura_id = ? AND factura_recepcao_id IS NULL)";
$stmt_pago = mysqli_prepare($db, $sql_total_pago);
mysqli_stmt_bind_param($stmt_pago, "ii", $fatura_id, $fatura_id);
mysqli_stmt_execute($stmt_pago);
$rs_pago = mysqli_stmt_get_result($stmt_pago);
$total_pago = 0;
if($rs_pago && mysqli_num_rows($rs_pago) > 0) {
    $pago_data = mysqli_fetch_array($rs_pago);
    $total_pago = floatval($pago_data['total_pago']);
}

// Total de notas de crédito (diminuem o valor)
$check_table_nc = "SHOW TABLES LIKE 'nota_credito_recepcao'";
$table_nc_exists = mysqli_query($db, $check_table_nc);
$total_nc = 0;
if($table_nc_exists && mysqli_num_rows($table_nc_exists) > 0) {
    $sql_total_nc = "SELECT COALESCE(SUM(valor), 0) as total_nc 
                     FROM nota_credito_recepcao 
                     WHERE factura_recepcao_id = ?";
    $stmt_nc = mysqli_prepare($db, $sql_total_nc);
    mysqli_stmt_bind_param($stmt_nc, "i", $fatura_id);
    mysqli_stmt_execute($stmt_nc);
    $rs_nc = mysqli_stmt_get_result($stmt_nc);
    if($rs_nc && mysqli_num_rows($rs_nc) > 0) {
        $nc_data = mysqli_fetch_array($rs_nc);
        $total_nc = floatval($nc_data['total_nc']);
    }
}

// Total de notas de débito (aumentam o valor)
$check_table_nd = "SHOW TABLES LIKE 'nota_debito_recepcao'";
$table_nd_exists = mysqli_query($db, $check_table_nd);
$total_nd = 0;
if($table_nd_exists && mysqli_num_rows($table_nd_exists) > 0) {
    $sql_total_nd = "SELECT COALESCE(SUM(valor), 0) as total_nd 
                     FROM nota_debito_recepcao 
                     WHERE factura_recepcao_id = ?";
    $stmt_nd = mysqli_prepare($db, $sql_total_nd);
    mysqli_stmt_bind_param($stmt_nd, "i", $fatura_id);
    mysqli_stmt_execute($stmt_nd);
    $rs_nd = mysqli_stmt_get_result($stmt_nd);
    if($rs_nd && mysqli_num_rows($rs_nd) > 0) {
        $nd_data = mysqli_fetch_array($rs_nd);
        $total_nd = floatval($nd_data['total_nd']);
    }
}

// Total de devoluções já feitas (diminuem o valor)
$check_table_dv = "SHOW TABLES LIKE 'devolucao_recepcao'";
$table_dv_exists = mysqli_query($db, $check_table_dv);
$total_dv_existentes = 0;
if($table_dv_exists && mysqli_num_rows($table_dv_exists) > 0) {
    $sql_total_dv = "SELECT COALESCE(SUM(valor), 0) as total_dv 
                     FROM devolucao_recepcao 
                     WHERE factura_recepcao_id = ?";
    $stmt_dv = mysqli_prepare($db, $sql_total_dv);
    mysqli_stmt_bind_param($stmt_dv, "i", $fatura_id);
    mysqli_stmt_execute($stmt_dv);
    $rs_dv = mysqli_stmt_get_result($stmt_dv);
    if($rs_dv && mysqli_num_rows($rs_dv) > 0) {
        $dv_data = mysqli_fetch_array($rs_dv);
        $total_dv_existentes = floatval($dv_data['total_dv']);
    }
}

// Calcular valor disponível para devolução
// Valor disponível = Valor original + ND - NC - DV existentes - Total pago
$valor_disponivel = $valor_fatura_original + $total_nd - $total_nc - $total_dv_existentes - $total_pago;

// Validar que a devolução não exceda o valor disponível
if($total > $valor_disponivel) {
    error_log("DV: Valor da devolução ($total) excede o valor disponível ($valor_disponivel)");
    echo "5|" . number_format($valor_disponivel, 2, ',', '.'); // Código 5 = valor excede disponível
    exit;
}

// Validar que o valor disponível seja positivo
if($valor_disponivel <= 0) {
    error_log("DV: Não há valor disponível para devolução. Valor disponível: $valor_disponivel");
    echo "6|" . number_format($valor_disponivel, 2, ',', '.'); // Código 6 = sem valor disponível
    exit;
}

// Buscar série fiscal (ano fiscal ativo)
$sql_serie = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs_serie = mysqli_query($db, $sql_serie);
$serie = date('Y'); // Fallback para ano atual
if($rs_serie && mysqli_num_rows($rs_serie) > 0) {
    $dados_serie = mysqli_fetch_array($rs_serie);
    $serie = $dados_serie['serie'] ?? date('Y');
}

$data = date("Y-m-d");

// Usar sempre a série fiscal, sem validar contra o ano atual
// Buscar próximo número de documento
$check_table_dv = "SHOW TABLES LIKE 'devolucao_recepcao'";
$rs_check_dv = mysqli_query($db, $check_table_dv);
if(!$rs_check_dv || mysqli_num_rows($rs_check_dv) == 0) {
    echo 4;
    exit;
}

$sql_max = "SELECT MAX(n_doc) as maxid FROM devolucao_recepcao WHERE serie = ?";
$stmt_max = mysqli_prepare($db, $sql_max);
mysqli_stmt_bind_param($stmt_max, "i", $serie);
mysqli_stmt_execute($stmt_max);
$rs_max = mysqli_stmt_get_result($stmt_max);

$new_id = 1;
if($rs_max && mysqli_num_rows($rs_max) > 0) {
    $dados_max = mysqli_fetch_array($rs_max);
    $max_id = $dados_max['maxid'] ?? 0;
    $new_id = $max_id + 1;
}

// Preparar valores
$new_id = intval($new_id);
$serie = intval($serie);
$paciente_id = intval($paciente_id);
$userID = intval($userID);
$total = floatval($total);
$fatura_id = intval($fatura_id);

// Inserir devolução - usar COALESCE para lidar com NULL
if($empresa_id_db && $empresa_id_db > 0) {
        $sql_dv = "INSERT INTO devolucao_recepcao (n_doc, factura_recepcao_id, paciente, empresa_id, valor, motivo, metodo, serie, usuario, dataa) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_dv = mysqli_prepare($db, $sql_dv);
        if(!$stmt_dv) {
            error_log("DV: Erro ao preparar statement de devolução: " . mysqli_error($db));
            echo 2;
            exit;
        }
        $bind_result = mysqli_stmt_bind_param($stmt_dv, "iiidsssiss", 
            $new_id,
            $fatura_id,
            $paciente_id,
            $empresa_id_db,
            $total,
            $motivo,
            $metodo,
            $serie,
            $userID,
            $data
        );
    } else {
        $sql_dv = "INSERT INTO devolucao_recepcao (n_doc, factura_recepcao_id, paciente, empresa_id, valor, motivo, metodo, serie, usuario, dataa) 
                   VALUES (?, ?, ?, NULL, ?, ?, ?, ?, ?, ?)";
        $stmt_dv = mysqli_prepare($db, $sql_dv);
        if(!$stmt_dv) {
            error_log("DV: Erro ao preparar statement de devolução (sem empresa): " . mysqli_error($db));
            echo 2;
            exit;
        }
        $bind_result = mysqli_stmt_bind_param($stmt_dv, "iiidsssis", 
            $new_id,
            $fatura_id,
            $paciente_id,
            $total,
            $motivo,
            $metodo,
            $serie,
            $userID,
            $data
        );
    }
    
if(!$bind_result) {
    error_log("DV: Erro ao fazer bind_param: " . mysqli_error($db));
    echo 2;
    exit;
}

if(mysqli_stmt_execute($stmt_dv)) {
        $dv_id = mysqli_insert_id($db);
        
        if(!$dv_id || $dv_id <= 0) {
            error_log("DV: ID da devolução não gerado. Erro: " . mysqli_error($db));
            echo 2;
            exit;
        }
        
        error_log("DV: Devolução criada com sucesso. ID: " . $dv_id);
        
        // Verificar se a tabela de serviços existe
        $check_table_serv = "SHOW TABLES LIKE 'dv_servicos_fact'";
        $rs_check_serv = mysqli_query($db, $check_table_serv);
        if(!$rs_check_serv || mysqli_num_rows($rs_check_serv) == 0) {
            $sql_delete_dv = "DELETE FROM devolucao_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_dv);
            mysqli_stmt_bind_param($stmt_del, "i", $dv_id);
            mysqli_stmt_execute($stmt_del);
            echo 4;
            exit;
        }
        
        // Inserir serviços
        mysqli_data_seek($rs_temp, 0);
        $erro_servicos = false;
        while($servico_temp = mysqli_fetch_array($rs_temp)) {
            $servico_id = intval($servico_temp['servico']);
            $qtd = intval($servico_temp['qtd']);
            $preco = floatval($servico_temp['preco']);
            $total_item = floatval($servico_temp['total']);
            
            $sql_item = "INSERT INTO dv_servicos_fact (servico, qtd, preco, total, user, devolucao_id) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($db, $sql_item);
            if(!$stmt_item) {
                $erro_servicos = true;
                error_log("DV: Erro ao preparar statement para inserir serviço: " . mysqli_error($db));
                break;
            }
            mysqli_stmt_bind_param($stmt_item, "iiddii", $servico_id, $qtd, $preco, $total_item, $userID, $dv_id);
            if(!mysqli_stmt_execute($stmt_item)) {
                $erro_servicos = true;
                error_log("DV: Erro ao inserir serviço: " . mysqli_error($db));
                break;
            }
        }
        
        if($erro_servicos) {
            $sql_delete_dv = "DELETE FROM devolucao_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_dv);
            mysqli_stmt_bind_param($stmt_del, "i", $dv_id);
            mysqli_stmt_execute($stmt_del);
            echo 2;
            exit;
        }
        
        // NÃO atualizar o valor da fatura diretamente
        // O valor da fatura deve permanecer como valor original
        // As devoluções são rastreadas na tabela devolucao_recepcao
        // O valor disponível é calculado dinamicamente: valor_original + ND - NC - DV - pagamentos
        
        // Limpar tabela temporária
        if($empresa_id_db && $empresa_id_db > 0) {
            $sql_delete = "DELETE FROM dv_servicos_temp WHERE user = ? AND empresa_id = ? AND factura_recepcao_id = ?";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "iii", $userID, $empresa_id_db, $fatura_id);
        } else {
            $sql_delete = "DELETE FROM dv_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0) AND factura_recepcao_id = ?";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "ii", $userID, $fatura_id);
        }
        mysqli_stmt_execute($stmt_delete);
        
        echo $dv_id;
    } else {
        $error_msg = mysqli_error($db);
        error_log("DV: Erro ao executar statement de devolução: " . $error_msg);
        error_log("DV: Parâmetros - new_id: $new_id, fatura_id: $fatura_id, paciente_id: $paciente_id, empresa_id_db: " . ($empresa_id_db ?? 'NULL') . ", total: $total, motivo: $motivo, metodo: $metodo, serie: $serie, userID: $userID, data: $data");
        echo 2;
    }
?>

