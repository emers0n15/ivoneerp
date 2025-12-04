<?php
session_start();
include '../../../conexao/index.php';
header('Content-Type: application/json');

// Buscar categorias ativas
$sql = "SELECT * FROM categorias_servicos WHERE ativo = 1 ORDER BY nome";
$rs = mysqli_query($db, $sql);

$categorias = array();
if($rs && mysqli_num_rows($rs) > 0) {
    while($categoria = mysqli_fetch_array($rs)) {
        $categorias[] = array(
            'id' => intval($categoria['id']),
            'nome' => $categoria['nome'],
            'descricao' => $categoria['descricao']
        );
    }
}

echo json_encode($categorias);
?>


