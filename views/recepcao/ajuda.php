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
                        <a href="dashboard.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-4"><i class="fa fa-question-circle icone-ajuda"></i> Como usar o sistema de recepção</h3>
                                
                                <div class="ajuda-item">
                                    <h4><i class="fa fa-home icone-ajuda"></i> Página Inicial</h4>
                                    <p>A página inicial mostra um resumo das informações importantes do dia:</p>
                                    <ul>
                                        <li><strong>Pacientes Hoje:</strong> Quantos pacientes foram cadastrados hoje</li>
                                        <li><strong>Faturas Hoje:</strong> Quantas faturas foram criadas hoje</li>
                                        <li><strong>Faturas Pendentes:</strong> Faturas que ainda não foram pagas</li>
                                        <li><strong>Faturas Pagas Hoje:</strong> Faturas que foram pagas hoje</li>
                                        <li><strong>Total Recebido Hoje:</strong> Quanto dinheiro foi recebido hoje</li>
                                        <li><strong>Total de Pacientes:</strong> Quantos pacientes estão cadastrados no sistema</li>
                                    </ul>
                                    <div class="destaque">
                                        <strong>💡 Dica:</strong> Use esta página para ter uma visão rápida do que aconteceu no dia.
                                    </div>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-user icone-ajuda"></i> Pacientes</h4>
                                    <p><strong>Ver Pacientes:</strong> Mostra uma lista de todos os pacientes cadastrados. Você pode:</p>
                                    <ul>
                                        <li>Pesquisar por nome, número de processo ou documento</li>
                                        <li>Ver informações de cada paciente</li>
                                        <li>Editar dados de um paciente</li>
                                        <li>Ver o histórico de atendimentos de um paciente</li>
                                    </ul>
                                    <p><strong>Cadastrar Paciente:</strong> Use esta opção para cadastrar um novo paciente. Preencha:</p>
                                    <ul>
                                        <li><strong>Nome e Apelido:</strong> Obrigatórios - apenas letras</li>
                                        <li><strong>Contacto:</strong> Obrigatório - formato: +258 84 000 0000 (9 dígitos)</li>
                                        <li><strong>Data de Nascimento:</strong> Opcional</li>
                                        <li><strong>Documento:</strong> Se escolher o tipo (BI, Passaporte), deve preencher o número</li>
                                        <li>Outros campos são opcionais</li>
                                    </ul>
                                    <div class="destaque">
                                        <strong>⚠️ Importante:</strong> O número de processo é gerado automaticamente. Não precisa preencher!
                                    </div>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-file-text icone-ajuda"></i> Faturas</h4>
                                    <p><strong>Ver Faturas:</strong> Mostra todas as faturas criadas. Você pode:</p>
                                    <ul>
                                        <li>Ver detalhes de cada fatura</li>
                                        <li>Pagar uma fatura pendente</li>
                                        <li>Cancelar uma fatura (se ainda não foi paga)</li>
                                        <li>Imprimir recibo de uma fatura paga</li>
                                    </ul>
                                    <p><strong>Criar Fatura:</strong> Para criar uma nova fatura:</p>
                                    <ul>
                                        <li>Selecione o paciente (ou cadastre um novo)</li>
                                        <li>Escolha a data do atendimento</li>
                                        <li>Adicione os serviços prestados clicando neles</li>
                                        <li>Se necessário, aplique um desconto</li>
                                        <li>Revise o total e clique em "Criar Fatura"</li>
                                    </ul>
                                    <div class="destaque">
                                        <strong>💡 Dica:</strong> Você pode adicionar vários serviços na mesma fatura. Cada serviço aparece na lista quando você clica nele.
                                    </div>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-money icone-ajuda"></i> Caixa do Dia</h4>
                                    <p>Esta tela mostra o resumo financeiro do dia:</p>
                                    <ul>
                                        <li><strong>Total de Faturas:</strong> Quantas faturas foram pagas hoje</li>
                                        <li><strong>Total Recebido:</strong> Quanto dinheiro foi recebido no total</li>
                                        <li><strong>Por Método de Pagamento:</strong> Quanto foi recebido em dinheiro, M-Pesa, Emola ou POS</li>
                                        <li><strong>Lista de Pagamentos:</strong> Detalhes de cada pagamento feito hoje</li>
                                    </ul>
                                    <div class="destaque">
                                        <strong>📊 Importante:</strong> Use esta tela para fazer o fechamento do caixa no final do dia.
                                    </div>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-question-circle icone-ajuda"></i> Dúvidas Frequentes</h4>
                                    <p><strong>Como cadastrar um paciente rapidamente?</strong></p>
                                    <p>Preencha apenas Nome, Apelido e Contacto. Os outros campos são opcionais e podem ser preenchidos depois.</p>
                                    
                                    <p><strong>O que fazer se o contacto não estiver no formato correto?</strong></p>
                                    <p>O sistema formata automaticamente. Digite apenas os 9 dígitos (ex: 840000000) e o sistema adiciona o +258 automaticamente.</p>
                                    
                                    <p><strong>Como cancelar uma fatura?</strong></p>
                                    <p>Na lista de faturas, clique no botão "Cancelar" (vermelho) ao lado da fatura pendente. Faturas pagas não podem ser canceladas.</p>
                                    
                                    <p><strong>Como imprimir um recibo?</strong></p>
                                    <p>Na lista de faturas, clique no botão "Recibo" (azul) ao lado da fatura paga. O recibo será aberto em uma nova janela para impressão.</p>
                                </div>

                                <div class="ajuda-item">
                                    <h4><i class="fa fa-info-circle icone-ajuda"></i> Informações Importantes</h4>
                                    <ul>
                                        <li>O sistema salva automaticamente todas as informações</li>
                                        <li>Você pode editar dados de pacientes a qualquer momento</li>
                                        <li>Faturas pagas não podem ser alteradas ou canceladas</li>
                                        <li>O número de processo do paciente é único e gerado automaticamente</li>
                                        <li>Use a pesquisa para encontrar pacientes ou faturas rapidamente</li>
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

