<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
error_reporting(E_ALL);
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

$caixa = $_GET['caixa'];

$sql = "SELECT fechoperiodo FROM periodo WHERE idperiodo = '$caixa'";
$rs = mysqli_query($db,$sql);
    while ($dados = mysqli_fetch_array($rs)) {
?>
    <tr>
        <td><?php echo "".number_format($dados['fechoperiodo'],2,".",","); ?></td>
    </tr>
<?php
    }
?>