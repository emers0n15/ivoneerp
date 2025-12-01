<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .ajuda-item {
            margin-bottom: 30px;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .ajuda-item h4 {
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .ajuda-item p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 10px;
        }
        .ajuda-item ul {
            margin-left: 20px;
            margin-top: 10px;
        }
        .ajuda-item li {
            margin-bottom: 8px;
            color: #555;
        }
        .icone-ajuda {
            font-size: 24px;
            color: #007bff;
            margin-right: 10px;
        }
        .destaque {
            background-color: #e7f3ff;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 15px 0;
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
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Ajuda e Orientações</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="index.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-4"><i class="fa fa-question-circle icone-ajuda"></i> Como usar o sistema</h3>
                                
                                <div class="ajuda-item">
                                    <h4><i class="fa fa-home icone-ajuda"></i> Página Inicial</h4>
                                    <p>A página inicial mostra um resumo geral do sistema com informações importantes e estatísticas.</p>
                                    <div class="destaque">
                                        <strong>💡 Dica:</strong> Use esta página para ter uma visão geral do sistema.
                                    </div>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-coffee icone-ajuda"></i> Artigos</h4>
                                    <p>Gerencie os produtos/artigos do sistema:</p>
                                    <ul>
                                        <li><strong>Artigos:</strong> Lista e gerencia todos os produtos</li>
                                        <li><strong>Grupo de Artigos:</strong> Organize artigos em grupos</li>
                                        <li><strong>Família de Artigos:</strong> Crie categorias maiores para organização</li>
                                    </ul>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-user icone-ajuda"></i> Entidades</h4>
                                    <p>Gerencie clientes e fornecedores:</p>
                                    <ul>
                                        <li><strong>Clientes:</strong> Cadastre e gerencie informações dos clientes</li>
                                        <li><strong>Fornecedores:</strong> Cadastre e gerencie informações dos fornecedores</li>
                                    </ul>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-file icone-ajuda"></i> Documentos</h4>
                                    <p>Crie e gerencie diferentes tipos de documentos:</p>
                                    <ul>
                                        <li><strong>VD - Venda a Dinheiro:</strong> Vendas pagas imediatamente</li>
                                        <li><strong>FA - Factura:</strong> Faturas para clientes</li>
                                        <li><strong>CT - Cotação:</strong> Cotações de preços</li>
                                        <li><strong>ES - Entrada de Stock:</strong> Registro de entrada de produtos</li>
                                        <li><strong>SS - Saída de Stock:</strong> Registro de saída de produtos</li>
                                        <li><strong>RI - Requisição Interna:</strong> Requisições dentro da empresa</li>
                                        <li><strong>RE - Requisição Externa:</strong> Requisições para fornecedores</li>
                                    </ul>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-database icone-ajuda"></i> Gestão de Stock</h4>
                                    <p>Controle o estoque de produtos:</p>
                                    <ul>
                                        <li><strong>Entrada de Stock:</strong> Registre quando produtos entram no estoque</li>
                                        <li><strong>Saída de Stock:</strong> Registre quando produtos saem do estoque</li>
                                        <li><strong>Inventário:</strong> Faça contagem física do estoque</li>
                                        <li><strong>Periodicidade:</strong> Configure periodicidade de reposição</li>
                                        <li><strong>Consumo Médio:</strong> Veja o consumo médio de produtos</li>
                                    </ul>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-money icone-ajuda"></i> Fluxo de Caixa</h4>
                                    <p>Controle financeiro do sistema:</p>
                                    <ul>
                                        <li><strong>Caixa:</strong> Visualize movimentações do caixa</li>
                                        <li><strong>Balanço Diário:</strong> Resumo financeiro do dia</li>
                                        <li><strong>Entrada de Caixa:</strong> Registre entradas de dinheiro</li>
                                        <li><strong>Retirada de Caixa:</strong> Registre saídas de dinheiro</li>
                                    </ul>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-file icone-ajuda"></i> Explorador de Docs</h4>
                                    <p>Visualize e gerencie todos os documentos criados no sistema.</p>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-newspaper-o icone-ajuda"></i> Contas Correntes</h4>
                                    <p>Gerencie contas a pagar e receber:</p>
                                    <ul>
                                        <li><strong>Contas a Pagar:</strong> Contas que a empresa deve pagar</li>
                                        <li><strong>Contas a Receber:</strong> Contas que a empresa deve receber</li>
                                        <li><strong>Extrato de Clientes:</strong> Histórico financeiro dos clientes</li>
                                        <li><strong>Extrato de Contas:</strong> Histórico de movimentações</li>
                                        <li><strong>Balancete:</strong> Relatório contábil</li>
                                    </ul>
                                </div>

                                <div class="destaque" style="margin-top: 30px;">
                                    <h5><i class="fa fa-phone"></i> Precisa de mais ajuda?</h5>
                                    <p>Se tiver dúvidas ou encontrar algum problema, entre em contato com o suporte técnico.</p>
                                </div>
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

