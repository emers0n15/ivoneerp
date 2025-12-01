<?php
session_start();
include '../../../conexao/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    $nome = mysqli_real_escape_string($db, trim($_POST['nome']));
    $nuit = !empty($_POST['nuit']) ? mysqli_real_escape_string($db, trim($_POST['nuit'])) : NULL;
    $contacto = !empty($_POST['contacto']) ? mysqli_real_escape_string($db, trim($_POST['contacto'])) : NULL;
    $email = !empty($_POST['email']) ? mysqli_real_escape_string($db, trim($_POST['email'])) : NULL;
    $contrato = !empty($_POST['contrato']) ? mysqli_real_escape_string($db, trim($_POST['contrato'])) : NULL;
    $data_inicio = !empty($_POST['data_inicio_contrato']) ? $_POST['data_inicio_contrato'] : NULL;
    $data_fim = !empty($_POST['data_fim_contrato']) ? $_POST['data_fim_contrato'] : NULL;
    $desconto_geral = !empty($_POST['desconto_geral']) ? floatval($_POST['desconto_geral']) : 0.00;
    $endereco = !empty($_POST['endereco']) ? mysqli_real_escape_string($db, substr($_POST['endereco'], 0, 500)) : NULL;
    $observacoes = !empty($_POST['observacoes']) ? mysqli_real_escape_string($db, substr($_POST['observacoes'], 0, 1000)) : NULL;
    $usuario_criacao = $_SESSION['idUsuario'];

    if (empty($nome)) {
        echo "<script>alert('Nome da empresa é obrigatório!'); window.location.href='../nova_empresa.php';</script>";
        exit;
    }

    $sql = "INSERT INTO empresas_seguros (
        nome, nuit, contacto, email, contrato, 
        data_inicio_contrato, data_fim_contrato, desconto_geral,
        endereco, observacoes, usuario_criacao
    ) VALUES (
        '$nome', " . ($nuit ? "'$nuit'" : "NULL") . ", 
        " . ($contacto ? "'$contacto'" : "NULL") . ", 
        " . ($email ? "'$email'" : "NULL") . ", 
        " . ($contrato ? "'$contrato'" : "NULL") . ", 
        " . ($data_inicio ? "'$data_inicio'" : "NULL") . ", 
        " . ($data_fim ? "'$data_fim'" : "NULL") . ", 
        $desconto_geral,
        " . ($endereco ? "'$endereco'" : "NULL") . ", 
        " . ($observacoes ? "'$observacoes'" : "NULL") . ", 
        $usuario_criacao
    )";

    if (mysqli_query($db, $sql)) {
        $empresa_id = mysqli_insert_id($db);
        echo "<script>alert('Empresa registada com sucesso!'); window.location.href='../tabela_precos.php?id=$empresa_id';</script>";
    } else {
        echo "<script>alert('Erro ao registar empresa: " . mysqli_error($db) . "'); window.location.href='../nova_empresa.php';</script>";
    }
} else {
    header("location:../empresas.php");
}
?>

