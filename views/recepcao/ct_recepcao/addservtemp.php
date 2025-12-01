<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$userID = $_SESSION['idUsuario'] ?? null;

if (!isset($_POST['idServico'])) {
    echo 40;
    exit;
}

$idServico = $_POST['idServico'];
$empresa_id = isset($_POST['empresa_id']) && $_POST['empresa_id'] != '' && $_POST['empresa_id'] != 'null' ? intval($_POST['empresa_id']) : null;

// Verificar se a tabela temporária existe
$check_table = "SHOW TABLES LIKE 'ct_servicos_temp'";
$table_exists = mysqli_query($db, $check_table);
if(!$table_exists || mysqli_num_rows($table_exists) == 0) {
    error_log("Erro: Tabela ct_servicos_temp não existe");
    echo 4;
    exit;
}

$sql = "SELECT * FROM servicos_clinica WHERE id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idServico);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $dados = mysqli_fetch_array($result);
    $preco = floatval($dados['preco']);
    
    if($empresa_id && $empresa_id > 0) {
        $sql_empresa = "SELECT tabela_precos_id, desconto_geral FROM empresas_seguros WHERE id = ?";
        $stmt = mysqli_prepare($db, $sql_empresa);
        mysqli_stmt_bind_param($stmt, "i", $empresa_id);
        mysqli_stmt_execute($stmt);
        $rs_empresa = mysqli_stmt_get_result($stmt);
        
        if($rs_empresa && mysqli_num_rows($rs_empresa) > 0) {
            $empresa_data = mysqli_fetch_array($rs_empresa);
            $tabela_precos_id = $empresa_data['tabela_precos_id'] ?? null;
            $desconto_geral = floatval($empresa_data['desconto_geral'] ?? 0);
            
            if($tabela_precos_id) {
                $sql_preco = "SELECT preco, desconto_percentual FROM tabela_precos_servicos 
                             WHERE tabela_precos_id = ? AND servico_id = ?";
                $stmt = mysqli_prepare($db, $sql_preco);
                mysqli_stmt_bind_param($stmt, "ii", $tabela_precos_id, $idServico);
                mysqli_stmt_execute($stmt);
                $rs_preco = mysqli_stmt_get_result($stmt);
                
                if($rs_preco && mysqli_num_rows($rs_preco) > 0) {
                    $preco_data = mysqli_fetch_array($rs_preco);
                    $preco = floatval($preco_data['preco']);
                    if(isset($preco_data['desconto_percentual']) && $preco_data['desconto_percentual'] > 0) {
                        $preco = $preco * (1 - floatval($preco_data['desconto_percentual']) / 100);
                    }
                } else {
                    if($desconto_geral > 0) {
                        $preco = $preco * (1 - $desconto_geral / 100);
                    }
                }
            } else {
                if($desconto_geral > 0) {
                    $preco = $preco * (1 - $desconto_geral / 100);
                }
            }
        }
    }
    
    $qt = 1;
    $total = $preco;

    if($empresa_id && $empresa_id > 0) {
        $sql = "SELECT * FROM ct_servicos_temp WHERE servico = ? AND user = ? AND empresa_id = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $idServico, $userID, $empresa_id);
    } else {
        $sql = "SELECT * FROM ct_servicos_temp WHERE servico = ? AND user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $idServico, $userID);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        if($empresa_id && $empresa_id > 0) {
            $sql = "UPDATE ct_servicos_temp SET qtd = qtd + 1, total = (qtd + 1)*preco WHERE servico = ? AND user = ? AND empresa_id = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $idServico, $userID, $empresa_id);
        } else {
            $sql = "UPDATE ct_servicos_temp SET qtd = qtd + 1, total = (qtd + 1)*preco WHERE servico = ? AND user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $idServico, $userID);
        }
        echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
        exit;
    } else {
        if($empresa_id && $empresa_id > 0) {
            $sql = "INSERT INTO ct_servicos_temp(servico, qtd, preco, total, user, empresa_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iiddii", $idServico, $qt, $preco, $total, $userID, $empresa_id);
        } else {
            $sql = "INSERT INTO ct_servicos_temp(servico, qtd, preco, total, user) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iiddi", $idServico, $qt, $preco, $total, $userID);
        }
        $result = mysqli_stmt_execute($stmt);
        if($result) {
            echo 3;
        } else {
            error_log("Erro ao inserir serviço: " . mysqli_error($db));
            echo 31;
        }
        exit;
    }
} else {
    echo 10;
    exit;
}
?>

