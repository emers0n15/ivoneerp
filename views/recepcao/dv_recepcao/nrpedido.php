<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$year = date('Y');
$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql);
$serie = $year;
if($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $serie = $dados['serie'] ?? $year;
}

// Buscar próximo número de documento para DV
$check_table = "SHOW TABLES LIKE 'devolucao_recepcao'";
$rs_check = mysqli_query($db, $check_table);
if($rs_check && mysqli_num_rows($rs_check) > 0) {
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
    
    echo "DV#" . $serie . "/" . str_pad($new_id, 6, '0', STR_PAD_LEFT);
} else {
    echo "DV#---/------";
}
?>

