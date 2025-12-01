<?php
session_start();
include '../../../conexao/index.php';

if (isset($_POST['empresa_id'])) {
    $empresa_id = intval($_POST['empresa_id']);
    
    // Buscar tabela de preços da empresa
    $sql_empresa = "SELECT tabela_precos_id, desconto_geral FROM empresas_seguros WHERE id = $empresa_id";
    $rs_empresa = mysqli_query($db, $sql_empresa);
    $empresa = mysqli_fetch_array($rs_empresa);
    
    $precos = array();
    
    if($empresa && $empresa['tabela_precos_id']) {
        // Buscar preços contratados
        $sql_precos = "SELECT servico_id, preco, desconto_percentual 
                      FROM tabela_precos_servicos 
                      WHERE tabela_precos_id = " . $empresa['tabela_precos_id'];
        $rs_precos = mysqli_query($db, $sql_precos);
        
        while($preco = mysqli_fetch_array($rs_precos)) {
            $precos[$preco['servico_id']] = array(
                'preco' => floatval($preco['preco']),
                'desconto' => floatval($preco['desconto_percentual'])
            );
        }
    }
    
    // Se não encontrou preços específicos, aplicar desconto geral
    if(empty($precos) && $empresa && $empresa['desconto_geral'] > 0) {
        $desconto_geral = floatval($empresa['desconto_geral']);
        // Buscar todos os serviços e aplicar desconto geral
        $sql_servicos = "SELECT id, preco FROM servicos_clinica WHERE ativo = 1";
        $rs_servicos = mysqli_query($db, $sql_servicos);
        
        while($servico = mysqli_fetch_array($rs_servicos)) {
            $precos[$servico['id']] = array(
                'preco' => floatval($servico['preco']),
                'desconto' => $desconto_geral
            );
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($precos);
}
?>

