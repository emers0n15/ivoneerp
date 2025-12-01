<?php 
session_start();
include '../../conexao/index.php';

$data1 = $_GET['data1'];
$data2 = $_GET['data2'];
$status = $_GET['status'];
$utilizador = $_GET['utilizador'];

// var_dump($data1);

if ($utilizador == "todos") {
	$sql = "SELECT *, (SELECT nome FROM users as g WHERE g.id = p.usuario) as users  FROM periodo as p WHERE datafechoperiodo BETWEEN '$data1' AND '$data2' AND diaperiodo = '$status'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {

    ?>
    <tr>
        <td><?php echo $dados['idperiodo']; ?></td>
        <td><?php echo "CX#".$dados['serie']."/".$dados['idperiodo']; ?></td>

        <td><?php echo $dados['aberturaperiodo']; ?></td>
        <td><?php echo $dados['fechoperiodo']; ?></td>
        <td><?php echo $dados['dataaberturaperiodo']; ?></td>
        <td><?php echo $dados['datafechoperiodo']; ?></td>

    </tr>
<?php
    }
                                    
} else {
	$sql = "SELECT *, (SELECT nome FROM users as g WHERE g.id = p.usuario) as users  FROM periodo as p WHERE datafechoperiodo BETWEEN '$data1' AND '$data2' AND diaperiodo = '$status' AND usuario = '$utilizador'";
    $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
    while ($dados = mysqli_fetch_array($rs)) {
    ?>
    <tr>
        <td><?php echo $dados['idperiodo']; ?></td>
        <td><?php echo "CX#".$dados['serie']."/".$dados['idperiodo']; ?></td>

        <td><?php echo $dados['aberturaperiodo']; ?></td>
        <td><?php echo $dados['fechoperiodo']; ?></td>
        <td><?php echo $dados['dataaberturaperiodo']; ?></td>
        <td><?php echo $dados['datafechoperiodo']; ?></td>

    </tr>
<?php
    }
}
