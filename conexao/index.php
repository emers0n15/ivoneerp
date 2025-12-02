<?php
// config/conexao.php

// Ativa exibição de erros para qualquer arquivo que inclua esta conexão
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $dbHost = 'localhost';            // Host do MySQL
$dbUser = 'crbhlspv_ivoneerptest';    // Usuário do banco
// $dbUser = 'root';    // Usuário do banco
$dbPass = '@Sinaboy123**@';       // Senha do usuário
// $dbPass = '';       // Senha do usuário
$dbName = 'crbhlspv_ivoneerptest';    // Nome do banco
// $dbName = 'ivoneerp';    // Nome do banco

// Conectar ao banco
$db = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Checar conexão
if (!$db) {
    die("<h3 style='text-align:center;margin-top:50px;'>Não é possível se conectar ao banco de dados</h3>");
}

// Definir charset para evitar problemas com acentuação
mysqli_set_charset($db, "utf8mb4");
?>
