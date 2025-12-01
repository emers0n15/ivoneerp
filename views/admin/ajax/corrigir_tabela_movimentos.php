<?php
session_start();
include_once '../../../conexao/index.php';

// Definir a codificação UTF-8 para garantir que os caracteres especiais sejam tratados corretamente
mysqli_set_charset($db, "utf8");

if (!isset($_SESSION['idUsuario'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Função para verificar se a tabela existe
function tableExists($db, $tableName) {
    $query = "SHOW TABLES LIKE '$tableName'";
    $result = mysqli_query($db, $query);
    
    if (!$result) {
        return false;
    }
    
    return mysqli_num_rows($result) > 0;
}

// Verifica se a tabela armazem_movimentos existe
$tableExists = tableExists($db, 'armazem_movimentos');

if (!$tableExists) {
    // Criar a tabela se não existir
    $createTableQuery = "CREATE TABLE armazem_movimentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        armazem_id INT NOT NULL,
        stock_id INT NOT NULL,
        produto_id INT NOT NULL,
        tipo_movimento ENUM('entrada', 'saida', 'transferencia', 'ajuste') NOT NULL,
        quantidade DECIMAL(10,2) NOT NULL,
        usuario_id INT NOT NULL,
        data_movimento DATETIME NOT NULL,
        observacao TEXT,
        INDEX idx_armazem (armazem_id),
        INDEX idx_stock (stock_id),
        INDEX idx_produto (produto_id),
        INDEX idx_usuario (usuario_id),
        INDEX idx_data (data_movimento),
        INDEX idx_tipo (tipo_movimento),
        FOREIGN KEY (armazem_id) REFERENCES armazem(id) ON DELETE RESTRICT,
        FOREIGN KEY (produto_id) REFERENCES produto(idproduto) ON DELETE RESTRICT,
        FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $createResult = mysqli_query($db, $createTableQuery);
    
    if (!$createResult) {
        // Se falhar com chaves estrangeiras, tentar novamente sem elas
        $createSimpleTableQuery = "CREATE TABLE armazem_movimentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            armazem_id INT NOT NULL,
            stock_id INT NOT NULL,
            produto_id INT NOT NULL,
            tipo_movimento ENUM('entrada', 'saida', 'transferencia', 'ajuste') NOT NULL,
            quantidade DECIMAL(10,2) NOT NULL,
            usuario_id INT NOT NULL,
            data_movimento DATETIME NOT NULL,
            observacao TEXT,
            INDEX idx_armazem (armazem_id),
            INDEX idx_stock (stock_id),
            INDEX idx_produto (produto_id),
            INDEX idx_usuario (usuario_id),
            INDEX idx_data (data_movimento),
            INDEX idx_tipo (tipo_movimento)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $createSimpleResult = mysqli_query($db, $createSimpleTableQuery);
        
        if (!$createSimpleResult) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'error',
                'message' => 'Erro ao criar tabela armazem_movimentos: ' . mysqli_error($db)
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'message' => 'Tabela armazem_movimentos criada com sucesso!'
    ], JSON_UNESCAPED_UNICODE);
    exit;
} else {
    // Verificar e corrigir a estrutura da tabela se necessário
    $checkColumnsQuery = "SHOW COLUMNS FROM armazem_movimentos";
    $columnsResult = mysqli_query($db, $checkColumnsQuery);
    
    if (!$columnsResult) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao verificar colunas: ' . mysqli_error($db)
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $existingColumns = [];
    while ($column = mysqli_fetch_assoc($columnsResult)) {
        $existingColumns[] = $column['Field'];
    }
    
    $requiredColumns = [
        'id', 'armazem_id', 'stock_id', 'produto_id', 'tipo_movimento', 
        'quantidade', 'usuario_id', 'data_movimento', 'observacao'
    ];
    
    $missingColumns = array_diff($requiredColumns, $existingColumns);
    
    if (!empty($missingColumns)) {
        $alterQueries = [];
        
        foreach ($missingColumns as $column) {
            switch ($column) {
                case 'armazem_id':
                    $alterQueries[] = "ADD COLUMN armazem_id INT NOT NULL AFTER id";
                    break;
                case 'stock_id':
                    $alterQueries[] = "ADD COLUMN stock_id INT NOT NULL AFTER armazem_id";
                    break;
                case 'produto_id':
                    $alterQueries[] = "ADD COLUMN produto_id INT NOT NULL AFTER stock_id";
                    break;
                case 'tipo_movimento':
                    $alterQueries[] = "ADD COLUMN tipo_movimento ENUM('entrada', 'saida', 'transferencia', 'ajuste') NOT NULL AFTER produto_id";
                    break;
                case 'quantidade':
                    $alterQueries[] = "ADD COLUMN quantidade DECIMAL(10,2) NOT NULL AFTER tipo_movimento";
                    break;
                case 'usuario_id':
                    $alterQueries[] = "ADD COLUMN usuario_id INT NOT NULL AFTER quantidade";
                    break;
                case 'data_movimento':
                    $alterQueries[] = "ADD COLUMN data_movimento DATETIME NOT NULL AFTER usuario_id";
                    break;
                case 'observacao':
                    $alterQueries[] = "ADD COLUMN observacao TEXT AFTER data_movimento";
                    break;
            }
        }
        
        if (!empty($alterQueries)) {
            $alterTableQuery = "ALTER TABLE armazem_movimentos " . implode(", ", $alterQueries);
            $alterResult = mysqli_query($db, $alterTableQuery);
            
            if (!$alterResult) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao alterar tabela: ' . mysqli_error($db),
                    'query' => $alterTableQuery
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    }
    
    // Adicionar índices se faltarem
    $addIndexes = [];
    
    $checkIndexesQuery = "SHOW INDEX FROM armazem_movimentos";
    $indexesResult = mysqli_query($db, $checkIndexesQuery);
    
    if ($indexesResult) {
        $existingIndexes = [];
        while ($index = mysqli_fetch_assoc($indexesResult)) {
            $existingIndexes[] = $index['Key_name'];
        }
        
        $requiredIndexes = [
            'idx_armazem', 'idx_stock', 'idx_produto', 'idx_usuario', 'idx_data', 'idx_tipo'
        ];
        
        foreach ($requiredIndexes as $index) {
            if (!in_array($index, $existingIndexes)) {
                switch ($index) {
                    case 'idx_armazem':
                        $addIndexes[] = "ADD INDEX idx_armazem (armazem_id)";
                        break;
                    case 'idx_stock':
                        $addIndexes[] = "ADD INDEX idx_stock (stock_id)";
                        break;
                    case 'idx_produto':
                        $addIndexes[] = "ADD INDEX idx_produto (produto_id)";
                        break;
                    case 'idx_usuario':
                        $addIndexes[] = "ADD INDEX idx_usuario (usuario_id)";
                        break;
                    case 'idx_data':
                        $addIndexes[] = "ADD INDEX idx_data (data_movimento)";
                        break;
                    case 'idx_tipo':
                        $addIndexes[] = "ADD INDEX idx_tipo (tipo_movimento)";
                        break;
                }
            }
        }
    }
    
    if (!empty($addIndexes)) {
        $alterIndexQuery = "ALTER TABLE armazem_movimentos " . implode(", ", $addIndexes);
        mysqli_query($db, $alterIndexQuery);
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'success',
        'message' => 'Tabela armazem_movimentos verificada e corrigida',
        'missing_columns' => $missingColumns,
        'added_indexes' => $addIndexes
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
