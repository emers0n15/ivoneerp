<?php
session_start();
include '../../../conexao/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    $fatura_id = intval($_POST['fatura_id']);
    $valor_pago = floatval($_POST['valor_pago']);
    $metodo_pagamento = mysqli_real_escape_string($db, $_POST['metodo_pagamento']);
    $referencia_pagamento = mysqli_real_escape_string($db, $_POST['referencia_pagamento']);
    $observacoes = mysqli_real_escape_string($db, $_POST['observacoes']);
    $usuario = $_SESSION['idUsuario'];
    $use_new_system = isset($_POST['use_new_system']) && $_POST['use_new_system'] == '1';
    
    $fatura = null;
    $valor_total = 0;
    
    if($use_new_system) {
        // Usar nova tabela factura_recepcao
        $sql_fatura = "SELECT * FROM factura_recepcao WHERE id = $fatura_id";
        $rs_fatura = mysqli_query($db, $sql_fatura);
        $fatura = mysqli_fetch_array($rs_fatura);
        
        if (!$fatura) {
            echo "<script>alert('Fatura não encontrada!'); window.location.href='../faturas.php';</script>";
            exit;
        }
        
        $valor_total = floatval($fatura['valor']);
        
        // Calcular total já pago
        $sql_total_pago = "SELECT COALESCE(SUM(valor_pago), 0) as total_pago FROM pagamentos_recepcao WHERE factura_recepcao_id = $fatura_id OR (fatura_id = $fatura_id AND factura_recepcao_id IS NULL)";
        $rs_total_pago = mysqli_query($db, $sql_total_pago);
        $total_pago_data = mysqli_fetch_array($rs_total_pago);
        $total_pago_anterior = floatval($total_pago_data['total_pago']);
        
        // Verificar se já está paga
        if($total_pago_anterior >= $valor_total) {
            echo "<script>alert('Fatura já foi totalmente paga!'); window.location.href='../faturas.php';</script>";
            exit;
        }
        
        // Validar valor
        $valor_restante = $valor_total - $total_pago_anterior;
        if ($valor_pago > $valor_restante) {
            echo "<script>alert('Valor excede o restante da fatura! Restante: " . number_format($valor_restante, 2, ',', '.') . " MT'); window.location.href='../pagar_fatura.php?id=$fatura_id';</script>";
            exit;
        }
        
        // Inserir pagamento
        $sql_pagamento = "INSERT INTO pagamentos_recepcao (
            factura_recepcao_id, valor_pago, metodo_pagamento, referencia_pagamento, observacoes, usuario
        ) VALUES (
            $fatura_id, $valor_pago, '$metodo_pagamento', " . 
            ($referencia_pagamento ? "'$referencia_pagamento'" : "NULL") . ", " . 
            ($observacoes ? "'$observacoes'" : "NULL") . ", $usuario
        )";
        
        if (mysqli_query($db, $sql_pagamento)) {
            // Atualizar caixa se não for fatura para empresa
            if($metodo_pagamento != 'fatura_empresa') {
                $data_hoje = date('Y-m-d');
                
                // Verificar/criar caixa
                $sql_caixa = "SELECT * FROM caixa_recepcao WHERE data = '$data_hoje' AND status = 'aberto'";
                $rs_caixa = mysqli_query($db, $sql_caixa);
                if(!$rs_caixa || mysqli_num_rows($rs_caixa) == 0) {
                    $sql_check = "SELECT * FROM caixa_recepcao WHERE data = '$data_hoje'";
                    $rs_check = mysqli_query($db, $sql_check);
                    if(!$rs_check || mysqli_num_rows($rs_check) == 0) {
                        mysqli_query($db, "INSERT INTO caixa_recepcao (data, status, usuario_abertura, data_abertura) VALUES ('$data_hoje', 'aberto', $usuario, NOW())");
                    } else {
                        mysqli_query($db, "UPDATE caixa_recepcao SET status = 'aberto' WHERE data = '$data_hoje'");
                    }
                }
                
                // Atualizar totais do caixa
                $metodo_caixa = '';
                switch($metodo_pagamento) {
                    case 'dinheiro':
                        $metodo_caixa = 'total_dinheiro';
                        break;
                    case 'm-pesa':
                    case 'm_pesa':
                        $metodo_caixa = 'total_mpesa';
                        break;
                    case 'emola':
                        $metodo_caixa = 'total_emola';
                        break;
                    case 'pos':
                        $metodo_caixa = 'total_pos';
                        break;
                }
                
                if($metodo_caixa) {
                    mysqli_query($db, "UPDATE caixa_recepcao SET 
                                     total_entradas = total_entradas + $valor_pago,
                                     $metodo_caixa = $metodo_caixa + $valor_pago,
                                     saldo_final = saldo_final + $valor_pago
                                     WHERE data = '$data_hoje' AND status = 'aberto'");
                }
            }
            
            $novo_total_pago = $total_pago_anterior + $valor_pago;
            $mensagem = $novo_total_pago >= $valor_total ? 'Pagamento completo registrado com sucesso!' : 
                       'Pagamento parcial registrado com sucesso! Restante: ' . number_format($valor_total - $novo_total_pago, 2, ',', '.') . ' MT';
            
            echo "<script>alert('$mensagem'); window.location.href='../faturas.php';</script>";
        } else {
            echo "<script>alert('Erro ao registrar pagamento: " . mysqli_error($db) . "'); window.location.href='../pagar_fatura.php?id=$fatura_id';</script>";
        }
    } else {
        // Usar tabela antiga faturas_atendimento
        $sql_fatura = "SELECT * FROM faturas_atendimento WHERE id = $fatura_id AND status IN ('pendente', 'parcial')";
        $rs_fatura = mysqli_query($db, $sql_fatura);
        $fatura = mysqli_fetch_array($rs_fatura);
        
        if (!$fatura) {
            echo "<script>alert('Fatura não encontrada ou já foi totalmente paga!'); window.location.href='../faturas.php';</script>";
            exit;
        }
        
        $valor_total = floatval($fatura['total']);
        
        // Calcular total já pago
        $sql_total_pago = "SELECT COALESCE(SUM(valor_pago), 0) as total_pago FROM pagamentos_recepcao WHERE fatura_id = $fatura_id AND factura_recepcao_id IS NULL";
        $rs_total_pago = mysqli_query($db, $sql_total_pago);
        $total_pago_data = mysqli_fetch_array($rs_total_pago);
        $total_pago_anterior = floatval($total_pago_data['total_pago']);
        
        // Validar valor
        $valor_restante = $valor_total - $total_pago_anterior;
        if ($valor_pago > $valor_restante) {
            echo "<script>alert('Valor excede o restante da fatura! Restante: " . number_format($valor_restante, 2, ',', '.') . " MT'); window.location.href='../pagar_fatura.php?id=$fatura_id';</script>";
            exit;
        }
        
        // Inserir pagamento
        $sql_pagamento = "INSERT INTO pagamentos_recepcao (
            fatura_id, valor_pago, metodo_pagamento, referencia_pagamento, observacoes, usuario
        ) VALUES (
            $fatura_id, $valor_pago, '$metodo_pagamento', " . 
            ($referencia_pagamento ? "'$referencia_pagamento'" : "NULL") . ", " . 
            ($observacoes ? "'$observacoes'" : "NULL") . ", $usuario
        )";
        
        if (mysqli_query($db, $sql_pagamento)) {
            // Atualizar caixa se não for fatura para empresa
            if($metodo_pagamento != 'fatura_empresa') {
                $data_hoje = date('Y-m-d');
                
                // Verificar/criar caixa
                $sql_caixa = "SELECT * FROM caixa_recepcao WHERE data = '$data_hoje' AND status = 'aberto'";
                $rs_caixa = mysqli_query($db, $sql_caixa);
                if(!$rs_caixa || mysqli_num_rows($rs_caixa) == 0) {
                    $sql_check = "SELECT * FROM caixa_recepcao WHERE data = '$data_hoje'";
                    $rs_check = mysqli_query($db, $sql_check);
                    if(!$rs_check || mysqli_num_rows($rs_check) == 0) {
                        mysqli_query($db, "INSERT INTO caixa_recepcao (data, status, usuario_abertura, data_abertura) VALUES ('$data_hoje', 'aberto', $usuario, NOW())");
                    } else {
                        mysqli_query($db, "UPDATE caixa_recepcao SET status = 'aberto' WHERE data = '$data_hoje'");
                    }
                }
                
                // Atualizar totais do caixa
                $metodo_caixa = '';
                switch($metodo_pagamento) {
                    case 'dinheiro':
                        $metodo_caixa = 'total_dinheiro';
                        break;
                    case 'm-pesa':
                    case 'm_pesa':
                        $metodo_caixa = 'total_mpesa';
                        break;
                    case 'emola':
                        $metodo_caixa = 'total_emola';
                        break;
                    case 'pos':
                        $metodo_caixa = 'total_pos';
                        break;
                }
                
                if($metodo_caixa) {
                    mysqli_query($db, "UPDATE caixa_recepcao SET 
                                     total_entradas = total_entradas + $valor_pago,
                                     $metodo_caixa = $metodo_caixa + $valor_pago,
                                     saldo_final = saldo_final + $valor_pago
                                     WHERE data = '$data_hoje' AND status = 'aberto'");
                }
            }
            
            // Calcular novo total pago
            $novo_total_pago = $total_pago_anterior + $valor_pago;
            
            // Determinar novo status
            $novo_status = 'pendente';
            if ($novo_total_pago >= $valor_total) {
                $novo_status = 'paga';
            } elseif ($novo_total_pago > 0) {
                $novo_status = 'parcial';
            }
            
            // Atualizar fatura
            $sql_update = "UPDATE faturas_atendimento 
                          SET status = '$novo_status', valor_pago = $novo_total_pago 
                          WHERE id = $fatura_id";
            mysqli_query($db, $sql_update);
            
            $mensagem = $novo_status == 'paga' ? 'Pagamento completo registrado com sucesso!' : 
                       'Pagamento parcial registrado com sucesso! Restante: ' . number_format($valor_total - $novo_total_pago, 2, ',', '.') . ' MT';
            
            echo "<script>alert('$mensagem'); window.location.href='../faturas.php';</script>";
        } else {
            echo "<script>alert('Erro ao registrar pagamento: " . mysqli_error($db) . "'); window.location.href='../pagar_fatura.php?id=$fatura_id';</script>";
        }
    }
} else {
    header("location:../faturas.php");
}
?>

