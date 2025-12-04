<?php
session_start();
include '../../../conexao/index.php';
header('Content-Type: application/json');

if (isset($_GET['empresa_id'])) {
    $empresa_id = intval($_GET['empresa_id']);
    
    // Buscar tabela de preços da empresa
    $sql_empresa = "SELECT tabela_precos_id FROM empresas_seguros WHERE id = $empresa_id";
    $rs_empresa = mysqli_query($db, $sql_empresa);
    $empresa = mysqli_fetch_array($rs_empresa);
    
    $tabela_precos_id = null;
    if($empresa && isset($empresa['tabela_precos_id'])) {
        $tabela_precos_id = $empresa['tabela_precos_id'];
    } else {
        // Criar tabela de preços se não existir
        $sql_criar = "INSERT INTO tabelas_precos (empresa_id, nome, ativo, usuario_criacao) 
                      VALUES ($empresa_id, 'Tabela Padrão', 1, " . $_SESSION['idUsuario'] . ")";
        mysqli_query($db, $sql_criar);
        $tabela_precos_id = mysqli_insert_id($db);
        
        // Atualizar empresa
        $sql_update = "UPDATE empresas_seguros SET tabela_precos_id = $tabela_precos_id WHERE id = $empresa_id";
        mysqli_query($db, $sql_update);
    }
    
    // Buscar serviços ativos
    $sql_servicos = "SELECT * FROM servicos_clinica WHERE ativo = 1 ORDER BY categoria, nome";
    $rs_servicos = mysqli_query($db, $sql_servicos);
    
    // Buscar preços cadastrados
    $precos_cadastrados = array();
    if($tabela_precos_id) {
        $sql_precos = "SELECT servico_id, preco FROM tabela_precos_servicos WHERE tabela_precos_id = $tabela_precos_id";
        $rs_precos = mysqli_query($db, $sql_precos);
        while($preco = mysqli_fetch_array($rs_precos)) {
            $precos_cadastrados[$preco['servico_id']] = floatval($preco['preco']);
        }
    }
    
    $servicos = array();
    while ($servico = mysqli_fetch_array($rs_servicos)) {
        $servico_id = intval($servico['id']);
        $preco_empresa = isset($precos_cadastrados[$servico_id]) ? $precos_cadastrados[$servico_id] : floatval($servico['preco']);
        
        $servicos[] = array(
            'id' => $servico_id,
            'codigo' => $servico['codigo'],
            'nome' => $servico['nome'],
            'categoria' => $servico['categoria'],
            'preco_padrao' => floatval($servico['preco']),
            'preco_empresa' => $preco_empresa
        );
    }
    
    echo json_encode(array(
        'servicos' => $servicos,
        'tabela_precos_id' => $tabela_precos_id
    ));
} else {
    echo json_encode(array('erro' => 'Empresa não especificada'));
}
?>


