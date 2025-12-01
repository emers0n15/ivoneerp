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

// Verificar se o ID do armazém foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID do armazém não fornecido!'); window.location='armazens.php';</script>";
    exit;
}

$armazem_id = (int) $_GET['id'];

// Buscar dados do armazém
$sqlArmazem = "SELECT * FROM armazem WHERE id = $armazem_id";
$resultadoArmazem = mysqli_query($db, $sqlArmazem);

if (mysqli_num_rows($resultadoArmazem) == 0) {
    echo "<script>alert('Armazém não encontrado!'); window.location='armazens.php';</script>";
    exit;
}

$armazem = mysqli_fetch_assoc($resultadoArmazem);

// Contar produtos no armazém
$sqlContarProdutos = "SELECT COUNT(*) as total FROM armazem_stock WHERE armazem_id = $armazem_id";
$resultadoContarProdutos = mysqli_query($db, $sqlContarProdutos);
$totalProdutos = mysqli_fetch_assoc($resultadoContarProdutos)['total'];

// Contar produtos ativos no armazém
$sqlContarProdutosAtivos = "SELECT COUNT(*) as total FROM armazem_stock WHERE armazem_id = $armazem_id AND estado = 'ativo'";
$resultadoContarProdutosAtivos = mysqli_query($db, $sqlContarProdutosAtivos);
$totalProdutosAtivos = mysqli_fetch_assoc($resultadoContarProdutosAtivos)['total'];

// Listar produtos disponíveis para entrada em armazém
$sqlProdutos = "SELECT idproduto, nomeproduto, codigobarra FROM produto WHERE estado = 'ativo' ORDER BY nomeproduto ASC";
$resultadoProdutos = mysqli_query($db, $sqlProdutos);

