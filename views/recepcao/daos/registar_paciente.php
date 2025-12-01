<?php
session_start();
include '../../../conexao/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    // Validações
    $erros = array();
    
    // Validar nome
    $nome = trim($_POST['nome']);
    if (empty($nome) || strlen($nome) < 2) {
        $erros[] = "Nome é obrigatório e deve ter pelo menos 2 caracteres";
    } elseif (!preg_match("/^[A-Za-zÀ-ÿ\s]+$/u", $nome)) {
        $erros[] = "Nome deve conter apenas letras";
    }
    $nome = mysqli_real_escape_string($db, $nome);
    
    // Validar apelido
    $apelido = trim($_POST['apelido']);
    if (empty($apelido) || strlen($apelido) < 2) {
        $erros[] = "Apelido é obrigatório e deve ter pelo menos 2 caracteres";
    } elseif (!preg_match("/^[A-Za-zÀ-ÿ\s]+$/u", $apelido)) {
        $erros[] = "Apelido deve conter apenas letras";
    }
    $apelido = mysqli_real_escape_string($db, $apelido);
    
    // Validar contacto moçambicano
    $contacto = trim($_POST['contacto']);
    $contacto_limpo = preg_replace('/\s+/', '', $contacto);
    if (empty($contacto_limpo)) {
        $erros[] = "Contacto é obrigatório";
    } elseif (strlen($contacto_limpo) != 9) {
        $erros[] = "Contacto deve ter 9 dígitos (formato: +258 84 000 0000)";
    } elseif (!preg_match("/^[82]/", $contacto_limpo)) {
        $erros[] = "Contacto deve começar com 8 (celular) ou 2 (fixo)";
    } elseif (!preg_match("/^[0-9]{9}$/", $contacto_limpo)) {
        $erros[] = "Contacto deve conter apenas números";
    }
    // Formatar contacto: +258 XX XXX XXXX
    $contacto_formatado = '+258 ' . substr($contacto_limpo, 0, 2) . ' ' . substr($contacto_limpo, 2, 3) . ' ' . substr($contacto_limpo, 5);
    $contacto = mysqli_real_escape_string($db, $contacto_formatado);
    
    // Validar contacto alternativo (se preenchido)
    $contacto_alternativo = NULL;
    if (!empty($_POST['contacto_alternativo'])) {
        $contacto_alt_limpo = preg_replace('/\s+/', '', $_POST['contacto_alternativo']);
        if (strlen($contacto_alt_limpo) != 9) {
            $erros[] = "Contacto alternativo deve ter 9 dígitos";
        } elseif (!preg_match("/^[82]/", $contacto_alt_limpo)) {
            $erros[] = "Contacto alternativo deve começar com 8 (celular) ou 2 (fixo)";
        } elseif (!preg_match("/^[0-9]{9}$/", $contacto_alt_limpo)) {
            $erros[] = "Contacto alternativo deve conter apenas números";
        } else {
            $contacto_alt_formatado = '+258 ' . substr($contacto_alt_limpo, 0, 2) . ' ' . substr($contacto_alt_limpo, 2, 3) . ' ' . substr($contacto_alt_limpo, 5);
            $contacto_alternativo = mysqli_real_escape_string($db, $contacto_alt_formatado);
        }
    }
    
    // Validar email (se preenchido)
    $email = NULL;
    if (!empty($_POST['email'])) {
        $email_temp = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if (!$email_temp) {
            $erros[] = "Email inválido";
        } else {
            $email = mysqli_real_escape_string($db, $email_temp);
        }
    }
    
    // Validar data de nascimento
    $data_nascimento = NULL;
    if (!empty($_POST['data_nascimento'])) {
        $data_temp = $_POST['data_nascimento'];
        $data_obj = new DateTime($data_temp);
        $hoje = new DateTime();
        if ($data_obj > $hoje) {
            $erros[] = "Data de nascimento não pode ser no futuro";
        } else {
            $data_nascimento = $data_temp;
        }
    }
    
    // Validar documento (se tipo foi selecionado, número é obrigatório)
    $documento_tipo = !empty($_POST['documento_tipo']) ? mysqli_real_escape_string($db, $_POST['documento_tipo']) : NULL;
    $documento_numero = !empty($_POST['documento_numero']) ? mysqli_real_escape_string($db, trim($_POST['documento_numero'])) : NULL;
    if ($documento_tipo && empty($documento_numero)) {
        $erros[] = "Número do documento é obrigatório quando o tipo é selecionado";
    }
    
    // Validar outros campos
    $sexo = !empty($_POST['sexo']) ? mysqli_real_escape_string($db, $_POST['sexo']) : NULL;
    $empresa_id = !empty($_POST['empresa_id']) ? intval($_POST['empresa_id']) : NULL;
    $endereco = !empty($_POST['endereco']) ? mysqli_real_escape_string($db, substr($_POST['endereco'], 0, 500)) : NULL;
    $bairro = !empty($_POST['bairro']) ? mysqli_real_escape_string($db, substr($_POST['bairro'], 0, 255)) : NULL;
    $cidade = !empty($_POST['cidade']) ? mysqli_real_escape_string($db, substr($_POST['cidade'], 0, 255)) : NULL;
    $provincia = !empty($_POST['provincia']) ? mysqli_real_escape_string($db, substr($_POST['provincia'], 0, 255)) : NULL;
    $observacoes = !empty($_POST['observacoes']) ? mysqli_real_escape_string($db, substr($_POST['observacoes'], 0, 1000)) : NULL;
    $usuario_registo = $_SESSION['idUsuario'];
    
    // Se houver erros, retornar
    if (!empty($erros)) {
        $mensagem_erro = implode("\\n", $erros);
        echo "<script>alert('Erros encontrados:\\n$mensagem_erro'); window.location.href='../novo_paciente.php';</script>";
        exit;
    }
    
    // Verificar se a tabela pacientes existe
    $check_table = "SHOW TABLES LIKE 'pacientes'";
    $table_exists = mysqli_query($db, $check_table);
    
    if (!$table_exists || mysqli_num_rows($table_exists) == 0) {
        echo "<script>alert('Erro: A tabela de pacientes não foi criada. Por favor, execute o script SQL: views/recepcao/recepcao.sql'); window.location.href='../verificar_tabelas.php';</script>";
        exit;
    }
    
    // Gerar número de processo automaticamente
    $ano = date('Y');
    $sql_ultimo = "SELECT numero_processo FROM pacientes WHERE numero_processo LIKE 'PROC-$ano-%' ORDER BY numero_processo DESC LIMIT 1";
    $rs_ultimo = mysqli_query($db, $sql_ultimo);
    
    if ($rs_ultimo && mysqli_num_rows($rs_ultimo) > 0) {
        $ultimo = mysqli_fetch_array($rs_ultimo);
        $partes = explode('-', $ultimo['numero_processo']);
        if (count($partes) == 3) {
            $sequencial = intval($partes[2]) + 1;
        } else {
            $sequencial = 1;
        }
    } else {
        $sequencial = 1;
    }
    
    $numero_processo = 'PROC-' . $ano . '-' . str_pad($sequencial, 6, '0', STR_PAD_LEFT);
    
    // Verificar se o número de processo já existe (dupla verificação)
    $check = "SELECT id FROM pacientes WHERE numero_processo = '$numero_processo'";
    $result = mysqli_query($db, $check);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // Se por algum motivo já existir, tentar o próximo
        $sequencial++;
        $numero_processo = 'PROC-' . $ano . '-' . str_pad($sequencial, 6, '0', STR_PAD_LEFT);
    }

    $sql = "INSERT INTO pacientes (
        numero_processo, nome, apelido, data_nascimento, sexo, 
        documento_tipo, documento_numero, contacto, contacto_alternativo, 
        email, endereco, bairro, cidade, provincia, empresa_id, observacoes, usuario_registo
    ) VALUES (
        '$numero_processo', '$nome', '$apelido', " . ($data_nascimento ? "'$data_nascimento'" : "NULL") . ", 
        " . ($sexo ? "'$sexo'" : "NULL") . ", " . ($documento_tipo ? "'$documento_tipo'" : "NULL") . ", 
        " . ($documento_numero ? "'$documento_numero'" : "NULL") . ", '$contacto', 
        " . ($contacto_alternativo ? "'$contacto_alternativo'" : "NULL") . ", 
        " . ($email ? "'$email'" : "NULL") . ", " . ($endereco ? "'$endereco'" : "NULL") . ", 
        " . ($bairro ? "'$bairro'" : "NULL") . ", " . ($cidade ? "'$cidade'" : "NULL") . ", 
        " . ($provincia ? "'$provincia'" : "NULL") . ", " . ($empresa_id ? "$empresa_id" : "NULL") . ", 
        " . ($observacoes ? "'$observacoes'" : "NULL") . ", 
        $usuario_registo
    )";

    if (mysqli_query($db, $sql)) {
        $paciente_id = mysqli_insert_id($db);
        
        // Registrar no histórico se tiver empresa
        if ($empresa_id) {
            $sql_hist = "INSERT INTO paciente_empresa_historico (paciente_id, empresa_id, data_inicio, usuario_registo) 
                        VALUES ($paciente_id, $empresa_id, CURDATE(), $usuario_registo)";
            mysqli_query($db, $sql_hist);
        }
        
        echo "<script>alert('Paciente registado com sucesso!'); window.location.href='../pacientes.php';</script>";
    } else {
        $erro_msg = mysqli_error($db);
        if (strpos($erro_msg, "doesn't exist") !== false || strpos($erro_msg, "não existe") !== false) {
            echo "<script>alert('Erro: A tabela de pacientes não foi criada. Por favor, execute o script SQL: views/recepcao/recepcao.sql'); window.location.href='../verificar_tabelas.php';</script>";
        } else {
            echo "<script>alert('Erro ao registar paciente: " . addslashes($erro_msg) . "'); window.location.href='../novo_paciente.php';</script>";
        }
    }
} else {
    header("location:../pacientes.php");
}
?>

