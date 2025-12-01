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
                        <h4 class="page-title">Novo Paciente</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="pacientes.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="POST" action="daos/registar_paciente.php" id="formPaciente" onsubmit="return validarFormulario(event)">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Número de Processo</label>
                                        <input class="form-control" type="text" id="numero_processo_display" readonly style="background-color: #f5f5f5;">
                                        <small class="form-text text-muted">Será gerado automaticamente</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Data de Nascimento</label>
                                        <input class="form-control" type="date" name="data_nascimento" id="data_nascimento" max="<?php echo date('Y-m-d'); ?>">
                                        <small class="form-text text-danger" id="erro_data_nascimento" style="display:none;"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nome <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="nome" id="nome" required maxlength="255" pattern="[A-Za-zÀ-ÿ\s]+" title="Apenas letras e espaços">
                                        <small class="form-text text-danger" id="erro_nome" style="display:none;"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Apelido <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="apelido" id="apelido" required maxlength="255" pattern="[A-Za-zÀ-ÿ\s]+" title="Apenas letras e espaços">
                                        <small class="form-text text-danger" id="erro_apelido" style="display:none;"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Sexo</label>
                                        <select class="form-control" name="sexo">
                                            <option value="">Selecione</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Feminino</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tipo de Documento</label>
                                        <select class="form-control" name="documento_tipo">
                                            <option value="">Selecione</option>
                                            <option value="BI">BI</option>
                                            <option value="Passaporte">Passaporte</option>
                                            <option value="Carta de Condução">Carta de Condução</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Número do Documento</label>
                                        <input class="form-control" type="text" name="documento_numero" id="documento_numero" maxlength="100">
                                        <small class="form-text text-danger" id="erro_documento_numero" style="display:none;"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Contacto <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">+258</span>
                                            </div>
                                            <input class="form-control" type="text" name="contacto" id="contacto" required placeholder="84 000 0000" maxlength="13" pattern="[0-9\s]+">
                                        </div>
                                        <small class="form-text text-muted">Formato: +258 84 000 0000 (9 dígitos após +258)</small>
                                        <small class="form-text text-danger" id="erro_contacto" style="display:none;"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Contacto Alternativo</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">+258</span>
                                            </div>
                                            <input class="form-control" type="text" name="contacto_alternativo" id="contacto_alternativo" placeholder="84 000 0000" maxlength="13" pattern="[0-9\s]+">
                                        </div>
                                        <small class="form-text text-danger" id="erro_contacto_alternativo" style="display:none;"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" id="email" maxlength="255">
                                        <small class="form-text text-danger" id="erro_email" style="display:none;"></small>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Empresa/Seguro</label>
                                        <select class="form-control" name="empresa_id" id="empresa_id">
                                            <option value="">Nenhuma (Particular)</option>
                                            <?php
                                            $sql_empresas = "SELECT id, nome FROM empresas_seguros WHERE ativo = 1 ORDER BY nome";
                                            $rs_empresas = mysqli_query($db, $sql_empresas);
                                            if($rs_empresas && mysqli_num_rows($rs_empresas) > 0):
                                                while($emp = mysqli_fetch_array($rs_empresas)):
                                            ?>
                                                <option value="<?php echo $emp['id']; ?>"><?php echo htmlspecialchars($emp['nome']); ?></option>
                                            <?php
                                                endwhile;
                                            endif;
                                            ?>
                                        </select>
                                        <small class="form-text text-muted">Selecione se o paciente possui plano corporativo</small>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Endereço</label>
                                        <textarea class="form-control" name="endereco" id="endereco" rows="2" maxlength="500"></textarea>
                                        <small class="form-text text-danger" id="erro_endereco" style="display:none;"></small>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Bairro</label>
                                        <input class="form-control" type="text" name="bairro" id="bairro" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input class="form-control" type="text" name="cidade" id="cidade" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Província</label>
                                        <input class="form-control" type="text" name="provincia" id="provincia" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea class="form-control" name="observacoes" id="observacoes" rows="3" maxlength="1000"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Registar Paciente</button>
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
        // Gerar número de processo automaticamente ao carregar a página
        $(document).ready(function() {
            gerarNumeroProcesso();
        });

        function gerarNumeroProcesso() {
            $.ajax({
                url: 'daos/gerar_numero_processo.php',
                type: 'GET',
                success: function(data) {
                    $('#numero_processo_display').val(data);
                },
                error: function() {
                    // Se falhar, usar formato padrão
                    var ano = new Date().getFullYear();
                    var numero = 'PROC-' + ano + '-000001';
                    $('#numero_processo_display').val(numero);
                }
            });
        }

        // Validação de contacto moçambicano
        function validarContactoMocambicano(contacto) {
            if (!contacto) return false;
            // Remove espaços e caracteres não numéricos
            var numeros = contacto.replace(/\s+/g, '').replace(/[^0-9]/g, '');
            // Deve ter exatamente 9 dígitos
            if (numeros.length !== 9) return false;
            // Primeiro dígito deve ser 8 (celular) ou 2 (fixo)
            var primeiroDigito = numeros.charAt(0);
            return (primeiroDigito === '8' || primeiroDigito === '2');
        }

        // Formatação automática do contacto
        $('#contacto').on('input', function() {
            var valor = $(this).val().replace(/\D/g, ''); // Remove tudo que não é número
            if (valor.length > 9) valor = valor.substring(0, 9);
            
            // Formata: XX XXX XXXX
            if (valor.length > 0) {
                var formatado = valor.substring(0, 2);
                if (valor.length > 2) {
                    formatado += ' ' + valor.substring(2, 5);
                }
                if (valor.length > 5) {
                    formatado += ' ' + valor.substring(5);
                }
                $(this).val(formatado);
            }
            
            // Validação em tempo real
            if (valor.length > 0) {
                if (validarContactoMocambicano(valor)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $('#erro_contacto').hide();
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    if (valor.length < 9) {
                        $('#erro_contacto').text('O contacto deve ter 9 dígitos').show();
                    } else if (valor.charAt(0) !== '8' && valor.charAt(0) !== '2') {
                        $('#erro_contacto').text('O contacto deve começar com 8 ou 2').show();
                    } else {
                        $('#erro_contacto').text('Formato inválido').show();
                    }
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
                $('#erro_contacto').hide();
            }
        });

        // Formatação do contacto alternativo
        $('#contacto_alternativo').on('input', function() {
            var valor = $(this).val().replace(/\D/g, '');
            if (valor.length > 9) valor = valor.substring(0, 9);
            
            if (valor.length > 0) {
                var formatado = valor.substring(0, 2);
                if (valor.length > 2) {
                    formatado += ' ' + valor.substring(2, 5);
                }
                if (valor.length > 5) {
                    formatado += ' ' + valor.substring(5);
                }
                $(this).val(formatado);
                
                if (validarContactoMocambicano(valor)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $('#erro_contacto_alternativo').hide();
                } else if (valor.length > 0) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    $('#erro_contacto_alternativo').text('Formato inválido. Use: 84 000 0000').show();
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
                $('#erro_contacto_alternativo').hide();
            }
        });

        // Validação de nome e apelido (apenas letras)
        $('#nome, #apelido').on('input', function() {
            var valor = $(this).val();
            var regex = /^[A-Za-zÀ-ÿ\s]+$/;
            
            if (valor.length > 0) {
                if (regex.test(valor) && valor.trim().length >= 2) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $(this).next('.form-text').hide();
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    if (!regex.test(valor)) {
                        $(this).next('.form-text').text('Apenas letras são permitidas').show();
                    } else {
                        $(this).next('.form-text').text('Mínimo de 2 caracteres').show();
                    }
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
                $(this).next('.form-text').hide();
            }
        });

        // Validação de email
        $('#email').on('blur', function() {
            var email = $(this).val();
            if (email.length > 0) {
                var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (regex.test(email)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $('#erro_email').hide();
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    $('#erro_email').text('Email inválido').show();
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
                $('#erro_email').hide();
            }
        });

        // Validação de data de nascimento
        $('#data_nascimento').on('change', function() {
            var data = new Date($(this).val());
            var hoje = new Date();
            hoje.setHours(0, 0, 0, 0);
            
            if (data > hoje) {
                $(this).addClass('is-invalid');
                $('#erro_data_nascimento').text('A data de nascimento não pode ser no futuro').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#erro_data_nascimento').hide();
            }
        });

        // Validação de documento (se tipo foi selecionado, número é obrigatório)
        $('select[name="documento_tipo"]').on('change', function() {
            var tipo = $(this).val();
            var numero = $('#documento_numero').val();
            
            if (tipo && !numero) {
                $('#documento_numero').addClass('is-invalid');
                $('#erro_documento_numero').text('Número do documento é obrigatório quando o tipo é selecionado').show();
            } else {
                $('#documento_numero').removeClass('is-invalid');
                $('#erro_documento_numero').hide();
            }
        });

        $('#documento_numero').on('blur', function() {
            var tipo = $('select[name="documento_tipo"]').val();
            var numero = $(this).val();
            
            if (tipo && !numero) {
                $(this).addClass('is-invalid');
                $('#erro_documento_numero').text('Número do documento é obrigatório').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#erro_documento_numero').hide();
            }
        });

        // Validação completa do formulário
        function validarFormulario(event) {
            var valido = true;
            var erros = [];

            // Validar nome
            var nome = $('#nome').val().trim();
            if (!nome || nome.length < 2) {
                $('#nome').addClass('is-invalid');
                $('#erro_nome').text('Nome é obrigatório e deve ter pelo menos 2 caracteres').show();
                valido = false;
            } else if (!/^[A-Za-zÀ-ÿ\s]+$/.test(nome)) {
                $('#nome').addClass('is-invalid');
                $('#erro_nome').text('Nome deve conter apenas letras').show();
                valido = false;
            }

            // Validar apelido
            var apelido = $('#apelido').val().trim();
            if (!apelido || apelido.length < 2) {
                $('#apelido').addClass('is-invalid');
                $('#erro_apelido').text('Apelido é obrigatório e deve ter pelo menos 2 caracteres').show();
                valido = false;
            } else if (!/^[A-Za-zÀ-ÿ\s]+$/.test(apelido)) {
                $('#apelido').addClass('is-invalid');
                $('#erro_apelido').text('Apelido deve conter apenas letras').show();
                valido = false;
            }

            // Validar contacto
            var contacto = $('#contacto').val().replace(/\s+/g, '');
            if (!contacto || !validarContactoMocambicano(contacto)) {
                $('#contacto').addClass('is-invalid');
                $('#erro_contacto').text('Contacto inválido. Formato: +258 84 000 0000 (9 dígitos)').show();
                valido = false;
            }

            // Validar contacto alternativo (se preenchido)
            var contactoAlt = $('#contacto_alternativo').val().replace(/\s+/g, '');
            if (contactoAlt && !validarContactoMocambicano(contactoAlt)) {
                $('#contacto_alternativo').addClass('is-invalid');
                $('#erro_contacto_alternativo').text('Contacto alternativo inválido').show();
                valido = false;
            }

            // Validar email (se preenchido)
            var email = $('#email').val();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $('#email').addClass('is-invalid');
                $('#erro_email').text('Email inválido').show();
                valido = false;
            }

            // Validar documento
            var docTipo = $('select[name="documento_tipo"]').val();
            var docNumero = $('#documento_numero').val();
            if (docTipo && !docNumero) {
                $('#documento_numero').addClass('is-invalid');
                $('#erro_documento_numero').text('Número do documento é obrigatório quando o tipo é selecionado').show();
                valido = false;
            }

            // Validar data de nascimento
            var dataNasc = $('#data_nascimento').val();
            if (dataNasc) {
                var data = new Date(dataNasc);
                var hoje = new Date();
                hoje.setHours(0, 0, 0, 0);
                if (data > hoje) {
                    $('#data_nascimento').addClass('is-invalid');
                    $('#erro_data_nascimento').text('A data de nascimento não pode ser no futuro').show();
                    valido = false;
                }
            }

            if (!valido) {
                alert('Por favor, corrija os erros no formulário antes de submeter.');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>

