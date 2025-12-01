<?php
// config/conexao.php

$dbHost = 'localhost';            // Host do MySQL
$dbUser = 'crbhlspv_ivoneerp';    // Usuário do banco
$dbPass = '@Sinaboy123**@';       // Senha do usuário
$dbName = 'crbhlspv_ivoneerp';    // Nome do banco

// Conectar ao banco
$mysqli = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Checar conexão
if (!$mysqli) {
    die("<h3 style='text-align:center;margin-top:50px;'>Não é possível se conectar ao banco de dados</h3>");
}

// Definir charset para evitar problemas com acentuação
mysqli_set_charset($mysqli, "utf8mb4");
?>
