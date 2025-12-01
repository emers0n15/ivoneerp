<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

// Verificar se o usuário tem permissão de recepção
if($_SESSION['categoriaUsuario'] != "recepcao"){
	header("location:../admin/");
	exit;
}

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

// Determinar tipo de documento
$tipo_doc = isset($_GET['tipo']) ? $_GET['tipo'] : 'fatura';
$titulo = $tipo_doc == 'vds' ? 'Criar VDS (Venda a Dinheiro/Serviço)' : ($tipo_doc == 'cotacao' ? 'Criar Cotação' : 'Criar Nova Fatura');

// Buscar serviços disponíveis
$sql_servicos = "SELECT * FROM servicos_clinica WHERE ativo = 1 ORDER BY categoria, nome";
$rs_servicos = mysqli_query($db, $sql_servicos);
$tem_servicos = false;
$servicos_html = '';
if($rs_servicos && mysqli_num_rows($rs_servicos) > 0) {
    $tem_servicos = true;
    $categoria_atual = '';
    while ($servico = mysqli_fetch_array($rs_servicos)) {
        if ($categoria_atual != $servico['categoria']) {
            if ($categoria_atual != '') {
                $servicos_html .= '</div>';
            }
            $servicos_html .= '<h6>' . htmlspecialchars($servico['categoria']) . '</h6><div class="mb-3">';
            $categoria_atual = $servico['categoria'];
        }
        $servicos_html .= '<div class="servico-item" data-id="' . $servico['id'] . '" data-nome="' . htmlspecialchars($servico['nome']) . '" data-preco="' . $servico['preco'] . '">';
        $servicos_html .= '<strong>' . htmlspecialchars($servico['nome']) . '</strong> - ';
        $servicos_html .= '<span class="text-primary">' . number_format($servico['preco'], 2, ',', '.') . ' MT</span>';
        $servicos_html .= '</div>';
    }
    if ($categoria_atual != '') {
        $servicos_html .= '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/recepcao-custom.css">
    <style>
        .servico-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .servico-item:hover {
            background-color: #f5f5f5;
        }
        .servico-item.selected {
            background-color: #e3f2fd;
            border-color: #2196F3;
        }
        #servicosSelecionados {
            max-height: 400px;
            overflow-y: auto;
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
                        <h4 class="page-title"><?php echo $titulo; ?></h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="faturas.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="daos/criar_fatura.php" id="formFatura">
                            <input type="hidden" name="tipo_documento" value="<?php echo $tipo_doc; ?>">
                            
                            <!-- Wizard Steps -->
                            <div class="wizard-container">
                                <div class="wizard-steps">
                                    <div class="wizard-step active" data-step="0">
                                        <div class="wizard-step-number">1</div>
                                        <div class="wizard-step-title">Dados da Fatura</div>
                                    </div>
                                    <div class="wizard-step" data-step="1">
                                        <div class="wizard-step-number">2</div>
                                        <div class="wizard-step-title">Serviços</div>
                                    </div>
                                    <div class="wizard-step" data-step="2">
                                        <div class="wizard-step-number">3</div>
                                        <div class="wizard-step-title">Resumo</div>
                                    </div>
                                </div>
                                
                                <div class="wizard-progress">
                                    <div class="wizard-progress-bar" id="wizardProgress" style="width: 33.33%"></div>
                                </div>
                                
                                <!-- Etapa 1: Dados da Fatura -->
                                <div class="wizard-content">
                                    <div class="wizard-pane active" data-pane="0">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Pesquisar Paciente <span class="text-danger">*</span></label>
                                                            <input type="text" id="pesquisaPaciente" class="form-control" placeholder="Digite nome, apelido, número de processo ou contacto..." required>
                                                            <input type="hidden" name="paciente_id" id="paciente_id" required>
                                                            <div id="resultadoPesquisa" style="display:none; position:absolute; z-index:1000; background:white; border:1px solid #ddd; max-height:200px; overflow-y:auto; width:100%; margin-top:5px; border-radius:5px; box-shadow:0 2px 8px rgba(0,0,0,0.1);"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Data do Atendimento <span class="text-danger">*</span></label>
                                                            <input type="date" name="data_atendimento" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Hora do Atendimento</label>
                                                            <input type="time" name="hora_atendimento" class="form-control" value="<?php echo date('H:i'); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="infoPaciente" style="display:none; padding:15px; background:#f5f5f5; border-radius:8px; margin-top:10px; border-left:4px solid #3D5DFF;">
                                                            <strong>Paciente Selecionado:</strong> <span id="nomePaciente"></span><br>
                                                            <small>Nº Processo: <span id="numeroProcesso"></span> | Contacto: <span id="contactoPaciente"></span></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Etapa 2: Serviços -->
                                    <div class="wizard-pane" data-pane="1">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5>Serviços Disponíveis</h5>
                                                        <div id="listaServicos" style="max-height:400px; overflow-y:auto;">
                                                            <?php if($tem_servicos): ?>
                                                                <?php echo $servicos_html; ?>
                                                            <?php else: ?>
                                                                <div class="alert alert-warning">
                                                                    <strong>Atenção!</strong> Nenhum serviço cadastrado no sistema. Entre em contato com o administrador para configurar os serviços disponíveis.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5>Serviços Selecionados</h5>
                                                        <div id="servicosSelecionados">
                                                            <p class="text-muted">Nenhum serviço selecionado</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <strong>Subtotal: <span id="subtotal">0,00</span> MT</strong><br>
                                                            <label>Desconto: <input type="number" name="desconto" id="desconto" class="form-control" value="0.00" step="0.01" min="0" style="display:inline-block; width:150px;"> MT</label><br>
                                                            <strong>Total: <span id="total">0,00</span> MT</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Etapa 3: Resumo -->
                                    <div class="wizard-pane" data-pane="2">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5>Resumo da Fatura</h5>
                                                <div id="resumoFatura" class="mb-3"></div>
                                                <div class="form-group">
                                                    <label>Observações</label>
                                                    <textarea name="observacoes" class="form-control" rows="3" placeholder="Observações adicionais sobre a fatura..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Botões de Navegação -->
                                <div class="wizard-actions">
                                    <div>
                                        <button type="button" class="btn wizard-btn wizard-btn-prev" id="btnPrev" style="display:none;" onclick="wizardPrev()">
                                            <i class="fa fa-arrow-left"></i> Voltar
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn wizard-btn wizard-btn-next" id="btnNext" onclick="wizardNext()">
                                            Prosseguir <i class="fa fa-arrow-right"></i>
                                        </button>
                                        <button type="submit" class="btn wizard-btn wizard-btn-submit" id="btnSubmit" style="display:none;" name="btn">
                                            <i class="fa fa-check"></i> Criar Fatura
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
    <script>
        var servicosSelecionados = [];
        var pacienteSelecionado = null;
        var currentStep = 0;
        var totalSteps = 3;

        // Inicializar wizard
        function updateWizard() {
            // Atualizar steps
            $('.wizard-step').each(function(index) {
                $(this).removeClass('active completed');
                if (index < currentStep) {
                    $(this).addClass('completed');
                } else if (index === currentStep) {
                    $(this).addClass('active');
                }
            });
            
            // Atualizar panes
            $('.wizard-pane').removeClass('active');
            $('.wizard-pane[data-pane="' + currentStep + '"]').addClass('active');
            
            // Atualizar progresso
            var progress = ((currentStep + 1) / totalSteps) * 100;
            $('#wizardProgress').css('width', progress + '%');
            
            // Atualizar botões
            if (currentStep === 0) {
                $('#btnPrev').hide();
            } else {
                $('#btnPrev').show();
            }
            
            if (currentStep === totalSteps - 1) {
                $('#btnNext').hide();
                $('#btnSubmit').show();
                atualizarResumo();
            } else {
                $('#btnNext').show();
                $('#btnSubmit').hide();
            }
        }

        function wizardNext() {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps - 1) {
                    currentStep++;
                    updateWizard();
                    $('html, body').animate({scrollTop: $('.wizard-container').offset().top - 100}, 300);
                }
            }
        }

        function wizardPrev() {
            if (currentStep > 0) {
                currentStep--;
                updateWizard();
                $('html, body').animate({scrollTop: $('.wizard-container').offset().top - 100}, 300);
            }
        }

        function validateStep(step) {
            if (step === 0) {
                if (!$('#paciente_id').val()) {
                    alert('Por favor, selecione um paciente antes de prosseguir.');
                    return false;
                }
                if (!$('input[name="data_atendimento"]').val()) {
                    alert('Por favor, selecione a data do atendimento.');
                    return false;
                }
            } else if (step === 1) {
                if (servicosSelecionados.length === 0) {
                    alert('Por favor, selecione pelo menos um serviço antes de prosseguir.');
                    return false;
                }
            }
            return true;
        }

        function atualizarResumo() {
            var html = '<div class="table-responsive"><table class="table table-bordered">';
            html += '<tr><th>Paciente:</th><td>' + (pacienteSelecionado ? pacienteSelecionado.nome : 'Não selecionado') + '</td></tr>';
            html += '<tr><th>Nº Processo:</th><td>' + (pacienteSelecionado ? pacienteSelecionado.numero : '-') + '</td></tr>';
            html += '<tr><th>Data:</th><td>' + $('input[name="data_atendimento"]').val() + '</td></tr>';
            html += '<tr><th>Hora:</th><td>' + ($('input[name="hora_atendimento"]').val() || '-') + '</td></tr>';
            html += '<tr><th>Serviços:</th><td><ul>';
            servicosSelecionados.forEach(function(s) {
                html += '<li>' + s.nome + ' (Qtd: ' + s.quantidade + ' x ' + number_format(s.preco, 2, ',', '.') + ' MT)</li>';
            });
            html += '</ul></td></tr>';
            var subtotal = 0;
            servicosSelecionados.forEach(function(s) {
                subtotal += s.preco * s.quantidade;
            });
            var desconto = parseFloat($('#desconto').val()) || 0;
            var total = subtotal - desconto;
            html += '<tr><th>Subtotal:</th><td>' + number_format(subtotal, 2, ',', '.') + ' MT</td></tr>';
            html += '<tr><th>Desconto:</th><td>' + number_format(desconto, 2, ',', '.') + ' MT</td></tr>';
            html += '<tr><th><strong>Total:</strong></th><td><strong>' + number_format(total, 2, ',', '.') + ' MT</strong></td></tr>';
            html += '</table></div>';
            $('#resumoFatura').html(html);
        }

        // Pesquisa de paciente
        $('#pesquisaPaciente').on('keyup', function() {
            var termo = $(this).val();
            if (termo.length >= 2) {
                $.ajax({
                    url: 'daos/pesquisar_paciente.php',
                    type: 'POST',
                    data: {termo: termo},
                    success: function(data) {
                        var pacientes = JSON.parse(data);
                        var html = '';
                        if (pacientes.length > 0) {
                            pacientes.forEach(function(p) {
                                var empresa_id = p.empresa_id || 'null';
                                var empresa_nome = p.empresa_nome || '';
                                var empresa_info = empresa_nome ? ' | <span class="text-primary">Empresa: ' + empresa_nome + '</span>' : '';
                                html += '<div class="p-2 border-bottom" style="cursor:pointer;" onclick="selecionarPaciente(' + 
                                       p.id + ', \'' + (p.nome + ' ' + p.apelido).replace(/'/g, "\\'") + '\', \'' + p.numero_processo + '\', \'' + (p.contacto || '') + '\', ' + 
                                       empresa_id + ', \'' + empresa_nome.replace(/'/g, "\\'") + '\')">';
                                html += '<strong>' + p.nome + ' ' + p.apelido + '</strong><br>';
                                html += '<small>Nº: ' + p.numero_processo + ' | Contacto: ' + (p.contacto || '-') + empresa_info + '</small>';
                                html += '</div>';
                            });
                            $('#resultadoPesquisa').html(html).show();
                        } else {
                            $('#resultadoPesquisa').html('<div class="p-2">Nenhum paciente encontrado</div>').show();
                        }
                    }
                });
            } else {
                $('#resultadoPesquisa').hide();
            }
        });

        function selecionarPaciente(id, nome, numero, contacto, empresa_id, empresa_nome) {
            pacienteSelecionado = {id: id, nome: nome, numero: numero, contacto: contacto, empresa_id: empresa_id};
            $('#paciente_id').val(id);
            $('#nomePaciente').text(nome);
            $('#numeroProcesso').text(numero);
            $('#contactoPaciente').text(contacto);
            if(empresa_nome) {
                $('#infoPaciente').html('<strong>Paciente Selecionado:</strong> ' + nome + '<br>' +
                    '<small>Nº Processo: ' + numero + ' | Contacto: ' + contacto + '</small><br>' +
                    '<small class="text-primary"><i class="fa fa-building"></i> Empresa: ' + empresa_nome + '</small>');
            } else {
                $('#infoPaciente').html('<strong>Paciente Selecionado:</strong> ' + nome + '<br>' +
                    '<small>Nº Processo: ' + numero + ' | Contacto: ' + contacto + '</small>');
            }
            $('#infoPaciente').show();
            $('#resultadoPesquisa').hide();
            $('#pesquisaPaciente').val(nome);
            
            if(empresa_id && empresa_id !== 'null') {
                buscarPrecosContratados(empresa_id);
            } else {
                $('.servico-item').each(function() {
                    var preco_padrao = $(this).data('preco');
                    $(this).data('preco', preco_padrao);
                    $(this).find('.text-primary').text(number_format(preco_padrao, 2, ',', '.') + ' MT');
                });
            }
        }
        
        function buscarPrecosContratados(empresa_id) {
            $.ajax({
                url: 'daos/buscar_precos_empresa.php',
                type: 'POST',
                data: {empresa_id: empresa_id},
                dataType: 'json',
                success: function(precos) {
                    $('.servico-item').each(function() {
                        var servico_id = $(this).data('id');
                        if(precos[servico_id]) {
                            var preco_contratado = precos[servico_id].preco;
                            if(precos[servico_id].desconto > 0) {
                                preco_contratado = preco_contratado * (1 - precos[servico_id].desconto / 100);
                            }
                            $(this).data('preco', preco_contratado);
                            $(this).find('.text-primary').html('<span class="text-success">' + number_format(preco_contratado, 2, ',', '.') + ' MT</span> <small class="text-muted">(contratado)</small>');
                        }
                    });
                }
            });
        }

        // Seleção de serviços
        $(document).on('click', '.servico-item', function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');
            var preco = parseFloat($(this).data('preco'));
            
            var index = servicosSelecionados.findIndex(s => s.id == id);
            if (index === -1) {
                servicosSelecionados.push({id: id, nome: nome, preco: preco, quantidade: 1});
                $(this).addClass('selected');
            } else {
                servicosSelecionados[index].quantidade++;
            }
            atualizarServicosSelecionados();
        });

        function atualizarServicosSelecionados() {
            var html = '';
            var subtotal = 0;
            
            servicosSelecionados.forEach(function(s, index) {
                var totalItem = s.preco * s.quantidade;
                subtotal += totalItem;
                html += '<div class="p-2 border mb-2">';
                html += '<strong>' + s.nome + '</strong> ';
                html += '<span class="float-right">';
                html += '<input type="number" class="form-control form-control-sm" style="width:60px; display:inline;" value="' + s.quantidade + '" min="1" onchange="alterarQuantidade(' + index + ', this.value)"> x ';
                html += number_format(s.preco, 2, ',', '.') + ' = ';
                html += '<strong>' + number_format(totalItem, 2, ',', '.') + ' MT</strong> ';
                html += '<button type="button" class="btn btn-sm btn-danger" onclick="removerServico(' + index + ')"><i class="fa fa-times"></i></button>';
                html += '</span>';
                html += '<input type="hidden" name="servicos[' + index + '][id]" value="' + s.id + '">';
                html += '<input type="hidden" name="servicos[' + index + '][quantidade]" value="' + s.quantidade + '">';
                html += '</div>';
            });
            
            if (servicosSelecionados.length === 0) {
                html = '<p class="text-muted">Nenhum serviço selecionado</p>';
            }
            
            $('#servicosSelecionados').html(html);
            atualizarTotal();
        }

        function alterarQuantidade(index, quantidade) {
            servicosSelecionados[index].quantidade = parseInt(quantidade);
            atualizarServicosSelecionados();
        }

        function removerServico(index) {
            var id = servicosSelecionados[index].id;
            servicosSelecionados.splice(index, 1);
            $('.servico-item[data-id="' + id + '"]').removeClass('selected');
            atualizarServicosSelecionados();
        }

        function atualizarTotal() {
            var subtotal = 0;
            servicosSelecionados.forEach(function(s) {
                subtotal += s.preco * s.quantidade;
            });
            
            var desconto = parseFloat($('#desconto').val()) || 0;
            var total = subtotal - desconto;
            
            $('#subtotal').text(number_format(subtotal, 2, ',', '.'));
            $('#total').text(number_format(total, 2, ',', '.'));
        }

        $('#desconto').on('change', atualizarTotal);

        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number;
            var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
            var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
            var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
            var s = '';
            var toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        // Validação do formulário
        $('#formFatura').on('submit', function(e) {
            if (!pacienteSelecionado) {
                e.preventDefault();
                alert('Por favor, selecione um paciente!');
                currentStep = 0;
                updateWizard();
                return false;
            }
            if (servicosSelecionados.length === 0) {
                e.preventDefault();
                alert('Por favor, selecione pelo menos um serviço!');
                currentStep = 1;
                updateWizard();
                return false;
            }
            atualizarServicosSelecionados();
        });

        // Fechar pesquisa ao clicar fora
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#pesquisaPaciente, #resultadoPesquisa').length) {
                $('#resultadoPesquisa').hide();
            }
        });
    </script>
</body>
</html>
