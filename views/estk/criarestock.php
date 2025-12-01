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

// Recebe o lote e prazo do formulário
$lote = $_POST['lote'];
$prazo = $_POST['prazo'];
$familia = $_POST['familia'];
$grupo = $_POST['grupo'];

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
    $siquela = "SELECT MAX(n_doc) as maxid FROM entrada_stock WHERE serie = '$serie'";
    $res = mysqli_query($db, $siquela) or die(mysqli_error($db));
    if (mysqli_num_rows($res) > 0) {
        $ddo = mysqli_fetch_array($res);
        $max_id = $ddo['maxid'];
        $new_id = $max_id + 1;
    } else {
        $new_id = 1;
    }

    $sql3 = "INSERT INTO entrada_stock(n_doc,descricao, `serie`, `grupo`, `familia`, `user`, lote, prazo, data) VALUES('$new_id','$data_hora','$serie','$grupo', '$familia', '$userID', '$lote', '$prazo', '$data_hora')";
$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
if ($rs3 > 0) {
    $sql5 = "SELECT MAX(id) as id FROM entrada_stock WHERE user = '$userID'";
    $rs5 = mysqli_query($db, $sql5) or die(mysqli_error($db));
    if (mysqli_num_rows($rs5) > 0) {
        $dados5 = mysqli_fetch_array($rs5);
        $id_es = $dados5['id'];

        $sql4 = "SELECT * FROM es_artigos_temp WHERE user = '$userID'";
        $rs4 = mysqli_query($db, $sql4) or die(mysqli_error($db));
        while ($dados4 = mysqli_fetch_array($rs4)) {
            $artigo = $dados4['artigo'];
            $qtd = $dados4['qtd'];
            $sql6 = "INSERT INTO `es_artigos`(`artigo`, `qtd`, `es`, `user`) VALUES('$artigo', '$qtd', '$id_es', '$userID')";
            $rs6 = mysqli_query($db, $sql6) or die(mysqli_error($db));

            // Verifica se já existe um registro para o mesmo artigo, lote e prazo
            $sqlCheck = "SELECT * FROM stock WHERE produto_id = '$artigo' AND lote = '$lote' AND prazo = '$prazo'";
            $rsCheck = mysqli_query($db, $sqlCheck) or die(mysqli_error($db));
            
            if (mysqli_num_rows($rsCheck) > 0) {
                // Se existir, atualiza a quantidade
                $sqlUpdate = "UPDATE stock SET quantidade = quantidade + '$qtd', quantidade_inicial = quantidade_inicial + '$qtd' WHERE produto_id = '$artigo' AND lote = '$lote' AND prazo = '$prazo'";
                mysqli_query($db, $sqlUpdate) or die(mysqli_error($db));
            } else {
                // Se não existir, insere um novo registro
                $sqlInsert = "INSERT INTO stock (produto_id, quantidade, quantidade_inicial, lote, prazo, estado) VALUES ('$artigo', '$qtd', '$qtd', '$lote', '$prazo', 'Ativo')";
                mysqli_query($db, $sqlInsert) or die(mysqli_error($db));
            }

        }
    }

    $sql8 = "DELETE FROM es_artigos_temp WHERE user = '$userID'";
    $rs8 = mysqli_query($db, $sql8) or die(mysqli_error($db));
    if ($rs8 > 0) {
        echo $id_es;
    }
}
} else {
    echo "2000000000000000000000000";
}

?>
