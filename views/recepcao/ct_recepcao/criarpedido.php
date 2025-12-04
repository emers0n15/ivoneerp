<?php
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;
$paciente_id = $_POST['paciente'] ?? null;
$empresa_id = $_POST['empresa_id'] ?? null;
$prazo = $_POST['prazo'] ?? '';

if(!$paciente_id || !$userID) {
    echo 1;
    exit;
}

// Buscar serviços da tabela temporária
if($empresa_id && $empresa_id > 0) {
    $sql_temp = "SELECT * FROM ct_servicos_temp WHERE user = ? AND empresa_id = ?";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $empresa_id);
} else {
    $sql_temp = "SELECT * FROM ct_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
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

// Buscar série fiscal
$sql_serie = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs_serie = mysqli_query($db, $sql_serie);
$serie = date('Y');
if($rs_serie && mysqli_num_rows($rs_serie) > 0) {
    $dados_serie = mysqli_fetch_array($rs_serie);
    $serie = $dados_serie['serie'] ?? date('Y');
}

$year = date('Y');
$data_hora = date("Y-m-d H:i:s");
$data = date("Y-m-d");

$serie = intval($serie);
    // Buscar próximo número de documento
    $sql_max = "SELECT MAX(n_doc) as maxid FROM cotacao_recepcao WHERE serie = ?";
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
    
    // Verificar se a tabela existe
    $check_table = "SHOW TABLES LIKE 'cotacao_recepcao'";
    $rs_check = mysqli_query($db, $check_table);
    if(!$rs_check || mysqli_num_rows($rs_check) == 0) {
        echo 4;
        exit;
    }
    
    // Inserir cotação
<<<<<<< HEAD
    $sql_ct = "INSERT INTO cotacao_recepcao SET 
        n_doc = ?, 
        paciente = ?, 
        empresa_id = ?, 
        valor = ?, 
        prazo = ?,
        serie = ?, 
        usuario = ?, 
        dataa = ?";
    
    $stmt_ct = mysqli_prepare($db, $sql_ct);
    $empresa_id_db = $empresa_id ? intval($empresa_id) : 0;
    $new_id = intval($new_id);
    $serie = intval($serie);
    $paciente_id = intval($paciente_id);
    $userID = intval($userID);
    $total = floatval($total);
    $prazo_db = $prazo ? $prazo : null;
    
    mysqli_stmt_bind_param($stmt_ct, "iiidsiis", 
        $new_id,
        $paciente_id,
        $empresa_id_db,
        $total,
        $prazo_db,
        $serie,
        $userID,
        $data
    );
=======
    // Verificar se a coluna "prazo" existe (para compatibilidade com bases antigas)
    $hasPrazo = false;
    $rs_col_prazo = mysqli_query($db, "SHOW COLUMNS FROM cotacao_recepcao LIKE 'prazo'");
    if ($rs_col_prazo && mysqli_num_rows($rs_col_prazo) > 0) {
        $hasPrazo = true;
    }

    if ($hasPrazo) {
        $sql_ct = "INSERT INTO cotacao_recepcao 
                      (n_doc, paciente, empresa_id, valor, prazo, serie, usuario, dataa)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_ct = mysqli_prepare($db, $sql_ct);
        $empresa_id_db = $empresa_id ? intval($empresa_id) : 0;
        $new_id = intval($new_id);
        $serie = intval($serie);
        $paciente_id = intval($paciente_id);
        $userID = intval($userID);
        $total = floatval($total);
        $prazo_db = $prazo ? $prazo : null;

        mysqli_stmt_bind_param(
            $stmt_ct,
            "iiidisis",
            $new_id,
            $paciente_id,
            $empresa_id_db,
            $total,
            $prazo_db,
            $serie,
            $userID,
            $data
        );
    } else {
        // Base antiga sem campo "prazo"
        $sql_ct = "INSERT INTO cotacao_recepcao 
                      (n_doc, paciente, empresa_id, valor, serie, usuario, dataa)
                   VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt_ct = mysqli_prepare($db, $sql_ct);
        $empresa_id_db = $empresa_id ? intval($empresa_id) : 0;
        $new_id = intval($new_id);
        $serie = intval($serie);
        $paciente_id = intval($paciente_id);
        $userID = intval($userID);
        $total = floatval($total);

        mysqli_stmt_bind_param(
            $stmt_ct,
            "iiidiis",
            $new_id,
            $paciente_id,
            $empresa_id_db,
            $total,
            $serie,
            $userID,
            $data
        );
    }
>>>>>>> 25a0cb3ed134b3fba392f117e5fda8254256a55b
    
    if(mysqli_stmt_execute($stmt_ct)) {
        $ct_id = mysqli_insert_id($db);
        
        if(!$ct_id) {
            echo 2;
            exit;
        }
        
        // Verificar se a tabela de serviços existe
        $check_table_serv = "SHOW TABLES LIKE 'ct_servicos_fact'";
        $rs_check_serv = mysqli_query($db, $check_table_serv);
        if(!$rs_check_serv || mysqli_num_rows($rs_check_serv) == 0) {
            $sql_delete_ct = "DELETE FROM cotacao_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_ct);
            mysqli_stmt_bind_param($stmt_del, "i", $ct_id);
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
            
            $sql_item = "INSERT INTO ct_servicos_fact (servico, qtd, preco, total, user, cotacao_id) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($db, $sql_item);
            if(!$stmt_item) {
                $erro_servicos = true;
                break;
            }
            mysqli_stmt_bind_param($stmt_item, "iiddii", $servico_id, $qtd, $preco, $total_item, $userID, $ct_id);
            if(!mysqli_stmt_execute($stmt_item)) {
                $erro_servicos = true;
                break;
            }
        }
        
        if($erro_servicos) {
            $sql_delete_ct = "DELETE FROM cotacao_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_ct);
            mysqli_stmt_bind_param($stmt_del, "i", $ct_id);
            mysqli_stmt_execute($stmt_del);
            echo 2;
            exit;
        }
        
        // Limpar tabela temporária
        if($empresa_id && $empresa_id > 0) {
            $sql_delete = "DELETE FROM ct_servicos_temp WHERE user = ? AND empresa_id = ?";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "ii", $userID, $empresa_id);
        } else {
            $sql_delete = "DELETE FROM ct_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "i", $userID);
        }
        mysqli_stmt_execute($stmt_delete);
        
        echo $ct_id;
    } else {
        echo 2;
    }

?>

