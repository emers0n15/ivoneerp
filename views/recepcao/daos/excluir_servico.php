<?php
session_start();
include '../../../conexao/index.php';

if(!isset($_SESSION['idUsuario'])){
    header("location:../../../");
    exit;
}

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Verificar se o serviço está sendo usado em alguma tabela de preços
    $sql_check = "SELECT COUNT(*) as total FROM tabela_precos_servicos WHERE servico_id = $id";
    $rs_check = mysqli_query($db, $sql_check);
    $check = mysqli_fetch_array($rs_check);
    
    if($check['total'] > 0) {
        // Não pode excluir, está sendo usado. Vamos apenas inativar
        $sql = "UPDATE servicos_clinica SET ativo = 0 WHERE id = $id";
        $mensagem = "Serviço inativado (estava em uso e não pode ser excluído)";
    } else {
        // Pode excluir
        $sql = "DELETE FROM servicos_clinica WHERE id = $id";
        $mensagem = "Serviço excluído com sucesso!";
    }
    
    if(mysqli_query($db, $sql)) {
        header("location:../servicos_clinica.php?success=" . urlencode($mensagem));
    } else {
        header("location:../servicos_clinica.php?error=Erro ao excluir serviço: " . mysqli_error($db));
    }
} else {
    header("location:../servicos_clinica.php");
}
?>
