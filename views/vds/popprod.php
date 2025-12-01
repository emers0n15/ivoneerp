<?php 
    include_once '../../conexao/index.php';
    error_reporting(E_ALL);
?>
<?php
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $sql = "SELECT * FROM produto WHERE grupo = '$id'";
    }else{
        $sql = "SELECT * FROM produto";
    }
    $rs = mysqli_query($db, $sql);
    while ($dados = mysqli_fetch_array($rs)) {
?>
    <tr>
        <td><?php echo $dados['idproduto'] ?></td>
        <td><?php echo $dados['nomeproduto'] ?></td>
        <td><?php echo $dados['preco'] ?></td>
        <td><?php echo $dados['stock'] ?></td>
    </tr>
<?php } ?>