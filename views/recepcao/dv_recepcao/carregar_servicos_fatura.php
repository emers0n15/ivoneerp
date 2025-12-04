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
// IMPORTANTE: aqui o parâmetro fatura_id passou a representar o ID da VDS (venda_dinheiro_servico)
$vds_id = isset($_POST['fatura_id']) && $_POST['fatura_id'] != '' && $_POST['fatura_id'] != 'null' ? intval($_POST['fatura_id']) : null;

// Log para debug
error_log("DV carregar_servicos (VDS): userID=" . ($userID ?? 'null') . ", vds_id=" . ($vds_id ?? 'null') . ", POST=" . json_encode($_POST));

if(!$vds_id || $vds_id <= 0) {
    error_log("DV carregar_servicos: vds_id inválido - " . ($_POST['fatura_id'] ?? 'não definido'));
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

// Limpar serviços temporários anteriores do usuário para esta VDS
$sql_limpar = "DELETE FROM dv_servicos_temp WHERE user = ? AND factura_recepcao_id = ?";
$stmt_limpar = mysqli_prepare($db, $sql_limpar);
mysqli_stmt_bind_param($stmt_limpar, "ii", $userID, $vds_id);
mysqli_stmt_execute($stmt_limpar);

// Buscar informações da VDS
$sql_vds = "SELECT * FROM venda_dinheiro_servico WHERE id = ?";
$stmt_vds = mysqli_prepare($db, $sql_vds);
mysqli_stmt_bind_param($stmt_vds, "i", $vds_id);
mysqli_stmt_execute($stmt_vds);
$rs_vds = mysqli_stmt_get_result($stmt_vds);

if(!$rs_vds || mysqli_num_rows($rs_vds) == 0) {
    echo 10; // VDS não encontrada
    exit;
}

$vds_data = mysqli_fetch_array($rs_vds);
$empresa_id = isset($vds_data['empresa_id']) ? intval($vds_data['empresa_id']) : null;

// Buscar serviços da VDS
$check_table_serv = "SHOW TABLES LIKE 'vds_servicos_fact'";
$table_serv_exists = mysqli_query($db, $check_table_serv);
if(!$table_serv_exists || mysqli_num_rows($table_serv_exists) == 0) {
    echo 4; // Tabela não existe
    exit;
}

// Para VDS, buscar serviços a partir de vds_servicos_fact e descontar o que já foi devolvido
$tem_dv_det = $tableExistsFn($db, 'dv_servicos_fact') && $tableExistsFn($db, 'devolucao_recepcao');

if($tem_dv_det) {
    $sql_servicos = "SELECT 
                        vs.servico,
                        SUM(vs.qtd) AS qtd_total,
                        SUM(vs.total) AS total_original,
                        (CASE WHEN SUM(vs.qtd) > 0 THEN SUM(vs.total)/SUM(vs.qtd) ELSE 0 END) AS preco_unit,
                        (SUM(vs.qtd) - (
                            SELECT COALESCE(SUM(dvf.qtd), 0)
                            FROM dv_servicos_fact dvf
                            INNER JOIN devolucao_recepcao dv ON dv.id = dvf.devolucao_id
                            WHERE dv.factura_recepcao_id = ? AND dvf.servico = vs.servico
                        )) AS qtd_disponivel
                     FROM vds_servicos_fact vs 
                     WHERE vs.vds_id = ?
                     GROUP BY vs.servico
                     HAVING qtd_disponivel > 0";
    $stmt_servicos = mysqli_prepare($db, $sql_servicos);
    mysqli_stmt_bind_param($stmt_servicos, "ii", $vds_id, $vds_id);
} else {
    $sql_servicos = "SELECT 
                        vs.servico,
                        SUM(vs.qtd) AS qtd_total,
                        SUM(vs.total) AS total_original,
                        (CASE WHEN SUM(vs.qtd) > 0 THEN SUM(vs.total)/SUM(vs.qtd) ELSE 0 END) AS preco_unit,
                        SUM(vs.qtd) AS qtd_disponivel
                     FROM vds_servicos_fact vs 
                     WHERE vs.vds_id = ?
                     GROUP BY vs.servico
                     HAVING qtd_disponivel > 0";
    $stmt_servicos = mysqli_prepare($db, $sql_servicos);
    mysqli_stmt_bind_param($stmt_servicos, "i", $vds_id);
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
        mysqli_stmt_bind_param($stmt_insert, "iiddiii", $servico_id, $qtd, $preco, $total, $userID, $empresa_id, $vds_id);
    } else {
        $sql_insert = "INSERT INTO dv_servicos_temp(servico, qtd, preco, total, user, factura_recepcao_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($db, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiddii", $servico_id, $qtd, $preco, $total, $userID, $vds_id);
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

