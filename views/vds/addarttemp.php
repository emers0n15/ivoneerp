<?php
session_start();
include_once '../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/* Variáveis do Sistema */
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;

if (!isset($_POST['idProduto'])) {
    echo 40; // Parâmetros não enviados
    exit;
}

$idProduto = $_POST['idProduto'];
$status = "Aberto";

// Verifique se o caixa está aberto
$sql = "SELECT diaperiodo FROM periodo WHERE diaperiodo = ? AND usuario = ?";
$stmt = mysqli_prepare($db, $sql);
if (!$stmt) {
    echo "Erro ao preparar consulta: " . mysqli_error($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "ss", $status, $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    if (!isset($_POST['idProduto'])) {
    echo 40; // Parâmetros não enviados
    exit;
}

$idProduto = $_POST['idProduto'];

$sql = "SELECT * FROM produto WHERE idproduto = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idProduto);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $dados = mysqli_fetch_array($result);
    $stocavel = $dados['stocavel'];
    $preco = $dados['preco'];
    $iva = $dados['iva'] / 100;

    if ($stocavel = 1) {
        // Produtos estocáveis
        $sql = "SELECT lote FROM stock WHERE produto_id = ? AND quantidade > 0 AND estado = 'ativo' ORDER BY prazo ASC LIMIT 1";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idProduto);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $lote = mysqli_fetch_array($result);
            $lt = $lote['lote'];
            $qt = 1;
            $totall = $preco * $qt;
            $ivs = $totall * $iva;

            
            // Verificar se a linha já existe
            $sql = "SELECT qtdfiladeespera FROM filadeespera WHERE produtofiladeespera = ? AND usuariofiladeespera = ? AND lote = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iis", $idProduto, $userID, $lt);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                // Atualizar a quantidade
                $sql = "UPDATE filadeespera SET qtdfiladeespera = qtdfiladeespera + ?, totalfiladeespera = totalfiladeespera + ?, iva = iva + ? 
                        WHERE produtofiladeespera = ? AND usuariofiladeespera = ? AND lote = ?";
                $stmt = mysqli_prepare($db, $sql);
                mysqli_stmt_bind_param($stmt, "iddiis", $qt, $totall, $ivs, $idProduto, $userID, $lt);
                echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
            } else {
                // Inserir nova linha
                $sql = "INSERT INTO filadeespera (produtofiladeespera, qtdfiladeespera, precofiladeespera, totalfiladeespera, iva, usuariofiladeespera, lote) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($db, $sql);
                mysqli_stmt_bind_param($stmt, "iidddis", $idProduto, $qt, $preco, $totall, $ivs, $userID, $lt);
                echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
            }
        } else {
            echo 7; // Sem estoque
        }
    } else {
        // Produtos não estocáveis
        $qt = 1;
        $totall = $preco * $qt;
        $ivs = $totall * $iva;

        // Verificar se a linha já existe
        $sql = "SELECT qtdfiladeespera FROM filadeespera WHERE produtofiladeespera = ? AND usuariofiladeespera = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $idProduto, $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            // Atualizar a quantidade
            $sql = "UPDATE filadeespera SET qtdfiladeespera = qtdfiladeespera + ?, totalfiladeespera = totalfiladeespera + ?, iva = iva + ? 
                    WHERE produtofiladeespera = ? AND usuariofiladeespera = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iddii", $qt, $totall, $ivs, $idProduto, $userID);
            echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
        } else {
            // Inserir nova linha
            $sql = "INSERT INTO filadeespera (produtofiladeespera, qtdfiladeespera, precofiladeespera, totalfiladeespera, iva, usuariofiladeespera) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iidddi", $idProduto, $qt, $preco, $totall, $ivs, $userID);
            echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
        }
    }

} else {
    echo 10; // Produto não encontrado
}
} else {
    echo 1; // Caixa fechado
    exit;
}
?>
