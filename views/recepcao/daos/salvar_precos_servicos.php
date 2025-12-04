<?php
session_start();
include '../../../conexao/index.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['empresa_id'])) {
    $empresa_id = intval($_POST['empresa_id']);
    $precos = json_decode($_POST['precos'], true);
    
    if(!$precos || !is_array($precos)) {
        echo json_encode(array('erro' => 'Dados inválidos'));
        exit;
    }
    
    // Buscar tabela de preços da empresa
    $sql_empresa = "SELECT tabela_precos_id FROM empresas_seguros WHERE id = $empresa_id";
    $rs_empresa = mysqli_query($db, $sql_empresa);
    $empresa = mysqli_fetch_array($rs_empresa);
    
    $tabela_precos_id = null;
    if($empresa && isset($empresa['tabela_precos_id'])) {
        $tabela_precos_id = $empresa['tabela_precos_id'];
    } else {
        // Criar tabela de preços
        $sql_criar = "INSERT INTO tabelas_precos (empresa_id, nome, ativo, usuario_criacao) 
                      VALUES ($empresa_id, 'Tabela Padrão', 1, " . $_SESSION['idUsuario'] . ")";
        mysqli_query($db, $sql_criar);
        $tabela_precos_id = mysqli_insert_id($db);
        
        // Atualizar empresa
        $sql_update = "UPDATE empresas_seguros SET tabela_precos_id = $tabela_precos_id WHERE id = $empresa_id";
        mysqli_query($db, $sql_update);
    }
    
    // Limpar preços antigos
    $sql_delete = "DELETE FROM tabela_precos_servicos WHERE tabela_precos_id = $tabela_precos_id";
    mysqli_query($db, $sql_delete);
    
    // Inserir novos preços
    foreach($precos as $servico_id => $preco) {
        $servico_id = intval($servico_id);
        $preco = floatval($preco);
        
        if($servico_id > 0 && $preco > 0) {
            $sql = "INSERT INTO tabela_precos_servicos (tabela_precos_id, servico_id, preco, desconto_percentual) 
                    VALUES ($tabela_precos_id, $servico_id, $preco, 0)";
            mysqli_query($db, $sql);
        }
    }
    
    echo json_encode(array('success' => true, 'mensagem' => 'Preços salvos com sucesso!'));
} else {
    echo json_encode(array('erro' => 'Requisição inválida'));
}
?>


