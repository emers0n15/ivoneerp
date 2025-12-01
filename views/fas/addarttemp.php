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

// Obtenha informações do produto
$sql = "SELECT * FROM produto WHERE idproduto = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idProduto);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $dados = mysqli_fetch_array($result);
    $stocavel = $dados['stocavel']; // Verifica se o produto é estocável
    $preco = $dados['preco'];
    $iv = $dados['iva'];
    $iva = $iv / 100;

    if ($stocavel = 1) {
        // Produto estocável - segue o fluxo de estoque
        $sql = "SELECT * FROM stock WHERE produto_id = ? AND quantidade > 0 AND estado = 'ativo' ORDER BY prazo ASC, quantidade DESC LIMIT 1";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idProduto);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $lote = mysqli_fetch_array($result);
            $stock_qty = $lote['quantidade']; // Quantidade no lote
            $lt = $lote['lote'];

            // Verifique a fila de espera
            $sql = "SELECT * FROM fa_artigos_temp WHERE artigo = ? AND user = ? AND lote = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iss", $idProduto, $userID, $lt);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $dados1 = mysqli_fetch_array($result);
                $qt = $dados1['qtd'];

                // Verifique se a quantidade disponível no estoque é suficiente
                if (($stock_qty - $qt - 1) < 0) {
                    echo 1; // Estoque insuficiente
                    exit;
                } else {
                    // Atualize o artigo na tabela temporária
                    $sql = "UPDATE fa_artigos_temp SET qtd = qtd + 1, total = total + ?, iva = iva + ? WHERE artigo = ? AND user = ? AND lote = ?";
                    $total = $preco + ($preco * $iva);
                    $valor_iva = $preco * $iva; // Valor do IVA
                    $stmt = mysqli_prepare($db, $sql);
                    mysqli_stmt_bind_param($stmt, "ddiss", $total, $valor_iva, $idProduto, $userID, $lt);
                    mysqli_stmt_execute($stmt);
                    echo (mysqli_stmt_affected_rows($stmt) > 0) ? 3 : 31;
                    exit;
                }
            } else {
                // Adicione o produto à fila de espera
                if ($stock_qty <= 0) {
                    echo 2; // Sem estoque
                    exit;
                } else {
                    $qt = 1;
                    $total =$preco * $iva;
                    $sql = "INSERT INTO fa_artigos_temp(artigo, qtd, preco, iva, total, user, lote) VALUES(?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($db, $sql);
                    mysqli_stmt_bind_param($stmt, "iidddss", $idProduto, $qt, $preco, $total, $preco, $userID, $lt);
                    echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
                    exit;
                }
            }
        } else {
            echo 7; // Sem estoque ou expirado
            exit;
        }
    } else {
        // Produto não estocável - apenas insira diretamente
        $qt = 1;
        $valor_iva = $preco * $iva; // Valor do IVA
        $total = $preco + $valor_iva;

        $sql = "SELECT * FROM fa_artigos_temp WHERE artigo = ? AND user = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "is", $idProduto, $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $sql = "UPDATE fa_artigos_temp SET qtd = qtd + 1, total = total + ?, iva = total * ? WHERE artigo = ? AND user = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "ddis", $total, $iva, $idProduto, $userID);
            echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
            exit;
        } else {

            $sql = "INSERT INTO fa_artigos_temp(artigo, qtd, preco, iva, total, user) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iidddi", $idProduto, $qt, $preco, $valor_iva, $total, $userID);
            echo (mysqli_stmt_execute($stmt)) ? 3 : 31;
            exit;
        }
    }
} else {
    echo 10; // Artigo não encontrado
    exit;
}
?>
