<?php 
session_start();
error_reporting(E_ALL);
include '../../../conexao/index.php';
date_default_timezone_set('Africa/Maputo');



	$sql = "SELECT *, (SELECT nome FROM users as p WHERE p.id = p.usuario) as cl FROM periodo as p ORDER BY p.idperiodo DESC LIMIT 8";
	$rs = mysqli_query($db, $sql) or die(mysqli_error($db));
		while ($dados = mysqli_fetch_array($rs)) {
			$fechoperiodo = $dados['fechoperiodo'];
	?>
	<li>
        <div class="contact-cont">
            <div class="float-left user-img m-r-10">
                <a><h1><?php echo "".number_format($fechoperiodo,2,".",","); ?></h1></a>
            </div>
            <div class="contact-info">
                <span class="contact-name text-ellipsis">Status: <?php echo $dados['diaperiodo']; ?> - Data: <?php echo $dados['datafechoperiodo']; ?></span>
                <span class="contact-date">Utilizador: <?php echo $dados['cl']; ?></span>
            </div>
        </div>
    </li>
<?php
	}
?>