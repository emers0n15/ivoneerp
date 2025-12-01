3<?php 
session_start();
include '../../conexao/index.php';

$data1 = $_GET['data1'];
$data2 = $_GET['data2'];

// var_dump($data1);


	$sql = "SELECT
    SUM(valor) as valor
FROM
    factura f
JOIN
    clientes c ON f.cliente = c.id
WHERE
    f.statuss = 0 AND f.data BETWEEN '$data1' AND '$data2'
ORDER BY
    f.dataa ";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {
    ?>
    <tr>
        <td>-</td>
        <td><?php echo $dados['valor']; ?></td>
    </tr>
<?php
    }

