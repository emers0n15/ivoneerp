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

if (!isset($_POST['idfatura'])) {
    echo 40;
    exit;
}

$idfatura = $_POST['idfatura'];
$idfatura = intval($_POST['idfatura']);

$sqld = "DELETE FROM nc_artigos_temp WHERE user = '$userID'";
$rsd = mysqli_query($db, $sqld);
        
        $sql = "SELECT * FROM fa_artigos_fact WHERE factura = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idfatura);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            if(mysqli_num_rows($result) > 0) {
                $success = true; // Indicador de sucesso
                while ($dados1 = mysqli_fetch_array($result)) {
                    $artigo = $dados1['artigo'];
                    $qtd = $dados1['qtd'];
                    $preco = $dados1['preco'];
                    $total = $dados1['total'];
                    $iva = $dados1['iva'];
                    $lote = $dados1['lote'];
                    
                    $sql_insert = "INSERT INTO `nc_artigos_temp`( `artigo`, `qtd`, `preco`, `total`, `iva`, `user`, lote) VALUES(?,?,?,?,?,?,?)";
                    $stmt_insert = mysqli_prepare($db, $sql_insert);
                    mysqli_stmt_bind_param($stmt_insert, "iidddis", $artigo, $qtd, $preco, $total, $iva, $userID, $lote);
                    mysqli_stmt_execute($stmt_insert);
                    
                    if(mysqli_stmt_affected_rows($stmt_insert) <= 0) {
                        $success = false; // Se uma inserção falhar, definir como falso
                    }
                }
                // Verificar o sucesso após o loop
                if($success) {
                    echo 3; // Todas as inserções foram bem-sucedidas
                } else {
                    echo 31;
                    exit; // Pelo menos uma inserção falhou
                }
            } else {
                echo "Nenhum registro encontrado.";
            }
        } else {
            echo "Erro ao executar consulta: " . mysqli_error($db);
        }

?>
