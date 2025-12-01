3<?php 
session_start();
include '../../conexao/index.php';

$data1 = $_GET['data1'];
$data2 = $_GET['data2'];
$serie = $_GET['serie'];
$cliente = $_GET['cliente'];

// var_dump($data1);

if ($cliente == "todos") {

                                    
} else {
	$sql = "SELECT * FROM `transacoes` as e WHERE data BETWEEN '$data1' AND '$data2' AND serie = '$serie' AND cliente = '$cliente' AND (doc = 'Factura' OR doc = 'Nota de Credito' OR doc = 'Nota de Debito' OR doc = 'Recibo')";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {
    ?>
    <tr>
        <td><?php echo $dados['id']; ?></td>
        <td><?php echo $dados['doc']."#".$dados['serie']."/".$dados['id']; ?></td>
        <td><?php echo $dados['iva']; ?></td>
        <td><?php echo $dados['ref_factura']; ?></td>
        <td><?php echo $dados['debito']; ?></td>
        <td><?php echo $dados['credito']; ?></td>
        <td><?php echo $dados['saldo']; ?></td>
    </tr>
<?php
    }
}
