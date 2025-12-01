<?php
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;
$fatura_id_raw = $_POST['fatura_id'] ?? null;
$empresa_id_raw = $_POST['empresa_id'] ?? null;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';

// Debug: logar valores recebidos
error_log("ND DEBUG - fatura_id recebido: " . var_export($fatura_id_raw, true));
error_log("ND DEBUG - empresa_id recebido: " . var_export($empresa_id_raw, true));
error_log("ND DEBUG - motivo recebido: " . var_export($motivo, true));
error_log("ND DEBUG - userID: " . var_export($userID, true));

// Processar fatura_id
if($fatura_id_raw === null || $fatura_id_raw === '' || $fatura_id_raw === 'null' || $fatura_id_raw === null) {
    error_log("ND: fatura_id inválido ou não fornecido");
    echo 1;
    exit;
}
$fatura_id = intval($fatura_id_raw);
if($fatura_id <= 0) {
    error_log("ND: fatura_id convertido para int inválido: " . $fatura_id);
    echo 1;
    exit;
}

// Processar empresa_id (pode ser null)
$empresa_id = null;
if($empresa_id_raw !== null && $empresa_id_raw !== '' && $empresa_id_raw !== 'null') {
    $empresa_id = intval($empresa_id_raw);
    if($empresa_id <= 0) {
        $empresa_id = null;
    }
}

if(!$userID || $userID <= 0) {
    error_log("ND: userID inválido - " . ($userID ?? 'não definido'));
    echo 1;
    exit;
}

if(!$motivo || $motivo == '') {
    error_log("ND: motivo vazio");
    echo 1;
    exit;
}

// Verificar se todas as tabelas necessárias existem
$tabelas_necessarias = [
    'factura_recepcao',
    'nota_debito_recepcao',
    'nd_servicos_temp',
    'nd_servicos_fact'
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
    error_log("ND: Tabelas faltando: " . implode(', ', $tabelas_faltando));
    echo 4;
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
// empresa_id_db pode ser NULL ou um inteiro
if($empresa_id && $empresa_id > 0) {
    $empresa_id_db = intval($empresa_id);
} else if(isset($fatura_data['empresa_id']) && $fatura_data['empresa_id'] != null && $fatura_data['empresa_id'] != '') {
    $empresa_id_db = intval($fatura_data['empresa_id']);
} else {
    $empresa_id_db = null;
}

// Buscar serviços da tabela temporária
if($empresa_id_db && $empresa_id_db > 0) {
    $sql_temp = "SELECT * FROM nd_servicos_temp WHERE user = ? AND empresa_id = ?";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $empresa_id_db);
} else {
    $sql_temp = "SELECT * FROM nd_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "i", $userID);
}
mysqli_stmt_execute($stmt);
$rs_temp = mysqli_stmt_get_result($stmt);

if(!$rs_temp || mysqli_num_rows($rs_temp) == 0) {
    error_log("ND: Nenhum serviço encontrado na tabela temporária para o usuário " . $userID . " e empresa " . ($empresa_id_db ?? 'null'));
    echo 1;
    exit;
}

// Calcular total
$total = 0;
while($servico_temp = mysqli_fetch_array($rs_temp)) {
    $total += floatval($servico_temp['total']);
}

if($total <= 0) {
    echo 1;
    exit;
}

// Buscar série fiscal
$sql_serie = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs_serie = mysqli_query($db, $sql_serie);
$serie = date('Y');
if($rs_serie && mysqli_num_rows($rs_serie) > 0) {
    $dados_serie = mysqli_fetch_array($rs_serie);
    $serie = $dados_serie['serie'] ?? date('Y');
}

$year = date('Y');
$data = date("Y-m-d");

