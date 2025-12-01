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

if (!isset($_POST['codbar'])) {
    echo 40; // Parâmetros não enviados
    exit;
}

$codbar = $_POST['codbar'];
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

if ($result) {
    if(mysqli_num_rows($result) > 0) {
        // Obtenha informações do produto
        $sql = "SELECT * FROM produto WHERE codbar = ?";
        $stmt = mysqli_prepare($db, $sql);
        if (!$stmt) {
            echo "Erro ao preparar consulta: " . mysqli_error($db);
            exit;
        }
        mysqli_stmt_bind_param($stmt, "s", $codbar);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if(mysqli_num_rows($result) > 0) {
                $dados = mysqli_fetch_array($result);
                $idp = $dados['idproduto'];
                // Obtenha o lote mais próximo de expiração
                $sql = "SELECT * FROM stock WHERE produto_id = ? AND quantidade > 0 AND estado = 'ativo' ORDER BY prazo DESC, quantidade DESC LIMIT 1";
                $stmt = mysqli_prepare($db, $sql);
                if (!$stmt) {
                    echo "Erro ao preparar consulta: " . mysqli_error($db);
                    exit;
                }
                mysqli_stmt_bind_param($stmt, "i", $idp);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($result && mysqli_num_rows($result) > 0) {
                    $lote = mysqli_fetch_array($result);
                    $stock_qty = $lote['quantidade'];
                    $lt = $lote['lote'];
                    $preco = $dados['preco'];
                    $iv = $dados['iva'];
                    $iva = $iv / 100;

                    // Verifique a fila de espera
                    $sql = "SELECT * FROM filadeespera WHERE produtofiladeespera = ? AND usuariofiladeespera = ? AND lote = ?";
                    $stmt = mysqli_prepare($db, $sql);
                    if (!$stmt) {
                        echo "Erro ao preparar consulta: " . mysqli_error($db);
                        exit;
                    }
                    mysqli_stmt_bind_param($stmt, "iis", $idp, $userID, $lt);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if ($result) {
                        if(mysqli_num_rows($result) > 0) {
                            $dados1 = mysqli_fetch_array($result);
                            $qtd = $dados1['qtdfiladeespera'];

                            // Verifique se a quantidade disponível no estoque é suficiente
                            if (($stock_qty - $qtd - 1) < 0) {
                                echo 2; // Estoque insuficiente
                                exit;
                            } else {
                                // Atualize a fila de espera
                                $sql = "UPDATE filadeespera SET qtdfiladeespera = qtdfiladeespera + 1, totalfiladeespera = qtdfiladeespera * precofiladeespera, iva = totalfiladeespera * ? WHERE produtofiladeespera = ? AND usuariofiladeespera = ? AND lote = ?";
                                $stmt = mysqli_prepare($db, $sql);
                                if (!$stmt) {
                                    echo "Erro ao preparar consulta: " . mysqli_error($db);
                                    exit;
                                }
                                mysqli_stmt_bind_param($stmt, "diis", $iva, $idp, $userID, $lt);
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
                                $totall = $preco * $iva;
                                $sql = "INSERT INTO filadeespera(produtofiladeespera, qtdfiladeespera, precofiladeespera, totalfiladeespera, iva, usuariofiladeespera, lote) VALUES (?, ?, ?, ?, ?, ?, ?)";
                                $stmt = mysqli_prepare($db, $sql);
                                if (!$stmt) {
                                    echo "Erro ao preparar consulta: " . mysqli_error($db);
                                    exit;
                                }
                                mysqli_stmt_bind_param($stmt, "iidddis", $idp, $qt, $preco, $preco, $totall, $userID, $lt);
                                if(mysqli_stmt_execute($stmt)) {
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
    } else {
        echo 1; // Caixa fechado
        exit;
    }
} else {
    echo "Erro ao executar consulta: " . mysqli_error($db);
    exit;
}
?>

