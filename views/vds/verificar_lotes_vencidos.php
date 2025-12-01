<?php
// Arquivo para verificar e marcar lotes vencidos como inativos
// Este arquivo deve ser executado sempre que a página principal de vendas for carregada
include_once '../conexao/index.php';
date_default_timezone_set('Africa/Maputo');

// Data atual
$hoje = date('Y-m-d');

// Consulta para atualizar os lotes vencidos
$sqlVerificaLotes = "UPDATE stock SET estado = 'inativo' 
                     WHERE prazo < '$hoje' 
                     AND estado = 'ativo'";

$rsVerificaLotes = mysqli_query($db, $sqlVerificaLotes);

// Verificar se a consulta foi executada com sucesso
if ($rsVerificaLotes) {
    $linhasAfetadas = mysqli_affected_rows($db);
    // Você pode usar esta variável para registros ou monitoramento, se necessário
    // echo "Lotes vencidos marcados como inativos: $linhasAfetadas";
} else {
    // Registrar erro se necessário
    // echo "Erro ao verificar lotes vencidos: " . mysqli_error($db);
}

// Fechar conexão
mysqli_close($db);
?>
