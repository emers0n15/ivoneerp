<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

if($_SESSION['categoriaUsuario'] != "recepcao"){
	header("location:../admin/");
	exit;
}

$empresa_id = intval($_GET['id'] ?? 0);
if(!$empresa_id){
	header("location:empresas.php");
	exit;
}

// Buscar dados da empresa
$sql_empresa = "SELECT * FROM empresas_seguros WHERE id = $empresa_id";
$rs_empresa = mysqli_query($db, $sql_empresa);
$empresa = mysqli_fetch_array($rs_empresa);

if(!$empresa){
	header("location:empresas.php");
	exit;
}

// Buscar ou criar tabela de preços
$sql_tabela = "SELECT * FROM tabelas_precos WHERE empresa_id = $empresa_id AND ativo = 1 LIMIT 1";
$rs_tabela = mysqli_query($db, $sql_tabela);
$tabela = mysqli_fetch_array($rs_tabela);

if(!$tabela){
	// Criar tabela de preços padrão
	$sql_criar = "INSERT INTO tabelas_precos (empresa_id, nome, ativo, usuario_criacao) 
	              VALUES ($empresa_id, 'Tabela Padrão', 1, " . $_SESSION['idUsuario'] . ")";
	mysqli_query($db, $sql_criar);
	$tabela_id = mysqli_insert_id($db);
} else {
	$tabela_id = $tabela['id'];
}

// Buscar serviços disponíveis
$sql_servicos = "SELECT * FROM servicos_clinica WHERE ativo = 1 ORDER BY categoria, nome";
$rs_servicos = mysqli_query($db, $sql_servicos);

// Buscar preços já cadastrados
$sql_precos = "SELECT * FROM tabela_precos_servicos WHERE tabela_precos_id = $tabela_id";
$rs_precos = mysqli_query($db, $sql_precos);
$precos_cadastrados = array();
while($preco = mysqli_fetch_array($rs_precos)){
	$precos_cadastrados[$preco['servico_id']] = $preco;
}

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
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
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Tabela de Preços - <?php echo htmlspecialchars($empresa['nome']); ?></h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="empresas.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Configurar Preços dos Serviços</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="daos/salvar_tabela_precos.php">
                                    <input type="hidden" name="tabela_precos_id" value="<?php echo $tabela_id; ?>">
                                    <input type="hidden" name="empresa_id" value="<?php echo $empresa_id; ?>">
                                    
                                    <div class="alert alert-info">
                                        <strong>Informação:</strong> Configure os preços específicos para esta empresa. 
                                        Se não configurar, será usado o preço padrão do serviço com desconto geral de <?php echo $empresa['desconto_geral']; ?>%.
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Serviço</th>
                                                    <th>Categoria</th>
                                                    <th>Preço Padrão</th>
                                                    <th>Preço Contratado</th>
                                                    <th>Desconto (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if($rs_servicos && mysqli_num_rows($rs_servicos) > 0):
                                                    while($servico = mysqli_fetch_array($rs_servicos)):
                                                        $preco_cadastrado = isset($precos_cadastrados[$servico['id']]) ? $precos_cadastrados[$servico['id']] : null;
                                                        $preco_contratado = $preco_cadastrado ? $preco_cadastrado['preco'] : $servico['preco'];
                                                        $desconto = $preco_cadastrado ? $preco_cadastrado['desconto_percentual'] : $empresa['desconto_geral'];
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($servico['nome']); ?></td>
                                                        <td><?php echo htmlspecialchars($servico['categoria']); ?></td>
                                                        <td><?php echo number_format($servico['preco'], 2, ',', '.'); ?> MT</td>
                                                        <td>
                                                            <input type="hidden" name="servico_id[]" value="<?php echo $servico['id']; ?>">
                                                            <input type="number" name="preco[]" class="form-control" 
                                                                   value="<?php echo $preco_contratado; ?>" 
                                                                   step="0.01" min="0" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="desconto[]" class="form-control" 
                                                                   value="<?php echo $desconto; ?>" 
                                                                   step="0.01" min="0" max="100">
                                                        </td>
                                                    </tr>
                                                <?php
                                                    endwhile;
                                                endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="m-t-20 text-center">
                                        <button type="submit" class="btn btn-primary" name="btn">Salvar Tabela de Preços</button>
                                    </div>
                                </form>
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

