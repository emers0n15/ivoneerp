<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
}
include '../../conexao/index.php';

// Verificar se o usuário tem permissão de recepção
if($_SESSION['categoriaUsuario'] != "recepcao"){
	header("location:../admin/");
	exit;
}

if(!isset($_GET['id'])){
	header("location:pacientes.php");
	exit;
}

$id = intval($_GET['id']);
$sql = "SELECT p.*, e.nome as empresa_nome FROM pacientes p 
        LEFT JOIN empresas_seguros e ON p.empresa_id = e.id 
        WHERE p.id = $id";
$rs = mysqli_query($db, $sql);
$paciente = mysqli_fetch_array($rs);

if(!$paciente){
	header("location:pacientes.php");
	exit;
}

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/head.php'; ?>
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <?php include 'includes/header.php' ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php include 'includes/side_bar.php'; ?>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Editar Paciente</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="pacientes.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-arrow-left"></i> Voltar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="POST" action="daos/atualizar_paciente.php">
                            <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Número de Processo <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="numero_processo" value="<?php echo $paciente['numero_processo']; ?>" required readonly>
                                        <small class="form-text text-muted">Número de processo não pode ser alterado</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Data de Nascimento</label>
                                        <input class="form-control" type="date" name="data_nascimento" value="<?php echo $paciente['data_nascimento']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nome <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="nome" value="<?php echo $paciente['nome']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Apelido <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="apelido" value="<?php echo $paciente['apelido']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Sexo</label>
                                        <select class="form-control" name="sexo">
                                            <option value="">Selecione</option>
                                            <option value="M" <?php echo $paciente['sexo'] == 'M' ? 'selected' : ''; ?>>Masculino</option>
                                            <option value="F" <?php echo $paciente['sexo'] == 'F' ? 'selected' : ''; ?>>Feminino</option>
                                            <option value="Outro" <?php echo $paciente['sexo'] == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tipo de Documento</label>
                                        <select class="form-control" name="documento_tipo">
                                            <option value="">Selecione</option>
                                            <option value="BI" <?php echo $paciente['documento_tipo'] == 'BI' ? 'selected' : ''; ?>>BI</option>
                                            <option value="Passaporte" <?php echo $paciente['documento_tipo'] == 'Passaporte' ? 'selected' : ''; ?>>Passaporte</option>
                                            <option value="Carta de Condução" <?php echo $paciente['documento_tipo'] == 'Carta de Condução' ? 'selected' : ''; ?>>Carta de Condução</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Número do Documento</label>
                                        <input class="form-control" type="text" name="documento_numero" value="<?php echo $paciente['documento_numero']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Contacto <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="contacto" value="<?php echo $paciente['contacto']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Contacto Alternativo</label>
                                        <input class="form-control" type="text" name="contacto_alternativo" value="<?php echo $paciente['contacto_alternativo']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" value="<?php echo $paciente['email']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Empresa/Seguro</label>
                                        <select class="form-control" name="empresa_id" id="empresa_id">
                                            <option value="">Nenhuma (Particular)</option>
                                            <?php
                                            $sql_empresas = "SELECT id, nome FROM empresas_seguros WHERE ativo = 1 ORDER BY nome";
                                            $rs_empresas = mysqli_query($db, $sql_empresas);
                                            if($rs_empresas && mysqli_num_rows($rs_empresas) > 0):
                                                while($emp = mysqli_fetch_array($rs_empresas)):
                                            ?>
                                                <option value="<?php echo $emp['id']; ?>" <?php echo ($paciente['empresa_id'] == $emp['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($emp['nome']); ?>
                                                </option>
                                            <?php
                                                endwhile;
                                            endif;
                                            ?>
                                        </select>
                                        <small class="form-text text-muted">Alterar empresa registra no histórico</small>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Endereço</label>
                                        <textarea class="form-control" name="endereco" rows="2"><?php echo $paciente['endereco']; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Bairro</label>
                                        <input class="form-control" type="text" name="bairro" value="<?php echo $paciente['bairro']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input class="form-control" type="text" name="cidade" value="<?php echo $paciente['cidade']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Província</label>
                                        <input class="form-control" type="text" name="provincia" value="<?php echo $paciente['provincia']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea class="form-control" name="observacoes" rows="3"><?php echo $paciente['observacoes']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit" name="btn">Atualizar Paciente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

