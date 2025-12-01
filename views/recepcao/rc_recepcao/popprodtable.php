<?php 
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;

// Verificar se a tabela temporária existe
$check_table_temp = "SHOW TABLES LIKE 'rc_faturas_temp_recepcao'";
$table_temp_exists = mysqli_query($db, $check_table_temp);
if(!$table_temp_exists || mysqli_num_rows($table_temp_exists) == 0) {
    echo '<p class="text-center text-muted" style="padding:20px;color:#666;">Tabela temporária não existe. Execute o SQL de criação.</p>';
    exit;
}

// Verificar se a tabela factura_recepcao existe
$check_table_fat = "SHOW TABLES LIKE 'factura_recepcao'";
$table_fat_exists = mysqli_query($db, $check_table_fat);
if(!$table_fat_exists || mysqli_num_rows($table_fat_exists) == 0) {
    echo '<p class="text-center text-danger" style="padding:20px;">Tabela factura_recepcao não existe. Execute o SQL de criação.</p>';
    exit;
}

$sql = "SELECT rt.*, f.serie, f.n_doc, p.nome, p.apelido, p.numero_processo, e.nome as empresa_nome,
        f.valor as valor_total,
        COALESCE((SELECT SUM(valor_pago) FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id), 0) as total_pago
        FROM rc_faturas_temp_recepcao rt
        INNER JOIN factura_recepcao f ON rt.factura_recepcao_id = f.id
        INNER JOIN pacientes p ON f.paciente = p.id
        LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
        WHERE rt.user = ?
        ORDER BY rt.id DESC";
$stmt = mysqli_prepare($db, $sql);
if($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userID);
    if(mysqli_stmt_execute($stmt)) {
        $rs = mysqli_stmt_get_result($stmt);
    } else {
        error_log("RC popprodtable: Erro ao executar query - " . mysqli_error($db));
        echo '<p class="text-center text-danger" style="padding:20px;">Erro ao executar query.</p>';
        exit;
    }
} else {
    error_log("RC popprodtable: Erro ao preparar query - " . mysqli_error($db));
    echo '<p class="text-center text-danger" style="padding:20px;">Erro ao preparar query.</p>';
    exit;
}

if($rs && mysqli_num_rows($rs) > 0) {
    while($dados = mysqli_fetch_array($rs)){
        $numero_fatura = "FA#" . $dados['serie'] . "/" . str_pad($dados['n_doc'], 6, '0', STR_PAD_LEFT);
        $valor_pendente = floatval($dados['valor_total']) - floatval($dados['total_pago']);
?>
<div class="fatura-item">
    <div class="row">
        <div class="col-md-8">
            <h6><strong><?php echo $numero_fatura; ?></strong></h6>
            <p style="margin: 5px 0; font-size: 12px;">
                <?php echo $dados['nome'] . " " . $dados['apelido']; ?><br>
                <small><?php echo $dados['numero_processo']; ?></small>
                <?php if($dados['empresa_nome']): ?>
                    <br><small><?php echo $dados['empresa_nome']; ?></small>
                <?php endif; ?>
            </p>
            <p style="margin: 5px 0; font-size: 11px; color: #666;">
                Total: <?php echo number_format($dados['valor_total'], 2, ',', '.'); ?> MT | 
                Pago: <?php echo number_format($dados['total_pago'], 2, ',', '.'); ?> MT | 
                Pendente: <?php echo number_format($valor_pendente, 2, ',', '.'); ?> MT
            </p>
        </div>
        <div class="col-md-4" style="display:flex;flex-direction:column;align-items:flex-end;">
            <label style="font-size: 11px; margin-bottom: 5px;">Valor do Recibo:</label>
            <input type="number" step="0.01" class="form-control valor-fatura valor-input" 
                   value="<?php echo number_format($dados['valor'], 2, '.', ''); ?>" 
                   data-faturaid="<?php echo $dados['factura_recepcao_id']; ?>" 
                   max="<?php echo $valor_pendente; ?>"
                   style="margin-bottom: 5px;">
            <button class="btn btn-danger btn-sm btnremove" type="button" data-faturaid="<?php echo $dados['factura_recepcao_id']; ?>">Remover</button>
        </div>
    </div>
</div>
<?php
    }
} else {
    echo '<p class="text-center text-muted" style="padding:20px;color:#666;">Nenhuma fatura selecionada</p>';
}
?>

