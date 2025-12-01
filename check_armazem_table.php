<?php
include_once 'conexao/index.php';

// Verificar se a conexão foi bem-sucedida
if (!$db) {
    die("Erro de conexão: " . mysqli_connect_error());
}

// Verificar se a tabela armazem existe
$tabela_existe = mysqli_query($db, "SHOW TABLES LIKE 'armazem'");
if (!$tabela_existe || mysqli_num_rows($tabela_existe) == 0) {
    die("A tabela 'armazem' não existe no banco de dados!");
}

// Obter a estrutura da tabela
$estrutura = mysqli_query($db, "DESCRIBE armazem");
echo "<h3>Estrutura da tabela 'armazem':</h3>";
echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";

if ($estrutura) {
    while ($campo = mysqli_fetch_assoc($estrutura)) {
        echo "<tr>";
        echo "<td>{$campo['Field']}</td>";
        echo "<td>{$campo['Type']}</td>";
        echo "<td>{$campo['Null']}</td>";
        echo "<td>{$campo['Key']}</td>";
        echo "<td>{$campo['Default']}</td>";
        echo "<td>{$campo['Extra']}</td>";
        echo "</tr>";
    }
}
echo "</table>";

// Verificar registros na tabela
$registros = mysqli_query($db, "SELECT COUNT(*) as total FROM armazem");
$total = mysqli_fetch_assoc($registros)['total'];
echo "<p>Total de registros na tabela: $total</p>";

// Verificar se os arquivos relacionados existem
$arquivos = [
    'views/admin/ajax/listar_armazens.php',
    'views/admin/ajax/armazem_cadastrar.php',
    'views/admin/ajax/armazem_editar.php',
    'views/admin/ajax/armazem_alterar_estado.php'
];

echo "<h3>Verificação de arquivos:</h3>";
echo "<ul>";
foreach ($arquivos as $arquivo) {
    $caminho = __DIR__ . '/' . $arquivo;
    if (file_exists($caminho)) {
        echo "<li>$arquivo - <span style='color:green'>Existe</span></li>";
    } else {
        echo "<li>$arquivo - <span style='color:red'>Não existe</span></li>";
    }
}
echo "</ul>";

// Verificar se há registros com dados inconsistentes
$inconsistentes = mysqli_query($db, "SELECT * FROM armazem WHERE 
                                    nome IS NULL OR 
                                    estado IS NULL OR 
                                    estado NOT IN ('ativo', 'inativo')");
                                    
if ($inconsistentes && mysqli_num_rows($inconsistentes) > 0) {
    echo "<h3>Registros com dados inconsistentes encontrados:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Estado</th></tr>";
    
    while ($inconsistente = mysqli_fetch_assoc($inconsistentes)) {
        echo "<tr>";
        echo "<td>{$inconsistente['id']}</td>";
        echo "<td>{$inconsistente['nome']}</td>";
        echo "<td>{$inconsistente['estado']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Não foram encontrados registros com dados inconsistentes.</p>";
}

echo "<h3>Erros no JavaScript:</h3>";
echo "<p>Verifique o console do navegador para possíveis erros de JavaScript ao usar a funcionalidade de armazéns.</p>";
?>
