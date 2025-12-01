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
        if (isset($_GET['_error']) && ($_GET['_error']) == '1') {
            echo '<h4 style="color: red;"> Não foi possível conectar o banco de dados, verifique se as informações do banco de dados estão corretas e tente novamente ! </h4>';
        }
        ?>
        <?php
        $cururl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $appurl = str_replace('/install/step3.php', '', $cururl);

        ?>

        <form action="step4.php" method="post">
            <fieldset>
                <legend>Conexão de banco de dados</legend>
                <label>Host do BD</label>
                <input type='text' class="form-control" name="dbhost" required>
                <span class='help-block'>e.g. localhost</span>
                <label>Usuário do BD</label>
                <input type='text' class="form-control" name="dbuser" required>
                <label>Senha do BD</label>
                <input type='text' class="form-control" name="dbpass">
                <label>Nome do BD</label>
                <input type='text' class="form-control" name="dbname" required>
                <label>&nbsp;</label>
                <button type='submit' name='send' class='btn btn-primary btn-block'>Submeter</button>
            </fieldset>
        </form>
    </div>
</div>
<!--  contents area end  -->
</div>
<div class="footer">Copyright &copy; iProjects Sociedade Unipessoal <?php echo date("Y"); ?> Todos os direitos reservados<br/>
    <br/>
</div>
</body>
</html>

