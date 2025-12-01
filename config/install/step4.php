<?php

error_reporting(0);

$db_host = $_POST['dbhost'];
$db_user = $_POST['dbuser'];
$db_password = $_POST['dbpass'];
$db_name = $_POST['dbname'];
$cn = '1';

if ($cn == '1') {
    $input1 = "<?php ";
	$input2 = "$";
	$input3 = "db";
	$input4 = " =";
	$input5 = " mysqli_connect('$db_host','$db_user','$db_password','$db_name')";
	$input6 = "?>";

    $wConfig = '../../conexao/index.php';	

	$fh = fopen($wConfig, 'w');

    if ($fh==false) {

        echo "Não é possível criar o arquivo de configuração, seu servidor não suporta a função 'fopen', forneça a permissão de sua pasta raiz ou crie um arquivo chamado - index.php com o seguinte conteúdo na pasta de configuração.";
        echo "<pre>";
        echo htmlentities($input1);
		echo htmlentities($input2);
		echo htmlentities($input3);
		echo htmlentities($input4);
		echo htmlentities($input5);
		echo htmlentities($input6);
        exit();

    }

    fwrite($fh, $input1);
	fwrite($fh, $input2);
	fwrite($fh, $input3);
	fwrite($fh, $input4);
	fwrite($fh, $input5);
	fwrite($fh, $input6);
    fclose($fh);

    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $sql = file_get_contents('db.sql');
    $qr = $dbh->exec($sql);
} else {
    header("location: step3.php?_error=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Instalador de iVoneERP</title>
	<link rel="shortcut icon" type="image/x-icon" href="../../img/ass.png"> 
    <style type="text/css">

    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>


</head>
<body style='background-color: #deead3;'>
<div class='main-container'>
    <div class='header'>
        <div class="header-box wrapper">
            <div class="hd-logo"><a href="#"><img src="../../img/config/5087847.png" width="50" height="50" alt="iVone"/></a></div>
        </div>

    </div>
    <!--  contents area start  -->
    <div class="col-lg-12">
        <h4>iVoneERP - Auto Instalador </h4>
        <?php
        if ($cn == '1') {
            $cururl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $appurl = str_replace('/config/install/step4.php', '', $cururl);
            $orginal_path=str_replace('views','',$appurl);
            ?>
            <p>
                <strong>Arquivo de configuração criado e banco de dados importado.</strong><br>
            </p>
            <form action="step5.php" method="post">
                <fieldset>
                    <legend>Configure a URL do seu aplicativo no banco de dados</legend>
                    <label>URL</label>
                    <input type='text' name="appurl" class="form-control" value="<?php echo $orginal_path; ?>">
                    <span class='help-block'>Por favor, não edite a url acima se não tiver certeza. Basta clicar em continuar.</span>

                    <button type='submit' class='btn btn-primary'>Continuar</button>
                </fieldset>
            </form>
        <?php
        } elseif ($cn == '2') {
            ?>
            <p>
                A conexão do MySQL foi bem-sucedida. 
            </p>

        <?php
        } else {
            ?>
    <p> Falha na conexão do MySQL contacte iprojectscompany@gmail.com ou contacte o seu administrador local. </p>
        <?php

        }
        ?>
    </div>
</div>
<!--  contents area end  -->
</div>
<div class="footer">Copyright &copy; iProjects Sociedade Unipessoal <?php echo date("Y"); ?> Todos os direitos reservados<br/>
    <br/>
</div>
</body>
</html>

