3<?php 
session_start();
include '../../conexao/index.php';

$data1 = $_GET['data1'];
$data2 = $_GET['data2'];

// var_dump($data1);


	$sql = "SELECT
    SUM(valor) as valor
FROM
    ordem_compra f
JOIN
    fornecedor c ON f.fornecedor = c.id
WHERE
    f.data BETWEEN '$data1' AND '$data2'
ORDER BY
    f.data ";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {
    ?>
    <tr>
        <td>-</td>
        <td><?php echo $dados['valor']; ?></td>
    </tr>
<?php
    }

