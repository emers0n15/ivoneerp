<?php
// Página para visualizar logs de verificação de estoque
session_start();
include_once '../../conexao/index.php';
include '../../dao/perm.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log de Verificação de Estoque</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        pre {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            white-space: pre-wrap;
            font-size: 14px;
            max-height: 500px;
            overflow-y: auto;
        }
        .criterio-sim {
            color: #dc3545;
            font-weight: bold;
        }
        .small-text {
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h2>Log de Verificação de Estoque e Consumo Médio</h2>
        
        <div class="row mb-3">
            <div class="col-md-12">
                <a href="javascript:void(0)" onclick="verificarAgora()" class="btn btn-primary">Verificar Agora</a>
                <a href="consumo_artigos_baixo.php" class="btn btn-success ml-2">Ver Resumo Dashboard</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Logs de Verificação de Estoque</h5>
                        <p class="small-text mb-0">Esta página mostra detalhes técnicos sobre a verificação de estoque e consumo médio. 
                           Útil para diagnóstico e ajustes de parâmetros.</p>
                    </div>
                    <div class="card-body">
                        <div id="logArea">
                            <div class="alert alert-info">
                                Clique em "Verificar Agora" para executar a verificação de estoque e visualizar o log.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function verificarAgora() {
            $('#logArea').html('<div class="alert alert-info">Executando verificação de estoque... aguarde.</div>');
            
            $.ajax({
                url: 'daos/atualizarStockConsumoMedio.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log("Resposta recebida:", response);
                    formatarLog(response);
                },
                error: function(xhr, status, error) {
                    $('#logArea').html('<div class="alert alert-danger">Erro ao executar verificação: ' + error + '</div>');
                    console.error("Erro AJAX:", xhr, status, error);
                }
            });
        }
        
        function formatarLog(response) {
            let html = '';
            
            if (response.status === 'sucesso') {
                html += '<div class="alert alert-success">Verificação concluída. ';
                html += 'Encontrados <strong>' + response.quantidade + '</strong> produtos com estoque baixo.</div>';
                
                // Mostrar produtos com estoque baixo
                if (response.produtos && response.produtos.length > 0) {
                    html += '<h5>Produtos com Estoque Baixo</h5>';
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-striped table-sm">';
                    html += '<thead><tr>';
                    html += '<th>ID</th>';
                    html += '<th>Produto</th>';
                    html += '<th>Estoque</th>';
                    html += '<th>Consumo/Mês</th>';
                    html += '<th>% do Consumo</th>';
                    html += '<th>Tipo Alerta</th>';
                    html += '</tr></thead><tbody>';
                    
                    response.produtos.forEach(function(prod) {
                        // Determinar classe para linha baseado no tipo de alerta
                        let rowClass = '';
                        if (prod.tipo_alerta === 'absoluto') rowClass = 'table-danger';
                        else if (prod.tipo_alerta === 'percentual') rowClass = 'table-warning';
                        
                        html += '<tr class="' + rowClass + '">';
                        html += '<td>' + prod.id + '</td>';
                        html += '<td>' + prod.nome + '</td>';
                        html += '<td>' + prod.stock_atual + '</td>';
                        html += '<td>' + prod.consumo_medio.toFixed(2) + '</td>';
                        html += '<td>' + prod.percentual.toFixed(2) + '%</td>';
                        html += '<td>' + (prod.tipo_alerta === 'absoluto' ? 'CRÍTICO' : 
                                          prod.tipo_alerta === 'percentual' ? 'PERCENTUAL BAIXO' : 'ABAIXO DO CONSUMO') + '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                }
                
                // Mostrar log detalhado
                html += '<h5 class="mt-4">Log Detalhado</h5>';
                html += '<pre class="small-text">';
                
                if (response.log && response.log.length > 0) {
                    response.log.forEach(function(line) {
                        // Destacar critérios relevantes
                        let formattedLine = line.replace(/SIM/g, '<span class="criterio-sim">SIM</span>');
                        html += formattedLine + '\n';
                    });
                }
                
                html += '</pre>';
            } else {
                html += '<div class="alert alert-danger">Erro: ' + (response.mensagem || 'Erro desconhecido') + '</div>';
                
                if (response.log && response.log.length > 0) {
                    html += '<pre class="small-text">';
                    response.log.forEach(function(line) {
                        html += line + '\n';
                    });
                    html += '</pre>';
                }
            }
            
            $('#logArea').html(html);
        }
    </script>
</body>
</html>
