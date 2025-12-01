<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

$data_hoje = date('Y-m-d');

$sql = "SELECT 
    COALESCE(total_dinheiro, 0) as dinheiro,
    COALESCE(total_mpesa, 0) as mpesa,
    COALESCE(total_emola, 0) as emola,
    COALESCE(total_pos, 0) as pos
FROM caixa_recepcao 
WHERE data = ? AND status = 'aberto'";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $data_hoje);
mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);

if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    
    $metodos = array(
        array('nome' => 'Dinheiro', 'valor' => floatval($dados['dinheiro'])),
        array('nome' => 'M-Pesa', 'valor' => floatval($dados['mpesa'])),
        array('nome' => 'Emola', 'valor' => floatval($dados['emola'])),
        array('nome' => 'POS', 'valor' => floatval($dados['pos']))
    );
    
    foreach($metodos as $metodo) {
        if($metodo['valor'] > 0) {
?>
    <div class="card mb-2" style="border: 1px solid #e0e0e0; border-radius: 5px;">
        <div class="card-body" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 10px;">
            <div style="flex: 1;">
                <h5 style="margin: 0; font-size: 14px; font-weight: bold; color: #333;"><?php echo $metodo['nome'];?></h5>
            </div>
            <div style="flex: 1; text-align: right;">
                <h3 style="margin: 0; color: #28a745; font-weight: bold; font-size: 20px;"><?php echo number_format($metodo['valor'], 2, ',', '.');?> MT</h3>
            </div>
        </div>
    </div>
<?php
        }
    }
    
    // Calcular total geral
    $total_geral = floatval($dados['dinheiro']) + floatval($dados['mpesa']) + floatval($dados['emola']) + floatval($dados['pos']);
    if($total_geral > 0) {
?>
    <div class="card mt-2" style="border: 2px solid #28a745; border-radius: 5px; background: #28a745;">
        <div class="card-body" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 15px;">
            <div style="flex: 1;">
                <h4 style="margin: 0; font-size: 16px; font-weight: bold; color: #fff;">TOTAL GERAL</h4>
            </div>
            <div style="flex: 1; text-align: right;">
                <h2 style="margin: 0; color: #fff; font-weight: bold; font-size: 28px;"><?php echo number_format($total_geral, 2, ',', '.');?> MT</h2>
            </div>
        </div>
    </div>
<?php
    }
} else {
    echo '<div class="alert alert-info" style="margin: 10px 0; padding: 10px; border-radius: 5px; border-left: 3px solid #17a2b8;">
            <p style="margin: 0; color: #0c5460; font-size: 12px;">Nenhum pagamento registrado hoje.</p>
          </div>';
}
?>

