<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");
$year = date("Y");

/* Variáveis do Sistema */
/*********************************************/
$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/

$cliente = $_POST['cliente'];
$modo = $_POST['modo'];
$valu = $_POST['valor'];
$status = 'Aberto';

// Obter a série da última fatura
$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $serie = $dados['serie'];
}

// Obter desconto do cliente
$sql = "SELECT desconto FROM clientes WHERE id = '$cliente'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $desconto = $dados['desconto'];
    $disconto = $desconto / 100;
}

// Verificar se o período está aberto
$sqlBoxx = "SELECT idperiodo FROM periodo WHERE diaperiodo = 'Aberto' AND usuario = '$userID'";
$rsBoxx = mysqli_query($db, $sqlBoxx);
if (mysqli_num_rows($rsBoxx) > 0) {
    $d = mysqli_fetch_assoc($rsBoxx);
    $periodo = $d['idperiodo'];
}

// Obter IVA e total da fila de espera
$sqlAPiva = "SELECT SUM(iva) as iv, SUM(totalfiladeespera) as ty FROM filadeespera WHERE usuariofiladeespera = '$userID'";
$rsAPiva = mysqli_query($db, $sqlAPiva);
$dadosiva = mysqli_fetch_array($rsAPiva);
$ivo = $dadosiva['iv'];
$ty = $dadosiva['ty'];
$n = "1000000000001";
if ($valu < $ty) {
    echo json_encode(array('ot' => $n));
    exit;
} else {
    $value = $ty;
}

