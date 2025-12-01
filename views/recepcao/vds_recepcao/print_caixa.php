<?php
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$data = $_GET['data'] ?? date('Y-m-d');
$userNOME = $_SESSION['nomeUsuario'] ?? 'Usuário';
$userID = $_SESSION['idUsuario'] ?? 0;

$sql = "SELECT * FROM caixa_recepcao WHERE data = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $data);
mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);

if (!$rs || mysqli_num_rows($rs) == 0) {
    die("Caixa não encontrado para a data: " . $data);
}

$caixa = mysqli_fetch_array($rs);

// Buscar dados da empresa
$sql_empresa = "SELECT * FROM empresa LIMIT 1";
$rs_empresa = mysqli_query($db, $sql_empresa);
$empresa = mysqli_fetch_array($rs_empresa);

$data_hora = date("Y-m-d H:i:s");
$valor_inicial = floatval($caixa['valor_inicial']);
$total_entradas = floatval($caixa['total_entradas']);
$saldo_final = floatval($caixa['saldo_final']);
$total_dinheiro = floatval($caixa['total_dinheiro']);
$total_mpesa = floatval($caixa['total_mpesa']);
$total_emola = floatval($caixa['total_emola']);
$total_pos = floatval($caixa['total_pos']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recibo de Fecho de Caixa</title>
    <script type="text/javascript" src="../../../js/jquery-3.3.1.min.js"></script>
    <style type="text/css">
        * {
            font-size: 10pt;
            font-family: sans-serif;
        }
        body {
            margin: 0;
            padding: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 12pt;
            margin: 5px 0;
        }
        .header p {
            margin: 2px 0;
        }
        .fact {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        .body {
            text-align: center;
            margin: 5px 0;
        }
        table {
            width: 95%;
            margin: 5px auto;
            border-collapse: collapse;
        }
        table th {
            background: #000;
            color: #fff;
            padding: 5px;
            text-align: center;
        }
        table td {
            padding: 5px;
            text-align: center;
            border-bottom: 1px dotted #ccc;
        }
        .total-box {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        .total-box h2 {
            font-size: 14pt;
            margin: 5px 0;
        }
        .processo {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dotted #ccc;
        }
        .processo p {
            margin: 5px 0;
        }
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="fatura">
        <div class="header">
            <h1><?php echo htmlspecialchars($empresa['nome']); ?></h1>
            <p><?php echo htmlspecialchars($empresa['endereco']); ?></p>
            <p>Contacto: <?php echo htmlspecialchars($empresa['contacto']); ?> - NUIT: <?php echo htmlspecialchars($empresa['nuit']); ?></p>
        </div>
        
        <div class="fact">
            <p>RECIBO DE FECHO DE CAIXA</p>
            <p>Data: <?php echo date('d/m/Y', strtotime($data)); ?></p>
            <p>Status: <?php echo strtoupper($caixa['status']); ?></p>
        </div>
        
        <div class="body">
            <p>Documento Original - <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Valor (MT)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Valor Inicial</td>
                    <td><?php echo number_format($valor_inicial, 2, '.', ','); ?></td>
                </tr>
                <tr>
                    <td>Total de Entradas</td>
                    <td><?php echo number_format($total_entradas, 2, '.', ','); ?></td>
                </tr>
            </tbody>
        </table>
        
        <table>
            <thead>
                <tr>
                    <th>Método de Pagamento</th>
                    <th>Valor (MT)</th>
                </tr>
            </thead>
            <tbody>
                <?php if($total_dinheiro > 0) { ?>
                <tr>
                    <td>Dinheiro</td>
                    <td><?php echo number_format($total_dinheiro, 2, '.', ','); ?></td>
                </tr>
                <?php } ?>
                <?php if($total_mpesa > 0) { ?>
                <tr>
                    <td>M-Pesa</td>
                    <td><?php echo number_format($total_mpesa, 2, '.', ','); ?></td>
                </tr>
                <?php } ?>
                <?php if($total_emola > 0) { ?>
                <tr>
                    <td>Emola</td>
                    <td><?php echo number_format($total_emola, 2, '.', ','); ?></td>
                </tr>
                <?php } ?>
                <?php if($total_pos > 0) { ?>
                <tr>
                    <td>POS</td>
                    <td><?php echo number_format($total_pos, 2, '.', ','); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <div class="total-box">
            <h2>SALDO FINAL: <?php echo number_format($saldo_final, 2, '.', ','); ?> MT</h2>
        </div>
        
        <div class="processo">
            <p>iVone ERP - Processado por programa</p>
            <p style="margin: 10px 0px; font-weight: bold;">Operador: <?php echo htmlspecialchars($userNOME); ?></p>
            <p>Impresso em: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
    
    <script type="text/javascript">
        $(function() {
            window.print();
        });
    </script>
</body>
</html>
