<?php
// Conexão com o banco de dados
include 'conexao/index.php';

echo "<h1>Criação de Tabelas para Gestão de Stock</h1>";

// Função para criar a tabela armazem_stock
function criarTabelaArmazemStock($db) {
    $sql = "CREATE TABLE IF NOT EXISTS `armazem_stock` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `armazem_id` int(11) NOT NULL,
      `produto_id` int(11) NOT NULL,
      `lote` varchar(50) NOT NULL,
      `quantidade` int(11) NOT NULL DEFAULT 0,
      `prazo` date DEFAULT NULL,
      `preco_custo` decimal(10,2) DEFAULT NULL,
      `fornecedor_id` int(11) DEFAULT NULL,
      `data_entrada` datetime NOT NULL,
      `usuario_id` int(11) NOT NULL,
      `estado` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
      PRIMARY KEY (`id`),
      KEY `fk_armazem_stock_armazem` (`armazem_id`),
      KEY `fk_armazem_stock_produto` (`produto_id`),
      KEY `fk_armazem_stock_usuario` (`usuario_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($db, $sql)) {
        echo "<p style='color:green'>Tabela armazem_stock criada com sucesso!</p>";
        return true;
    } else {
        echo "<p style='color:red'>Erro ao criar tabela armazem_stock: " . mysqli_error($db) . "</p>";
        return false;
    }
}

// Função para criar a tabela armazem_movimentos
function criarTabelaArmazemMovimentos($db) {
    $sql = "CREATE TABLE IF NOT EXISTS `armazem_movimentos` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `armazem_id` int(11) NOT NULL,
      `stock_id` int(11) NOT NULL,
      `produto_id` int(11) NOT NULL,
      `tipo_movimento` enum('entrada','saida','transferencia') NOT NULL,
      `quantidade` int(11) NOT NULL,
      `usuario_id` int(11) NOT NULL,
      `data_movimento` datetime NOT NULL,
      `observacao` text DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_armazem_movimentos_armazem` (`armazem_id`),
      KEY `fk_armazem_movimentos_stock` (`stock_id`),
      KEY `fk_armazem_movimentos_produto` (`produto_id`),
      KEY `fk_armazem_movimentos_usuario` (`usuario_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (mysqli_query($db, $sql)) {
        echo "<p style='color:green'>Tabela armazem_movimentos criada com sucesso!</p>";
        return true;
    } else {
        echo "<p style='color:red'>Erro ao criar tabela armazem_movimentos: " . mysqli_error($db) . "</p>";
        return false;
    }
}

// Verificar se as tabelas existem
$sqlArmazemStock = "SHOW TABLES LIKE 'armazem_stock'";
$resultArmazemStock = mysqli_query($db, $sqlArmazemStock);

$sqlArmazemMovimentos = "SHOW TABLES LIKE 'armazem_movimentos'";
$resultArmazemMovimentos = mysqli_query($db, $sqlArmazemMovimentos);

// Criar as tabelas se não existirem
if (mysqli_num_rows($resultArmazemStock) == 0) {
    criarTabelaArmazemStock($db);
} else {
    echo "<p style='color:blue'>A tabela armazem_stock já existe.</p>";
}

if (mysqli_num_rows($resultArmazemMovimentos) == 0) {
    criarTabelaArmazemMovimentos($db);
} else {
    echo "<p style='color:blue'>A tabela armazem_movimentos já existe.</p>";
}

// Fechar conexão
mysqli_close($db);

echo "<p><a href='views/admin/armazem_stock.php?id=1&nome=Armazem%20Chongile'>Voltar para a gestão de stock</a></p>";
?>
