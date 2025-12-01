<?php
/**
 * Script para criar as tabelas de DV (Devolução) se não existirem
 * Execute este arquivo via navegador
 */

session_start();
if(!isset($_SESSION['idUsuario'])){
    die("Erro: Você precisa estar logado para executar este script.");
}

include_once '../../../conexao/index.php';

echo "<h2>Criação de Tabelas DV (Devolução)</h2>";
echo "<pre>";

$tabelas_dv = [
    'dv_servicos_temp' => "
        CREATE TABLE IF NOT EXISTS `dv_servicos_temp` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `servico` int(11) NOT NULL,
          `qtd` int(11) NOT NULL DEFAULT 1,
          `preco` decimal(10,2) NOT NULL,
          `total` decimal(10,2) NOT NULL,
          `user` int(11) NOT NULL,
          `empresa_id` int(11) DEFAULT NULL,
          `factura_recepcao_id` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `servico` (`servico`),
          KEY `user` (`user`),
          KEY `empresa_id` (`empresa_id`),
          KEY `factura_recepcao_id` (`factura_recepcao_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ",
    
    'devolucao_recepcao' => "
        CREATE TABLE IF NOT EXISTS `devolucao_recepcao` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `n_doc` int(11) NOT NULL,
          `factura_recepcao_id` int(11) NOT NULL,
          `paciente` int(11) NOT NULL,
          `empresa_id` int(11) DEFAULT NULL,
          `valor` decimal(10,2) NOT NULL,
          `motivo` text DEFAULT NULL,
          `metodo` varchar(50) NOT NULL,
          `serie` int(11) NOT NULL,
          `usuario` int(11) NOT NULL,
          `dataa` date NOT NULL,
          `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `factura_recepcao_id` (`factura_recepcao_id`),
          KEY `paciente` (`paciente`),
          KEY `empresa_id` (`empresa_id`),
          KEY `usuario` (`usuario`),
          KEY `serie` (`serie`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ",
    
    'dv_servicos_fact' => "
        CREATE TABLE IF NOT EXISTS `dv_servicos_fact` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `servico` int(11) NOT NULL,
          `qtd` int(11) NOT NULL,
          `preco` decimal(10,2) NOT NULL,
          `total` decimal(10,2) NOT NULL,
          `user` int(11) NOT NULL,
          `devolucao_id` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `servico` (`servico`),
          KEY `devolucao_id` (`devolucao_id`),
          KEY `user` (`user`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    "
];

foreach($tabelas_dv as $nome_tabela => $sql_create) {
    // Verificar se a tabela existe
    $check_table = "SHOW TABLES LIKE '$nome_tabela'";
    $table_exists = mysqli_query($db, $check_table);
    
    if($table_exists && mysqli_num_rows($table_exists) > 0) {
        echo "✓ Tabela '$nome_tabela' já existe\n";
    } else {
        echo "✗ Tabela '$nome_tabela' não existe. Criando...\n";
        if(mysqli_query($db, $sql_create)) {
            echo "  ✓ Tabela '$nome_tabela' criada com sucesso!\n";
        } else {
            echo "  ✗ Erro ao criar tabela '$nome_tabela': " . mysqli_error($db) . "\n";
        }
    }
}

echo "\n";
echo "========================================\n";
echo "Processo concluído!\n";
echo "========================================\n";
echo "</pre>";
echo "<p><a href='../dv_recepcao.php'>Voltar para tela de Devolução</a></p>";
?>

