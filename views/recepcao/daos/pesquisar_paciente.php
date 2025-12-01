<?php
session_start();
include '../../../conexao/index.php';

if (isset($_POST['termo'])) {
    $termo = mysqli_real_escape_string($db, $_POST['termo']);
    
    $sql = "SELECT p.id, p.nome, p.apelido, p.numero_processo, p.contacto, p.documento_numero, 
                   p.empresa_id, e.nome as empresa_nome
            FROM pacientes p
            LEFT JOIN empresas_seguros e ON p.empresa_id = e.id
            WHERE p.ativo = 1 AND (
                p.nome LIKE '%$termo%' OR 
                p.apelido LIKE '%$termo%' OR 
                p.numero_processo LIKE '%$termo%' OR 
                p.contacto LIKE '%$termo%' OR
                p.documento_numero LIKE '%$termo%'
            )
            ORDER BY p.nome, p.apelido
            LIMIT 10";
    
    $rs = mysqli_query($db, $sql);
    $pacientes = array();
    
    while ($row = mysqli_fetch_assoc($rs)) {
        $pacientes[] = $row;
    }
    
    echo json_encode($pacientes);
}
?>

