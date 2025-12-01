<?php
session_start();
include '../../../conexao/index.php';

// Gerar número de processo único
// Formato: PROC-YYYY-NNNNNN (ex: PROC-2025-000001)

$ano = date('Y');

// Buscar o último número de processo do ano atual
$sql = "SELECT numero_processo FROM pacientes WHERE numero_processo LIKE 'PROC-$ano-%' ORDER BY numero_processo DESC LIMIT 1";
$rs = mysqli_query($db, $sql);

if ($rs && mysqli_num_rows($rs) > 0) {
    $ultimo = mysqli_fetch_array($rs);
    $ultimo_numero = $ultimo['numero_processo'];
    
    // Extrair o número sequencial
    $partes = explode('-', $ultimo_numero);
    if (count($partes) == 3) {
        $sequencial = intval($partes[2]) + 1;
    } else {
        $sequencial = 1;
    }
} else {
    $sequencial = 1;
}

// Formatar com zeros à esquerda (6 dígitos)
$numero_processo = 'PROC-' . $ano . '-' . str_pad($sequencial, 6, '0', STR_PAD_LEFT);

echo $numero_processo;
?>

