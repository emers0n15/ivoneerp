<?php
include '../../../settings/config.php';
include '../../../settings/connect.php';

// Verificar sessão
session_start();
if (!isset($_SESSION['username'])) {
    echo '<div class="alert alert-danger">Sessão expirada. Por favor, faça login novamente.</div>';
    exit();
}

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div class="alert alert-danger">ID do produto não fornecido.</div>';
    exit();
}

$stock_id = intval($_GET['id']);

// Obter detalhes do stock
$sqlStock = "SELECT as.*, p.nomeproduto, p.codbar, p.preco, f.nome as fornecedor_nome
            FROM armazem_stock as
            INNER JOIN produto p ON as.produto_id = p.idproduto
            LEFT JOIN fornecedor f ON as.fornecedor_id = f.id
            WHERE as.id = $stock_id";
$resultadoStock = mysqli_query($db, $sqlStock);

if (mysqli_num_rows($resultadoStock) == 0) {
    echo '<div class="alert alert-danger">Produto não encontrado.</div>';
    exit();
}

$stock = mysqli_fetch_assoc($resultadoStock);

// Obter histórico de movimentos
$sqlMovimentos = "SELECT am.*, u.nome as usuario_nome
                FROM armazem_movimentos am
                INNER JOIN users u ON am.usuario_id = u.id
                WHERE am.stock_id = $stock_id
                ORDER BY am.data_movimento DESC
                LIMIT 10";
$resultadoMovimentos = mysqli_query($db, $sqlMovimentos);

// Status de validade
$classe_prazo = '';
$status_prazo = '';
$dias_restantes = 'N/A';

if ($stock['prazo']) {
    $hoje = new DateTime();
    $data_validade = new DateTime($stock['prazo']);
    $diferenca = $hoje->diff($data_validade);
    $dias_restantes = $diferenca->days;
    
    if ($hoje > $data_validade) {
        $classe_prazo = 'text-danger';
        $status_prazo = 'VENCIDO';
    } elseif ($dias_restantes <= 30) {
        $classe_prazo = 'text-warning';
        $status_prazo = 'Próximo ao vencimento';
    } else {
        $status_prazo = 'Dentro do prazo';
    }
}
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detalhes do Produto</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Produto:</th>
                                <td><?php echo $stock['nomeproduto']; ?></td>
                            </tr>
                            <tr>
                                <th>Código de Barras:</th>
                                <td><?php echo $stock['codigobarra']; ?></td>
                            </tr>
                            <tr>
                                <th>Lote:</th>
                                <td><?php echo $stock['lote']; ?></td>
                            </tr>
                            <tr>
                                <th>Quantidade:</th>
                                <td><?php echo $stock['quantidade']; ?> un.</td>
                            </tr>
                            <tr>
                                <th>Preço de Venda:</th>
                                <td><?php echo number_format($stock['preco'], 2, ',', '.'); ?> €</td>
                            </tr>
                            <tr>
                                <th>Preço de Custo:</th>
                                <td><?php echo $stock['preco_custo'] ? number_format($stock['preco_custo'], 2, ',', '.') . ' €' : 'Não definido'; ?></td>
                            </tr>
                            <tr>
                                <th>Prazo de Validade:</th>
                                <td class="<?php echo $classe_prazo; ?>">
                                    <?php if ($stock['prazo']): ?>
                                        <?php echo date('d/m/Y', strtotime($stock['prazo'])); ?> 
                                        <span class="badge badge-<?php echo $hoje > $data_validade ? 'danger' : ($dias_restantes <= 30 ? 'warning' : 'success'); ?>">
                                            <?php echo $status_prazo; ?> (<?php echo $dias_restantes; ?> dias)
                                        </span>
                                    <?php else: ?>
                                        Não definido
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Fornecedor:</th>
                                <td><?php echo $stock['fornecedor_nome'] ?: 'Não definido'; ?></td>
                            </tr>
                            <tr>
                                <th>Data de Entrada:</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($stock['data_entrada'])); ?></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="custom-badge status-<?php echo $stock['estado'] == 'ativo' ? 'green' : 'red'; ?>">
                                        <?php echo ucfirst($stock['estado']); ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Histórico de Movimentos</h4>
                <?php if (mysqli_num_rows($resultadoMovimentos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Usuário</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($movimento = mysqli_fetch_assoc($resultadoMovimentos)): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($movimento['data_movimento'])); ?></td>
                                        <td>
                                            <?php 
                                            $tipo_badge = '';
                                            switch ($movimento['tipo_movimento']) {
                                                case 'entrada':
                                                    $tipo_movimento = 'Entrada';
                                                    $tipo_badge = 'success';
                                                    break;
                                                case 'transferencia':
                                                    $tipo_movimento = 'Transferência';
                                                    $tipo_badge = 'info';
                                                    break;
                                                case 'ajuste':
                                                    $tipo_movimento = 'Ajuste';
                                                    $tipo_badge = 'warning';
                                                    break;
                                                default:
                                                    $tipo_movimento = ucfirst($movimento['tipo_movimento']);
                                                    $tipo_badge = 'secondary';
                                            }
                                            ?>
                                            <span class="badge badge-<?php echo $tipo_badge; ?>">
                                                <?php echo $tipo_movimento; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $movimento['quantidade']; ?> un.</td>
                                        <td><?php echo $movimento['usuario_nome']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Nenhum movimento registrado para este produto.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($stock['observacao'])): ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Observações</h4>
                <p><?php echo nl2br($stock['observacao']); ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
