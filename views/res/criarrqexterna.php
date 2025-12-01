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

$fornecedor = $_POST['fornecedor'];
$sector = $_POST['sector'];
$utente = $_POST['utente'];
$motivo = $_POST['motivo'];

$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    $serie = $dados['serie'];
} else {
    echo "Erro ao obter série de fatura.";
    exit;
}


if ($year == $serie) {
    $siquela = "SELECT MAX(n_doc) as maxid FROM requisicao_externa WHERE serie = '$serie'";
    $res = mysqli_query($db, $siquela) or die(mysqli_error($db));
    if (mysqli_num_rows($res) > 0) {
        $ddo = mysqli_fetch_array($res);
        $max_id = $ddo['maxid'];
        $new_id = $max_id + 1;
    } else {
        $new_id = 1;
    }

    $sql3 = "INSERT INTO requisicao_externa(n_doc,`descricao`, `serie`,`fornecedor`, `sector`,solicitante, motivo, user) VALUES('$new_id','$data_hora', '$serie','$fornecedor', '$sector','$utente', '$motivo', '$userID')";
    $rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
    if ($rs3 > 0) {
        $sql5 = "SELECT MAX(id) as id FROM requisicao_externa WHERE serie = '$serie'";
        $rs5 = mysqli_query($db, $sql5) or die(mysqli_error($db));
        if (mysqli_num_rows($rs5) > 0) {
            $dados5 = mysqli_fetch_array($rs5);
            $id_re = $dados5['id'];
        }

        $sql10 = "UPDATE `sector` SET `qtdrequisicaoexterna` = qtdrequisicaoexterna + 1 WHERE `id` = '$sector'";
        $rs10 = mysqli_query($db, $sql10) or die(mysqli_error($db));

        $sql4 = "SELECT * FROM re_artigos_temp WHERE user = '$userID'";
        $rs4 = mysqli_query($db, $sql4) or die(mysqli_error($db));
        while ($dados4 = mysqli_fetch_array($rs4)) {
            $artigo = $dados4['artigo'];
            $qtd = $dados4['qtd'];
            $sql6 = "INSERT INTO `re_artigos`(`artigo`, `qtd`, `re`, `user`) VALUES('$artigo', '$qtd', '$id_re', '$userID')";
            $rs6 = mysqli_query($db, $sql6) or die(mysqli_error($db));

        }

        $sql8 = "DELETE FROM re_artigos_temp WHERE user = '$userID'";
        $rs8 = mysqli_query($db, $sql8) or die(mysqli_error($db));
        if ($rs8 > 0) {
            echo $id_re;
        }
    }
} else {
    echo "2000000000000000000000000";
}
?>
