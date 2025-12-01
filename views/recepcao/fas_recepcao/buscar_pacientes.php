<?php
session_start();
include_once '../../../conexao/index.php';
header('Content-Type: application/json');

// Aceitar tanto GET quanto POST
$empresa_id = $_GET['empresa_id'] ?? $_POST['empresa_id'] ?? null;

// Buscar pacientes
if($empresa_id && $empresa_id != '' && $empresa_id != 'null') {
    // Buscar pacientes da empresa específica
    $sql_pacientes = "SELECT id, nome, apelido, numero_processo 
                      FROM pacientes 
                      WHERE empresa_id = ? AND ativo = 1 
                      ORDER BY nome, apelido";
    $stmt = mysqli_prepare($db, $sql_pacientes);
    mysqli_stmt_bind_param($stmt, "i", $empresa_id);
    mysqli_stmt_execute($stmt);
    $rs_pacientes = mysqli_stmt_get_result($stmt);
} else {
    // Buscar todos os pacientes ativos
    $sql_pacientes = "SELECT id, nome, apelido, numero_processo 
                      FROM pacientes 
                      WHERE ativo = 1 
                      ORDER BY nome, apelido";
    $rs_pacientes = mysqli_query($db, $sql_pacientes);
}

$pacientes = [];
if($rs_pacientes && mysqli_num_rows($rs_pacientes) > 0) {
    while ($paciente = mysqli_fetch_array($rs_pacientes)) {
        $pacientes[] = [
            'id' => $paciente['id'],
            'nome' => $paciente['nome'],
            'apelido' => $paciente['apelido'],
            'numero_processo' => $paciente['numero_processo']
        ];
    }
}

// Retornar array direto (sem wrapper 'pacientes')
echo json_encode($pacientes);
?>

