<?php
/**
 * Script para criar as tabelas do módulo de recepção
 * Execute este arquivo uma vez para criar todas as tabelas necessárias
 */

session_start();
if(!isset($_SESSION['idUsuario'])){
	header("location:../../");
	exit;
}

include '../../conexao/index.php';

// Verificar se o usuário tem permissão
if($_SESSION['categoriaUsuario'] != "recepcao" && $_SESSION['categoriaUsuario'] != "admin"){
	header("location:../admin/");
	exit;
}

$erros = array();
$sucessos = array();

// Ler o arquivo SQL
$sql_file = __DIR__ . '/sql/create_tables_recepcao.sql';
if(!file_exists($sql_file)){
	$erros[] = "Arquivo SQL não encontrado: $sql_file";
} else {
	$sql_content = file_get_contents($sql_file);
	
	// Remover comentários e dividir em comandos
	$sql_content = preg_replace('/--.*$/m', '', $sql_content);
	$sql_content = preg_replace('/\/\*.*?\*\//s', '', $sql_content);
	
	// Dividir por ponto e vírgula, mas manter dentro de strings
	$commands = array();
	$current = '';
	$in_string = false;
	$string_char = '';
	
	for($i = 0; $i < strlen($sql_content); $i++){
		$char = $sql_content[$i];
		
		if(!$in_string && ($char == '"' || $char == "'")){
			$in_string = true;
			$string_char = $char;
		} elseif($in_string && $char == $string_char && ($i == 0 || $sql_content[$i-1] != '\\')){
			$in_string = false;
			$string_char = '';
		}
		
		$current .= $char;
		
		if(!$in_string && $char == ';'){
			$command = trim($current);
			if(strlen($command) > 5){
				$commands[] = $command;
			}
			$current = '';
		}
	}
	
	// Executar cada comando
	foreach($commands as $command){
		$command = trim($command);
		if(empty($command) || strlen($command) < 10){
			continue;
		}
		
		// Pular comandos CREATE TABLE IF NOT EXISTS que já existem
		if(preg_match('/CREATE TABLE IF NOT EXISTS `(\w+)`/i', $command, $matches)){
			$table_name = $matches[1];
			$check = "SHOW TABLES LIKE '$table_name'";
			$rs_check = mysqli_query($db, $check);
			if($rs_check && mysqli_num_rows($rs_check) > 0){
				$sucessos[] = "Tabela '$table_name' já existe, pulando...";
				continue;
			}
		}
		
		// Executar comando
		if(mysqli_query($db, $command)){
			if(preg_match('/CREATE TABLE/i', $command)){
				preg_match('/`(\w+)`/i', $command, $matches);
				$table_name = isset($matches[1]) ? $matches[1] : 'tabela';
				$sucessos[] = "Tabela '$table_name' criada com sucesso!";
			} elseif(preg_match('/INSERT INTO/i', $command)){
				$sucessos[] = "Dados inseridos com sucesso!";
			}
		} else {
			$erros[] = "Erro ao executar comando: " . mysqli_error($db);
		}
	}
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Módulo de Recepção</title>
    <link rel="stylesheet" type="text/css" href="../bootstrap.min.css">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0; }
        .info { color: #004085; padding: 10px; background: #cce5ff; border: 1px solid #b3d7ff; border-radius: 4px; margin: 10px 0; }
        h1 { color: #333; }
        .btn { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Instalação das Tabelas - Módulo de Recepção</h1>
        
        <?php if(!empty($sucessos)): ?>
            <div class="info">
                <strong>Operações Realizadas:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <?php foreach($sucessos as $sucesso): ?>
                        <li><?php echo htmlspecialchars($sucesso); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if(!empty($erros)): ?>
            <div class="error">
                <strong>Erros Encontrados:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <?php foreach($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if(empty($erros) && !empty($sucessos)): ?>
            <div class="success">
                <strong>✓ Instalação concluída com sucesso!</strong>
                <p>Todas as tabelas foram criadas. Você pode agora usar o módulo de recepção.</p>
            </div>
        <?php elseif(empty($erros) && empty($sucessos)): ?>
            <div class="info">
                <strong>ℹ Todas as tabelas já existem!</strong>
                <p>As tabelas do módulo de recepção já foram criadas anteriormente.</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="dashboard.php" class="btn btn-primary">Ir para Dashboard</a>
            <a href="pacientes.php" class="btn btn-info">Ver Pacientes</a>
        </div>
        
        <div class="info" style="margin-top: 30px;">
            <strong>Nota:</strong> Se você encontrar erros, verifique:
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Se o banco de dados está acessível</li>
                <li>Se o usuário do banco tem permissões para criar tabelas</li>
                <li>Se as tabelas já não existem (neste caso, o script as ignora)</li>
            </ul>
        </div>
    </div>
</body>
</html>

