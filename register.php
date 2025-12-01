<?php
include_once '../../conexao/index.php';

$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];

if ($data_inicio && $data_fim) {
    $sqlUpdate = "UPDATE licenca SET status = 'inativa'";
    mysqli_query($db, $sqlUpdate);

    $sqlInsert = "INSERT INTO licenca (data_inicio, data_fim, status) VALUES ('$data_inicio', '$data_fim', 'ativa')";
    if (mysqli_query($db, $sqlInsert)) {
        echo "<script>alert('Licença salva com sucesso!')</script>";
    } else {
        echo "<script>alert('Erro ao salvar licença.')</script>";
    }
} else {
    echo "<script>alert('Preencha todos os campos.')</script>";
}
echo "<script>window.location.href=''</script>";
?>

<form method="POST" action="">
    <label for="data_inicio">Data de Início:</label>
    <input type="date" name="data_inicio" id="data_inicio" required>
    
    <label for="data_fim">Data de Fim:</label>
    <input type="date" name="data_fim" id="data_fim" required>
    
    <button type="submit">Salvar Licença</button>
</form>
