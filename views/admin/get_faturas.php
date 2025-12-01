<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("location:../../");
}
error_reporting(E_ALL);
include '../../conexao/index.php';

// Verifica se o ID do recibo foi passado
if (isset($_GET['id_rc'])) {
    $id_rc = intval($_GET['id_rc']);

    // Consulta as faturas pagas relacionadas ao recibo
    $sql = "
        SELECT (SELECT CONCAT('FA#', f.serie, '/', f.n_doc) FROM factura AS f WHERE f.id = rc.factura) as factur, valor, iva, total, data
        FROM rc_fact as rc
        WHERE id_rc = ?
    ";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Erro de preparação: ' . $db->error]);
        exit;
    }
    
    $stmt->bind_param('i', $id_rc);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $faturas = [];
        while ($row = $result->fetch_assoc()) {
            $faturas[] = $row;
        }
        // Retorna as faturas em formato JSON
        echo json_encode($faturas);
    } else {
        echo json_encode([]); // Retorna um array vazio se não houver faturas
    }
} else {
    echo json_encode(['error' => 'ID do recibo não fornecido']);
}

?>
