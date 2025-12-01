<?php
// Conexão com o banco de dados
include 'conexao/index.php';

echo "<h1>Verificação de tabelas</h1>";

// Verificar a tabela armazem_stock
$sql = "SHOW TABLES LIKE 'armazem_stock'";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<p style='color:green'>Tabela armazem_stock existe!</p>";
    
    // Verificar a estrutura da tabela
    $sql = "DESCRIBE armazem_stock";
    $result = mysqli_query($db, $sql);
    
    if ($result) {
        echo "<h2>Estrutura da tabela armazem_stock:</h2>";
        echo "<table border='1'><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p style='color:red'>Erro ao verificar estrutura da tabela: " . mysqli_error($db) . "</p>";
    }
} else {
    echo "<p style='color:red'>Tabela armazem_stock NÃO existe!</p>";
}

// Verificar a tabela fornecedor
$sql = "SHOW TABLES LIKE 'fornecedor'";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<p style='color:green'>Tabela fornecedor existe!</p>";
} else {
    echo "<p style='color:red'>Tabela fornecedor NÃO existe!</p>";
}

// Verificar a tabela fornecedores
$sql = "SHOW TABLES LIKE 'fornecedores'";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<p style='color:green'>Tabela fornecedores existe!</p>";
} else {
    echo "<p style='color:red'>Tabela fornecedores NÃO existe!</p>";
}

// Fechando a conexão
mysqli_close($db);
?>
