<?php 

session_start();
error_reporting(E_ALL);
 ini_set("display_errors", 1);

include_once '../conexao/index.php';

date_default_timezone_set('Africa/Maputo');

$data_hora = date("Y-m-d H:i:s");



/*Variaveis do Sistema*/

/*********************************************/

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];

$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];

$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 



$userID = $_SESSION['idUsuario'];

$userNOME = $_SESSION['nomeUsuario'];

$userCATE = $_SESSION['categoriaUsuario'];

/*********************************************/
$id_vd = $_GET['id_vd'];

// Dados da Factura
$sql2 = "SELECT * FROM pedido WHERE idpedido = '$id_vd'";
$rs2 = mysqli_query($db, $sql2);
$dados2 = mysqli_fetch_array($rs2);
$n_doc = $dados2['n_doc'];
$v = $dados2['pagamentopedido'];
$iva = $dados2['iva'];
$desc = $dados2['disconto'];
$v1 = $v + $desc;
$tot = $v + $iva;

// Dados da Empresa a Faturar (exemplo)
$sql = "SELECT * FROM empresa";
$rs = mysqli_query($db, $sql);
$dados = mysqli_fetch_array($rs);
$img = $dados['img'];

// Dados da Empresa Faturada (exemplo)
$sql1 = "SELECT * FROM clientes WHERE id = (SELECT clientepedido FROM pedido WHERE idpedido = '$id_vd')";
$rs1 = mysqli_query($db, $sql1);
$dados1 = mysqli_fetch_array($rs1);
?>

<!DOCTYPE html>

<html>

<head>

	<title>VD #<?php echo $dados2['serie']."/".$n_doc; ?></title>

	<script type="text/javascript" src="../js/jquery-3.3.1.min.js"></script>
	<style type="text/css">
		*{
			font-size: 8pt;
		}
	</style>

</head>

<body>

<div class="fatura">

	<div class="header" style="text-align: center;">
		<p><?php echo $dados['nome']; ?></p>
		<p><?php echo $dados['endereco']; ?></p>
		<p>Cell:<?php echo $dados['contacto']; ?>  -  NUIT: <?php echo $dados['nuit']; ?></p>
	</div>

	<div class="fact" style="text-align: center;">

		<p><span>VD: </span><span class="pd"><?php echo $dados2['serie']."/".$n_doc; ?></span></p>
	</div>
	<div class="fact" style="text-align: center;">

		<p><span>Cliente: </span><span class="pd"><?php echo $dados1['nome'].' '.$dados1['apelido']; ?></span></p>
		<p><span>Nuit: </span><span class="pd"><?php echo $dados1['nuit']; ?></span> - <span>Contacto: </span><span class="pd"><?php echo $dados1['contacto']; ?></span></p>
	</div>
	<div class="body" style="text-align: center;">

		<p class="p"><span>Documento Original</span><span style="margin-left: 10px;"><?php echo $data_hora; ?></span></p>

	</div>
	
	<div>
		<table>
			<thead>
		        <th style="width: 40%;font-weight: bold;">Descrição</th>
		        <th style="width: 10%;font-weight: bold;text-align:center;">Qtd</th>
		        <th style="width: 15%;font-weight: bold;text-align:center;">Preço</th>
		        <th style="width: 10%;font-weight: bold;text-align:center;">IVA</th>
		        <th style="width: 15%;font-weight: bold;text-align:center;">Total</th>
			</thead>
			<tbody>
				<?php 
					// Use uma consulta preparada para evitar injeção de SQL
				    $sql = "SELECT *,(SELECT nomeproduto FROM produto as p WHERE p.idproduto = e.produtoentrega) as n FROM entrega as e WHERE pedidoentrega = ?";
				    
				    // Prepara a consulta
				    $stmt = mysqli_prepare($db, $sql);
				    
				    // Verifica se a preparação da consulta foi bem-sucedida
				    if ($stmt) {
				        // Associa o valor de $id_vd à consulta
				        mysqli_stmt_bind_param($stmt, "s", $id_vd);
				        
				        // Executa a consulta
				        mysqli_stmt_execute($stmt);
				        
				        // Obtém os resultados da consulta
				        $result = mysqli_stmt_get_result($stmt);
				        
				        // Itera pelos resultados
				        while ($dados3 = mysqli_fetch_array($result)) {
				            $t = $dados3['totalentrega'];
				            $iv = $dados3['iva'];
				            $cal = $t + $iv;
				        ?>
				                <tr style="border-bottom: 1px solid #666">
				                    <td><?php echo $dados3['n']; ?></td>
				                    <td><?php echo $dados3['qtdentrega']; ?></td>
				                    <td><?php echo number_format($dados3['precoentrega'], 2, ".", ","); ?></td>
				                    <td><?php echo number_format($dados3['iva'], 2, ".", ","); ?></td>
				                    <td><?php echo number_format($cal, 2, ".", ","); ?></td>
				                </tr>
				        <?php
				        }
				        
				        // Fecha a consulta preparada
				        mysqli_stmt_close($stmt);
				    }
				?>
			</tbody>
		</table>
	</div>
	<div class="totall">

		<table style="width: 100%;text-align: center;">
			<thead>
				<th>Mercadoria</th>
				<th>Desconto</th>
				<th>IVA</th>
				<th>TOTAL</th>
			</thead>
			<tbody>
				<tr>
					<td><?php echo number_format($v1, 2); ?></td>
					<td><?php echo number_format($desc, 2); ?></td>
					<td><?php echo number_format($iva, 2); ?></td>
					<td><?php echo number_format($tot, 2); ?></td>
				</tr>
			</tbody>
		</table>

	</div>

	<div class="processo" style="text-align: center;">

		<p>iVone - Processado por programa</p>

		<p style="margin: 10px 0px;">Operador: <?php echo $_SESSION['nomeUsuario']; ?></p>

		<p>Obrigado pela sua preferencia!</p>

	</div>

</div>

<script type="text/javascript">

	$(function() {

		window.print();

	});

</script>

</body>

</html>