// Listar fornecedores para o modal de entrada
$sqlFornecedores = "SELECT idfornecedor, nomeempresa FROM fornecedor WHERE estado = '1' ORDER BY nomeempresa ASC";
$resultadoFornecedores = mysqli_query($db, $sqlFornecedores);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table.dataTable {
            width: 100% !important;
        }
        .highlighted-row {
            background-color: #f7d8db !important;
        }
        .validade-alerta {
            color: #ffbc34;
            font-weight: bold;
        }
        .validade-expirada {
            color: #f62d51;
            font-weight: bold;
        }
        .validade-ok {
            color: #55ce63;
        }
        .card-counter {
            box-shadow: 0px 4px 8px rgb(0 0 0 / 19%);
            padding: 20px 10px;
            background-color: #fff;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .card-counter .count-numbers {
            font-size: 32px;
            display: block;
        }
        .card-counter .count-name {
            font-style: italic;
        }
        .card-counter .float-left {
            font-size: 40px;
            margin-right: 20px;
        }
        .card-counter.primary {
            background-color: #007bff;
            color: #FFF;
        }
        .card-counter.success {
            background-color: #28a745;
            color: #FFF;
        }
        .card-counter.danger {
            background-color: #dc3545;
            color: #FFF;
        }
        /* Estilo para as abas */
        .tab-content {
            width: 100%;
        }
        .tab-pane {
            width: 100%;
            min-width: 100%;
        }
        /* Garantir que a tabela ocupe o espaço total */
        .tab-pane .table-responsive {
            width: 100%;
            min-width: 100%;
        }
        
        .validade-expirada {
            color: #dc3545;
            font-weight: bold;
        }
        
        .validade-alerta {
            color: #ffc107;
            font-weight: bold;
        }
        
        .validade-ok {
            color: #28a745;
        }
        
        .highlighted-row {
            background-color: #fff3cd !important;
        }
        
        /* Estilos para garantir que as abas funcionem corretamente */
        .tab-content > .tab-pane {
            display: none;
        }
        
        .tab-content > .active {
            display: block;
        }
        
        /* Debug visual para ver o conteúdo das abas */
        .tab-content {
            border: 1px solid #dee2e6;
            border-top: 0;
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <?php include 'includes/header.php'; ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php include 'includes/side_bar.php'; ?>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title">Detalhes do Armazém</h4>
                    </div>
                    <div class="col-sm-5 col-6 text-right m-b-30">
                        <a href="armazens.php" class="btn btn-secondary btn-rounded"><i class="fa fa-arrow-left"></i> Voltar</a>
                        <button class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#modalEntradaStock"><i class="fa fa-plus"></i> Entrada de Stock</button>
                    </div>
                </div>

                <!-- Informações do Armazém -->
                <div class="card-box profile-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-view">
                                <div class="profile-img-wrap">
                                    <div class="profile-img">
                                        <i class="fa fa-warehouse fa-5x text-primary"></i>
                                    </div>
                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="profile-info-left">
                                                <h3 class="user-name m-t-0 mb-0"><?php echo $armazem['nome']; ?></h3>
                                                <span class="badge badge-<?php echo $armazem['estado'] == 'ativo' ? 'success' : 'danger'; ?>"><?php echo ucfirst($armazem['estado']); ?></span>
                                                <div class="staff-id">ID: <?php echo $armazem['id']; ?></div>
                                                <div class="staff-id">Responsável: <?php echo $armazem['responsavel'] ?: 'Não definido'; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="personal-info">
                                                <li>
                                                    <span class="title">Telefone:</span>
                                                    <span class="text"><?php echo $armazem['telefone'] ?: 'Não definido'; ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Endereço:</span>
                                                    <span class="text"><?php echo $armazem['endereco'] ?: 'Não definido'; ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Data de Cadastro:</span>
                                                    <span class="text"><?php echo date('d/m/Y H:i', strtotime($armazem['data_cadastro'])); ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Descrição:</span>
                                                    <span class="text"><?php echo $armazem['descricao'] ?: 'Não definida'; ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card-counter primary">
                            <i class="fa fa-boxes float-left"></i>
                            <span class="count-numbers"><?php echo $totalProdutos; ?></span>
                            <span class="count-name">Total de Produtos</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-counter success">
                            <i class="fa fa-check-circle float-left"></i>
                            <span class="count-numbers"><?php echo $totalProdutosAtivos; ?></span>
                            <span class="count-name">Produtos Ativos</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-counter danger">
                            <i class="fa fa-times-circle float-left"></i>
                            <span class="count-numbers"><?php echo $totalProdutos - $totalProdutosAtivos; ?></span>
                            <span class="count-name">Produtos Inativos/Esgotados</span>
                        </div>
                    </div>
                </div>

                <!-- Abas de Conteúdo -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card-box">
                            <!-- Abas de navegação simplificadas para evitar problemas -->
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" id="btn-tab-produtos" data-toggle="tab" href="#conteudo-produtos">Produtos no Armazém</a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a class="nav-link" id="btn-tab-transferencia" data-toggle="tab" href="#conteudo-transferencia">Transferência de Stock</a>
                                </li> -->
                            </ul>
                            
                            <!-- Conteúdo das abas -->
                            <div class="tab-content mt-3">
                                <!-- Aba de Produtos -->
                                <div class="tab-pane fade show active" id="conteudo-produtos">
                                    <div class="table-responsive">
                                        <table id="tabelaProdutos" class="display nowrap table table-striped table-bordered datatable mb-0" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Produto</th>
                                                    <th>Lote</th>
                                                    <th>Validade</th>
                                                    <th>Quantidade</th>
                                                    <th>Estado</th>
                                                    <th class="text-right">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sqlProdutosArmazem = "SELECT s.*, p.nomeproduto 
                                                                    FROM armazem_stock s
                                                                    INNER JOIN produto p ON s.produto_id = p.idproduto 
                                                                    WHERE s.armazem_id = $armazem_id 
                                                                    ORDER BY s.data_entrada DESC";
                                                
                                                // Adicionar log para diagnóstico
                                                error_log("SQL Produtos Armazem: " . $sqlProdutosArmazem);
                                                
                                                $resultadoProdutosArmazem = mysqli_query($db, $sqlProdutosArmazem);
                                                
                                                if (!$resultadoProdutosArmazem) {
                                                    error_log("Erro na consulta: " . mysqli_error($db));
                                                    echo "<tr><td colspan='7' class='text-center'>Erro ao carregar produtos: " . mysqli_error($db) . "</td></tr>";
                                                } else if (mysqli_num_rows($resultadoProdutosArmazem) > 0) {
                                                    while ($produto = mysqli_fetch_assoc($resultadoProdutosArmazem)) {
                                                        // Verificar status de validade
                                                        $classe_prazo = '';
                                                        $status_prazo = '';
                                                        
                                                        if ($produto['prazo']) {
                                                            $hoje = new DateTime();
                                                            $data_validade = new DateTime($produto['prazo']);
                                                            $diferenca = $hoje->diff($data_validade);
                                                            $dias_restantes = $diferenca->days;
                                                            
                                                            if ($hoje > $data_validade) {
                                                                $classe_prazo = 'validade-expirada';
                                                                $status_prazo = 'VENCIDO';
                                                            } elseif ($dias_restantes <= 30) {
                                                                $classe_prazo = 'validade-alerta';
                                                                $status_prazo = "$dias_restantes dias";
                                                            } else {
                                                                $classe_prazo = 'validade-ok';
                                                                $status_prazo = "$dias_restantes dias";
                                                            }
                                                        }
                                                ?>
                                                <tr class="<?php echo $produto['estado'] == 'inativo' ? 'highlighted-row' : ''; ?>">
                                                    <td><?php echo $produto['id']; ?></td>
                                                    <td><?php echo $produto['nomeproduto']; ?></td>
                                                    <td><?php echo $produto['lote']; ?></td>
                                                    <td class="<?php echo $classe_prazo; ?>">
                                                        <?php echo $produto['prazo'] ? date('d/m/Y', strtotime($produto['prazo'])) : 'N/A'; ?>
                                                        <?php if ($status_prazo) echo '<br><small>' . $status_prazo . '</small>'; ?>
                                                    </td>
                                                    <td><?php echo $produto['quantidade']; ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($produto['data_entrada'])); ?></td>
                                                    <td>
                                                        <?php if ($produto['estado'] == 'ativo') { ?>
                                                            <span class="badge badge-success">Ativo</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger">Inativo</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <?php if ($produto['estado'] == 'ativo') { ?>
                                                                    <a class="dropdown-item btn-transferir" href="#" data-id="<?php echo $produto['id']; ?>" data-produto-id="<?php echo $produto['produto_id']; ?>" data-produto="<?php echo $produto['nomeproduto']; ?>" data-lote="<?php echo $produto['lote']; ?>" data-quantidade="<?php echo $produto['quantidade']; ?>" data-toggle="modal" data-target="#modalTransferirStock"><i class="fa fa-exchange-alt"></i> Transferir</a>
                                                                    <a class="dropdown-item btn-alterar-estado" href="#" data-id="<?php echo $produto['id']; ?>" data-estado="inativo"><i class="fa fa-times-circle"></i> Desativar</a>
                                                                <?php } else { ?>
                                                                    <a class="dropdown-item btn-alterar-estado" href="#" data-id="<?php echo $produto['id']; ?>" data-estado="ativo"><i class="fa fa-check-circle"></i> Ativar</a>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php 
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='7' class='text-center'>Nenhum produto encontrado</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Aba de Transferência de Stock -->
                                <div class="tab-pane fade" id="conteudo-transferencia">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4 class="card-title">Transferência em Massa para Prateleiras</h4>
                                            <p>Selecione os produtos que deseja transferir para as prateleiras de venda.</p>
                                        </div>
                                    </div>
                                    <form id="formTransferirMultiplos">
                                        <input type="hidden" name="armazem_id" value="<?php echo $armazem_id; ?>">
                                        
                                        <div class="table-responsive">
                                            <table id="tabelaTransferencia" class="display nowrap table table-striped table-bordered datatable mb-0" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Selecionar</th>
                                                        <th>Produto</th>
                                                        <th>Lote</th>
                                                        <th>Validade</th>
                                                        <th>Quantidade Disponível</th>
                                                        <th>Quantidade a Transferir</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Uma nova consulta para a segunda tabela para evitar problemas de reutilização
                                                    $sqlStockAtivo = "SELECT s.*, p.nomeproduto 
                                                                      FROM armazem_stock s 
                                                                      INNER JOIN produto p ON s.produto_id = p.idproduto 
                                                                      WHERE s.armazem_id = $armazem_id AND s.estado = 'ativo'
                                                                      ORDER BY s.data_entrada DESC";
                                                    
                                                    $resultadoStockAtivo = mysqli_query($db, $sqlStockAtivo);
                                                    
                                                    if (!$resultadoStockAtivo) {
                                                        error_log("Erro na consulta de stock ativo: " . mysqli_error($db));
                                                        echo "<tr><td colspan='7' class='text-center'>Erro ao carregar produtos: " . mysqli_error($db) . "</td></tr>";
                                                    } else if (mysqli_num_rows($resultadoStockAtivo) > 0) {
                                                        while ($produto = mysqli_fetch_assoc($resultadoStockAtivo)) {
                                                            // Verificar status de validade
                                                            $classe_prazo = '';
                                                            $status_prazo = '';
                                                            
                                                            if ($produto['prazo']) {
                                                                $hoje = new DateTime();
                                                                $data_validade = new DateTime($produto['prazo']);
                                                                $diferenca = $hoje->diff($data_validade);
                                                                $dias_restantes = $diferenca->days;
                                                                
                                                                if ($hoje > $data_validade) {
                                                                    $classe_prazo = 'validade-expirada';
                                                                    $status_prazo = 'VENCIDO';
                                                                } elseif ($dias_restantes <= 30) {
                                                                    $classe_prazo = 'validade-alerta';
                                                                    $status_prazo = "$dias_restantes dias";
                                                                } else {
                                                                    $classe_prazo = 'validade-ok';
                                                                    $status_prazo = "$dias_restantes dias";
                                                                }
                                                            }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $produto['id']; ?></td>
                                                        <td>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input produto-checkbox" id="produto_<?php echo $produto['id']; ?>" name="produtos[]" value="<?php echo $produto['id']; ?>">
                                                                <label class="custom-control-label" for="produto_<?php echo $produto['id']; ?>"></label>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $produto['nomeproduto']; ?></td>
                                                        <td><?php echo $produto['lote']; ?></td>
                                                        <td class="<?php echo $classe_prazo; ?>">
                                                            <?php echo $produto['prazo'] ? date('d/m/Y', strtotime($produto['prazo'])) : 'N/A'; ?>
                                                            <?php if ($status_prazo) echo '<br><small>' . $status_prazo . '</small>'; ?>
                                                        </td>
                                                        <td><?php echo $produto['quantidade']; ?> un.</td>
                                                        <td>
                                                            <input type="number" class="form-control quantidade-transferir" name="quantidades[<?php echo $produto['id']; ?>]" min="1" max="<?php echo $produto['quantidade']; ?>" value="1" disabled>
                                                            <input type="hidden" name="produto_ids[<?php echo $produto['id']; ?>]" value="<?php echo $produto['produto_id']; ?>">
                                                            <input type="hidden" name="lotes[<?php echo $produto['id']; ?>]" value="<?php echo $produto['lote']; ?>">
                                                        </td>
                                                    </tr>
                                                    <?php 
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7' class='text-center'>Nenhum produto disponível para transferência</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="observacao">Observação</label>
                                                    <textarea class="form-control" id="observacao" name="observacao" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary" id="btnTransferirMultiplos" disabled>Transferir Selecionados</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Entrada de Stock -->
    <div class="modal fade" id="modalEntradaStock" tabindex="-1" role="dialog" aria-labelledby="modalEntradaStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEntradaStockLabel">Entrada de Stock no Armazém</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEntradaStock">
                    <div class="modal-body">
                        <input type="hidden" name="armazem_id" value="<?php echo $armazem_id; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="produto_id">Produto <span class="text-danger">*</span></label>
                                    <select class="form-control select" id="produto_id" name="produto_id" required>
                                        <option value="">Selecione o Produto</option>
                                        <?php while ($produto = mysqli_fetch_assoc($resultadoProdutos)) { ?>
                                            <option value="<?php echo $produto['idproduto']; ?>"><?php echo $produto['nomeproduto']; ?> (<?php echo $produto['codigobarra']; ?>)</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantidade">Quantidade <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lote">Lote <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lote" name="lote" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prazo">Prazo de Validade</label>
                                    <input type="date" class="form-control" id="prazo" name="prazo">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preco_custo">Preço de Custo</label>
                                    <input type="number" step="0.01" class="form-control" id="preco_custo" name="preco_custo" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fornecedor">Fornecedor</label>
                                    <select class="form-control select" id="fornecedor" name="fornecedor">
                                        <option value="">Selecione o Fornecedor</option>
                                        <?php while ($fornecedor = mysqli_fetch_assoc($resultadoFornecedores)) { ?>
                                            <option value="<?php echo $fornecedor['idfornecedor']; ?>"><?php echo $fornecedor['nomeempresa']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="observacao_entrada">Observação</label>
                            <textarea class="form-control" id="observacao_entrada" name="observacao" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Entrada</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Transferência de Stock Individual -->
    <div class="modal fade" id="modalTransferirStock" tabindex="-1" role="dialog" aria-labelledby="modalTransferirStockLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTransferirStockLabel">Transferir Stock para Prateleiras</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formTransferirStock">
                    <div class="modal-body">
                        <input type="hidden" name="armazem_id" value="<?php echo $armazem_id; ?>">
                        <input type="hidden" id="stock_id" name="stock_id">
                        <input type="hidden" id="produto_id" name="produto_id">
                        
                        <div class="form-group">
                            <label>Produto</label>
                            <p id="produto_detalhes" class="form-control-static font-weight-bold"></p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantidade_disponivel">Quantidade Disponível</label>
                                    <input type="number" class="form-control" id="quantidade_disponivel" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantidade_transferir">Quantidade a Transferir <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="quantidade_transferir" name="quantidade" min="1" value="1" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="observacao">Observação</label>
                            <textarea class="form-control" id="observacao" name="observacao" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Transferir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Visualizar Produto -->
    <div class="modal fade" id="modalVisualizarProduto" tabindex="-1" role="dialog" aria-labelledby="modalVisualizarProdutoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVisualizarProdutoLabel">Detalhes do Produto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="detalhes-produto">
                        <div class="text-center">
                            <i class="fa fa-spinner fa-pulse fa-3x"></i>
                            <p>Carregando detalhes do produto...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
    
    <script>
        $(document).ready(function() {
            console.log("Inicializando página de detalhes do armazém");
            
            // Inicializar DataTable para produtos no armazém
            var tabelaProdutos = $('#tabelaProdutos').DataTable({
                "language": {
                    "url": "../../js/dataTables.portuguese.json"
                },
                "responsive": true,
                "autoWidth": false,
                "scrollX": true
            });
            
            // Inicializar DataTable para transferência de stock
            var tabelaTransferencia = $('#tabelaTransferencia').DataTable({
                "language": {
                    "url": "../../js/dataTables.portuguese.json"
                },
                "responsive": true,
                "autoWidth": false,
                "scrollX": true,
                "columnDefs": [
                    { "orderable": false, "targets": [1, 6] }
                ]
            });
            
            // Controlar checkboxes na transferência múltipla
            $('.produto-checkbox').change(function() {
                var $row = $(this).closest('tr');
                var $input = $row.find('input[type="number"]');
                
                if ($(this).is(':checked')) {
                    $input.prop('disabled', false);
                } else {
                    $input.prop('disabled', true);
                }
                
                // Habilitar/desabilitar botão de transferência
                if ($('.produto-checkbox:checked').length > 0) {
                    $('#btnTransferirMultiplos').prop('disabled', false);
                } else {
                    $('#btnTransferirMultiplos').prop('disabled', true);
                }
            });
            
            // Evento para quando as abas são mostradas
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                console.log("Aba mostrada: " + $(e.target).attr('href'));
                var targetTab = $(e.target).attr('href');
                
                if (targetTab === '#conteudo-transferencia') {
                    tabelaTransferencia.columns.adjust().draw();
                } else if (targetTab === '#conteudo-produtos') {
                    tabelaProdutos.columns.adjust().draw();
                }
            });
            
            // Modal de transferência de stock
            $('#modalTransferirStock').on('show.bs.modal', function (e) {
                console.log("Abrindo modal de transferência");
                var button = $(e.relatedTarget);
                var id = button.data('id');
                var produto_id = button.data('produto-id');
                var produto = button.data('produto');
                var lote = button.data('lote');
                var quantidade = button.data('quantidade');
                
                console.log("Stock ID:", id);
                console.log("Produto ID:", produto_id);
                console.log("Produto:", produto);
                console.log("Lote:", lote);
                console.log("Quantidade:", quantidade);
                
                // Atualizar o modal com os dados do produto
                $('#stock_id').val(id);
                $('#produto_id').val(produto_id);
                $('#produto_detalhes').text(produto + ' - Lote: ' + lote);
                $('#quantidade_disponivel').val(quantidade);
                $('#quantidade_transferir').attr('max', quantidade);
                $('#quantidade_transferir').val(1);
            });
            
            // Botão de alterar estado (ativar/desativar)
            $(document).on('click', '.btn-alterar-estado', function(e) {
                e.preventDefault();
                
                var id = $(this).data('id');
                var estado = $(this).data('estado');
                
                console.log("Alterando estado: ID=" + id + ", Estado=" + estado);
                
                if(confirm('Tem certeza que deseja ' + (estado === 'ativo' ? 'ativar' : 'desativar') + ' este item?')) {
                    $.ajax({
                        url: 'ajax/armazem_alterar_estado.php',
                        type: 'POST',
                        data: {
                            id: id,
                            estado: estado
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                location.reload();
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Erro na requisição:", error);
                            alert('Erro ao processar a requisição: ' + error);
                        }
                    });
                }
            });
            
            // Formulário de entrada de stock
            $('#formEntradaStock').submit(function(e) {
                e.preventDefault();
                
                console.log("Submetendo formulário de entrada de stock");
                
                $.ajax({
                    url: 'ajax/armazem_entrada_stock.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#modalEntradaStock').modal('hide');
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro na requisição:", error);
                        alert('Erro ao processar a requisição: ' + error);
                    }
                });
            });
            
            // Formulário de transferência individual
            $('#formTransferirStock').submit(function(e) {
                e.preventDefault();
                
                console.log("Submetendo formulário de transferência individual");
                
                var quantidade = parseInt($('#quantidade_transferir').val());
                var disponivel = parseInt($('#quantidade_disponivel').val());
                
                console.log("Quantidade a transferir:", quantidade);
                console.log("Quantidade disponível:", disponivel);
                
                if (quantidade <= 0 || quantidade > disponivel) {
                    alert('Quantidade inválida. Verifique a quantidade disponível.');
                    return;
                }
                
                $.ajax({
                    url: 'ajax/armazem_transferir_stock.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#modalTransferirStock').modal('hide');
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro na requisição:", error);
                        alert('Erro ao processar a requisição: ' + error);
                    }
                });
            });
            
            // Formulário de transferência múltipla
            $('#formTransferirMultiplos').submit(function(e) {
                e.preventDefault();
                
                console.log("Submetendo formulário de transferência múltipla");
                
                // Verificar se algum produto foi selecionado
                if ($('.produto-checkbox:checked').length === 0) {
                    alert('Selecione pelo menos um produto para transferir.');
                    return;
                }
                
                // Validar quantidades
                var valido = true;
                $('.produto-checkbox:checked').each(function() {
                    var id = $(this).val();
                    var $input = $('input[name="quantidades[' + id + ']"]');
                    var quantidade = parseInt($input.val());
                    var max = parseInt($input.attr('max'));
                    
                    console.log("Validando produto ID:", id);
                    console.log("Quantidade:", quantidade);
                    console.log("Máximo:", max);
                    
                    if (quantidade <= 0 || quantidade > max) {
                        alert('Quantidade inválida para um ou mais produtos selecionados.');
                        valido = false;
                        return false;
                    }
                });
                
                if (!valido) return;
                
                $.ajax({
                    url: 'ajax/armazem_transferir_multiplos.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro na requisição:", error);
                        alert('Erro ao processar a requisição: ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>
