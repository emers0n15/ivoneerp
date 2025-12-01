<?php
session_start();
include '../../../conexao/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    $id = intval($_POST['id']);
    $nome = mysqli_real_escape_string($db, $_POST['nome']);
    $apelido = mysqli_real_escape_string($db, $_POST['apelido']);
    $data_nascimento = !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : NULL;
    $sexo = mysqli_real_escape_string($db, $_POST['sexo']);
    $documento_tipo = mysqli_real_escape_string($db, $_POST['documento_tipo']);
    $documento_numero = mysqli_real_escape_string($db, $_POST['documento_numero']);
    $contacto = mysqli_real_escape_string($db, $_POST['contacto']);
    $contacto_alternativo = mysqli_real_escape_string($db, $_POST['contacto_alternativo']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $endereco = mysqli_real_escape_string($db, $_POST['endereco']);
    $bairro = mysqli_real_escape_string($db, $_POST['bairro']);
    $cidade = mysqli_real_escape_string($db, $_POST['cidade']);
    $provincia = mysqli_real_escape_string($db, $_POST['provincia']);
    $empresa_id_novo = !empty($_POST['empresa_id']) ? intval($_POST['empresa_id']) : NULL;
    $observacoes = mysqli_real_escape_string($db, $_POST['observacoes']);
    $usuario = $_SESSION['idUsuario'];
    
    // Buscar empresa atual do paciente
    $sql_atual = "SELECT empresa_id FROM pacientes WHERE id = $id";
    $rs_atual = mysqli_query($db, $sql_atual);
    $atual = mysqli_fetch_array($rs_atual);
    $empresa_id_antigo = $atual['empresa_id'] ?? NULL;
    
    // Se empresa mudou, registrar no histórico
    if($empresa_id_novo != $empresa_id_antigo) {
        // Fechar histórico anterior se existir
        if($empresa_id_antigo) {
            $sql_fechar = "UPDATE paciente_empresa_historico 
                          SET data_fim = CURDATE() 
                          WHERE paciente_id = $id AND empresa_id = $empresa_id_antigo AND data_fim IS NULL";
            mysqli_query($db, $sql_fechar);
        }
        
        // Criar novo registro se nova empresa foi selecionada
        if($empresa_id_novo) {
            $sql_hist = "INSERT INTO paciente_empresa_historico (paciente_id, empresa_id, data_inicio, usuario_registo) 
                        VALUES ($id, $empresa_id_novo, CURDATE(), $usuario)";
            mysqli_query($db, $sql_hist);
        }
    }

    $sql = "UPDATE pacientes SET 
        nome = '$nome',
        apelido = '$apelido',
        data_nascimento = " . ($data_nascimento ? "'$data_nascimento'" : "NULL") . ",
        sexo = " . ($sexo ? "'$sexo'" : "NULL") . ",
        documento_tipo = " . ($documento_tipo ? "'$documento_tipo'" : "NULL") . ",
        documento_numero = " . ($documento_numero ? "'$documento_numero'" : "NULL") . ",
        contacto = '$contacto',
        contacto_alternativo = " . ($contacto_alternativo ? "'$contacto_alternativo'" : "NULL") . ",
        email = " . ($email ? "'$email'" : "NULL") . ",
        endereco = " . ($endereco ? "'$endereco'" : "NULL") . ",
        bairro = " . ($bairro ? "'$bairro'" : "NULL") . ",
        cidade = " . ($cidade ? "'$cidade'" : "NULL") . ",
        provincia = " . ($provincia ? "'$provincia'" : "NULL") . ",
        empresa_id = " . ($empresa_id_novo ? "$empresa_id_novo" : "NULL") . ",
        observacoes = " . ($observacoes ? "'$observacoes'" : "NULL") . "
        WHERE id = $id";

    if (mysqli_query($db, $sql)) {
        echo "<script>alert('Paciente atualizado com sucesso!'); window.location.href='../pacientes.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar paciente: " . mysqli_error($db) . "'); window.location.href='../editar_paciente.php?id=$id';</script>";
    }
} else {
    header("location:../pacientes.php");
}
?>

