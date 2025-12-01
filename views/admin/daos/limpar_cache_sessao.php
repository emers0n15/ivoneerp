<?php
// Arquivo para limpar o cache da sessão relacionado aos produtos com estoque baixo
session_start();
header('Content-Type: application/json');

// Registrar o que será limpo
$itens_limpos = [];

// Limpar variáveis de sessão relacionadas aos produtos com estoque baixo
if (isset($_SESSION['produtos_consumo_baixo'])) {
    $itens_limpos[] = 'produtos_consumo_baixo';
    unset($_SESSION['produtos_consumo_baixo']);
}

if (isset($_SESSION['total_produtos_consumo_baixo'])) {
    $itens_limpos[] = 'total_produtos_consumo_baixo';
    unset($_SESSION['total_produtos_consumo_baixo']);
}

if (isset($_SESSION['total_produtos_criticos'])) {
    $itens_limpos[] = 'total_produtos_criticos';
    unset($_SESSION['total_produtos_criticos']);
}

if (isset($_SESSION['timestamp_produtos_consumo_baixo'])) {
    $itens_limpos[] = 'timestamp_produtos_consumo_baixo';
    unset($_SESSION['timestamp_produtos_consumo_baixo']);
}

// Responder com sucesso
echo json_encode([
    'status' => 'sucesso',
    'mensagem' => 'Cache limpo com sucesso',
    'itens_limpos' => $itens_limpos,
    'timestamp' => time()
]);
?>
