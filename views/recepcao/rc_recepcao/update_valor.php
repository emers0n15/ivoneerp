<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$fatura_id = $_POST['fatura_id'] ?? null;
$novoValor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;

if(!$fatura_id || $novoValor < 0) {
    echo "Erro: Parâmetros inválidos";
    exit;
}

// Buscar valor pendente da fatura
$sql_fatura = "SELECT f.valor,
               COALESCE((SELECT SUM(valor_pago) FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id), 0) as total_pago
               FROM factura_recepcao f WHERE f.id = ?";
$stmt_fatura = mysqli_prepare($db, $sql_fatura);
mysqli_stmt_bind_param($stmt_fatura, "i", $fatura_id);
mysqli_stmt_execute($stmt_fatura);
$rs_fatura = mysqli_stmt_get_result($stmt_fatura);

if($rs_fatura && mysqli_num_rows($rs_fatura) > 0) {
    $fatura_data = mysqli_fetch_array($rs_fatura);
    $valor_total = floatval($fatura_data['valor']);
    $total_pago = floatval($fatura_data['total_pago']);
    $valor_pendente = $valor_total - $total_pago;
    
    // Validar que o valor não seja maior que o pendente
    if($novoValor > $valor_pendente) {
        $novoValor = $valor_pendente;
    }
}

$userID = $_SESSION['idUsuario'] ?? null;

// Verificar se a tabela temporária existe
$check_table_temp = "SHOW TABLES LIKE 'rc_faturas_temp_recepcao'";
$table_temp_exists = mysqli_query($db, $check_table_temp);
if(!$table_temp_exists || mysqli_num_rows($table_temp_exists) == 0) {
    echo "Erro: Tabela temporária não existe";
    exit;
}

$sql = "UPDATE rc_faturas_temp_recepcao SET valor = ? WHERE factura_recepcao_id = ? AND user = ?";
$stmt = mysqli_prepare($db, $sql);
if($stmt) {
    mysqli_stmt_bind_param($stmt, "dii", $novoValor, $fatura_id, $userID);
    mysqli_stmt_execute($stmt);
}
?>

