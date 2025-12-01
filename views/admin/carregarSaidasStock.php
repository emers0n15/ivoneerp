<?php 
session_start();
include '../../conexao/index.php';

$data1 = $_GET['data1'];
$data2 = $_GET['data2'];
$serie = $_GET['serie'];
$utilizador = $_GET['utilizador'];

// var_dump($data1);

if ($utilizador == "todos") {
	$sql = "SELECT *, (SELECT nome FROM users as u WHERE u.id = e.user) as user  FROM `saida_stock` as e WHERE data BETWEEN '$data1' AND '$data2' AND serie = '$serie'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {

    ?>
    <tr>
        <td><?php echo $dados['id']; ?></td>
        <td><?php echo "SS#".$dados['serie']."/".$dados['id']; ?></td>
        <td><?php echo $dados['solicitante']; ?></td>
        <td><?php echo $dados['motivo']; ?></td>
        <td><?php echo $dados['user']; ?></td>
        
    </tr>
<?php
    }
                                    
} else {
	$sql = "SELECT *, (SELECT nome FROM users as u WHERE u.id = e.user) as user  FROM `saida_stock` as e WHERE data BETWEEN '$data1' AND '$data2' AND serie = '$serie' AND user = '$utilizador'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {
    ?>
    <tr>
        <td><?php echo $dados['id']; ?></td>
        <td><?php echo "SS#".$dados['serie']."/".$dados['id']; ?></td>
        <td><?php echo $dados['solicitante']; ?></td>
        <td><?php echo $dados['motivo']; ?></td>
        <td><?php echo $dados['user']; ?></td>
    </tr>
<?php
    }
}
