<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = intval($_POST['usar'] ?? $_SESSION['idUsuario']);
$valor = floatval($_POST['valor'] ?? 0);
$data_hoje = date('Y-m-d');

if($valor <= 0) {
    echo "<p class='mt-2' style='color: red;'>Por favor, informe um valor válido!</p>";
    exit;
}

// Verificar se já existe caixa aberto
$sql_check = "SELECT * FROM caixa_recepcao WHERE data = ? AND status = 'aberto'";
$stmt_check = mysqli_prepare($db, $sql_check);
mysqli_stmt_bind_param($stmt_check, "s", $data_hoje);
mysqli_stmt_execute($stmt_check);
$rs_check = mysqli_stmt_get_result($stmt_check);

if ($rs_check && mysqli_num_rows($rs_check) > 0) {
    echo "<p class='mt-2' style='color: orange;'>O caixa já se encontra aberto para hoje!</p>";
} else {
    // Verificar se existe caixa fechado para hoje
    $sql_check_fechado = "SELECT * FROM caixa_recepcao WHERE data = ? AND status = 'fechado'";
    $stmt_check_fechado = mysqli_prepare($db, $sql_check_fechado);
    mysqli_stmt_bind_param($stmt_check_fechado, "s", $data_hoje);
    mysqli_stmt_execute($stmt_check_fechado);
    $rs_check_fechado = mysqli_stmt_get_result($stmt_check_fechado);
    
    if ($rs_check_fechado && mysqli_num_rows($rs_check_fechado) > 0) {
        // Reabrir caixa
        $sql_update = "UPDATE caixa_recepcao SET 
                      status = 'aberto', 
                      valor_inicial = ?,
                      usuario_abertura = ?,
                      data_abertura = NOW(),
                      usuario_fechamento = NULL,
                      data_fechamento = NULL
                      WHERE data = ?";
        $stmt_update = mysqli_prepare($db, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "dis", $valor, $userID, $data_hoje);
        
        if(mysqli_stmt_execute($stmt_update)) {
            echo "<p class='mt-2' style='color: green;'>Caixa reaberto com sucesso!</p>";
        } else {
            echo "<p class='mt-2' style='color: red;'>Ocorreu um erro ao reabrir o caixa!</p>";
        }
    } else {
        // Criar novo caixa
        $sql = "INSERT INTO caixa_recepcao (data, valor_inicial, status, usuario_abertura, data_abertura) 
                VALUES (?, ?, 'aberto', ?, NOW())";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "sdi", $data_hoje, $valor, $userID);
        
        if(mysqli_stmt_execute($stmt)) {
            echo "<p class='mt-2' style='color: green;'>Caixa aberto com sucesso!</p>";
        } else {
            echo "<p class='mt-2' style='color: red;'>Ocorreu um erro ao abrir o caixa!</p>";
        }
    }
}
?>

