<?php
session_start();
include '../../../conexao/index.php';

if(!isset($_SESSION['idUsuario'])){
    header("location:../../../");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = mysqli_real_escape_string($db, $_POST['codigo']);
    $nome = mysqli_real_escape_string($db, $_POST['nome']);
    $descricao = mysqli_real_escape_string($db, $_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $categoria = mysqli_real_escape_string($db, $_POST['categoria']);
    $ativo = intval($_POST['ativo']);
    $usuario_id = $_SESSION['idUsuario'];
    
    // Verificar se é criação ou edição
    if(isset($_POST['btn_criar'])) {
        // Verificar se código já existe
        $sql_check = "SELECT id FROM servicos_clinica WHERE codigo = '$codigo'";
        $rs_check = mysqli_query($db, $sql_check);
        
        if(mysqli_num_rows($rs_check) > 0) {
            header("location:../servicos_clinica.php?error=Código já existe!");
            exit;
        }
        
        // Criar novo serviço
        $sql = "INSERT INTO servicos_clinica (codigo, nome, descricao, preco, categoria, ativo, usuario_criacao) 
                VALUES ('$codigo', '$nome', '$descricao', $preco, '$categoria', $ativo, $usuario_id)";
        
        if(mysqli_query($db, $sql)) {
            header("location:../servicos_clinica.php?success=Serviço criado com sucesso!");
        } else {
            header("location:../servicos_clinica.php?error=Erro ao criar serviço: " . mysqli_error($db));
        }
        
    } elseif(isset($_POST['btn_editar'])) {
        $id = intval($_POST['id']);
        
        // Verificar se código já existe em outro registro
        $sql_check = "SELECT id FROM servicos_clinica WHERE codigo = '$codigo' AND id != $id";
        $rs_check = mysqli_query($db, $sql_check);
        
        if(mysqli_num_rows($rs_check) > 0) {
            header("location:../servicos_clinica.php?error=Código já existe em outro serviço!");
            exit;
        }
        
        // Atualizar serviço
        $sql = "UPDATE servicos_clinica SET 
                codigo = '$codigo',
                nome = '$nome',
                descricao = '$descricao',
                preco = $preco,
                categoria = '$categoria',
                ativo = $ativo
                WHERE id = $id";
        
        if(mysqli_query($db, $sql)) {
            header("location:../servicos_clinica.php?success=Serviço atualizado com sucesso!");
        } else {
            header("location:../servicos_clinica.php?error=Erro ao atualizar serviço: " . mysqli_error($db));
        }
    }
} else {
    header("location:../servicos_clinica.php");
}
?>
