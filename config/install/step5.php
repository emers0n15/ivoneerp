<?php
error_reporting(0);
$appurl = $_POST['appurl'];

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

        <p>
            <strong>Parabéns!</strong><br>
            Você acabou de instalar o iVoneERP!<br>
            <p><b>Para fazer login no Portal do Administrador:</b><p>
            Use este link -
            <?php
            $cururl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $appurl = str_replace('config/install/step5.php', '', $cururl);
            $orginal_path=str_replace('views','',$appurl);

            echo '<b><a href="' . $orginal_path .'">' . $orginal_path . '</a></b>';
            ?>
            <br>
        </p>

    </div>
</div>
<!--  contents area end  -->
</div>
<div class="footer">Copyright &copy; iProjects Sociedade Unipessoal <?php echo date("Y"); ?> Todos os direitos reservados<br/>
    <br/>
</div>
</body>
</html>