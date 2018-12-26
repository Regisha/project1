<?php
	header("Content-Type:text/html;charset=utf-8");
	setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
	$time_start = microtime(true);
	error_reporting(E_ALL);

	include_once('config.php');
	define('ABSOLUTE__PATH__',$DOCUMENR_ROOT);
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/functions.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/mysql.php');
	
	session_start();

	if (isset($_SESSION['__PANEL__BOARD__']))
	{
	$link = (isset($_GET['login']) AND !empty($_GET['login'])) ?base64_decode($_GET['login']):'rupka.php?page=dashboard_1';
	die ("<meta http-equiv=refresh content='0; url=".$link."'>");
	}
	
	if 	(
		(isset($_SESSION['spam'])) &&
		($_SESSION['spam'] >= 3) and
		(!isset($_SESSION['mktime']))
		)
	{
	$_SESSION['mktime'] = time() + (60 * rand(1,10));
	}
	
	

	$numbers_arrs = array 	(
							1 => 'oдин',
							2 => 'двa',
							3 => 'три',
							4 => 'четырe',
							5 => 'пять',
							6 => 'шeсть',
							7 => 'сeмь',
							8 => 'вoсeмь',
							9 => 'дeвять'
							);

	$numbers_arrs_flip = array_flip($numbers_arrs);

	$matematic_arr =array	(
							1 => '+прибaвить',
							2 => '-oтнять'
							);

	$secret_code = $numbers_arrs[rand(7,9)].' '.$matematic_arr[rand(1,2)].' '.$numbers_arrs[rand(1,6)];
	
	$_SESSION['addform']['captcha'] = empty($_SESSION['addform']['captcha']) ? $secret_code : $_SESSION['addform']['captcha'];	

	if (isset($_SESSION['addform']['capcha_count_time']) AND time() > $_SESSION['addform']['capcha_count_time'])
	{
	unset($_SESSION['addform']);
	die ("<meta http-equiv=refresh content='0; url=?login'>");
	}	
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MigomCRM | Авторизация</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">

	<?php
	if(isset($_POST['Submit_login']))
	{
		if (isset($_SESSION['antispam']))
		{
		$_SESSION['addform']['time'] = time();	
		//--А-Н-Т-И-С-П-А-М--проверка кода--
		$captcha = trim($_POST['send_captcha']);

		$exp = explode (' ',$_SESSION['addform']['captcha']);
		$exp[1] = ($exp[1] == '+прибaвить') ? "+" : "-";

		$sum = ($exp[1] == '+') ? ($numbers_arrs_flip[$exp[0]] + $numbers_arrs_flip[$exp[2]]) : ($numbers_arrs_flip[$exp[0]] - $numbers_arrs_flip[$exp[2]]);

			if (trim($sum) !== $captcha)
			{
			$_SESSION['addform']['captcha'] = '';
			$_SESSION['addform']['captcha_count'] = isset($_SESSION['addform']['captcha_count']) ? $_SESSION['addform']['captcha_count']+1:1;
			$_SESSION['spam'] = isset($_SESSION['spam']) ? $_SESSION['spam'] + 1 : 1;
			exit ('<div class="alert alert-danger col-12 text-center" role="alert">
			<h3>Введенный ответ не верен.</h3>
			<p>(Правильный ответ: '.$sum.', а Вы ввели:'.$captcha.')</p>
			<p align="center" style="color:red;">Осталось <b>'. (3 - $_SESSION['spam']) . '</b> попыток ввести правильную капчу.<br />После чего вы будете заблокированны.</p>
			<a href="#" class="btn btn-lg btn-primary" onclick="history.back(); return false;">Вернуться назад</a></div>');
			}
		}
							
		$mail = mysqli_real_escape_string($mysql,trim($_POST['mail']));	
		$SQLs = "SELECT * FROM CRM_worker WHERE mail='". $mail ."' LIMIT 1";
		$result = mysqli_query($mysql, $SQLs) or  die(mysqli_error($mysql).' - CRM_worker_ENTER');
		$usr = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		
		if (!isset($usr['id']))
		{
		?>
		<div class="alert alert-dismissible alert-danger">
			<h3 class="text-center text-danger">Ошибка</h3>
			<strong>Введенный логин не существует</strong><br /> 
		
			<p class="text-center">
				<input type="button" value=" Вернуться " onclick="location.href = 'http://<?php echo $HTTP_HOST; ?>/kpp.php<?php echo isset($_POST['referer'])?'&login='.$_POST['referer']:''; ?>';return false;" class="btn btn-primary btn-sm"/>
			</p>
		</div>
		<div class="clearfix"></div>
		<?php
		$_SESSION['antispam'] = true;
		exit();
		}
		else
			{
			$pass = mysqli_real_escape_string($mysql,md5($_POST['pasw']));
				if ($pass !== $usr['pass'])
				{
				?>
				<div class="alert alert-dismissible alert-danger">
					<h3 class="text-center text-danger">Ошибка</h3>
					<strong>Введенный пароль не верен.</strong><br /> 
				
					<p class="text-center">
						<input type="button" value=" Вернуться " onclick="location.href = 'http://<?php echo $HTTP_HOST; ?>/kpp.php';return false;" class="btn btn-primary btn-sm"/>
					</p>
				</div>
				<div class="clearfix"></div>
				<?php
				$_SESSION['spam'] = isset($_SESSION['spam']) ? $_SESSION['spam'] + 1 : 1;
				$_SESSION['antispam'] = true;
				exit();
				}
			unset($_SESSION['antispam'],$_SESSION['spam'],$_SESSION['addform'],$_SESSION['mktime']);
			$_SESSION['__PANEL__BOARD__'] = $usr['id'];
			$link = isset($_POST['referer'])?base64_decode($_POST['referer']):'rupka.php?page=dashboard_1';
			die ("<meta http-equiv=refresh content='0; url=".$link."'>");
			}
	}
	
	if (isset($_SESSION['addform']['captcha_count']) AND $_SESSION['addform']['captcha_count'] >= 3)
	{
	$_SESSION['addform']['capcha_count_time'] = time()+(mt_rand(1, 5)*3600);
	exit ('<div class="alert alert-danger col-12 text-center" role="alert">
	<h3>Вы ввели неправильно три раза подряд капчу! Вы БОТ!</h3>
	<h4>или таких логина и пароля не существует</h4>
	<p>Система автоматически Вас заблокировала до '.date('d.m.Y H:i',$_SESSION['addform']['capcha_count_time']).'</p>
	</div>');	
	}	
	
	if 	((isset($_SESSION['mktime'])) &&  ($_SESSION['mktime'] > time()))
	{
	?>
	<div class='alert alert-danger col-12 text-center' role='alert'>
		<strong>Вы исчерпали все попытки ввести правильные логин и пароль.<br/> Вы заблокированны до <?php echo date("H:i",$_SESSION['mktime'])." ".date("d-m-Y",$_SESSION['mktime']); ?></strong><br />
		<a class='btn btn-primary' href='http://<?php echo $HTTP_HOST; ?>'>&larr; На сайт.</a>
	</div>
	<?php
	exit();
	}	
	
	if 	(isset($_GET['login']))
	{
	echo "
	<div class='alert alert-dismissible alert-warning text-center'>
		<strong>Вы не авторизированны, пожалуйста авторизируйтесь.</strong>
	</div>";
	}

	if 	(
		(isset($_SESSION['spam'])) &&
		($_SESSION['spam'] >= 3) and
		(!isset($_SESSION['mktime']))
		)
	{
	$_SESSION['mktime'] = time() + (3600 * rand(1,5));
	}
	?>
	
            <div>

                <h1 class="logo-name text-center hidden-xs"><?php echo $settings_crm_name; ?></h1>
				<div class="col-lg-4 col-lg-offset-4"><img class="center-block img-responsive" src="<?php echo $settings_crm_logo; ?>" alt=""></div>
            </div>	

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>

            <h3><?php echo $settings_crm_about; ?></h3>
            <p>Укажите свои @-mail и пароль</p>
            <form class="m-t" role="form" action="" method="post">
                <div class="form-group">
                    <input name="mail" type="text" class="form-control" placeholder="электронка @-mail" autocomplete="off" required="">
                </div>
                <div class="form-group">
                    <input name="pasw" type="password" class="form-control" placeholder="Пароль" autocomplete="off" required="">
                </div>
				<?php
				if (isset($_SESSION['antispam']))
				{
				?>
				<div class="form-group alert alert-warning text-danger">
					<div class="form-group">
						<label class="form-label">Секретный вопрос*</label>
						<h4 class="text-center text-danger"><strong><?php echo $_SESSION['addform']['captcha']; ?></strong></h4>
						<input class="form-control" id="send_captcha" name="send_captcha" type="text" value="" placeholder="только цифра" required/>
						<span class="help-block small">Впишите только цифру</span>
					</div> 
				</div>
				<?php
				}
				?>   
				<?php echo (isset($_GET['login']) AND !empty($_GET['login']))?'<input name="referer" type="hidden" value="'.trim($_GET['login']).'">':'';?>
                <button name="Submit_login" type="submit" class="btn btn-primary block full-width m-b">Вход</button>
            </form>

        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
