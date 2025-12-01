<?php 
session_start();
include_once '../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;
/*********************************************/

$sql = "SELECT *, (SELECT CONCAT(nome,' ',apelido) FROM clientes as c WHERE c.id = r.cliente) as cl FROM rc_fact_temp as r WHERE user = '$userID'";
$rs = mysqli_query($db, $sql);
while($dados = mysqli_fetch_array($rs)){
?>
<div class="container mt-2 col-sm-12">
    <div class="card" style="border: none">
        <!-- Div 1: Nome do Produto e Valor Total -->
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="card-title"><?php echo "FA#".$dados['serie']."/".$dados['factura']; ?></h6>
                </div>
                <div class="col-md-4" style="display:flex;flex-direction:row;justify-content:flex-end;">
                    <h4 class="card-text"><?php echo number_format($dados['total'], 2, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
    
        <!-- Div 2: Botões de Ação -->
        <div class="card-body" style="margin-top: -25px;">
            <div class="row">
                <div class="col-md-8" style="display:flex;flex-direction:row">
                    <?php echo "Cliente: ".$dados['cl']; ?>
                </div>
                <div class="col-md-4 text-right" style="display:flex;flex-direction:row;justify-content:flex-end;">
                    <button class="btn btn-danger btnremove" type="button" data-fatura="<?php echo $dados['id']; ?>">Remover</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
}
?>

