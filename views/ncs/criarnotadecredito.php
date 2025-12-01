<?php
session_start();
error_reporting(E_ALL);
 ini_set("display_errors", 1);
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");
$year = date("Y");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;
/*********************************************/

$cliente = $_POST['cliente'] ?? null;
$motivo = $_POST['motivo'] ?? null;
$idp = $_POST['idp'] ?? null;
$status = 'Aberto';

if (!$cliente || !$motivo || !$idp) {
    echo "Parâmetros insuficientes fornecidos.";
    exit;
}

$sql = "SELECT SUM(total+iva) as val FROM nc_artigos_temp WHERE user = '$userID'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $val = $dados['val'];
} else {
    echo "Erro ao calcular o valor.";
    exit;
}

$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $serie = $dados['serie'];
    if ($year == $serie) {
        $siquela = "SELECT MAX(n_doc) as maxid FROM nota_de_credito WHERE serie = '$serie'";
        $res = mysqli_query($db, $siquela) or die(mysqli_error($db));
        if ($res && mysqli_num_rows($res) > 0) {
            $ddo = mysqli_fetch_array($res);
            $max_id = $ddo['maxid'];
            $new_id = $max_id + 1;
        } else {
            $new_id = 1;
        }

        $sqlFunc = "INSERT INTO `nota_de_credito`(`n_doc`, `descricao`, `valor`, `motivo`, `id_factura`, `serie`, `cliente`, `user`) VALUES ('$new_id','$data_hora','$val', '$motivo', '$idp', '$serie', '$cliente', '$userID')";
        $rsFunc = mysqli_query($db, $sqlFunc);
        $id = mysqli_insert_id($db);
        if ($rsFunc) {
            $sqlAP = "SELECT * FROM nc_artigos_temp WHERE user = '$userID'";
            $rsAP = mysqli_query($db, $sqlAP);
            while ($dados = mysqli_fetch_array($rsAP)) {
                $artigo = $dados['artigo'];
                $qtd = $dados['qtd'];
                $preco = $dados['preco'];
                $total = $dados['total'];
                $iva = $dados['iva'];
                $lote = $dados['lote'];
                $tt = $iva + $total;
                $sqlEN = "INSERT INTO `nc_artigos` (`artigo`, `qtd`, `preco`, `iva`, `total`, `cliente`, `user`, `id_nota`, lote) VALUES ('$artigo', '$qtd', '$preco', '$iva', '$total', '$cliente', '$userID', '$id', '$lote')";
                $rsEN = mysqli_query($db, $sqlEN) or die(mysqli_error($db));
                if ($rsEN) {
                    $sql = "UPDATE stock SET quantidade = quantidade + '$qtd' WHERE produto_id = '$artigo' AND lote = '$lote'";
                    $rsA = mysqli_query($db, $sql);
                }
            }

            $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Nota de Credito', '$new_id', 0, '$serie')";
            $rs = mysqli_query($db, $sql) or die(mysqli_error($db));

            $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Nota de Credito', '$new_id', '$cliente', '$serie')";
            $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
            $sql = "UPDATE factura SET nota_credito = '$id' WHERE id = '$idp'";
            $rsA = mysqli_query($db, $sql);
            $sqlDL = "DELETE FROM nc_artigos_temp WHERE user = '$userID'";
            $rsDL = mysqli_query($db, $sqlDL);
            if ($rsDL) {
                echo $id;
            } else {
                echo "Erro ao limpar itens temporários.";
            }
        } else {
            echo "Erro ao inserir nota de crédito: " . mysqli_error($db);
            exit;
        }
    } else {
        echo "O ano fiscal não corresponde à série atual.";
    }
} else {
    echo "Erro ao obter série de fatura.";
}
?>
