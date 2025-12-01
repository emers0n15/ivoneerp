<?php
session_start();
include_once '../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");
$data = date("Y-m-d");
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
$utente = $_POST['utente'];
$fatura = $_POST['fatura'];
$prazo = $_POST['prazo'];
$metodo = $_POST['metodo'];
$condicoes = $_POST['condicoes'];
$apolice = $_POST['apolice'];
$codigo1 = $_POST['codigo1'];
$codigo2 = $_POST['codigo2'];

$sql = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_array($rs);
	$serie = $dados['serie'];
}

$sql = "SELECT desconto FROM clientes WHERE id = '$cliente'";
$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
if (mysqli_num_rows($rs) > 0) {
	$dados = mysqli_fetch_array($rs);
	$desconto = $dados['desconto'];
	$percentagemDisc = $desconto/100;
}

$sql2 = "SELECT SUM(total) as total, SUM(iva) as ivas FROM nd_artigos_temp WHERE user = '$userID'";
$rs2 = mysqli_query($db, $sql2) or die(mysqli_error($db));
if (mysqli_num_rows($rs2) > 0) {
	$dados2 = mysqli_fetch_array($rs2);
	$total = $dados2['total'];
	$iva_incluso = $dados2['ivas'];
	$valorDisc = $percentagemDisc*$total;
	if ($total <= $valorDisc) {
		exit();
	}else{
		$total = $total - $valorDisc;
	}
	
	$totall = $dados2['total'] + $iva_incluso;
}else{
	echo "<script>window.location.href='../nota_debitos.php</script>";
}

