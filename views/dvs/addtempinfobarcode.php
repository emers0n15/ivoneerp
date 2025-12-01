<?php 
session_start();
include_once '../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');
$data_hora = date("Y-m-d H:i:s");

/*Variaveis do Sistema*/
/*********************************************/
$userID = $_SESSION['idUsuario'] ?? null;
$userNOME = $_SESSION['nomeUsuario'] ?? null;
$userCATE = $_SESSION['categoriaUsuario'] ?? null;
/*********************************************/

if (!isset($_POST['codbar'])) {
    echo json_encode(array("error" => "ID do pedido não foi fornecido"));
    exit;
}

$codbar = $_POST['codbar'];
$status = "Aberto";

$sql = "SELECT diaperiodo FROM periodo WHERE diaperiodo = ? AND usuario = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "ss", $status, $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt); // Obter o resultado da consulta

if ($result) {
    if(mysqli_num_rows($result) > 0) {
        $sql = "SELECT * FROM pedido WHERE idpedido = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $codbar);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if(mysqli_num_rows($result) > 0) {
                $dados1 = mysqli_fetch_assoc($result); 
                // Convertendo para JSON
                echo json_encode($dados1);
            } else {
                echo json_encode(array("error" => "Nenhum pedido encontrado com este ID"));
            }
        } else {
            echo json_encode(array("error" => "Erro ao executar consulta: " . mysqli_error($db)));
        }
    } else {
        echo json_encode(array("error" => "Período não encontrado"));
    }
} else {
    echo json_encode(array("error" => "Erro ao executar consulta: " . mysqli_error($db)));
}

?>
