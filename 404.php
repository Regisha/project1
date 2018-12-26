<?php
	header("Content-Type:text/html;charset=utf-8");
	setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
	error_reporting(E_ALL);
	include_once('config.php');
	define('ABSOLUTE__PATH__',$DOCUMENR_ROOT);
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/functions.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/mysql.php');
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $settings_crm_name; ?> | 404 Ошибка</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">


    <div class="middle-box text-center animated fadeInDown">
        <h1>404</h1>
        <h3 class="font-bold">Страница не найдена</h3>

        <div class="error-desc">
            Извините но страница, которую Вы запрашиваете не найдена.
            <br>
            
            <p>
				<a class="btn btn-sm btn-primary" href="http://<?php echo $HTTP_HOST; ?>/kpp.php"><i class="fa fa-lock"></i> Авторизация</a>
			</p>
            
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
