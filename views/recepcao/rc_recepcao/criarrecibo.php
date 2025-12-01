<?php
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;
$metodo = $_POST['metodo'] ?? '';

if(!$userID || !$metodo || trim($metodo) == '') {
    echo 1;
    exit;
}

// Verificar se a tabela temporária existe
$check_table_temp = "SHOW TABLES LIKE 'rc_faturas_temp_recepcao'";
$table_temp_exists = mysqli_query($db, $check_table_temp);
if(!$table_temp_exists || mysqli_num_rows($table_temp_exists) == 0) {
    echo 4;
    exit;
}

// Buscar faturas da tabela temporária
$sql_temp = "SELECT * FROM rc_faturas_temp_recepcao WHERE user = ?";
$stmt = mysqli_prepare($db, $sql_temp);
if(!$stmt) {
    echo 2;
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$rs_temp = mysqli_stmt_get_result($stmt);

if(!$rs_temp || mysqli_num_rows($rs_temp) == 0) {
    echo 1;
    exit;
}

// Calcular total
$total = 0;
$paciente_id = null;
$empresa_id = null;
while($fatura_temp = mysqli_fetch_array($rs_temp)) {
    $total += floatval($fatura_temp['valor']);
    
    // Buscar informações da primeira fatura para paciente e empresa
    if(!$paciente_id) {
        $sql_fatura = "SELECT paciente, empresa_id FROM factura_recepcao WHERE id = ?";
        $stmt_fatura = mysqli_prepare($db, $sql_fatura);
        mysqli_stmt_bind_param($stmt_fatura, "i", $fatura_temp['factura_recepcao_id']);
        mysqli_stmt_execute($stmt_fatura);
        $rs_fatura = mysqli_stmt_get_result($stmt_fatura);
        if($rs_fatura && mysqli_num_rows($rs_fatura) > 0) {
            $fatura_data = mysqli_fetch_array($rs_fatura);
            $paciente_id = intval($fatura_data['paciente']);
            $empresa_id = isset($fatura_data['empresa_id']) ? intval($fatura_data['empresa_id']) : 0;
        }
    }
}

if($total <= 0 || !$paciente_id) {
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
    $check_table_rc = "SHOW TABLES LIKE 'recibo_recepcao'";
    $rs_check_rc = mysqli_query($db, $check_table_rc);
    if(!$rs_check_rc || mysqli_num_rows($rs_check_rc) == 0) {
        echo 4;
        exit;
    }
    
    $sql_max = "SELECT MAX(n_doc) as maxid FROM recibo_recepcao WHERE serie = ?";
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
    
    // Inserir recibo
    $sql_rc = "INSERT INTO recibo_recepcao SET 
        n_doc = ?, 
        paciente = ?, 
        empresa_id = ?, 
        valor = ?, 
        metodo = ?,
        serie = ?, 
        usuario = ?, 
        dataa = ?";
    
    $stmt_rc = mysqli_prepare($db, $sql_rc);
    $new_id = intval($new_id);
    $serie = intval($serie);
    $paciente_id = intval($paciente_id);
    $userID = intval($userID);
    $total = floatval($total);
    $empresa_id_db = $empresa_id ? intval($empresa_id) : 0;
    
    mysqli_stmt_bind_param($stmt_rc, "iiidssis", 
        $new_id,
        $paciente_id,
        $empresa_id_db,
        $total,
        $metodo,
        $serie,
        $userID,
        $data
    );
    
    if(mysqli_stmt_execute($stmt_rc)) {
        $rc_id = mysqli_insert_id($db);
        
        if(!$rc_id) {
            echo 2;
            exit;
        }
        
        // Verificar se a tabela de ligação existe
        $check_table_lig = "SHOW TABLES LIKE 'recibo_factura_recepcao'";
        $rs_check_lig = mysqli_query($db, $check_table_lig);
        if(!$rs_check_lig || mysqli_num_rows($rs_check_lig) == 0) {
            $sql_delete_rc = "DELETE FROM recibo_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_rc);
            mysqli_stmt_bind_param($stmt_del, "i", $rc_id);
            mysqli_stmt_execute($stmt_del);
            echo 4;
            exit;
        }
        
        // Inserir ligações com faturas
        mysqli_data_seek($rs_temp, 0);
        $erro_faturas = false;
        while($fatura_temp = mysqli_fetch_array($rs_temp)) {
            $fatura_id = intval($fatura_temp['factura_recepcao_id']);
            $valor_fatura = floatval($fatura_temp['valor']);
            
            $sql_item = "INSERT INTO recibo_factura_recepcao (recibo_id, factura_recepcao_id, valor) 
                         VALUES (?, ?, ?)";
            $stmt_item = mysqli_prepare($db, $sql_item);
            if(!$stmt_item) {
                $erro_faturas = true;
                break;
            }
            mysqli_stmt_bind_param($stmt_item, "iid", $rc_id, $fatura_id, $valor_fatura);
            if(!mysqli_stmt_execute($stmt_item)) {
                $erro_faturas = true;
                break;
            }
            
            // Registrar pagamento na fatura
            $sql_pagamento = "INSERT INTO pagamentos_recepcao (factura_recepcao_id, valor_pago, metodo_pagamento, usuario) 
                             VALUES (?, ?, ?, ?)";
            $stmt_pagamento = mysqli_prepare($db, $sql_pagamento);
            if($stmt_pagamento) {
                mysqli_stmt_bind_param($stmt_pagamento, "idsi", $fatura_id, $valor_fatura, $metodo, $userID);
                mysqli_stmt_execute($stmt_pagamento);
            }
        }
        
        if($erro_faturas) {
            $sql_delete_rc = "DELETE FROM recibo_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_rc);
            mysqli_stmt_bind_param($stmt_del, "i", $rc_id);
            mysqli_stmt_execute($stmt_del);
            echo 2;
            exit;
        }
        
        // Limpar tabela temporária
        $sql_delete = "DELETE FROM rc_faturas_temp_recepcao WHERE user = ?";
        $stmt_delete = mysqli_prepare($db, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $userID);
        mysqli_stmt_execute($stmt_delete);
        
        echo $rc_id;
    } else {
        echo 2;
    }
} else {
    echo 3;
}
?>

