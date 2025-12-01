<?php
session_start();
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");
$year = date("Y");
error_log("Início do script", 0);
/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/

$cliente = $_POST['cliente'];
$modo = $_POST['modo'];
$motivo = $_POST['motivo'];
$idp = $_POST['idp'];
$status = 'Aberto';



$sql = "SELECT SUM(total+iva) as val FROM artigos_devolvidos_temp WHERE user = '$userID'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $val = $dados['val'];
}else{
	echo 400000000000000000000;
}

$sqlBoxx = "SELECT * FROM periodo WHERE diaperiodo = '$status' AND usuario = '$userID'";
$rsBoxx = mysqli_query($db, $sqlBoxx);
if (mysqli_num_rows($rsBoxx) > 0) {
    $d = mysqli_fetch_assoc($rsBoxx);
    $periodo = $d['idperiodo'];
    $fechoperiodo = $d['fechoperiodo']; // Corrigido aqui
}else{
	echo 400000000000000000000;
}

if ($val >= $fechoperiodo) {
    $sql8 = "DELETE FROM artigos_devolvidos_temp";
    $rs8 = mysqli_query($db, $sql8) or die(mysqli_error($db));
    echo 300000000000000000000;
} 

$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $serie = $dados['serie'];
    if ($year == $serie) {
        $siquela = "SELECT MAX(n_doc) as maxid FROM devolucao WHERE serie = '$serie'";
        $res = mysqli_query($db, $siquela) or die(mysqli_error($db));
        if (mysqli_num_rows($res) > 0) {
            $ddo = mysqli_fetch_array($res);
            $max_id = $ddo['maxid'];
            $new_id = $max_id + 1;
            $sqlFunc = "INSERT INTO `devolucao`(`n_doc`, `descricao`, `valor`, `modo`, `motivo`, `idpedido`, `serie`, `idcliente`, `iduser`, `idperiodo`) VALUES ('$new_id','$data_hora','$val','$modo', '$motivo', '$idp', '$serie', '$cliente', '$userID', '$periodo')";
            $rsFunc = mysqli_query($db, $sqlFunc);
            $id = mysqli_insert_id($db);
            if ($rsFunc) {
                $sqlAP = "SELECT * FROM artigos_devolvidos_temp WHERE user = '$userID'";
                $rsAP = mysqli_query($db, $sqlAP);
                while ($dados = mysqli_fetch_array($rsAP)) {
                    $artigo = $dados['artigo'];
                    $qtd = $dados['qtd'];
                    $preco = $dados['preco'];
                    $total = $dados['total'];
                    $iva = $dados['iva'];
                    $lote = $dados['lote'];
                    $tt = $iva + $total;
                    $sqlEN = "INSERT INTO `artigos_devolvidos` (`produto`, `qtd`, `preco`, `iva`, `total`, `cliente`, `usuario`, `devolucao`, lote) VALUES ('$artigo', '$qtd', '$preco', '$iva', '$total', '$cliente', '$userID', '$id', '$lote')";
                    $rsEN = mysqli_query($db, $sqlEN) or die(mysqli_error($db));
                    if ($rsEN) {
                        $sql = "UPDATE stock SET quantidade = quantidade + '$qtd' WHERE produto_id = '$artigo' AND lote = '$lote'";
                        $rsA = mysqli_query($db, $sql);
                        if ($rsA) {
                            $sq = "UPDATE periodo SET fechoperiodo = fechoperiodo - '$tt', datafechoperiodo = '$data_hora' WHERE diaperiodo = 'Aberto' AND usuario = '$userID'";
                            $rsas = mysqli_query($db, $sq);
                        }
                    }
                }

                $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Devolucao', '$new_id', 0, '$serie')";
                $rs = mysqli_query($db, $sql) or die(mysqli_error($db));

                $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Devolucao', '$new_id', '$cliente', '$serie')";
                $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
                $sql = "UPDATE pedido SET devolucao = '$id' WHERE idpedido = '$idp'";
                $rsA = mysqli_query($db, $sql);
                $sqlDL = "DELETE FROM artigos_devolvidos_temp WHERE user = '$userID'";
                $rsDL = mysqli_query($db, $sqlDL);
                if ($rsDL) {
                    echo $id;
                }
            }
        } else {
            $sqlFunc = "INSERT INTO `devolucao`(`n_doc`, `descricao`, `valor`, `modo`, `motivo`, `idpedido`, `serie`, `idcliente`, `iduser`, `idperiodo`) VALUES (1,'$data_hora','$val','$modo', '$motivo', '$idp', '$serie', '$cliente', '$userID', '$periodo')";
            $rsFunc = mysqli_query($db, $sqlFunc);
            $id = mysqli_insert_id($db);
            if ($rsFunc) {
                $sqlAP = "SELECT * FROM artigos_devolvidos_temp WHERE user = '$userID'";
                $rsAP = mysqli_query($db, $sqlAP);
                while ($dados = mysqli_fetch_array($rsAP)) {
                    $artigo = $dados['artigo'];
                    $qtd = $dados['qtd'];
                    $preco = $dados['preco'];
                    $total = $dados['total'];
                    $iva = $dados['iva'];
                    $tt = $iva + $total;
                    $sqlEN = "INSERT INTO `artigos_devolvidos` (`produto`, `qtd`, `preco`, `iva`, `total`, `cliente`, `usuario`, `devolucao`) VALUES ('$artigo', '$qtd', '$preco', '$iva', '$total', '$cliente', '$userID', '$id')";
                    $rsEN = mysqli_query($db, $sqlEN) or die(mysqli_error($db));
                    if ($rsEN) {
                        $sql = "UPDATE stock SET quantidade = quantidade + '$qtd' WHERE produto_id = '$artigo' AND lote = '$lote'";
                        $rsA = mysqli_query($db, $sql);
                        if ($rsA) {
                            $sq = "UPDATE periodo SET fechoperiodo = fechoperiodo - '$tt', datafechoperiodo = '$data_hora', numero_devolucoes = numero_devolucoes + 1 WHERE diaperiodo = 'Aberto' AND usuario = '$userID'";
                            $rsas = mysqli_query($db, $sq);
                        }
                    }
                }

                $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Devolucao', 1, 0, '$serie')";
                $rs = mysqli_query($db, $sql) or die(mysqli_error($db));

                $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Devolucao', 1, '$cliente', '$serie')";
                $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
                $sql = "UPDATE pedido SET devolucao = '$id' WHERE idpedido = '$idp'";
                $rsA = mysqli_query($db, $sql);
                $sqlDL = "DELETE FROM artigos_devolvidos_temp WHERE user = '$userID'";
                $rsDL = mysqli_query($db, $sqlDL);
                if ($rsDL) {
                    echo $id;
                }
            }
        }
    } else {
        echo 450000000000000000000;
    }
}else{
	echo 400000000000000000000;
}
    


error_log("Fim do script", 0);
?>
