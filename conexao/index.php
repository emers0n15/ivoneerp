<?php
// config/conexao.php

$dbHost = 'localhost';            // Host do MySQL
$dbUser = 'crbhlspv_ivoneerptest';    // Usuário do banco
$dbPass = '@Sinaboy123**@';       // Senha do usuário
$dbName = 'crbhlspv_ivoneerptest';    // Nome do banco

// Conectar ao banco
$db = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Checar conexão
if (!$db) {
    die("<h3 style='text-align:center;margin-top:50px;'>Não é possível se conectar ao banco de dados</h3>");
}

// Definir charset para evitar problemas com acentuação
mysqli_set_charset($db, "utf8mb4");
?>
