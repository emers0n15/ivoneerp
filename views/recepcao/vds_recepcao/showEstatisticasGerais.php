<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);

// Estatísticas gerais de todos os dias
$sql = "SELECT 
    COUNT(*) as total_dias,
    SUM(valor_inicial) as total_abertura,
    SUM(total_entradas) as total_entradas_geral,
    SUM(total_dinheiro) as total_dinheiro_geral,
    SUM(total_mpesa) as total_mpesa_geral,
    SUM(total_emola) as total_emola_geral,
    SUM(total_pos) as total_pos_geral,
    SUM(saldo_final) as total_saldo_final,
    AVG(total_entradas) as media_entradas,
    MAX(total_entradas) as max_entradas,
    MIN(total_entradas) as min_entradas
FROM caixa_recepcao 
WHERE status = 'fechado'";

$rs = mysqli_query($db, $sql);

if ($rs && mysqli_num_rows($rs) > 0) {
    $dados = mysqli_fetch_array($rs);
    
    $total_dias = intval($dados['total_dias']);
    $total_abertura = floatval($dados['total_abertura']);
    $total_entradas = floatval($dados['total_entradas_geral']);
    $total_dinheiro = floatval($dados['total_dinheiro_geral']);
    $total_mpesa = floatval($dados['total_mpesa_geral']);
    $total_emola = floatval($dados['total_emola_geral']);
    $total_pos = floatval($dados['total_pos_geral']);
    $total_saldo = floatval($dados['total_saldo_final']);
    $media_entradas = floatval($dados['media_entradas']);
    $max_entradas = floatval($dados['max_entradas']);
    $min_entradas = floatval($dados['min_entradas']);
    $total_geral_metodos = $total_dinheiro + $total_mpesa + $total_emola + $total_pos;
    
    if($total_dias > 0) {
?>
    <div class="row mb-3">
        <div class="col-sm-6 mb-2">
            <div class="card" style="border: 1px solid #e0e0e0; border-radius: 3px; padding: 10px;">
                <h6 style="font-size: 11px; color: #666; margin: 0 0 5px 0;">Total de Dias Fechados</h6>
                <h4 style="font-size: 20px; font-weight: bold; color: #333; margin: 0;"><?php echo $total_dias; ?></h4>
            </div>
        </div>
        <div class="col-sm-6 mb-2">
            <div class="card" style="border: 1px solid #e0e0e0; border-radius: 3px; padding: 10px;">
                <h6 style="font-size: 11px; color: #666; margin: 0 0 5px 0;">Total de Entradas</h6>
                <h4 style="font-size: 20px; font-weight: bold; color: #28a745; margin: 0;"><?php echo number_format($total_entradas, 2, ',', '.'); ?> MT</h4>
            </div>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-sm-4 mb-2">
            <div class="card" style="border: 1px solid #e0e0e0; border-radius: 3px; padding: 10px;">
                <h6 style="font-size: 11px; color: #666; margin: 0 0 5px 0;">Média de Entradas/Dia</h6>
                <h5 style="font-size: 16px; font-weight: bold; color: #333; margin: 0;"><?php echo number_format($media_entradas, 2, ',', '.'); ?> MT</h5>
            </div>
        </div>
        <div class="col-sm-4 mb-2">
            <div class="card" style="border: 1px solid #e0e0e0; border-radius: 3px; padding: 10px;">
                <h6 style="font-size: 11px; color: #666; margin: 0 0 5px 0;">Maior Entrada</h6>
                <h5 style="font-size: 16px; font-weight: bold; color: #28a745; margin: 0;"><?php echo number_format($max_entradas, 2, ',', '.'); ?> MT</h5>
            </div>
        </div>
        <div class="col-sm-4 mb-2">
            <div class="card" style="border: 1px solid #e0e0e0; border-radius: 3px; padding: 10px;">
                <h6 style="font-size: 11px; color: #666; margin: 0 0 5px 0;">Menor Entrada</h6>
                <h5 style="font-size: 16px; font-weight: bold; color: #dc3545; margin: 0;"><?php echo number_format($min_entradas, 2, ',', '.'); ?> MT</h5>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <h5 style="font-size: 13px; font-weight: bold; color: #333; margin-bottom: 8px; border-bottom: 1px solid #38D0ED; padding-bottom: 3px;">Totais por Método de Pagamento (Todos os Dias)</h5>
        <div class="card mb-2" style="border: 1px solid #e0e0e0; border-radius: 3px;">
            <div class="card-body" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 8px;">
                <div style="flex: 1;">
                    <h6 style="margin: 0; font-size: 12px; font-weight: bold; color: #333;">Dinheiro</h6>
                </div>
                <div style="flex: 1; text-align: right;">
                    <h5 style="margin: 0; color: #28a745; font-weight: bold; font-size: 16px;"><?php echo number_format($total_dinheiro, 2, ',', '.');?> MT</h5>
                </div>
            </div>
        </div>
        <div class="card mb-2" style="border: 1px solid #e0e0e0; border-radius: 3px;">
            <div class="card-body" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 8px;">
                <div style="flex: 1;">
                    <h6 style="margin: 0; font-size: 12px; font-weight: bold; color: #333;">M-Pesa</h6>
                </div>
                <div style="flex: 1; text-align: right;">
                    <h5 style="margin: 0; color: #28a745; font-weight: bold; font-size: 16px;"><?php echo number_format($total_mpesa, 2, ',', '.');?> MT</h5>
                </div>
            </div>
        </div>
        <div class="card mb-2" style="border: 1px solid #e0e0e0; border-radius: 3px;">
            <div class="card-body" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 8px;">
                <div style="flex: 1;">
                    <h6 style="margin: 0; font-size: 12px; font-weight: bold; color: #333;">Emola</h6>
                </div>
                <div style="flex: 1; text-align: right;">
                    <h5 style="margin: 0; color: #28a745; font-weight: bold; font-size: 16px;"><?php echo number_format($total_emola, 2, ',', '.');?> MT</h5>
                </div>
            </div>
        </div>
        <div class="card mb-2" style="border: 1px solid #e0e0e0; border-radius: 3px;">
            <div class="card-body" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 8px;">
                <div style="flex: 1;">
                    <h6 style="margin: 0; font-size: 12px; font-weight: bold; color: #333;">POS</h6>
                </div>
                <div style="flex: 1; text-align: right;">
                    <h5 style="margin: 0; color: #28a745; font-weight: bold; font-size: 16px;"><?php echo number_format($total_pos, 2, ',', '.');?> MT</h5>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-2" style="border: 2px solid #28a745; border-radius: 3px; background: #28a745;">
        <div class="card-body" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 12px;">
            <div style="flex: 1;">
                <h4 style="margin: 0; font-size: 14px; font-weight: bold; color: #fff;">TOTAL GERAL</h4>
            </div>
            <div style="flex: 1; text-align: right;">
                <h3 style="margin: 0; color: #fff; font-weight: bold; font-size: 24px;"><?php echo number_format($total_geral_metodos, 2, ',', '.');?> MT</h3>
            </div>
        </div>
    </div>
<?php
    } else {
        echo '<p style="text-align: center; color: #666; padding: 20px;">Nenhum caixa fechado encontrado.</p>';
    }
} else {
    echo '<p style="text-align: center; color: #dc3545; padding: 20px;">Erro ao carregar estatísticas.</p>';
}
?>

