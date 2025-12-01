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

$sql = "SELECT *, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = ps.artigo) as nome FROM ri_artigos_temp as ps WHERE user = '$userID'";
$rs = mysqli_query($db, $sql);
while($dados = mysqli_fetch_array($rs)){
?>
<div class="container mt-2 col-sm-12">
    <div class="card" style="border: none">
        <!-- Div 1: Nome do Produto e Valor Total -->
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="card-title"><?php echo $dados['nome']; ?></h6>
                </div>
            </div>
        </div>
    
        <!-- Div 2: Botões de Ação -->
        <div class="card-body" style="margin-top: -25px;">
            <div class="row">
                <div class="col-md-8" style="display:flex;flex-direction:row">
                    <button class="btn btn-outline-secondary btn_diminuir" type="button" data-idproduto="<?php echo $dados['id']; ?>">-</button>
                    <input type="text" class="form-control text-center qtd" value="<?php echo $dados['qtd']; ?>" style="width: 50px;" data-idproduto="<?php echo $dados['id']; ?>">
                    <button class="btn btn-outline-secondary btn_aumentar" type="button" data-idproduto="<?php echo $dados['id']; ?>">+</button>
                </div>
                <div class="col-md-4 text-right" style="display:flex;flex-direction:row;justify-content:flex-end;">
                    <button class="btn btn-danger btnremove" type="button" data-idproduto="<?php echo $dados['id']; ?>">Remover</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
}
?>

