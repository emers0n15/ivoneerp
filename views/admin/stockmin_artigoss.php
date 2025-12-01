<?php 
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("location:../../");
    exit();
}

include '../../conexao/index.php';

header('Content-Type: application/json');

$sql = "SELECT produto.idproduto, produto.nomeproduto, stock.quantidade, stock.lote, stock.prazo
        FROM produto 
        INNER JOIN stock ON produto.idproduto = stock.produto_id
        WHERE stock.quantidade <= produto.stock_min";

$result = mysqli_query($db, $sql);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode(["data" => $data]);
?>
