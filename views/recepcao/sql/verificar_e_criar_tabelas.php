<?php
/**
 * Script para verificar e criar todas as tabelas necessárias para os módulos de recepção
 * Execute este arquivo via navegador ou linha de comando
 */

include_once '../../../conexao/index.php';

$tabelas_necessarias = [
    // Temporárias
    'vds_servicos_temp',
    'ct_servicos_temp',
    'nc_servicos_temp',
    'nd_servicos_temp',
    'dv_servicos_temp',
    'rc_faturas_temp_recepcao',
    
    // Permanentes - VDS
    'venda_dinheiro_servico',
    'vds_servicos_fact',
    
    // Permanentes - CT
    'cotacao_recepcao',
    'ct_servicos_fact',
    
    // Permanentes - NC
    'nota_credito_recepcao',
    'nc_servicos_fact',
    
    // Permanentes - ND
    'nota_debito_recepcao',
    'nd_servicos_fact',
    
    // Permanentes - DV
    'devolucao_recepcao',
    'dv_servicos_fact',
    
    // Permanentes - RC
    'recibo_recepcao',
    'recibo_factura_recepcao',
];

echo "<h2>Verificação e Criação de Tabelas - Módulo de Recepção</h2>";
echo "<pre>";

$tabelas_faltando = [];
$tabelas_existentes = [];

foreach($tabelas_necessarias as $tabela) {
    $check_table = "SHOW TABLES LIKE '$tabela'";
    $table_exists = mysqli_query($db, $check_table);
    if($table_exists && mysqli_num_rows($table_exists) > 0) {
        $tabelas_existentes[] = $tabela;
        echo "✓ Tabela '$tabela' existe\n";
    } else {
        $tabelas_faltando[] = $tabela;
        echo "✗ Tabela '$tabela' NÃO existe\n";
    }
}

echo "\n";
echo "========================================\n";
echo "Resumo:\n";
echo "========================================\n";
echo "Tabelas existentes: " . count($tabelas_existentes) . "\n";
echo "Tabelas faltando: " . count($tabelas_faltando) . "\n";

if(!empty($tabelas_faltando)) {
    echo "\n";
    echo "Tabelas que precisam ser criadas:\n";
    foreach($tabelas_faltando as $tabela) {
        echo "  - $tabela\n";
    }
    echo "\n";
    echo "Para criar as tabelas faltantes, execute o arquivo SQL:\n";
    echo "views/recepcao/sql/create_documentos_recepcao_tables.sql\n";
    echo "\n";
    echo "Ou execute o seguinte comando no MySQL:\n";
    echo "mysql -u seu_usuario -p nome_do_banco < views/recepcao/sql/create_documentos_recepcao_tables.sql\n";
} else {
    echo "\n";
    echo "✓ Todas as tabelas necessárias estão criadas!\n";
}

echo "</pre>";
?>

