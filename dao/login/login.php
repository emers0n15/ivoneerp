<?php 
session_start();
include_once '../../conexao/index.php';

// Verificar status da licença
$sqlLicenca = "SELECT data_inicio, data_fim, status FROM licenca WHERE status = 'ativa'";
$resultLicenca = mysqli_query($db, $sqlLicenca);
$licenca = mysqli_fetch_array($resultLicenca);

$dataAtual = date("Y-m-d");

if (!$licenca) {
    echo "<script>alert('Licença não configurada. Entre em contato com o suporte.')</script>";
    echo "<script>window.location.href='../../'</script>";
    exit;
} elseif ($dataAtual > $licenca['data_fim']) {
    echo "<script>alert('Licença expirada. Entre em contato com o suporte.')</script>";
    echo "<script>window.location.href='../../'</script>";
    exit;
}


$user = $_POST['user'];
$pass = $_POST['pass'];

if ($user == NULL || $pass == NULL) {
	echo "<script>alert('Preencha todos os campos por favor!')</script>";
	echo "<script>window.location.href='../../'</script>";
}else{
	$sql = "SELECT id, categoria, nome FROM users WHERE user = '$user' AND pass = '$pass'";
	$rs = mysqli_query($db, $sql);
	$dados = mysqli_fetch_array($rs);
	if (mysqli_num_rows($rs) > 0) {
		$_SESSION['idUsuario'] = $dados['id'];
		$_SESSION['categoriaUsuario'] = $dados['categoria'];
		$_SESSION['nomeUsuario'] = $dados['nome'];
		
		// Redirecionar baseado na categoria do usuário
		if ($dados['categoria'] == "recepcao") {
			echo "<script>window.location.href='../../views/recepcao/dashboard.php'</script>";
		} else {
		echo "<script>window.location.href='../../views/admin/'</script>";
		}
	}else{
		echo "<script>alert('Usuario ou senha incorretos!')</script>";
		echo "<script>window.location.href='../../'</script>";
	}
}
