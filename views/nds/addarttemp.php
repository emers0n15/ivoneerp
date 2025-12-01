<?php 
session_start();
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/* Variáveis do Sistema */
/*********************************************/
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;
/*********************************************/

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

if ($result) {
    if(mysqli_num_rows($result) > 0) {
        $dados = mysqli_fetch_array($result);
        $preco = $dados['preco'];
        $iv = $dados['iva'];
        $iva = $iv / 100;

        // Obtenha o lote mais próximo de expiração com estoque disponível
        $sql = "SELECT * FROM stock WHERE produto_id = ? AND quantidade > 0 AND estado = 'ativo' ORDER BY prazo ASC, quantidade DESC LIMIT 1";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idProduto);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $lote = mysqli_fetch_array($result);
            $stock_qty = $lote['quantidade']; // Quantidade disponível no lote
            $lt = $lote['lote']; // Número do lote

            // Verifique a fila de espera
            $sql = "SELECT * FROM nd_artigos_temp WHERE artigo = ? AND user = ? AND lote = ?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "iss", $idProduto, $userID, $lt);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                if(mysqli_num_rows($result) > 0) {
                    $dados1 = mysqli_fetch_array($result);
                    $qt = $dados1['qtd'];

                    // Verifique se a quantidade disponível no estoque é suficiente
                    if (($stock_qty - $qt - 1) < 0) {
                        echo 1; // Estoque insuficiente
                        exit;
                    } else {
                        // Atualize o artigo na tabela temporária
                        $sql = "UPDATE nd_artigos_temp SET qtd = qtd + 1, total = total + ?, iva = total * ? WHERE artigo = ? AND user = ? AND lote = ?";
                        $total = $preco + ($preco * $iva);
                        $stmt = mysqli_prepare($db, $sql);
                        mysqli_stmt_bind_param($stmt, "ddiss", $total, $iva, $idProduto, $userID, $lt);
                        mysqli_stmt_execute($stmt);
                        if(mysqli_stmt_affected_rows($stmt) > 0) {
                            echo 3; // Sucesso na atualização
                            exit;
                        } else {
                            echo 31; // Erro ao atualizar
                            exit;
                        }
                    }
                } else {
                    // Se não existe na fila de espera, adicione
                    if ($stock_qty <= 0) {
                        echo 2; // Sem estoque
                        exit;
                    } else {
                        $qt = 1;
                        $total = $preco + ($preco * $iva);
                        $sql = "INSERT INTO nd_artigos_temp(artigo, qtd, preco, iva, total, user, lote) VALUES(?, ?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_prepare($db, $sql);
                        mysqli_stmt_bind_param($stmt, "iidddss", $idProduto, $qt, $preco, $iva, $total, $userID, $lt);
                        if(mysqli_stmt_execute($stmt) > 0) {
                            echo 3; // Sucesso na inserção
                            exit;
                        } else {
                            echo 31; // Erro ao inserir
                            exit;
                        }
                    }
                }
            } else {
                echo "Erro ao executar consulta: " . mysqli_error($db);
                exit;
            }
        } else {
            echo 7; // Artigo fora de estoque ou expirado
            exit;
        }
    } else {
        echo 10; // Artigo não encontrado
        exit;
    }
} else {
    echo "Erro ao executar consulta: " . mysqli_error($db);
    exit;
}
?>
