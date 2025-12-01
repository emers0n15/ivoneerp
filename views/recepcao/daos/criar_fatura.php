<?php
// Habilitar exibição de erros para debug (remover em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar se usuário está logado
if(!isset($_SESSION['idUsuario'])) {
    echo "<script>alert('Erro: Sessão expirada. Faça login novamente.'); window.location.href='../../';</script>";
    exit;
}

include '../../../conexao/index.php';

// Verificar conexão com banco
if(!$db) {
    echo "<script>alert('Erro: Não foi possível conectar ao banco de dados.'); window.location.href='../nova_fatura.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    // Validar campos obrigatórios
    if(empty($_POST['paciente_id'])) {
        echo "<script>alert('Erro: Paciente não selecionado.'); window.location.href='../nova_fatura.php';</script>";
        exit;
    }
    if(empty($_POST['data_atendimento'])) {
        echo "<script>alert('Erro: Data de atendimento não informada.'); window.location.href='../nova_fatura.php';</script>";
        exit;
    }
    
    $paciente_id = intval($_POST['paciente_id']);
    $data_atendimento = mysqli_real_escape_string($db, $_POST['data_atendimento']);
    $hora_atendimento = !empty($_POST['hora_atendimento']) ? mysqli_real_escape_string($db, $_POST['hora_atendimento']) : NULL;
    $desconto = isset($_POST['desconto']) ? floatval($_POST['desconto']) : 0;
    $observacoes = isset($_POST['observacoes']) ? mysqli_real_escape_string($db, $_POST['observacoes']) : '';
    $tipo_documento = isset($_POST['tipo_documento']) ? mysqli_real_escape_string($db, $_POST['tipo_documento']) : 'fatura';
    // Validar tipo_documento (deve ser: fatura, vds ou cotacao)
    if (!in_array($tipo_documento, ['fatura', 'vds', 'cotacao'])) {
        $tipo_documento = 'fatura';
    }
    $usuario = $_SESSION['idUsuario'];
    
    // Validar paciente_id
    if(empty($paciente_id) || $paciente_id <= 0) {
        echo "<script>alert('Erro: Paciente não selecionado.'); window.location.href='../nova_fatura.php';</script>";
        exit;
    }
    
    // Buscar dados do paciente para verificar se tem empresa
    $sql_paciente = "SELECT empresa_id FROM pacientes WHERE id = $paciente_id";
    $rs_paciente = mysqli_query($db, $sql_paciente);
    if(!$rs_paciente) {
        echo "<script>alert('Erro ao buscar dados do paciente: " . addslashes(mysqli_error($db)) . "'); window.location.href='../nova_fatura.php';</script>";
        exit;
    }
    $paciente_data = mysqli_fetch_array($rs_paciente);
    $empresa_id = ($paciente_data && isset($paciente_data['empresa_id'])) ? $paciente_data['empresa_id'] : NULL;
    
    // Buscar tabela de preços da empresa se existir
    $tabela_precos_id = NULL;
    $desconto_geral = 0;
    if($empresa_id) {
        $sql_empresa = "SELECT tabela_precos_id, desconto_geral FROM empresas_seguros WHERE id = $empresa_id";
        $rs_empresa = mysqli_query($db, $sql_empresa);
        if($rs_empresa && mysqli_num_rows($rs_empresa) > 0) {
            $empresa_data = mysqli_fetch_array($rs_empresa);
            $tabela_precos_id = ($empresa_data && isset($empresa_data['tabela_precos_id'])) ? $empresa_data['tabela_precos_id'] : NULL;
            $desconto_geral = ($empresa_data && isset($empresa_data['desconto_geral'])) ? floatval($empresa_data['desconto_geral']) : 0;
        }
    }
    
    // Validar se há serviços ANTES de processar
    if(!isset($_POST['servicos']) || !is_array($_POST['servicos']) || count($_POST['servicos']) == 0) {
        echo "<script>alert('Erro: Selecione pelo menos um serviço.'); window.location.href='../nova_fatura.php';</script>";
        exit;
    }
    
    // Calcular subtotal e total
    $subtotal = 0;
    if (isset($_POST['servicos']) && is_array($_POST['servicos'])) {
        foreach ($_POST['servicos'] as $index => $servico) {
            // Validar estrutura do array de serviços
            if(!isset($servico['id']) || !isset($servico['quantidade'])) {
                echo "<script>alert('Erro: Estrutura de dados inválida. Recarregue a página e tente novamente.'); window.location.href='../nova_fatura.php';</script>";
                exit;
            }
            
            $servico_id = intval($servico['id']);
            $quantidade = intval($servico['quantidade']);
            
            if($servico_id <= 0 || $quantidade <= 0) {
                continue; // Pular serviços inválidos
            }
            
            // Buscar preço: primeiro da tabela contratada, depois padrão
            $preco_final = NULL;
            if($tabela_precos_id) {
                $sql_preco_contratado = "SELECT preco, desconto_percentual FROM tabela_precos_servicos 
                                        WHERE tabela_precos_id = $tabela_precos_id AND servico_id = $servico_id";
                $rs_preco_contratado = mysqli_query($db, $sql_preco_contratado);
                if($rs_preco_contratado && mysqli_num_rows($rs_preco_contratado) > 0) {
                    $preco_data = mysqli_fetch_array($rs_preco_contratado);
                    if($preco_data && isset($preco_data['preco'])) {
                        $preco_final = floatval($preco_data['preco']);
                        // Aplicar desconto se houver
                        if(isset($preco_data['desconto_percentual']) && $preco_data['desconto_percentual'] > 0) {
                            $preco_final = $preco_final * (1 - floatval($preco_data['desconto_percentual']) / 100);
                        }
                    }
                }
            }
            
            // Se não encontrou preço contratado, usar preço padrão
            if($preco_final === NULL) {
                $sql_servico = "SELECT preco FROM servicos_clinica WHERE id = $servico_id";
                $rs_servico = mysqli_query($db, $sql_servico);
                if($rs_servico && mysqli_num_rows($rs_servico) > 0) {
                    $servico_data = mysqli_fetch_array($rs_servico);
                    $preco_final = ($servico_data && isset($servico_data['preco'])) ? floatval($servico_data['preco']) : 0;
                    
                    // Aplicar desconto geral da empresa se houver
                    if($empresa_id && $desconto_geral > 0) {
                        $preco_final = $preco_final * (1 - $desconto_geral / 100);
                    }
                } else {
                    echo "<script>alert('Erro: Serviço não encontrado (ID: $servico_id)'); window.location.href='../nova_fatura.php';</script>";
                    exit;
                }
            }
            
            if($preco_final > 0) {
                $subtotal += $preco_final * $quantidade;
            }
        }
    }
    
    $total = $subtotal - $desconto;
    
    // Gerar número único da fatura
    $ano = date('Y');
    $sql_ultima = "SELECT COUNT(*) as total FROM faturas_atendimento WHERE YEAR(data_criacao) = $ano";
    $rs_ultima = mysqli_query($db, $sql_ultima);
    $total_existente = 0;
    if($rs_ultima && mysqli_num_rows($rs_ultima) > 0) {
        $ultima = mysqli_fetch_array($rs_ultima);
        $total_existente = ($ultima && isset($ultima['total'])) ? intval($ultima['total']) : 0;
    }
    $numero_fatura = 'FAT-' . $ano . '-' . str_pad($total_existente + 1, 6, '0', STR_PAD_LEFT);
    
    // Inserir fatura/documento
    $sql_fatura = "INSERT INTO faturas_atendimento (
        numero_fatura, paciente_id, empresa_id, tipo_documento, data_atendimento, hora_atendimento,
        subtotal, desconto, total, status, observacoes, usuario_criacao
    ) VALUES (
        '$numero_fatura', $paciente_id, " . ($empresa_id ? "$empresa_id" : "NULL") . ", 
        '$tipo_documento', '$data_atendimento', " . ($hora_atendimento ? "'$hora_atendimento'" : "NULL") . ",
        $subtotal, $desconto, $total, 'pendente', " . ($observacoes ? "'$observacoes'" : "NULL") . ", $usuario
    )";
    
    if (mysqli_query($db, $sql_fatura)) {
        $fatura_id = mysqli_insert_id($db);
        
        // Inserir serviços da fatura
        if (isset($_POST['servicos']) && is_array($_POST['servicos'])) {
            foreach ($_POST['servicos'] as $servico) {
                $servico_id = intval($servico['id']);
                $quantidade = intval($servico['quantidade']);
                
                // Buscar preço usado (mesma lógica de cima)
                $preco_unitario_final = NULL;
                if($tabela_precos_id) {
                    $sql_preco_contratado = "SELECT preco, desconto_percentual FROM tabela_precos_servicos 
                                            WHERE tabela_precos_id = $tabela_precos_id AND servico_id = $servico_id";
                    $rs_preco_contratado = mysqli_query($db, $sql_preco_contratado);
                    if($rs_preco_contratado && mysqli_num_rows($rs_preco_contratado) > 0) {
                        $preco_data = mysqli_fetch_array($rs_preco_contratado);
                        if($preco_data && isset($preco_data['preco'])) {
                            $preco_unitario_final = floatval($preco_data['preco']);
                            if(isset($preco_data['desconto_percentual']) && $preco_data['desconto_percentual'] > 0) {
                                $preco_unitario_final = $preco_unitario_final * (1 - floatval($preco_data['desconto_percentual']) / 100);
                            }
                        }
                    }
                }
                
                if($preco_unitario_final === NULL) {
                    $sql_servico = "SELECT preco FROM servicos_clinica WHERE id = $servico_id";
                    $rs_servico = mysqli_query($db, $sql_servico);
                    if($rs_servico && mysqli_num_rows($rs_servico) > 0) {
                        $servico_data = mysqli_fetch_array($rs_servico);
                        $preco_unitario_final = ($servico_data && isset($servico_data['preco'])) ? floatval($servico_data['preco']) : 0;
                        if($empresa_id && $desconto_geral > 0) {
                            $preco_unitario_final = $preco_unitario_final * (1 - $desconto_geral / 100);
                        }
                    } else {
                        $preco_unitario_final = 0;
                    }
                }
                
                $subtotal_item = $preco_unitario_final * $quantidade;
                
                $sql_item = "INSERT INTO fatura_servicos (fatura_id, servico_id, quantidade, preco_unitario, subtotal) 
                            VALUES ($fatura_id, $servico_id, $quantidade, $preco_unitario_final, $subtotal_item)";
                mysqli_query($db, $sql_item);
            }
        }
        
        // Registrar no histórico
        $servicos_nomes = array();
        if (isset($_POST['servicos']) && is_array($_POST['servicos'])) {
            foreach ($_POST['servicos'] as $servico) {
                $servico_id = intval($servico['id']);
                $sql_nome = "SELECT nome FROM servicos_clinica WHERE id = $servico_id";
                $rs_nome = mysqli_query($db, $sql_nome);
                if($rs_nome && mysqli_num_rows($rs_nome) > 0) {
                    $nome_data = mysqli_fetch_array($rs_nome);
                    if($nome_data && isset($nome_data['nome'])) {
                        $servicos_nomes[] = $nome_data['nome'];
                    }
                }
            }
        }
        
        if(count($servicos_nomes) > 0) {
            $servicos_texto = mysqli_real_escape_string($db, implode(', ', $servicos_nomes));
            $sql_historico = "INSERT INTO historico_atendimentos (
                paciente_id, fatura_id, tipo_atendimento, servicos_realizados, data_atendimento, usuario_registo
            ) VALUES (
                $paciente_id, $fatura_id, 'Fatura de Atendimento', '$servicos_texto', '$data_atendimento', $usuario
            )";
            mysqli_query($db, $sql_historico);
        }
        
        $tipo_nome = $tipo_documento == 'vds' ? 'VDS' : ($tipo_documento == 'cotacao' ? 'Cotação' : 'Fatura');
        $numero_fatura_escaped = addslashes($numero_fatura);
        echo "<script>alert('$tipo_nome criado com sucesso! Número: $numero_fatura_escaped'); window.location.href='../faturas.php';</script>";
    } else {
        $erro_msg = addslashes(mysqli_error($db));
        echo "<script>alert('Erro ao criar fatura: $erro_msg'); window.location.href='../nova_fatura.php';</script>";
    }
} else {
    header("location:../faturas.php");
}
?>

