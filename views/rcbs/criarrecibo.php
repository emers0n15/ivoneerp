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
$metodo = $_POST['metodo'] ?? null;
$status = 'Aberto';


$sql = "SELECT SUM(total) as val FROM rc_fact_temp WHERE user = '$userID'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $val = $dados['val'];
}

$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $serie = $dados['serie'];
    if ($year == $serie) {
        $siquela = "SELECT MAX(n_doc) as maxid FROM recibo WHERE serie = '$serie'";
        $res = mysqli_query($db, $siquela) or die(mysqli_error($db));
        if ($res && mysqli_num_rows($res) > 0) {
            $ddo = mysqli_fetch_array($res);
            $max_id = $ddo['maxid'];
            $new_id = $max_id + 1;
        } else {
            $new_id = 1;
        }

        $sqlFunc = "INSERT INTO `recibo`(`n_doc`, `descricao`, `valor`, `modo`, `serie`, `cliente`, `user`) VALUES ('$new_id','$data_hora','$val', '$metodo', '$serie', '$cliente', '$userID')";
        $rsFunc = mysqli_query($db, $sqlFunc);
        $id = mysqli_insert_id($db);
        if ($rsFunc) {
            $sqlAP = "SELECT * FROM rc_fact_temp WHERE user = '$userID'";
            $rsAP = mysqli_query($db, $sqlAP);
            while ($dados = mysqli_fetch_array($rsAP)) {
                $factura = $dados['factura'];
                $valor = $dados['valor'];
                $iva = $dados['iva'];
                $total = $dados['total'];
                $serie = $dados['serie'];
                $cliente = $dados['cliente'];
                $sqlEN = "INSERT INTO `rc_fact` (`factura`, `valor`, `iva`, `total`, `serie`, `cliente`, `user`, `id_rc`) VALUES ('$factura', '$valor', '$iva', '$total', '$serie', '$cliente', '$userID', '$id')";
                $rsEN = mysqli_query($db, $sqlEN) or die(mysqli_error($db));
                $sql = "UPDATE factura SET recibo = '$id', statuss = 1 WHERE n_doc = '$factura' AND serie = '$serie'";
                $rsA = mysqli_query($db, $sql);
            }

            $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Recibo', '$new_id', 0, '$serie')";
            $rs = mysqli_query($db, $sql) or die(mysqli_error($db));

            $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Recibo', '$new_id', '$cliente', '$serie')";
            $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
            
            $sqlDL = "DELETE FROM rc_fact_temp WHERE user = '$userID'";
            $rsDL = mysqli_query($db, $sqlDL);
            if ($rsDL) {
                echo $id;
            } else {
                echo "Erro ao limpar itens temporários.";
            }
        } else {
            echo "Erro ao inserir recibo: " . mysqli_error($db);
            exit;
        }
    } else {
        
    }
} else {
    echo "Erro ao obter série de fatura.";
}
?>
