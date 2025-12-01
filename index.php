<?php 
$install = file_exists(__DIR__ . '/conexao/index.php');

if ($install == false) {

    header("location:config/install/index.php");

}else{
	include_once 'conexao/index.php';
}
?>


<!DOCTYPE html>
<html>
<head>
	<title>iVone ERP - Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width-device-width, initial-scale=1">
	<link rel="shorcut icon"  href="img/config/iCone.png">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
</head>
<body>
<div class="principal">
	<div class="esquerda" >
		
	</div>
	<div class="direita">
		<div class="dentro">
			<img src="img/ERP.png" style="max-width: 100%;width: 40%;">
		</div>
		<?php
			// Verificar licença
			$sqlLicenca = "SELECT data_fim FROM licenca WHERE status = 'ativa'";
			$resultLicenca = mysqli_query($db, $sqlLicenca);
			$licenca = mysqli_fetch_array($resultLicenca);
			$dataAtual = date("Y-m-d");

			if (mysqli_num_rows($resultLicenca) > 0) {
				if ($dataAtual > $licenca['data_fim']) {
				    die("<h1>Licença Expirada. Contate o Suporte.</h1>");
				}
			}else{
				die("<h1>Licença inativa ou não configurada. Contate o Suporte.</h1>");
			}

			
		?>
		<?php 
			// Consulta o status da licença
			$sql = "SELECT data_fim, status FROM licenca ORDER BY id DESC LIMIT 1";
			$result = mysqli_query($db, $sql);

			if ($result && mysqli_num_rows($result) > 0) {
			    $licenca = mysqli_fetch_assoc($result);
			    $dataFim = $licenca['data_fim'];
			    $status = $licenca['status'];

			    $dataAtual = date('Y-m-d');
			    $diasRestantes = (strtotime($dataFim) - strtotime($dataAtual)) / (60 * 60 * 24); // Calcula a diferença em dias

			    // Valida a licença
			    if ($status !== 'ativa') {
			        echo "<p style='color: red;'>Licença Expirada. Contate o Suporte.</p>";
			        exit;
			    } elseif ($diasRestantes <= 15 && $diasRestantes > 0) {
			        echo "<p style='color: orange;'>Atenção: Sua licença expira em $diasRestantes dias. Renove para evitar interrupções.</p>";
			    } elseif ($diasRestantes <= 0) {
			        echo "<p style='color: red;'>Licença Expirada. Contate o Suporte.</p>";
			        exit;
			    }
			} else {
			    echo "<p style='color: red;'>Nenhuma licença configurada. Contate o suporte.</p>";
			    exit;
			}
		?>
		<form class="form" method="POST" action="dao/login/login.php">
			<select name="user" id="user" style="border: 1px solid #268FC1;width: 95%;">
				<option>Selecionar usuário</option>
				<?php 
					$sql = "SELECT * FROM users";
					$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
					while($dados = mysqli_fetch_array($rs)){
				?>
					<option value="<?php echo $dados['user'] ?>"><?php echo $dados['nome'] ?></option>
				<?php
					}
				?>
			</select>
			<input type="password" id="pass" name="pass" style="border: 1px solid #268FC1;width: 95%;">
			<br>
			<div class="parte">
				<div class="btns">
					<button class="botao" type="button">
						<a>7</a>
					</button>
					<button class="botao" type="button">
						<a>8</a>
					</button>
					<button class="botao" type="button">
						<a>9</a>
					</button>
					<button class="botao" type="button">
						<a>4</a>
					</button>
					<button class="botao" type="button">
						<a>5</a>
					</button>
					<button class="botao" type="button">
						<a>6</a>
					</button>
					<button class="botao" type="button">
						<a>1</a>
					</button>
					<button class="botao" type="button">
						<a>2</a>
					</button>
					<button class="botao" type="button">
						<a>3</a>
					</button>
					<button class="botao" type="button">
						<a>0</a>
					</button>
				</div>
				<div class="submt">
					<button type="reset">
						C
					</button>
					<button type="submit">
						Entrar
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$(".botao").on('click', function() {
			var valor = $(this).children().text();
			var existe = $("#pass").val();
			var ja = existe+""+valor;
			$("#pass").val(ja);
		});
		
		$("img").click(function() {
			openFullscreen();
		});
		/* Get the documentElement (<html>) to display the page in fullscreen */
		var elem = document.documentElement;

		/* View in fullscreen */
		function openFullscreen() {
		  if (elem.requestFullscreen) {
		    elem.requestFullscreen();
		  } else if (elem.webkitRequestFullscreen) { /* Safari */
		    elem.webkitRequestFullscreen();
		  } else if (elem.msRequestFullscreen) { /* IE11 */
		    elem.msRequestFullscreen();
		  }
		}

		/* Close fullscreen */
		function closeFullscreen() {
		  if (document.exitFullscreen) {
		    document.exitFullscreen();
		  } else if (document.webkitExitFullscreen) { /* Safari */
		    document.webkitExitFullscreen();
		  } else if (document.msExitFullscreen) { /* IE11 */
		    document.msExitFullscreen();
		  }
		}
	});
</script>
</body>
</html>