<?php 
session_start();
include '../../conexao/index.php';

$data1 = $_GET['data1'];
$data2 = $_GET['data2'];
$cliente = $_GET['cliente'];
$serie = $_GET['serie'];

if ($cliente == "todos") {
	$sql = "SELECT `id`,n_doc, `descricao`, `valor`, `iva`, `serie`, `prazo`, `metodo`, `statuss`, `nota_credito`, `recibo`, `nota_debito`, `cotacao`, (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = factura.cliente) as cliente, (SELECT nome FROM users WHERE users.id = factura.usuario) as usuario FROM `factura` WHERE data BETWEEN '$data1' AND '$data2' AND serie = '$serie'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {

    ?>
    <tr>
        <td><?php echo $dados['id']; ?></td>
        <td><?php echo $dados['serie']; ?></td>
        <td><?php echo "FA#".$dados['id']."/".$dados['n_doc']; ?></td>
        <td><?php echo $dados['valor']; ?></td>
        <td><?php echo $dados['iva']; ?></td>
        <td><?php echo $dados['cliente']; ?></td>
        <td><?php echo $dados['prazo']; ?></td>
        <td>
        <?php 
        	if ($dados['statuss'] == 1) {
	    		echo "<b style='color: green'>Paga</b>";
	    	}else{
	    		"<b style='color: red'>Pendente</b>";
	    	}
         ?>
     	</td>
        <td><?php echo $dados['recibo']; ?></td>
        <td><?php echo $dados['nota_credito']; ?></td>
        <td><?php echo $dados['nota_debito']; ?></td>
        <td><?php echo $dados['usuario']; ?></td>
    </tr>
<?php
    }
                                    
} else {
	$sql = "SELECT `id`,n_doc, `descricao`, `valor`, `iva`, `serie`, `prazo`, `metodo`, `statuss`, `nota_credito`, `recibo`, `nota_debito`, `cotacao`, (SELECT CONCAT(nome,' ',apelido) FROM clientes WHERE clientes.id = factura.cliente) as cliente, (SELECT nome FROM users WHERE users.id = factura.usuario) as usuario FROM `factura` WHERE dataa BETWEEN '$data1' AND '$data2' AND cliente = '$cliente' AND serie = '$serie'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {
    ?>
    <tr>
        <td><?php echo $dados['id']; ?></td>
        <td><?php echo $dados['serie']; ?></td>
        <td><?php echo "FA#".$dados['id']."/".$dados['n_doc']; ?></td>
        <td><?php echo $dados['valor']; ?></td>
        <td><?php echo $dados['iva']; ?></td>
        <td><?php echo $dados['cliente']; ?></td>
        <td><?php echo $dados['prazo']; ?></td>
        <td>
        <?php 
        	if ($dados['statuss'] == 1) {
	    		echo "<b style='color: green'>Paga</b>";
	    	}else{
	    		"<b style='color: red'>Pendente</b>";
	    	}
         ?>
     	</td>
        <td><?php echo $dados['recibo']; ?></td>
        <td><?php echo $dados['nota_credito']; ?></td>
        <td><?php echo $dados['nota_debito']; ?></td>
        <td><?php echo $dados['usuario']; ?></td>
    </tr>
<?php
    }
}
