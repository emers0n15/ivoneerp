<?php
session_start();
include '../../../conexao/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    $empresa_id = intval($_POST['empresa_id']);
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

    if (empty($nome)) {
        echo "<script>alert('Nome da empresa é obrigatório!'); window.location.href='../editar_empresa.php?id=$empresa_id';</script>";
        exit;
    }

    $sql = "UPDATE empresas_seguros SET
        nome = '$nome',
        nuit = " . ($nuit ? "'$nuit'" : "NULL") . ",
        contacto = " . ($contacto ? "'$contacto'" : "NULL") . ",
        email = " . ($email ? "'$email'" : "NULL") . ",
        contrato = " . ($contrato ? "'$contrato'" : "NULL") . ",
        data_inicio_contrato = " . ($data_inicio ? "'$data_inicio'" : "NULL") . ",
        data_fim_contrato = " . ($data_fim ? "'$data_fim'" : "NULL") . ",
        desconto_geral = $desconto_geral,
        endereco = " . ($endereco ? "'$endereco'" : "NULL") . ",
        observacoes = " . ($observacoes ? "'$observacoes'" : "NULL") . "
        WHERE id = $empresa_id";

    if (mysqli_query($db, $sql)) {
        echo "<script>alert('Empresa atualizada com sucesso!'); window.location.href='../empresas.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar empresa: " . mysqli_error($db) . "'); window.location.href='../editar_empresa.php?id=$empresa_id';</script>";
    }
} else {
    header("location:../empresas.php");
}
?>