if ($year == $serie) {
	$siquela = "SELECT MAX(n_doc) as maxid FROM nota_debito WHERE serie = '$serie'";
	$res = mysqli_query($db, $siquela) or die(mysqli_error($db));
	if (mysqli_num_rows($res) > 0) {
		$ddo = mysqli_fetch_array($res);
		$max_id = $ddo['maxid'];
		$new_id = $max_id + 1;
		$sql3 = "INSERT INTO nota_debito SET n_doc = '$new_id',descricao = '$data_hora', valor = '$total', iva = '$iva_incluso', disconto = '$valorDisc', serie = '$serie', prazo = '$prazo', metodo = '$metodo', condicoes = '$condicoes', apolice = '$apolice', codigo1 = '$codigo1', codigo2 = '$codigo2', cliente = '$cliente', utente = '$utente', usuario = '$userID',id_factura = '$fatura', data = '$data'";
		$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
		$id = mysqli_insert_id($db);
	if ($rs3 > 0) {
		$sql10 = "UPDATE `clientes` SET `qtd_nota_debito` = qtd_nota_debito + 1 WHERE `id` = '$cliente'";
		$rs10 = mysqli_query($db, $sql10) or die(mysqli_error($db));

		$sql4 = "SELECT * FROM nd_artigos_temp WHERE user = '$userID'";
		$rs4 = mysqli_query($db, $sql4) or die(mysqli_error($db));
		while ($dados4 = mysqli_fetch_array($rs4)) {
			$artigo = $dados4['artigo'];
			$qtd = $dados4['qtd'];
			$preco = $dados4['preco'];
			$total = $dados4['total'];
			$iva = $dados4['iva'];
			$lote = $dados4['lote'];
			$sql6 = "INSERT INTO `nd_artigos`(`artigo`, `qtd`, `preco`, `iva`, `total`, lote, `user`, `id_nd`) VALUES('$artigo', '$qtd', '$preco', '$iva', '$total', '$lote', '$userID', '$id')";
			$rs6 = mysqli_query($db, $sql6) or die(mysqli_error($db));

			// Seleciona o lote com a data de validade mais próxima e que ainda tem stock
			$sql_lote = "SELECT id, quantidade FROM stock WHERE produto_id = '$artigo' AND quantidade > 0 AND estado = 'ativo' ORDER BY prazo ASC LIMIT 1";
			$rs_lote = mysqli_query($db, $sql_lote) or die(mysqli_error($db));

			if (mysqli_num_rows($rs_lote) > 0) {
			    $lote = mysqli_fetch_array($rs_lote);
			    $lote_id = $lote['id'];
			    $lote_stock = $lote['quantidade'];
			    
			    // Verifica se o stock do lote é suficiente
			    if ($lote_stock >= $qtd) {
			        // Atualiza o stock do lote, subtraindo a quantidade vendida
			        $sql_update_lote = "UPDATE stock SET quantidade = quantidade - '$qtd' WHERE id = '$lote_id'";
			        mysqli_query($db, $sql_update_lote) or die(mysqli_error($db));
			    } else {
			        // Se o stock do lote for insuficiente, lançar um erro ou tratar de outra forma
			        die("Quantidade insuficiente no lote.");
			    }
			} else {
			    // Se não houver lote disponível, lançar um erro
			    die("Nenhum lote disponível para o artigo.");
			}
		}

		$sql = "SELECT saldo FROM transacoes WHERE id = (SELECT MAX(id) FROM transacoes WHERE cliente = 0 AND saldo != 'NULL')";
			$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			if (mysqli_num_rows($rs) > 0) {
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Nota de Debito', '$new_id', 0, '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}else{
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Nota de Debito', '$new_id', 0, '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}

			$sql = "SELECT saldo FROM transacoes WHERE id = (SELECT MAX(id) FROM transacoes WHERE cliente = '$cliente' AND saldo != 'NULL')";
			$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			if (mysqli_num_rows($rs) > 0) {
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Nota de Debito', '$new_id', '$cliente', '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}else{
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Nota de Debito', '$new_id', '$cliente', '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}

		$sql8 = "DELETE FROM nd_artigos_temp WHERE user = '$userID'";
		$rs8 = mysqli_query($db, $sql8) or die(mysqli_error($db));
		if ($rs8 > 0) {
			echo $id;
		}
	}
	}else{
		$sql3 = "INSERT INTO nota_debito SET n_doc = 1,descricao = '$data_hora', valor = '$total', iva = '$iva_incluso', disconto = '$valorDisc', serie = '$serie', prazo = '$prazo', metodo = '$metodo', condicoes = '$condicoes', apolice = '$apolice', codigo1 = '$codigo1', codigo2 = '$codigo2', cliente = '$cliente', utente = '$utente', usuario = '$userID',id_factura = '$fatura', data = '$data'";
		$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
		$id = mysqli_insert_id($db);
	if ($rs3 > 0) {
		$sql10 = "UPDATE `clientes` SET `qtd_nota_debito` = qtd_nota_debito + 1 WHERE `id` = '$cliente'";
		$rs10 = mysqli_query($db, $sql10) or die(mysqli_error($db));

		$sql4 = "SELECT * FROM nd_artigos_temp WHERE user = '$userID'";
		$rs4 = mysqli_query($db, $sql4) or die(mysqli_error($db));
		while ($dados4 = mysqli_fetch_array($rs4)) {
			$artigo = $dados4['artigo'];
			$qtd = $dados4['qtd'];
			$preco = $dados4['preco'];
			$total = $dados4['total'];
			$iva = $dados4['iva'];
			$lote = $dados4['lote'];
			$sql6 = "INSERT INTO `nd_artigos`(`artigo`, `qtd`, `preco`, `iva`, `total`, lote, `user`, `id_nd`) VALUES('$artigo', '$qtd', '$preco', '$iva', '$total', '$lote', '$userID', '$id')";
			$rs6 = mysqli_query($db, $sql6) or die(mysqli_error($db));

			// Seleciona o lote com a data de validade mais próxima e que ainda tem stock
			$sql_lote = "SELECT id, quantidade FROM stock WHERE produto_id = '$artigo' AND quantidade > 0 AND estado = 'ativo' ORDER BY prazo ASC LIMIT 1";
			$rs_lote = mysqli_query($db, $sql_lote) or die(mysqli_error($db));

			if (mysqli_num_rows($rs_lote) > 0) {
			    $lote = mysqli_fetch_array($rs_lote);
			    $lote_id = $lote['id'];
			    $lote_stock = $lote['quantidade'];
			    
			    // Verifica se o stock do lote é suficiente
			    if ($lote_stock >= $qtd) {
			        // Atualiza o stock do lote, subtraindo a quantidade vendida
			        $sql_update_lote = "UPDATE stock SET quantidade = quantidade - '$qtd' WHERE id = '$lote_id'";
			        mysqli_query($db, $sql_update_lote) or die(mysqli_error($db));
			    } else {
			        // Se o stock do lote for insuficiente, lançar um erro ou tratar de outra forma
			        die("Quantidade insuficiente no lote.");
			    }
			} else {
			    // Se não houver lote disponível, lançar um erro
			    die("Nenhum lote disponível para o artigo.");
			}
		}

		$sql = "SELECT saldo FROM transacoes WHERE id = (SELECT MAX(id) FROM transacoes WHERE cliente = 0 AND saldo != 'NULL')";
			$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			if (mysqli_num_rows($rs) > 0) {
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Nota de Debito', 1, 0, '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}else{
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Nota de Debito', 1, 0, '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}

			$sql = "SELECT saldo FROM transacoes WHERE id = (SELECT MAX(id) FROM transacoes WHERE cliente = '$cliente' AND saldo != 'NULL')";
			$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			if (mysqli_num_rows($rs) > 0) {
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Nota de Debito', 1, '$cliente', '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}else{
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Nota de Debito', 1, '$cliente', '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}

		$sql8 = "DELETE FROM nd_artigos_temp WHERE user = '$userID'";
		$rs8 = mysqli_query($db, $sql8) or die(mysqli_error($db));
		if ($rs8 > 0) {
			echo $id;
		}
	}
	}

}else{
	echo 2000000000000000000000000;
}
