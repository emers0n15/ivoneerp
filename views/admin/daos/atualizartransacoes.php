<?php 
session_start();
error_reporting(E_ALL);
include '../../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');



	$sql = "SELECT *, (SELECT CONCAT(nome,' ',apelido) FROM clientes as p WHERE p.id = p.cliente) as cl FROM transacoes as p WHERE cliente != 0 ORDER BY p.id DESC LIMIT 5";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
		while ($dados = mysqli_fetch_array($rs)) {
			$debito = $dados['debito'];
			$credito = $dados['credito'];
	?>
	<tr>
	<td style="min-width: 200px;">
		<a class="avatar" ><i class="fa fa-file"></i> </a>
		<h2><a><?php echo $dados['doc']; ?> <span><?php echo $dados['serie']; ?>/<?php echo $dados['n_doc']; ?></span></a></h2>
	</td>                 
	<td>
		<h5 class="time-title p-0">Cliente</h5>
		<p><?php echo $dados['cl']; ?></p>
	</td>
	<td>
		<h5 class="time-title p-0">Valor</h5>
		<p>
			<?php 
				if ($debito != NULL) {
					echo "".number_format($debito,2,".",",");
				}else if ($credito != NULL) {
					echo "".number_format($credito,2,".",",");
				}
				?>
		</p>
	</td>									
</tr>
<?php
	}
?>