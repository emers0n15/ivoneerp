<?php
session_start();
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");
$year = date("Y");
/*Variaveis do Sistema*/
/*********************************************/
$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$userID = $_SESSION['idUsuario'];
$userNOME = $_SESSION['nomeUsuario'];
$userCATE = $_SESSION['categoriaUsuario'];
/*********************************************/

$cliente = $_POST['cliente'];
$prazo = $_POST['prazo'];

$sql = "SELECT desconto FROM clientes WHERE id = '$cliente'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $desconto = $dados['desconto'];
    $percentagemDisc = $desconto/100;
} else {
    echo 1;
    exit;
}

$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $serie = $dados['serie'];
} else {
    echo "Erro ao obter série de fatura.";
    exit;
}

$sql2 = "SELECT SUM(total) as total, SUM(iva) as ivas FROM ct_artigos_temp WHERE user = '$userID'";
$rs2 = mysqli_query($db, $sql2) or die(mysqli_error($db));
if (mysqli_num_rows($rs2) > 0) {
    $dados2 = mysqli_fetch_array($rs2);
    $total = $dados2['total'];
    $iva_incluso = $dados2['ivas'];
    $valorDisc = $percentagemDisc*$total;
    if ($total <= $valorDisc) {
        echo "O desconto excede o valor total.";
        exit();
    } else {
        $total = $total - $valorDisc;
    }

    $totall = $dados2['total'] + $iva_incluso;
} else {
    echo "<script>window.location.href='cotacoes.php</script>";
    exit;
}

if ($year == $serie) {
    $siquela = "SELECT MAX(n_doc) as maxid FROM cotacao WHERE serie = '$serie'";
    $res = mysqli_query($db, $siquela) or die(mysqli_error($db));
    if (mysqli_num_rows($res) > 0) {
        $ddo = mysqli_fetch_array($res);
        $max_id = $ddo['maxid'];
        $new_id = $max_id + 1;
    } else {
        $new_id = 1;
    }

    $sql3 = "INSERT INTO cotacao(n_doc,`descricao`, `valor`, `iva`, `disconto`, `serie`, `prazo`, cliente, usuario) VALUES('$new_id','$data_hora', '$total', '$iva_incluso', '$valorDisc', '$serie', '$prazo', '$cliente', '$userID')";
    $rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
    if ($rs3 > 0) {
        $sql5 = "SELECT MAX(id) as id_cotacao FROM cotacao WHERE usuario = '$userID' AND cliente = '$cliente' AND serie = '$serie'";
        $rs5 = mysqli_query($db, $sql5) or die(mysqli_error($db));
        if (mysqli_num_rows($rs5) > 0) {
            $dados5 = mysqli_fetch_array($rs5);
            $id_cotacao = $dados5['id_cotacao'];
        }

        $sql10 = "UPDATE `clientes` SET `qtd_cotacao` = qtd_cotacao + 1 WHERE `id` = '$cliente'";
        $rs10 = mysqli_query($db, $sql10) or die(mysqli_error($db));

        $sql4 = "SELECT * FROM ct_artigos_temp WHERE user = '$userID'";
        $rs4 = mysqli_query($db, $sql4) or die(mysqli_error($db));
        while ($dados4 = mysqli_fetch_array($rs4)) {
            $artigo = $dados4['artigo'];
            $qtd = $dados4['qtd'];
            $preco = $dados4['preco'];
            $total = $dados4['total'];
            $iva = $dados4['iva'];
            $sql6 = "INSERT INTO `ct_artigos_cotados`(`artigo`, `qtd`, `preco`, `total`, iva, `usuario`, `cotacao`) VALUES('$artigo', '$qtd', '$preco', '$total', '$iva', '$userID', '$id_cotacao')";
            $rs6 = mysqli_query($db, $sql6) or die(mysqli_error($db));

        }


        $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Cotacao', '$id_cotacao', 0, '$serie')";
        $rs = mysqli_query($db, $sql) or die(mysqli_error($db));

        $sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `serie`) VALUES ('Cotacao', '$id_cotacao', '$cliente', '$serie')";
        $rs = mysqli_query($db, $sql) or die(mysqli_error($db));

        $sql8 = "DELETE FROM ct_artigos_temp WHERE user = '$userID'";
        $rs8 = mysqli_query($db, $sql8) or die(mysqli_error($db));
        if ($rs8 > 0) {
            echo $id_cotacao;
        }
    }
} else {
    echo "2000000000000000000000000";
}
?>
