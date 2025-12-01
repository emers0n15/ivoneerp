<?php
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;
$fatura_id = isset($_POST['fatura_id']) && $_POST['fatura_id'] != '' && $_POST['fatura_id'] != 'null' ? intval($_POST['fatura_id']) : null;
$empresa_id = isset($_POST['empresa_id']) && $_POST['empresa_id'] != '' && $_POST['empresa_id'] != 'null' ? intval($_POST['empresa_id']) : null;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';

if(!$fatura_id || $fatura_id <= 0) {
    error_log("NC: fatura_id inválido - " . ($_POST['fatura_id'] ?? 'não definido'));
    echo 1;
    exit;
}

if(!$userID || $userID <= 0) {
    error_log("NC: userID inválido - " . ($userID ?? 'não definido'));
    echo 1;
    exit;
}

if(!$motivo || $motivo == '') {
    error_log("NC: motivo vazio");
    echo 1;
    exit;
}

// Verificar se todas as tabelas necessárias existem
$tabelas_necessarias = [
    'factura_recepcao',
    'nota_credito_recepcao',
    'nc_servicos_temp',
    'nc_servicos_fact'
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
    error_log("NC: Tabelas faltando: " . implode(', ', $tabelas_faltando));
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
$empresa_id_db = $empresa_id ? intval($empresa_id) : (isset($fatura_data['empresa_id']) ? intval($fatura_data['empresa_id']) : 0);

// Buscar serviços da tabela temporária
if($empresa_id_db && $empresa_id_db > 0) {
    $sql_temp = "SELECT * FROM nc_servicos_temp WHERE user = ? AND empresa_id = ?";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $empresa_id_db);
} else {
    $sql_temp = "SELECT * FROM nc_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "i", $userID);
}
mysqli_stmt_execute($stmt);
$rs_temp = mysqli_stmt_get_result($stmt);

if(!$rs_temp || mysqli_num_rows($rs_temp) == 0) {
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
    $check_table_nc = "SHOW TABLES LIKE 'nota_credito_recepcao'";
    $rs_check_nc = mysqli_query($db, $check_table_nc);
    if(!$rs_check_nc || mysqli_num_rows($rs_check_nc) == 0) {
        echo 4;
        exit;
    }
    
    $sql_max = "SELECT MAX(n_doc) as maxid FROM nota_credito_recepcao WHERE serie = ?";
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
    
    // Inserir nota de crédito
    $sql_nc = "INSERT INTO nota_credito_recepcao SET 
        n_doc = ?, 
        factura_recepcao_id = ?,
        paciente = ?, 
        empresa_id = ?, 
        valor = ?, 
        motivo = ?,
        serie = ?, 
        usuario = ?, 
        dataa = ?";
    
    $stmt_nc = mysqli_prepare($db, $sql_nc);
    $new_id = intval($new_id);
    $serie = intval($serie);
    $paciente_id = intval($paciente_id);
    $userID = intval($userID);
    $total = floatval($total);
    $fatura_id = intval($fatura_id);
    
    mysqli_stmt_bind_param($stmt_nc, "iiidssiss", 
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
    
    if(mysqli_stmt_execute($stmt_nc)) {
        $nc_id = mysqli_insert_id($db);
        
        if(!$nc_id) {
            echo 2;
            exit;
        }
        
        // Verificar se a tabela de serviços existe
        $check_table_serv = "SHOW TABLES LIKE 'nc_servicos_fact'";
        $rs_check_serv = mysqli_query($db, $check_table_serv);
        if(!$rs_check_serv || mysqli_num_rows($rs_check_serv) == 0) {
            $sql_delete_nc = "DELETE FROM nota_credito_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_nc);
            mysqli_stmt_bind_param($stmt_del, "i", $nc_id);
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
            
            $sql_item = "INSERT INTO nc_servicos_fact (servico, qtd, preco, total, user, nota_credito_id) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($db, $sql_item);
            if(!$stmt_item) {
                $erro_servicos = true;
                break;
            }
            mysqli_stmt_bind_param($stmt_item, "iiddii", $servico_id, $qtd, $preco, $total_item, $userID, $nc_id);
            if(!mysqli_stmt_execute($stmt_item)) {
                $erro_servicos = true;
                break;
            }
        }
        
        if($erro_servicos) {
            $sql_delete_nc = "DELETE FROM nota_credito_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_nc);
            mysqli_stmt_bind_param($stmt_del, "i", $nc_id);
            mysqli_stmt_execute($stmt_del);
            echo 2;
            exit;
        }
        
        // NÃO atualizar o valor da fatura diretamente
        // O valor da fatura deve permanecer como valor original
        // As notas de crédito são rastreadas na tabela nota_credito_recepcao
        // O valor disponível é calculado dinamicamente: valor_original + ND - NC - DV - pagamentos
        
        // Limpar tabela temporária
        if($empresa_id_db && $empresa_id_db > 0) {
            $sql_delete = "DELETE FROM nc_servicos_temp WHERE user = ? AND empresa_id = ?";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "ii", $userID, $empresa_id_db);
        } else {
            $sql_delete = "DELETE FROM nc_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "i", $userID);
        }
        mysqli_stmt_execute($stmt_delete);
        
        echo $nc_id;
    } else {
        echo 2;
    }
} else {
    echo 3;
}
?>

