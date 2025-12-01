<?php
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 

// Query SQL para recuperar os dados
$sql = "SELECT
            YEAR(dataa) AS ano,
            MONTH(dataa) AS mes,
            statuss,
            SUM(valor) AS total
        FROM
            factura
        GROUP BY
            ano, mes, statuss
        ORDER BY
            ano, mes, statuss";

$result = mysqli_query($db, $sql);

// Inicializa arrays para armazenar os dados
$meses = [];
$pendentes = [];
$pagas = [];

if (mysqli_num_rows($result)) {
    while ($row = mysqli_fetch_array($result)) {
        $ano_mes = $row["ano"] . "-" . str_pad($row["mes"], 2, "0", STR_PAD_LEFT);
        array_push($meses, $ano_mes);
        if ($row["statuss"] == 0) {
            array_push($pendentes, $row["total"]);
            array_push($pagas, 0);
        } else {
            array_push($pagas, $row["total"]);
            array_push($pendentes, 0);
        }
    }
}

$db->close();
?>