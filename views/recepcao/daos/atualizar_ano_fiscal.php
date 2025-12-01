<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit;
}

include '../../../conexao/index.php';

if (isset($_POST['btn'])) {
    $ano = intval($_POST['ano']);
    
    // Validar ano
    if($ano < 2020 || $ano > 2100) {
        echo "<script>alert('Ano inválido! Por favor, digite um ano entre 2020 e 2100.');</script>";
        echo "<script>window.location='../dashboard.php';</script>";
        exit;
    }
    
    // Verificar se o ano já existe
    $sql_check = "SELECT id FROM serie_factura WHERE ano_fiscal = ?";
    $stmt_check = mysqli_prepare($db, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $ano);
    mysqli_stmt_execute($stmt_check);
    $rs_check = mysqli_stmt_get_result($stmt_check);
    
    if($rs_check && mysqli_num_rows($rs_check) > 0) {
        echo "<script>alert('Este ano fiscal já está cadastrado!');</script>";
        echo "<script>window.location='../dashboard.php';</script>";
        exit;
    }
    
    // Inserir novo ano fiscal
    $sql = "INSERT INTO serie_factura(ano_fiscal) VALUES(?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $ano);
    $rs = mysqli_stmt_execute($stmt);
    
    if ($rs) {
        echo "<script>alert('Ano fiscal atualizado com sucesso!');</script>";
        echo "<script>window.location='../ano_fiscal.php';</script>";
    } else {
        echo "<script>alert('Ocorreu um erro ao atualizar o ano fiscal. Por favor, tente novamente!');</script>";
        echo "<script>window.location='../ano_fiscal.php';</script>";
    }
} else {
    header("location:../ano_fiscal.php");
}
?>

