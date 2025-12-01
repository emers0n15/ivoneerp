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
        <h4>iVone - Auto Instalador </h4>
        <?php
        $passed = '';
        $ltext = '';
        if (version_compare(PHP_VERSION, '5.3.9') >= 0) {
            $ltext .= 'Para correr o iVoneERP é necessário a versão 5.3.9 até 7.4.0 do PHP, Sua versão de PHP é: ' . PHP_VERSION . " Testado <strong>---PASSOU---</strong><br/>";
            $passed .= '1';

        } else {
            $ltext .= 'Para correr o iVoneERP é necessário a versão 5.3.9 até 7.4.0 do PHP, Sua versão de PHP é: ' . PHP_VERSION . " Testado <strong>---FALHOU---</strong><br/>";
            $passed .= '0';

        }


        if (extension_loaded('mysql')) {
            $ltext .= 'MySQL está disponível em seu servidor: ' . "Testado <strong>---PASSOU---</strong><br/>";
            $passed .= '1';
        } else {
            $ltext .= 'MySQL está disponível em seu servidor: ' . "Testado <strong>---FALHOU---</strong><br/>";
            $passed .= '0';

        }

        if (extension_loaded('fileinfo')) {
            $ltext .= 'Extensão php_fileinfo.dll está habilitada em seu servidor: ' . "Testado <strong>---PASSOU---</strong><br/>";
            $passed .= '1';
        } else {
            $ltext .= 'Extensão php_fileinfo.dll está habilitada em seu servidor: ' . "Testado <strong>---FALHOU---</strong><br/>";
            $passed .= '0';

        }

        if ($passed == '111') {
            echo("<br/> $ltext <br/> Excelente! Teste de Sistema Completo. Podes rodar o iVone em seu servidor. Clica em continuar para o próximo passo.
 <br><br>
 <a href=\"step3.php\" class=\"btn btn-primary\">Continuar</a> 
 ");
        } else {
            echo("<br/> $ltext <br/> Desculpe. Os requisitos do iVoneERP não estão disponíveis em seu servidor.
 Por favor contacte-nos- iprojectscompany@gmail.com com este código- $passed Ou contacte o administrador do seu servidor
  <br><br>
 <a href=\"#\" class=\"btn btn-primary disabled\">Corrija o problema para continuar</a> 
 ");
        }


        ?>
    </div>


    <!--  contents area end  -->
</div>
<div class="footer">Copyright &copy; iProjects Sociedade Unipessoal <?php echo date("Y"); ?> Todos os direitos reservados<br/>
    <br/>
</div>
</body>
</html>

