<?php
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
header('Content-Type: application/json');

$userID = $_SESSION['idUsuario'] ?? null;
$paciente_id = $_POST['paciente'] ?? null;
$empresa_id = $_POST['empresa_id'] ?? null;
$metodo = $_POST['metodo'] ?? 'dinheiro';
$valor = floatval($_POST['valor'] ?? 0);

if(!$paciente_id || !$userID) {
    echo json_encode(['error' => 'Parâmetros não enviados']);
    exit;
}

// Buscar serviços da tabela temporária
if($empresa_id && $empresa_id > 0) {
    $sql_temp = "SELECT * FROM vds_servicos_temp WHERE user = ? AND empresa_id = ?";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $empresa_id);
} else {
    $sql_temp = "SELECT * FROM vds_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "i", $userID);
}
mysqli_stmt_execute($stmt);
$rs_temp = mysqli_stmt_get_result($stmt);

if(!$rs_temp || mysqli_num_rows($rs_temp) == 0) {
    echo json_encode(['error' => 'Nenhum serviço selecionado']);
    exit;
}

// Calcular total
$total = 0;
while($servico_temp = mysqli_fetch_array($rs_temp)) {
    $total += floatval($servico_temp['total']);
}

// Verificar se o valor pago é suficiente
if($valor < $total) {
    echo json_encode(['ot' => 1000000000001]);
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
$data_hora = date("Y-m-d H:i:s");
$data = date("Y-m-d");

if ($year == $serie) {
    // Buscar próximo número de documento
    $sql_max = "SELECT MAX(n_doc) as maxid FROM venda_dinheiro_servico WHERE serie = ?";
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
    $check_table = "SHOW TABLES LIKE 'venda_dinheiro_servico'";
    $rs_check = mysqli_query($db, $check_table);
    if(!$rs_check || mysqli_num_rows($rs_check) == 0) {
        echo json_encode(['error' => 'Tabela venda_dinheiro_servico não existe']);
        exit;
    }
    
    // Inserir VDS
    $sql_vds = "INSERT INTO venda_dinheiro_servico SET 
        n_doc = ?, 
        paciente = ?, 
        empresa_id = ?, 
        valor = ?, 
        valor_pago = ?,
        metodo = ?,
        serie = ?, 
        usuario = ?, 
        dataa = ?";
    
    $stmt_vds = mysqli_prepare($db, $sql_vds);
    $empresa_id_db = $empresa_id ? intval($empresa_id) : 0;
    $new_id = intval($new_id);
    $serie = intval($serie);
    $paciente_id = intval($paciente_id);
    $userID = intval($userID);
    $total = floatval($total);
    $valor = floatval($valor);
    
    mysqli_stmt_bind_param($stmt_vds, "iiiddsiss", 
        $new_id,
        $paciente_id,
        $empresa_id_db,
        $total,
        $valor,
        $metodo,
        $serie,
        $userID,
        $data
    );
    
    if(mysqli_stmt_execute($stmt_vds)) {
        $vds_id = mysqli_insert_id($db);
        
        if(!$vds_id) {
            echo json_encode(['error' => 'Erro ao obter ID da venda']);
            exit;
        }
        
        // Verificar se a tabela de serviços existe
        $check_table_serv = "SHOW TABLES LIKE 'vds_servicos_fact'";
        $rs_check_serv = mysqli_query($db, $check_table_serv);
        if(!$rs_check_serv || mysqli_num_rows($rs_check_serv) == 0) {
            // Reverter a inserção
            $sql_delete_vds = "DELETE FROM venda_dinheiro_servico WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_vds);
            mysqli_stmt_bind_param($stmt_del, "i", $vds_id);
            mysqli_stmt_execute($stmt_del);
            echo json_encode(['error' => 'Tabela vds_servicos_fact não existe']);
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
            
            $sql_item = "INSERT INTO vds_servicos_fact (servico, qtd, preco, total, user, vds_id) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($db, $sql_item);
            if(!$stmt_item) {
                $erro_servicos = true;
                break;
            }
            mysqli_stmt_bind_param($stmt_item, "iiddii", $servico_id, $qtd, $preco, $total_item, $userID, $vds_id);
            if(!mysqli_stmt_execute($stmt_item)) {
                $erro_servicos = true;
                break;
            }
        }
        
        if($erro_servicos) {
            // Reverter a inserção
            $sql_delete_vds = "DELETE FROM venda_dinheiro_servico WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_vds);
            mysqli_stmt_bind_param($stmt_del, "i", $vds_id);
            mysqli_stmt_execute($stmt_del);
            echo json_encode(['error' => 'Erro ao inserir serviços']);
            exit;
        }
        
        // Verificar se o caixa está aberto
        $data_hoje = date('Y-m-d');
        $sql_caixa = "SELECT * FROM caixa_recepcao WHERE data = ? AND status = 'aberto'";
        $stmt_caixa = mysqli_prepare($db, $sql_caixa);
        mysqli_stmt_bind_param($stmt_caixa, "s", $data_hoje);
        mysqli_stmt_execute($stmt_caixa);
        $rs_caixa = mysqli_stmt_get_result($stmt_caixa);
        
        if(!$rs_caixa || mysqli_num_rows($rs_caixa) == 0) {
            // Verificar se já existe caixa fechado para hoje
            $sql_check = "SELECT * FROM caixa_recepcao WHERE data = ?";
            $stmt_check = mysqli_prepare($db, $sql_check);
            mysqli_stmt_bind_param($stmt_check, "s", $data_hoje);
            mysqli_stmt_execute($stmt_check);
            $rs_check = mysqli_stmt_get_result($stmt_check);
            
            if(!$rs_check || mysqli_num_rows($rs_check) == 0) {
                // Criar caixa se não existir
                $sql_criar_caixa = "INSERT INTO caixa_recepcao (data, status, usuario_abertura, data_abertura) 
                                   VALUES (?, 'aberto', ?, NOW())";
                $stmt_criar = mysqli_prepare($db, $sql_criar_caixa);
                mysqli_stmt_bind_param($stmt_criar, "si", $data_hoje, $userID);
                mysqli_stmt_execute($stmt_criar);
            } else {
                // Reabrir caixa se estiver fechado
                $sql_reabrir = "UPDATE caixa_recepcao SET status = 'aberto' WHERE data = ?";
                $stmt_reabrir = mysqli_prepare($db, $sql_reabrir);
                mysqli_stmt_bind_param($stmt_reabrir, "s", $data_hoje);
                mysqli_stmt_execute($stmt_reabrir);
            }
        }
        
        // Atualizar caixa com o pagamento
        $metodo_caixa = '';
        switch($metodo) {
            case 'dinheiro':
                $metodo_caixa = 'total_dinheiro';
                break;
            case 'm_pesa':
                $metodo_caixa = 'total_mpesa';
                break;
            case 'emola':
                $metodo_caixa = 'total_emola';
                break;
            case 'pos':
                $metodo_caixa = 'total_pos';
                break;
        }
        
        if($metodo_caixa) {
            // Usar prepared statement com nome de coluna fixo
            $sql_update_caixa = "UPDATE caixa_recepcao SET 
                                total_entradas = total_entradas + ?,
                                " . $metodo_caixa . " = " . $metodo_caixa . " + ?,
                                saldo_final = saldo_final + ?
                                WHERE data = ? AND status = 'aberto'";
            $stmt_update_caixa = mysqli_prepare($db, $sql_update_caixa);
            if($stmt_update_caixa) {
                mysqli_stmt_bind_param($stmt_update_caixa, "ddds", $valor, $valor, $valor, $data_hoje);
                mysqli_stmt_execute($stmt_update_caixa);
            }
        }
        
        // Limpar tabela temporária
        if($empresa_id && $empresa_id > 0) {
            $sql_delete = "DELETE FROM vds_servicos_temp WHERE user = ? AND empresa_id = ?";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "ii", $userID, $empresa_id);
        } else {
            $sql_delete = "DELETE FROM vds_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "i", $userID);
        }
        mysqli_stmt_execute($stmt_delete);
        
        echo json_encode(['id' => $vds_id]);
    } else {
        echo json_encode(['error' => 'Erro ao criar venda: ' . mysqli_error($db)]);
    }
} else {
    echo json_encode(['error' => 'Ano fiscal não corresponde ao ano atual']);
}
?>