if ($year == $serie) {
    // Buscar próximo número de documento
    $check_table_nd = "SHOW TABLES LIKE 'nota_debito_recepcao'";
    $rs_check_nd = mysqli_query($db, $check_table_nd);
    if(!$rs_check_nd || mysqli_num_rows($rs_check_nd) == 0) {
        echo 4;
        exit;
    }
    
    $sql_max = "SELECT MAX(n_doc) as maxid FROM nota_debito_recepcao WHERE serie = ?";
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
    
    // Inserir nota de débito
    $new_id = intval($new_id);
    $serie = intval($serie);
    $paciente_id = intval($paciente_id);
    $userID = intval($userID);
    $total = floatval($total);
    $fatura_id = intval($fatura_id);
    
    if($empresa_id_db !== null && $empresa_id_db > 0) {
        $sql_nd = "INSERT INTO nota_debito_recepcao SET 
            n_doc = ?, 
            factura_recepcao_id = ?,
            paciente = ?, 
            empresa_id = ?, 
            valor = ?, 
            motivo = ?,
            serie = ?, 
            usuario = ?, 
            dataa = ?";
        $stmt_nd = mysqli_prepare($db, $sql_nd);
        if(!$stmt_nd) {
            error_log("ND: Erro ao preparar statement - " . mysqli_error($db));
            echo 2;
            exit;
        }
        mysqli_stmt_bind_param($stmt_nd, "iiidssiss", 
            $new_id,
            $fatura_id,
            $paciente_id,
            $empresa_id_db,
            $total,
            $motivo,
            $serie,
            $userID,
            $data
        );
    } else {
        $sql_nd = "INSERT INTO nota_debito_recepcao SET 
            n_doc = ?, 
            factura_recepcao_id = ?,
            paciente = ?, 
            empresa_id = NULL, 
            valor = ?, 
            motivo = ?,
            serie = ?, 
            usuario = ?, 
            dataa = ?";
        $stmt_nd = mysqli_prepare($db, $sql_nd);
        if(!$stmt_nd) {
            error_log("ND: Erro ao preparar statement - " . mysqli_error($db));
            echo 2;
            exit;
        }
        // Bind sem empresa_id (8 parâmetros ao invés de 9)
        mysqli_stmt_bind_param($stmt_nd, "iiidssis", 
            $new_id,
            $fatura_id,
            $paciente_id,
            $total,
            $motivo,
            $serie,
            $userID,
            $data
        );
    }
    
    if(mysqli_stmt_execute($stmt_nd)) {
        $nd_id = mysqli_insert_id($db);
        
        if(!$nd_id) {
            error_log("ND: Erro - mysqli_insert_id retornou 0 ou false");
            error_log("ND: Último erro MySQL - " . mysqli_error($db));
            echo 2;
            exit;
        }
        
        // Verificar se a tabela de serviços existe
        $check_table_serv = "SHOW TABLES LIKE 'nd_servicos_fact'";
        $rs_check_serv = mysqli_query($db, $check_table_serv);
        if(!$rs_check_serv || mysqli_num_rows($rs_check_serv) == 0) {
            $sql_delete_nd = "DELETE FROM nota_debito_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_nd);
            mysqli_stmt_bind_param($stmt_del, "i", $nd_id);
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
            
            $sql_item = "INSERT INTO nd_servicos_fact (servico, qtd, preco, total, user, nota_debito_id) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($db, $sql_item);
            if(!$stmt_item) {
                $erro_servicos = true;
                break;
            }
            mysqli_stmt_bind_param($stmt_item, "iiddii", $servico_id, $qtd, $preco, $total_item, $userID, $nd_id);
            if(!mysqli_stmt_execute($stmt_item)) {
                error_log("ND: Erro ao inserir serviço - " . mysqli_error($db));
                error_log("ND: Dados do serviço - servico_id: $servico_id, qtd: $qtd, preco: $preco, total: $total_item, userID: $userID, nd_id: $nd_id");
                $erro_servicos = true;
                break;
            }
        }
        
        if($erro_servicos) {
            $sql_delete_nd = "DELETE FROM nota_debito_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_nd);
            mysqli_stmt_bind_param($stmt_del, "i", $nd_id);
            mysqli_stmt_execute($stmt_del);
            echo 2;
            exit;
        }
        
        // NÃO atualizar o valor da fatura diretamente
        // O valor da fatura deve permanecer como valor original
        // As notas de débito são rastreadas na tabela nota_debito_recepcao
        // O valor disponível é calculado dinamicamente: valor_original + ND - NC - DV - pagamentos
        
        // Limpar tabela temporária
        if($empresa_id_db && $empresa_id_db > 0) {
            $sql_delete = "DELETE FROM nd_servicos_temp WHERE user = ? AND empresa_id = ?";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "ii", $userID, $empresa_id_db);
        } else {
            $sql_delete = "DELETE FROM nd_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "i", $userID);
        }
        mysqli_stmt_execute($stmt_delete);
        
        echo $nd_id;
    } else {
        error_log("ND: Erro ao executar INSERT da nota de débito");
        error_log("ND: Último erro MySQL - " . mysqli_error($db));
        error_log("ND: SQL - " . $sql_nd);
        error_log("ND: Valores - new_id: $new_id, fatura_id: $fatura_id, paciente_id: $paciente_id, empresa_id_db: $empresa_id_db, total: $total, serie: $serie, userID: $userID, data: $data");
        echo 2;
    }
} else {
    echo 3;
}
?>

