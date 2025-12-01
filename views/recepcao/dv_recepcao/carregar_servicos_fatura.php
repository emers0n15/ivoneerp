<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$tableExistsFn = function(mysqli $con, string $nomeTabela): bool {
    $nomeTabela = mysqli_real_escape_string($con, $nomeTabela);
    $resultado = mysqli_query($con, "SHOW TABLES LIKE '$nomeTabela'");
    return $resultado && mysqli_num_rows($resultado) > 0;
};

$userID = $_SESSION['idUsuario'] ?? null;
$fatura_id = isset($_POST['fatura_id']) && $_POST['fatura_id'] != '' && $_POST['fatura_id'] != 'null' ? intval($_POST['fatura_id']) : null;

// Log para debug
error_log("DV carregar_servicos: userID=" . ($userID ?? 'null') . ", fatura_id=" . ($fatura_id ?? 'null') . ", POST=" . json_encode($_POST));

if(!$fatura_id || $fatura_id <= 0) {
    error_log("DV carregar_servicos: fatura_id inválido - " . ($_POST['fatura_id'] ?? 'não definido'));
    echo 40; // Parâmetros insuficientes
    exit;
}

if(!$userID || $userID <= 0) {
    error_log("DV carregar_servicos: userID inválido - " . ($userID ?? 'não definido'));
    echo 40;
    exit;
}

// Verificar se a tabela temporária existe
$check_table = "SHOW TABLES LIKE 'dv_servicos_temp'";
$table_exists = mysqli_query($db, $check_table);
if(!$table_exists || mysqli_num_rows($table_exists) == 0) {
    error_log("Erro: Tabela dv_servicos_temp não existe");
    echo 4;
    exit;
}

// Limpar serviços temporários anteriores do usuário para esta fatura
$sql_limpar = "DELETE FROM dv_servicos_temp WHERE user = ? AND factura_recepcao_id = ?";
$stmt_limpar = mysqli_prepare($db, $sql_limpar);
mysqli_stmt_bind_param($stmt_limpar, "ii", $userID, $fatura_id);
mysqli_stmt_execute($stmt_limpar);

// Buscar informações da fatura
$sql_fatura = "SELECT * FROM factura_recepcao WHERE id = ?";
$stmt_fatura = mysqli_prepare($db, $sql_fatura);
mysqli_stmt_bind_param($stmt_fatura, "i", $fatura_id);
mysqli_stmt_execute($stmt_fatura);
$rs_fatura = mysqli_stmt_get_result($stmt_fatura);

if(!$rs_fatura || mysqli_num_rows($rs_fatura) == 0) {
    echo 10; // Fatura não encontrada
    exit;
}

$fatura_data = mysqli_fetch_array($rs_fatura);
$empresa_id = isset($fatura_data['empresa_id']) ? intval($fatura_data['empresa_id']) : null;

// Buscar serviços da fatura
$check_table_serv = "SHOW TABLES LIKE 'fa_servicos_fact_recepcao'";
$table_serv_exists = mysqli_query($db, $check_table_serv);
if(!$table_serv_exists || mysqli_num_rows($table_serv_exists) == 0) {
    echo 4; // Tabela não existe
    exit;
}

$tem_dv_det = $tableExistsFn($db, 'dv_servicos_fact') && $tableExistsFn($db, 'devolucao_recepcao');

if($tem_dv_det) {
    $sql_servicos = "SELECT 
                        fs.servico,
                        SUM(fs.qtd) AS qtd_total,
                        SUM(fs.total) AS total_original,
                        (CASE WHEN SUM(fs.qtd) > 0 THEN SUM(fs.total)/SUM(fs.qtd) ELSE 0 END) AS preco_unit,
                        (SUM(fs.qtd) - (
                            SELECT COALESCE(SUM(dvf.qtd), 0)
                            FROM dv_servicos_fact dvf
                            INNER JOIN devolucao_recepcao dv ON dv.id = dvf.devolucao_id
                            WHERE dv.factura_recepcao_id = ? AND dvf.servico = fs.servico
                        )) AS qtd_disponivel
                     FROM fa_servicos_fact_recepcao fs 
                     WHERE fs.factura = ?
                     GROUP BY fs.servico
                     HAVING qtd_disponivel > 0";
    $stmt_servicos = mysqli_prepare($db, $sql_servicos);
    mysqli_stmt_bind_param($stmt_servicos, "ii", $fatura_id, $fatura_id);
} else {
    $sql_servicos = "SELECT 
                        fs.servico,
                        SUM(fs.qtd) AS qtd_total,
                        SUM(fs.total) AS total_original,
                        (CASE WHEN SUM(fs.qtd) > 0 THEN SUM(fs.total)/SUM(fs.qtd) ELSE 0 END) AS preco_unit,
                        SUM(fs.qtd) AS qtd_disponivel
                     FROM fa_servicos_fact_recepcao fs 
                     WHERE fs.factura = ?
                     GROUP BY fs.servico
                     HAVING qtd_disponivel > 0";
    $stmt_servicos = mysqli_prepare($db, $sql_servicos);
    mysqli_stmt_bind_param($stmt_servicos, "i", $fatura_id);
}

mysqli_stmt_execute($stmt_servicos);
$rs_servicos = mysqli_stmt_get_result($stmt_servicos);

if(!$rs_servicos || mysqli_num_rows($rs_servicos) == 0) {
    echo 7; // Nenhum serviço disponível para devolução
    exit;
}

// Inserir serviços disponíveis na tabela temporária
$erro = false;
$servicos_inseridos = 0;
while($servico = mysqli_fetch_array($rs_servicos)) {
    $servico_id = intval($servico['servico']);
    $qtd = intval($servico['qtd_disponivel']);
    if($qtd <= 0) {
        continue;
    }
    $preco = floatval($servico['preco_unit']);
    $total = $preco * $qtd;
    
    if($empresa_id && $empresa_id > 0) {
        $sql_insert = "INSERT INTO dv_servicos_temp(servico, qtd, preco, total, user, empresa_id, factura_recepcao_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($db, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiddiii", $servico_id, $qtd, $preco, $total, $userID, $empresa_id, $fatura_id);
    } else {
        $sql_insert = "INSERT INTO dv_servicos_temp(servico, qtd, preco, total, user, factura_recepcao_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($db, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiddii", $servico_id, $qtd, $preco, $total, $userID, $fatura_id);
    }
    
    if(!mysqli_stmt_execute($stmt_insert)) {
        $erro = true;
        error_log("Erro ao inserir serviço na devolução: " . mysqli_error($db));
        break;
    }
    $servicos_inseridos++;
}

if($erro) {
    echo 31; // Erro ao inserir serviços
} elseif($servicos_inseridos === 0) {
    echo 7; // Nenhum serviço disponível após validação
} else {
    echo 3; // Sucesso
}
?>

