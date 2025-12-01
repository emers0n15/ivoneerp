<?php
session_start();
include '../../../conexao/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn'])) {
    $tabela_precos_id = intval($_POST['tabela_precos_id']);
    $empresa_id = intval($_POST['empresa_id']);
    $servicos_ids = $_POST['servico_id'];
    $precos = $_POST['preco'];
    $descontos = $_POST['desconto'];
    
    // Limpar preços antigos
    $sql_delete = "DELETE FROM tabela_precos_servicos WHERE tabela_precos_id = $tabela_precos_id";
    mysqli_query($db, $sql_delete);
    
    // Inserir novos preços
    for($i = 0; $i < count($servicos_ids); $i++){
        $servico_id = intval($servicos_ids[$i]);
        $preco = floatval($precos[$i]);
        $desconto = floatval($descontos[$i]);
        
        $sql = "INSERT INTO tabela_precos_servicos (tabela_precos_id, servico_id, preco, desconto_percentual) 
                VALUES ($tabela_precos_id, $servico_id, $preco, $desconto)";
        mysqli_query($db, $sql);
    }
    
    // Atualizar empresa com tabela de preços
    $sql_update = "UPDATE empresas_seguros SET tabela_precos_id = $tabela_precos_id WHERE id = $empresa_id";
    mysqli_query($db, $sql_update);
    
    echo "<script>alert('Tabela de preços salva com sucesso!'); window.location.href='../empresas.php';</script>";
} else {
    header("location:../empresas.php");
}
?>

