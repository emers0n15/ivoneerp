<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;
$empresa_id = isset($_GET['empresa']) ? intval($_GET['empresa']) : null;

if($empresa_id && $empresa_id > 0) {
    $sql = "SELECT st.*, (SELECT nome FROM servicos_clinica as s WHERE s.id = st.servico) as nome 
            FROM vds_servicos_temp as st 
            WHERE st.user = ? AND st.empresa_id = ? 
            ORDER BY st.id DESC";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $empresa_id);
} else {
    $sql = "SELECT st.*, (SELECT nome FROM servicos_clinica as s WHERE s.id = st.servico) as nome 
            FROM vds_servicos_temp as st 
            WHERE st.user = ? AND (st.empresa_id IS NULL OR st.empresa_id = 0) 
            ORDER BY st.id DESC";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);
}
mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);

if($rs && mysqli_num_rows($rs) > 0) {
    while($dados = mysqli_fetch_array($rs)){
?>
<div class="container mt-2 col-sm-12">
    <div class="card" style="border: none">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="card-title"><?php echo $dados['nome']; ?></h6>
                </div>
                <div class="col-md-4" style="display:flex;flex-direction:row;justify-content:flex-end;">
                    <h4 class="card-text"><?php echo number_format($dados['total'], 2, ',', '.'); ?> MT</h4>
                </div>
            </div>
        </div>
        <div class="card-body" style="margin-top: -25px;">
            <div class="row">
                <div class="col-md-8" style="display:flex;flex-direction:row">
                    <button class="btn btn-outline-secondary btn_diminuir" type="button" data-idservico="<?php echo $dados['id']; ?>">-</button>
                    <input type="text" class="form-control text-center qtd" value="<?php echo $dados['qtd']; ?>" style="width: 50px;" data-idservico="<?php echo $dados['id']; ?>">
                    <button class="btn btn-outline-secondary btn_aumentar" type="button" data-idservico="<?php echo $dados['id']; ?>">+</button>
                </div>
                <div class="col-md-4 text-right" style="display:flex;flex-direction:row;justify-content:flex-end;">
                    <button class="btn btn-danger btnremove" type="button" data-idservico="<?php echo $dados['id']; ?>">Remover</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    }
} else {
    echo '<p class="text-center text-muted" style="padding:20px;color:#666;">Nenhum serviço selecionado</p>';
}
?>

