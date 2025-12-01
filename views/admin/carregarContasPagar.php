3<?php 
session_start();
include '../../conexao/index.php';

$data1 = $_GET['data1'];
$data2 = $_GET['data2'];

// var_dump($data1);


	$sql = "SELECT
    f.id,
    f.descricao,
    f.valor,
    f.prazo,
    f.serie,
    c.nome,
    f.data
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
        <td><?php echo $dados['id']; ?></td>
        <td><?php echo "OC"."#".$dados['serie']."/".$dados['id']; ?></td>
        <td><?php echo $dados['valor']; ?></td>
        <td><?php echo $dados['nome']; ?></td>
        <td><?php echo $dados['data']; ?></td>
        <td><?php echo $dados['prazo']; ?></td>
    </tr>
<?php
    }

