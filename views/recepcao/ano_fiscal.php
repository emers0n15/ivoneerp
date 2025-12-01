<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit;
}

// Verificar se o usuário tem permissão de recepção
if($_SESSION['categoriaUsuario'] != "recepcao"){
    header("location:../admin/");
    exit;
}

include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario'];

// Buscar ano fiscal atual
$sql_serie = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs_serie = mysqli_query($db, $sql_serie);
$ano_atual = date('Y');
$serie_atual = $ano_atual;
if($rs_serie && mysqli_num_rows($rs_serie) > 0) {
    $dados_serie = mysqli_fetch_array($rs_serie);
    $serie_atual = $dados_serie['serie'] ?? $ano_atual;
}
$ano_corresponde = ($serie_atual == $ano_atual);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .ano-fiscal-page {
            max-width: 1200px;
            margin: 0 auto;
        }
        .page-title-simple {
            font-size: 28px;
            font-weight: 600;
            color: #1f2937;
            margin: 30px 0 40px 0;
            padding: 0;
        }
        .ano-fiscal-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .ano-fiscal-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 25px;
        }
        .ano-fiscal-card h4 {
            margin: 0 0 20px 0;
            font-size: 18px;
            font-weight: 600;
            color: #374151;
        }
        .status-display {
            margin-bottom: 25px;
        }
        .status-display p {
            margin: 8px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .status-display strong {
            color: #1f2937;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 10px;
        }
        .status-badge.ok {
            background: #d1fae5;
            color: #059669;
        }
        .status-badge.alert {
            background: #fee2e2;
            color: #dc2626;
        }
        .form-simple {
            margin-top: 20px;
        }
        .form-simple .form-group {
            margin-bottom: 15px;
        }
        .form-simple label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }
        .form-simple input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
        }
        .form-simple button {
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }
        .form-simple button:hover {
            background: #2563eb;
        }
        .history-horizontal {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .history-item-simple {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            color: #6b7280;
        }
        .history-item-simple.active {
            background: #dbeafe;
            border-color: #3b82f6;
            color: #1e40af;
            font-weight: 600;
        }
        .info-simple {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
        }
        .info-simple ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .info-simple li {
            margin: 5px 0;
        }
        @media (max-width: 768px) {
            .ano-fiscal-main {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <?php include 'includes/header.php' ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php include 'includes/side_bar.php'; ?>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="ano-fiscal-page">
                    <!-- Título Simples -->
                    <h1 class="page-title-simple">Ano Fiscal</h1>

                    <!-- Configuração de Ano Fiscal -->
                    <div class="ano-fiscal-card">
                        <h4>Status e Atualização</h4>
                        <div class="status-display">
                            <p>Ano Fiscal Atual: <strong><?php echo $serie_atual; ?></strong></p>
                            <p>Ano do Sistema: <strong><?php echo $ano_atual; ?></strong></p>
                            <?php if(!$ano_corresponde): ?>
                                <span class="status-badge alert">
                                    <i class="fa fa-exclamation-triangle"></i> Ano não corresponde
                                </span>
                            <?php else: ?>
                                <span class="status-badge ok">
                                    <i class="fa fa-check-circle"></i> Configurado corretamente
                                </span>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="daos/atualizar_ano_fiscal.php" class="form-simple">
                            <div class="form-group">
                                <label>Novo Ano Fiscal <span class="text-danger">*</span></label>
                                <input type="number" name="ano" value="<?php echo $ano_atual; ?>" min="2020" max="2100" required>
                            </div>
                            <button type="submit" name="btn">
                                <i class="fa fa-save"></i> Atualizar
                            </button>
                        </form>
                    </div>

                    <!-- Histórico e Informações em Linha -->
                    <div class="ano-fiscal-main">
                        <div class="ano-fiscal-card">
                            <h4>Histórico</h4>
                            <div class="history-horizontal">
                                <?php
                                $sql_historico = "SELECT * FROM serie_factura ORDER BY id DESC";
                                $rs_historico = mysqli_query($db, $sql_historico);
                                if($rs_historico && mysqli_num_rows($rs_historico) > 0):
                                    while($hist = mysqli_fetch_array($rs_historico)):
                                ?>
                                    <div class="history-item-simple <?php echo $hist['ano_fiscal'] == $serie_atual ? 'active' : ''; ?>">
                                        <i class="fa fa-calendar"></i>
                                        <span><?php echo $hist['ano_fiscal']; ?></span>
                                        <?php if($hist['ano_fiscal'] == $serie_atual): ?>
                                            <span style="margin-left: 5px; font-size: 11px; background: #3b82f6; color: white; padding: 2px 6px; border-radius: 10px;">Atual</span>
                                        <?php endif; ?>
                                    </div>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <p style="color: #9ca3af; font-style: italic;">Nenhum ano fiscal registrado</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="ano-fiscal-card">
                            <h4>Informações</h4>
                            <div class="info-simple">
                                <ul>
                                    <li>O ano fiscal deve corresponder ao ano atual para criar faturas</li>
                                    <li>Atualize sempre que necessário, especialmente no início de um novo ano</li>
                                    <li>O sistema usa o ano fiscal mais recente como padrão</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

