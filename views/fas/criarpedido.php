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
$prazo = $_POST['prazo'];
$metodo = $_POST['metodo'];
$condicoes = $_POST['condicoes'];
$apolice = $_POST['apolice'];
$codigo1 = $_POST['codigo1'];
$codigo2 = $_POST['codigo2'];
$codigo3 = $_POST['codigo3'];

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

$sql2 = "SELECT SUM(total) as total, SUM(iva) as ivas FROM fa_artigos_temp WHERE user = '$userID'";
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
	echo "<script>window.location.href='../facturas.php</script>";
}

if ($year == $serie) {
	$siquela = "SELECT MAX(n_doc) as maxid FROM factura WHERE serie = '$serie'";
	$res = mysqli_query($db, $siquela) or die(mysqli_error($db));
	if (mysqli_num_rows($res) > 0) {
		$ddo = mysqli_fetch_array($res);
		$max_id = $ddo['maxid'];
		$new_id = $max_id + 1;
		$sql3 = "INSERT INTO factura SET n_doc = '$new_id',descricao = '$data_hora', valor = '$total', iva = '$iva_incluso', disconto = '$valorDisc', serie = '$serie', prazo = '$prazo', metodo = '$metodo', condicoes = '$condicoes', apolice = '$apolice', codigo1 = '$codigo1', codigo2 = '$codigo2', codigo3 = '$codigo3', cliente = '$cliente', utente = '$utente', usuario = '$userID', dataa = '$data'";
		$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
		$id = mysqli_insert_id($db);
	if ($rs3 > 0) {
		$sql10 = "UPDATE `clientes` SET `qtd_factura` = qtd_factura + 1 WHERE `id` = '$cliente'";
            $rs10 = mysqli_query($db, $sql10) or die(mysqli_error($db));

            // Loop para processar os artigos na tabela temporária
			$sql4 = "SELECT * FROM fa_artigos_temp WHERE user = '$userID'";
			$rs4 = mysqli_query($db, $sql4) or die(mysqli_error($db));
			while ($dados4 = mysqli_fetch_array($rs4)) {
			    $artigo = $dados4['artigo'];
			    $qtd = $dados4['qtd'];
			    $preco = $dados4['preco'];
			    $total = $dados4['total'];
			    $iva = $dados4['iva'];
			    $ls = $dados4['lote'];

			    // Verificar se o artigo é estocável
			    $sqlCheckStockable = "SELECT stocavel FROM produto WHERE idproduto = '$artigo'";
			    $stockableResult = mysqli_query($db, $sqlCheckStockable) or die(mysqli_error($db));
			    if (mysqli_num_rows($stockableResult) > 0) {
			        $stockableData = mysqli_fetch_array($stockableResult);
			        if ($stockableData['stocavel'] = 1) {
			            // Verifica o lote mais próximo da data de validade
			            $sqlBatch = "SELECT * FROM stock WHERE produto_id = '$artigo' AND quantidade > 0 AND estado = 'ativo' ORDER BY prazo ASC, quantidade DESC LIMIT 1";
			            $batchResult = mysqli_query($db, $sqlBatch) or die(mysqli_error($db));

			            if (mysqli_num_rows($batchResult) > 0) {
			                $batchData = mysqli_fetch_array($batchResult);
			                $batch_id = $batchData['id'];
			                $batch_qtd = $batchData['quantidade'];

			                if ($batch_qtd >= $qtd) {
			                    // Atualiza o estoque do lote
			                    $sql6 = "INSERT INTO `fa_artigos_fact`(`artigo`, `qtd`, `preco`, `iva`, `total`, `user`, `factura`, `lote`) 
			                            VALUES('$artigo', '$qtd', '$preco', '$iva', '$total', '$userID', '$id', '$ls')";
			                    $rs6 = mysqli_query($db, $sql6) or die(mysqli_error($db));

			                    $sqlUpdateStock = "UPDATE stock SET quantidade = quantidade - '$qtd' WHERE id = '$batch_id'";
			                    mysqli_query($db, $sqlUpdateStock) or die(mysqli_error($db));
			                } else {
			                    // Caso não haja quantidade suficiente no lote, você pode decidir como proceder
			                    // Por exemplo, buscar o próximo lote ou exibir uma mensagem de erro.
			                }
			            } else {
			                // Caso não haja lote disponível, você pode decidir como proceder
			            }
			        }
			    }
			}


		$sql = "SELECT saldo FROM transacoes WHERE id = (SELECT MAX(id) FROM transacoes WHERE cliente = 0 AND saldo != 'NULL')";
			$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			if (mysqli_num_rows($rs) > 0) {
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Factura', '$new_id', 0, '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}else{
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Factura', '$new_id', 0, '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}

			$sql = "SELECT saldo FROM transacoes WHERE id = (SELECT MAX(id) FROM transacoes WHERE cliente = '$cliente' AND saldo != 'NULL')";
			$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			if (mysqli_num_rows($rs) > 0) {
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Factura', '$new_id', '$cliente', '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}else{
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Factura', '$new_id', '$cliente', '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}

		$sql8 = "DELETE FROM fa_artigos_temp WHERE user = '$userID'";
		$rs8 = mysqli_query($db, $sql8) or die(mysqli_error($db));
		if ($rs8 > 0) {
			echo $id;
		}
	}
	}else{
		$sql3 = "INSERT INTO factura SET n_doc = 1,descricao = '$data_hora', valor = '$total', iva = '$iva_incluso', disconto = '$valorDisc', serie = '$serie', prazo = '$prazo', metodo = '$metodo', condicoes = '$condicoes', apolice = '$apolice', codigo1 = '$codigo1', codigo2 = '$codigo2', codigo3 = '$codigo3', cliente = '$cliente', utente = '$utente', usuario = '$userID', dataa = '$data'";
		$rs3 = mysqli_query($db, $sql3) or die(mysqli_error($db));
		$id = mysqli_insert_id($db);
	if ($rs3 > 0) {
		$sql10 = "UPDATE `clientes` SET `qtd_factura` = qtd_factura + 1 WHERE `id` = '$cliente'";
            $rs10 = mysqli_query($db, $sql10) or die(mysqli_error($db));

            // Loop para processar os artigos na tabela temporária
			$sql4 = "SELECT * FROM fa_artigos_temp WHERE user = '$userID'";
			$rs4 = mysqli_query($db, $sql4) or die(mysqli_error($db));
			while ($dados4 = mysqli_fetch_array($rs4)) {
			    $artigo = $dados4['artigo'];
			    $qtd = $dados4['qtd'];
			    $preco = $dados4['preco'];
			    $total = $dados4['total'];
			    $iva = $dados4['iva'];
			    $ls = $dados4['lote'];

			    // Verificar se o artigo é estocável
			    $sqlCheckStockable = "SELECT stocavel FROM produto WHERE idproduto = '$artigo'";
			    $stockableResult = mysqli_query($db, $sqlCheckStockable) or die(mysqli_error($db));
			    if (mysqli_num_rows($stockableResult) > 0) {
			        $stockableData = mysqli_fetch_array($stockableResult);
			        if ($stockableData['stocavel'] = 1) {
			            // Verifica o lote mais próximo da data de validade
			            $sqlBatch = "SELECT * FROM stock WHERE produto_id = '$artigo' AND quantidade > 0 AND estado = 'ativo' ORDER BY prazo ASC, quantidade DESC LIMIT 1";
			            $batchResult = mysqli_query($db, $sqlBatch) or die(mysqli_error($db));

			            if (mysqli_num_rows($batchResult) > 0) {
			                $batchData = mysqli_fetch_array($batchResult);
			                $batch_id = $batchData['id'];
			                $batch_qtd = $batchData['quantidade'];

			                if ($batch_qtd >= $qtd) {
			                    // Atualiza o estoque do lote
			                    $sql6 = "INSERT INTO `fa_artigos_fact`(`artigo`, `qtd`, `preco`, `iva`, `total`, `user`, `factura`, `lote`) 
			                            VALUES('$artigo', '$qtd', '$preco', '$iva', '$total', '$userID', '$id', '$ls')";
			                    $rs6 = mysqli_query($db, $sql6) or die(mysqli_error($db));

			                    $sqlUpdateStock = "UPDATE stock SET quantidade = quantidade - '$qtd' WHERE id = '$batch_id'";
			                    mysqli_query($db, $sqlUpdateStock) or die(mysqli_error($db));
			                } else {
			                    // Caso não haja quantidade suficiente no lote, você pode decidir como proceder
			                    // Por exemplo, buscar o próximo lote ou exibir uma mensagem de erro.
			                }
			            } else {
			                // Caso não haja lote disponível, você pode decidir como proceder
			            }
			        }
			    }
			}


		$sql = "SELECT saldo FROM transacoes WHERE id = (SELECT MAX(id) FROM transacoes WHERE cliente = 0 AND saldo != 'NULL')";
			$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			if (mysqli_num_rows($rs) > 0) {
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Factura', 1, 0, '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}else{
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Factura', 1, 0, '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}

			$sql = "SELECT saldo FROM transacoes WHERE id = (SELECT MAX(id) FROM transacoes WHERE cliente = '$cliente' AND saldo != 'NULL')";
			$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			if (mysqli_num_rows($rs) > 0) {
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Factura', 1, '$cliente', '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}else{
				$dados = mysqli_fetch_array($rs);
				$sal = $dados['saldo'];
				$novo_saldo = $sal + $totall;
				$sql = "INSERT INTO `transacoes`(`doc`, `n_doc`, `cliente`, `iva`, `serie`, `debito`, `saldo`) VALUES ('Factura', 1, '$cliente', '$iva_incluso', '$serie', '$totall', '$novo_saldo')";
				$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
			}

		$sql8 = "DELETE FROM fa_artigos_temp WHERE user = '$userID'";
		$rs8 = mysqli_query($db, $sql8) or die(mysqli_error($db));
		if ($rs8 > 0) {
			echo $id;
		}
	}
	}

}else{
	echo 2;
}
