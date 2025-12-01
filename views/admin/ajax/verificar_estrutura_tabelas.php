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

// Função para obter estrutura da tabela
function getTableStructure($db, $tableName) {
    $query = "DESCRIBE $tableName";
    $result = mysqli_query($db, $query);
    
    if (!$result) {
        return [
            'status' => 'error',
            'message' => "Erro ao obter estrutura da tabela $tableName: " . mysqli_error($db)
        ];
    }
    
    $columns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row;
    }
    
    return [
        'status' => 'success',
        'table' => $tableName,
        'columns' => $columns
    ];
}

// Obter estrutura das tabelas relevantes
$tables = [
    'armazem_movimentos',
    'armazem_stock',
    'produto',
    'usuario'
];

$results = [];
foreach ($tables as $table) {
    $results[$table] = getTableStructure($db, $table);
}

// Verificar relações de chave estrangeira
$foreignKeys = [];
$tablesWithFkQuery = "SELECT 
                        CONSTRAINT_NAME,
                        TABLE_NAME,
                        COLUMN_NAME,
                        REFERENCED_TABLE_NAME,
                        REFERENCED_COLUMN_NAME
                    FROM
                        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE
                        REFERENCED_TABLE_SCHEMA = DATABASE()
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                        AND (TABLE_NAME IN ('armazem_movimentos', 'armazem_stock')
                             OR REFERENCED_TABLE_NAME IN ('armazem_movimentos', 'armazem_stock'))";

$fkResult = mysqli_query($db, $tablesWithFkQuery);
if ($fkResult) {
    while ($row = mysqli_fetch_assoc($fkResult)) {
        $foreignKeys[] = $row;
    }
}

// Verificar estrutura de armazem_movimentos
$expectedColumns = [
    'id',
    'armazem_id',
    'stock_id',
    'produto_id',
    'tipo_movimento',
    'quantidade',
    'usuario_id',
    'data_movimento',
    'observacao'
];

$missingColumns = [];
if ($results['armazem_movimentos']['status'] === 'success') {
    $existingColumns = array_column($results['armazem_movimentos']['columns'], 'Field');
    foreach ($expectedColumns as $col) {
        if (!in_array($col, $existingColumns)) {
            $missingColumns[] = $col;
        }
    }
}

// Retornar resposta
$response = [
    'tables' => $results,
    'foreignKeys' => $foreignKeys,
    'analysis' => [
        'armazem_movimentos_missing_columns' => $missingColumns,
        'has_all_required_columns' => empty($missingColumns)
    ]
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
?>