if ($year == $serie) {
    // Obter o próximo número de pedido
    $siquela = "SELECT MAX(n_doc) as maxid FROM pedido WHERE serie = '$serie'";
    $res = mysqli_query($db, $siquela) or die(mysqli_error($db));
    if (mysqli_num_rows($res) > 0) {
        
        $ddo = mysqli_fetch_array($res);
        $max_id = $ddo['maxid'];
        $new_id = $max_id + 1;

        // Inserir pedido na tabela
        $sqlFunc = "INSERT INTO pedido(n_doc, clientepedido, serie, pagamentopedido, iva, modo, disconto, userpedido, periodo) 
                    VALUES('$new_id','$cliente', '$serie', '$value', '$ivo', '$modo', '$disconto', '$userID', '$periodo')";
        $rsFunc = mysqli_query($db, $sqlFunc);

        $id = mysqli_insert_id($db);

        if ($rsFunc > 0) {

            // Obter os itens da fila de espera
            $sqlAP = "SELECT * FROM filadeespera WHERE usuariofiladeespera = '$userID'";
            $rsAP = mysqli_query($db, $sqlAP);
            while ($dados = mysqli_fetch_array($rsAP)) {
                $produto = $dados['produtofiladeespera'];
                $qtd = $dados['qtdfiladeespera'];
                $preco = $dados['precofiladeespera'];
                $total = $dados['totalfiladeespera'];
                $iva = $dados['iva'];
                $lts = $dados['lote'];
                $tt = ($iva + $total) - $disconto;

                // Inserir entrega
                $sqlEN = "INSERT INTO entrega(produtoentrega, qtdentrega, precoentrega, totalentrega, iva, clienteentrega, usuarioentrega, pedidoentrega, datavenda, periodo, lote) 
                          VALUES('$produto', '$qtd', '$preco', '$total', '$iva', '$cliente', '$userID', '$id', '$data_hora', '$periodo', '$lts')";
                $rsEN = mysqli_query($db, $sqlEN) or die(mysqli_error($db));

                // Atualizar quantidade no estoque
                if ($rsEN > 0) {

                    // Verificar se o produto é estocável
                    $sqlStocavel = "SELECT stocavel FROM produto WHERE idproduto = '$produto'";
                    $rsStocavel = mysqli_query($db, $sqlStocavel);
                    if (mysqli_num_rows($rsStocavel) > 0) {
                        $stocavelData = mysqli_fetch_assoc($rsStocavel);
                        if ($stocavelData['stocavel'] = 1) {
                            // Encontrar o lote mais próximo de expiração
                            $sqlStock = "SELECT id, quantidade, prazo, lote FROM stock WHERE produto_id = '$produto' AND lote = '$lts'";
                            $rsStock = mysqli_query($db, $sqlStock);
                            if (mysqli_num_rows($rsStock) > 0) {
                                $stockData = mysqli_fetch_array($rsStock);
                                $stockId = $stockData['id'];
                                $stockQuantity = $stockData['quantidade'];
                                $lotaria = $stockData['lote'];

                                // Diminuir a quantidade no lote
                                $newQuantity = $stockQuantity - $qtd;
                                if ($newQuantity < 0) {
                                    // Caso em que não há quantidade suficiente no lote atual
                                    // Vamos usar toda a quantidade disponível neste lote e procurar os próximos
                                    $quantidadeUsada = $stockQuantity; // Usar toda a quantidade disponível neste lote
                                    $quantidadeRestante = $qtd - $quantidadeUsada; // Calcular quantidade ainda necessária
                                    
                                    // Atualizar este lote como esgotado (quantidade = 0) e inativo
                                    $sqlUpdateCurrentLot = "UPDATE stock SET quantidade = 0, estado = 'inativo' WHERE id = '$stockId'";
                                    if (!mysqli_query($db, $sqlUpdateCurrentLot)) {
                                        echo json_encode(array('error' => "Erro ao atualizar estoque do lote atual: " . mysqli_error($db)));
                                        exit;
                                    }
                                    
                                    // Vamos processar a quantidade restante usando vários lotes se necessário
                                    $processarQuantidade = true;
                                    
                                    while ($quantidadeRestante > 0 && $processarQuantidade) {
                                        // Procurar o próximo lote com prazo mais próximo a findar
                                        $sqlNextLot = "SELECT id, quantidade, prazo, lote FROM stock 
                                                     WHERE produto_id = '$produto' AND quantidade > 0 AND estado = 'ativo'
                                                     ORDER BY prazo ASC LIMIT 1";
                                        $rsNextLot = mysqli_query($db, $sqlNextLot);
                                        
                                        if (mysqli_num_rows($rsNextLot) > 0) {
                                            $nextLotData = mysqli_fetch_array($rsNextLot);
                                            $nextLotId = $nextLotData['id'];
                                            $nextLotQuantity = $nextLotData['quantidade'];
                                            
                                            // Determinar quanto podemos retirar deste lote
                                            $quantidadeARetirar = min($quantidadeRestante, $nextLotQuantity);
                                            
                                            // Atualizar a quantidade no próximo lote
                                            $sqlUpdateNextLot = "UPDATE stock SET quantidade = quantidade - '$quantidadeARetirar' WHERE id = '$nextLotId'";
                                            if (!mysqli_query($db, $sqlUpdateNextLot)) {
                                                echo json_encode(array('error' => "Erro ao atualizar estoque do próximo lote: " . mysqli_error($db)));
                                                exit;
                                            }
                                            
                                            // Verificar se o lote ficou com quantidade 0
                                            if ($nextLotQuantity - $quantidadeARetirar <= 0) {
                                                $sqlInactivateNextLot = "UPDATE stock SET estado = 'inativo' WHERE id = '$nextLotId'";
                                                mysqli_query($db, $sqlInactivateNextLot);
                                            }
                                            
                                            // Atualizar a quantidade restante
                                            $quantidadeRestante -= $quantidadeARetirar;
                                            
                                        } else {
                                            // Não há mais lotes disponíveis
                                            $processarQuantidade = false;
                                        }
                                    }
                                    
                                    // Verificar se ainda há quantidade restante não processada
                                    if ($quantidadeRestante > 0) {
                                        echo json_encode(array('error' => "Stock insuficiente mesmo combinando lotes para o produto com ID: $produto"));
                                        exit;
                                    }
                                } else {
                                    // Se o lote atual tem quantidade suficiente, apenas atualiza normalmente
                                    $sqlUpdateStock = "UPDATE stock SET quantidade = quantidade - '$qtd' WHERE id = '$stockId'";
                                    if (!mysqli_query($db, $sqlUpdateStock)) {
                                        echo json_encode(array('error' => "Erro ao atualizar estoque: " . mysqli_error($db)));
                                        exit;
                                    }
                                    
                                    // Se a quantidade ficou zero, marcar o lote como inativo
                                    if ($newQuantity == 0) {
                                        $sqlInactivateLot = "UPDATE stock SET estado = 'inativo' WHERE id = '$stockId'";
                                        mysqli_query($db, $sqlInactivateLot);
                                    }
                                }
                            } else {
                                echo json_encode(array('error' => "Não há lotes disponíveis para o produto com ID: $produto"));
                                exit;
                            }
                        }
                    } else {
                        echo json_encode(array('error' => "Produto com ID $produto não encontrado."));
                        exit;
                    }

                    // Atualizar o período
                    $sq = "UPDATE periodo SET fechoperiodo = fechoperiodo + '$tt', datafechoperiodo = '$data_hora' WHERE diaperiodo = 'Aberto' AND usuario = '$userID'";
                    mysqli_query($db, $sq);
                }

            }

            // Inserir transações
            $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Venda a Dinheiro', '$new_id', 0, '$serie')";
            mysqli_query($db, $sql) or die(mysqli_error($db));

            $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Venda a Dinheiro', '$new_id', '$cliente', '$serie')";
            mysqli_query($db, $sql) or die(mysqli_error($db));

            // Limpar a fila de espera
            $sqlDL = "DELETE FROM filadeespera WHERE usuariofiladeespera = '$userID'";
            $rsDL = mysqli_query($db, $sqlDL);
            if ($rsDL) {
                echo json_encode(array('id' => $id));
            }
        }else{
            echo json_encode(array('error' => 'Erro ao inserir pedido: ' . mysqli_error($db)));
            exit;
        }
    } else {
        
        // Criar um novo pedido se não houver
        $sqlFunc = "INSERT INTO pedido(n_doc, descpedido, clientepedido, serie, pagamentopedido, iva, modo, disconto, userpedido, periodo, data) 
                    VALUES(1, '$data_hora', '$cliente', '$serie', '$value', '$ivo', '$modo', '$disconto', '$userID', '$periodo', '$data_hora')";
        $rsFunc = mysqli_query($db, $sqlFunc);
        $id = mysqli_insert_id($db);
        if ($rsFunc > 0) {
            $sqlAP = "SELECT * FROM filadeespera WHERE usuariofiladeespera = '$userID'";
            $rsAP = mysqli_query($db, $sqlAP);
            while ($dados = mysqli_fetch_array($rsAP)) {
                $produto = $dados['produtofiladeespera'];
                $qtd = $dados['qtdfiladeespera'];
                $preco = $dados['precofiladeespera'];
                $total = $dados['totalfiladeespera'];
                $iva = $dados['iva'];
                $lts = $dados['lote'];
                $tt = ($iva + $total) - $disconto;

                // Inserir entrega
                $sqlEN = "INSERT INTO entrega(produtoentrega, qtdentrega, precoentrega, totalentrega, iva, clienteentrega, usuarioentrega, pedidoentrega, datavenda, periodo, lote) 
                          VALUES('$produto', '$qtd', '$preco', '$total', '$iva', '$cliente', '$userID', '$id', '$data_hora', '$periodo', '$lts')";
                $rsEN = mysqli_query($db, $sqlEN) or die(mysqli_error($db));

                // Atualizar quantidade no estoque
                if ($rsEN > 0) {
                    // Verificar se o produto é estocável
                    $sqlStocavel = "SELECT stocavel FROM produto WHERE idproduto = '$produto'";
                    $rsStocavel = mysqli_query($db, $sqlStocavel);
                    if (mysqli_num_rows($rsStocavel) > 0) {
                        $stocavelData = mysqli_fetch_assoc($rsStocavel);
                        if ($stocavelData['stocavel'] = 1) {
                            // Encontrar o lote mais próximo de expiração
                            $sqlStock = "SELECT id, quantidade, prazo, lote FROM stock WHERE produto_id = '$produto' AND lote = '$lts'";
                            $rsStock = mysqli_query($db, $sqlStock);
                            if (mysqli_num_rows($rsStock) > 0) {
                                $stockData = mysqli_fetch_array($rsStock);
                                $stockId = $stockData['id'];
                                $stockQuantity = $stockData['quantidade'];
                                $lotaria = $stockData['lote'];

                                // Diminuir a quantidade no lote
                                $newQuantity = $stockQuantity - $qtd;
                                if ($newQuantity < 0) {
                                    // Caso em que não há quantidade suficiente no lote atual
                                    // Vamos usar toda a quantidade disponível neste lote e procurar os próximos
                                    $quantidadeUsada = $stockQuantity; // Usar toda a quantidade disponível neste lote
                                    $quantidadeRestante = $qtd - $quantidadeUsada; // Calcular quantidade ainda necessária
                                    
                                    // Atualizar este lote como esgotado (quantidade = 0) e inativo
                                    $sqlUpdateCurrentLot = "UPDATE stock SET quantidade = 0, estado = 'inativo' WHERE id = '$stockId'";
                                    if (!mysqli_query($db, $sqlUpdateCurrentLot)) {
                                        echo json_encode(array('error' => "Erro ao atualizar estoque do lote atual: " . mysqli_error($db)));
                                        exit;
                                    }
                                    
                                    // Vamos processar a quantidade restante usando vários lotes se necessário
                                    $processarQuantidade = true;
                                    
                                    while ($quantidadeRestante > 0 && $processarQuantidade) {
                                        // Procurar o próximo lote com prazo mais próximo a findar
                                        $sqlNextLot = "SELECT id, quantidade, prazo, lote FROM stock 
                                                     WHERE produto_id = '$produto' AND quantidade > 0 AND estado = 'ativo'
                                                     ORDER BY prazo ASC LIMIT 1";
                                        $rsNextLot = mysqli_query($db, $sqlNextLot);
                                        
                                        if (mysqli_num_rows($rsNextLot) > 0) {
                                            $nextLotData = mysqli_fetch_array($rsNextLot);
                                            $nextLotId = $nextLotData['id'];
                                            $nextLotQuantity = $nextLotData['quantidade'];
                                            
                                            // Determinar quanto podemos retirar deste lote
                                            $quantidadeARetirar = min($quantidadeRestante, $nextLotQuantity);
                                            
                                            // Atualizar a quantidade no próximo lote
                                            $sqlUpdateNextLot = "UPDATE stock SET quantidade = quantidade - '$quantidadeARetirar' WHERE id = '$nextLotId'";
                                            if (!mysqli_query($db, $sqlUpdateNextLot)) {
                                                echo json_encode(array('error' => "Erro ao atualizar estoque do próximo lote: " . mysqli_error($db)));
                                                exit;
                                            }
                                            
                                            // Verificar se o lote ficou com quantidade 0
                                            if ($nextLotQuantity - $quantidadeARetirar <= 0) {
                                                $sqlInactivateNextLot = "UPDATE stock SET estado = 'inativo' WHERE id = '$nextLotId'";
                                                mysqli_query($db, $sqlInactivateNextLot);
                                            }
                                            
                                            // Atualizar a quantidade restante
                                            $quantidadeRestante -= $quantidadeARetirar;
                                            
                                        } else {
                                            // Não há mais lotes disponíveis
                                            $processarQuantidade = false;
                                        }
                                    }
                                    
                                    // Verificar se ainda há quantidade restante não processada
                                    if ($quantidadeRestante > 0) {
                                        echo json_encode(array('error' => "Stock insuficiente mesmo combinando lotes para o produto com ID: $produto"));
                                        exit;
                                    }
                                } else {
                                    // Se o lote atual tem quantidade suficiente, apenas atualiza normalmente
                                    $sqlUpdateStock = "UPDATE stock SET quantidade = quantidade - '$qtd' WHERE id = '$stockId'";
                                    if (!mysqli_query($db, $sqlUpdateStock)) {
                                        echo json_encode(array('error' => "Erro ao atualizar estoque: " . mysqli_error($db)));
                                        exit;
                                    }
                                    
                                    // Se a quantidade ficou zero, marcar o lote como inativo
                                    if ($newQuantity == 0) {
                                        $sqlInactivateLot = "UPDATE stock SET estado = 'inativo' WHERE id = '$stockId'";
                                        mysqli_query($db, $sqlInactivateLot);
                                    }
                                }
                            } else {
                                echo json_encode(array('error' => "Não há lotes disponíveis para o produto com ID: $produto"));
                                exit;
                            }
                        }
                    } else {
                        echo json_encode(array('error' => "Produto com ID $produto não encontrado."));
                        exit;
                    }

                    // Atualizar o período
                    $sq = "UPDATE periodo SET fechoperiodo = fechoperiodo + '$tt', datafechoperiodo = '$data_hora' WHERE diaperiodo = 'Aberto' AND usuario = '$userID'";
                    mysqli_query($db, $sq);
                }
            }

            // Limpar a fila de espera
            $sqlDL = "DELETE FROM filadeespera WHERE usuariofiladeespera = '$userID'";
            $rsDL = mysqli_query($db, $sqlDL);
            if ($rsDL) {
                echo json_encode(array('id' => $id));
            }
        }else{
            echo json_encode(array('error' => 'Erro ao inserir pedido: ' . mysqli_error($db)));
            exit;
        }
    }
}
?>
