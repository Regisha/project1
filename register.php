<?php
	header("Content-Type:text/html;charset=utf-8");
	setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
	$time_start = microtime(true);
	error_reporting(E_ALL);

	include_once('config.php');
	define('ABSOLUTE__PATH__',$DOCUMENR_ROOT);
	define('__PANEL__BOARD__',true);
	
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/mysql.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/functions.php');

	session_start();
	$arr_pol = array ("Не указано","Женщина","Мужчина");
	
	mysqli_query($mysql, "CREATE TABLE IF NOT EXISTS CRM_zayavka (
	zayavka_id int auto_increment primary key,
	issled_id int(15) NOT NULL,
	dobrov_id  int(15) NOT NULL,
	status_issled int(15) NOT NULL,
	rez_ych int(15) NOT NULL,
	random varchar(15) NOT NULL,
	comment  varchar(255) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Формирование добровольцев на исследование'");

	    mysqli_query($mysql, "CREATE TABLE IF NOT EXISTS CRM_dobrov_edit (
        dobrov_id int(10) NOT NULL,
        fio varchar(150) NOT NULL,
        pol int(2) NOT NULL,
        birthday varchar(11) NOT NULL, 
        phone varchar(15) NOT NULL, 
        snils varchar(15) NOT NULL,
        vk varchar(50) NOT NULL, 
        last_ych varchar(11) NOT NULL, 
        rating varchar(5) NOT NULL, 
        block int(1) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Добровольцы'");
	
	
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>База добровольцев</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/wizardwidget.css" rel="stylesheet">
    <link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    
</head>

<body class="gray-bg">

	<?php
		// Ошибка не найден $_GET['issled']
	if (isset($_GET['message']) AND $_GET['message'] == 'err_issled')
	{
	?>
	<div class="alert alert-danger col-md-6 col-md-offset-3">
		<h3 class="text-center text-danger">Ошибка</h3>
		<p class="text-center">
			Скорее всего исследование уже закончилось.
		</p>
	</div>
	<div class="clearfix"></div>
	<?php
	exit();
	}
	$issled = intval($_GET['issled']);
	//	Ошибка не найден $_GET['step'] то рефрешним его на степ 1
	if (!isset($_GET['step']))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=1'>");
	}
	
	
	if(isset($_POST['Add_1']))
	{
	look($_POST);
	look($_GET);
	look($_SESSION);
		if (!isset($_POST['phone']) OR empty($_POST['phone']))
		{
// 		  die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=2'>");
		}
        
        $issled = intval($_POST['issled']);
        $phone = tel_replace($_POST['phone']);
        $_SESSION['form']['phone'] = $phone;
        $_SESSION['form']['issled'] = $issled;
          
			$SQL2= "SELECT * FROM CRM_dobrov WHERE phone='". $phone ."' OR phone2='". $phone ."' LIMIT 1";
			$IntSQL2=mysqli_query($mysql, $SQL2);
			if(!$IntSQL2) die (mysqli_error($mysql));
			$arr=mysqli_fetch_assoc($IntSQL2);
			mysqli_free_result($IntSQL2);
// 			echo $phone;
		
		if (isset($arr['dobrov_id'])) 
		{
		die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=2'>");
		}
		else 
			{
			die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=2&add'>");
			}
          
        
    }
          
	if(isset($_POST['Add_2']))
	{
// 	look($_POST);
// 	look($_GET);
// 	look($_SESSION);
		$issled =  $_SESSION['form']['issled'];
		$fio = mysqli_real_escape_string($mysql,trim(strip_tags($_POST['fio'])));
		$pol = intval($_POST['pol']);
		$birthday = isset($_POST['birthday'])?strtotime($_POST['birthday']):'';
		$phone = tel_replace($_POST['phone']);
		$snils = (isset($_POST['snils']) AND !empty($_POST['snils']))?str_replace('-','',$_POST['snils']):'';
		$vk =vk_linko($_POST['vk']);
		
		$status_issled =  isset($_POST['status_issled'])?intval($_POST['status_issled']):'1';
		$rez_ych =  isset($_POST['rez_ych'])?intval($_POST['rez_ych']):'1';
		$random =  isset($_POST['random'])?trim($_POST['random']):'';
		$comment = isset($_POST['comment']) ? mysqli_real_escape_string($mysql, trim($_POST['comment'])) : '';
		$rating = isset($_POST['rating'])?trim($_POST['rating']):'1';
		$povtor = isset($_POST['povtor'])?trim($_POST['povtor']):'0';
		$phone2 = isset($_POST['phone2']) ? tel_replace($_POST['phone2']) : '';
		if(isset($_POST['dobrov_id']))
		{
			$dobrov_id = intval($_POST['dobrov_id']);
			if( isset($_POST['edit_dobr']))
			{
				$query_count = "UPDATE CRM_dobrov SET edit='1' WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
				mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
				
				$queryT = "DELETE FROM CRM_dobrov_edit WHERE CRM_dobrov_edit.dobrov_id='".intval($dobrov_id)."' ";
				mysqli_query($mysql,$queryT) or die(mysqli_error($mysql));
				
				$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_dobrov_edit (dobrov_id,fio,pol,birthday,phone,snils,vk,rating) VALUES ('".$dobrov_id."','".$fio."','".$pol."','".$birthday."','".$phone."','".$snils."','".$vk."','1')");
				if(!$insertSQL) die(mysqli_error($mysql));
// 				$dobrov_id= mysqli_insert_id($mysql);
			
				// Для проверки, при редактировании
				$SQL2= "SELECT * FROM CRM_issled WHERE issled_id='".intval($_GET['issled'])."' LIMIT 1";
				$IntSQL2=mysqli_query($mysql, $SQL2);
				if(!$IntSQL2) die (mysqli_error($mysql));
				$arr=mysqli_fetch_assoc($IntSQL2);
				mysqli_free_result($IntSQL2);
				
				$SQL3= "SELECT * FROM CRM_dobrov WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
				$IntSQL3=mysqli_query($mysql, $SQL3);
				if(!$IntSQL3) die (mysqli_error($mysql));
				$arr3=mysqli_fetch_assoc($IntSQL3);
				mysqli_free_result($IntSQL3);
				
					$vozr = date('Y') - date ('Y', $arr3['birthday']);
					$month3 = (isset($arr3['last_ych']) AND !empty ($arr3['last_ych']))?($arr3['last_ych']+(86400*90)):'';
					if (($arr['pol']==$arr3['pol']) AND ($vozr >= $arr['minim_vozr']) AND ($vozr <=$arr['max_vozr']) AND ($month3>=$arr['date_start']))
					{
					$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_zayavka (issled_id, dobrov_id ,status_issled,rez_ych,random, comment ) VALUES ('".$issled."','".$dobrov_id."', '".$status_issled."', '".$rez_ych."','".$random."','".$comment."')");
					if(!$insertSQL) die(mysqli_error($mysql));
					$zayavka_id= mysqli_insert_id($mysql);
			
					die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=3'>");
					}
					else 
					{
						$SQL2= "SELECT * FROM CRM_issled WHERE issled_id='".intval($_GET['issled'])."' LIMIT 1";
						$IntSQL2=mysqli_query($mysql, $SQL2);
						if(!$IntSQL2) die (mysqli_error($mysql));
						$arr=mysqli_fetch_assoc($IntSQL2);
						mysqli_free_result($IntSQL2);
						
						
						$SQL3= "SELECT * FROM CRM_dobrov WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
						$IntSQL3=mysqli_query($mysql, $SQL3);
						if(!$IntSQL3) die (mysqli_error($mysql));
						$arr3=mysqli_fetch_assoc($IntSQL3);
						mysqli_free_result($IntSQL3);

						$vozr = date('Y') - date ('Y', $arr3['birthday']);
						$month3 = (isset($arr3['last_ych']) AND !empty ($arr3['last_ych']))?($arr3['last_ych']+(86400*90)):'';

						$pol =  (isset($pol) AND $pol!=$arr['pol'])? 'по критерию «Пол»':'';
						$vozr1 =  (isset($vozr) AND ($vozr < $arr['minim_vozr']))? ' по критерию «Возраст»':'';
						$vozr2 =  (isset($vozr) AND  ($vozr >$arr['max_vozr']))? ' по критерию «Возраст»':'';
						$month03 =  (isset($month3) AND !empty($month3) AND ($month3<$arr['date_start']))? '  т. к промежуток между исследованиями должен составлять 3 месяца, вы сможете принять участие после: ':'';
						die(message_s ('Ошибка', 'К сожалению, Вы не прошли '.$pol.' '.$vozr1.' '.$vozr2.' '.$month03.'  ','','<a href="">ОК</a>', 'danger'));
						
					}
                    
				
				
			}
			else
			{
				// Для проверки, когда есть dobrov_id, проверяем таблицу
				$SQL2= "SELECT * FROM CRM_issled WHERE issled_id='".intval($_GET['issled'])."' LIMIT 1";
				$IntSQL2=mysqli_query($mysql, $SQL2);
				if(!$IntSQL2) die (mysqli_error($mysql));
				$arr=mysqli_fetch_assoc($IntSQL2);
				mysqli_free_result($IntSQL2);
				
				$SQL3= "SELECT * FROM CRM_dobrov WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
				$IntSQL3=mysqli_query($mysql, $SQL3);
				if(!$IntSQL3) die (mysqli_error($mysql));
				$arr3=mysqli_fetch_assoc($IntSQL3);
				mysqli_free_result($IntSQL3);
				
				$SQL10= "SELECT * FROM CRM_zayavka WHERE dobrov_id='".$dobrov_id."'  AND issled_id ='".intval($_GET['issled'])."'  LIMIT  1";
				$IntSQL10=mysqli_query($mysql, $SQL10);
				if(!$IntSQL10) die (mysqli_error($mysql));
				$arr10=mysqli_fetch_assoc($IntSQL10);
				mysqli_free_result($IntSQL10);
				
					if (isset ($arr10['zayavka_id']) AND !empty($arr10['zayavka_id'])) 
					{
					die(message_s ('Ошибка', 'Вы уже участвуете в исследовании','','<a href="">ОК</a>', 'danger'));
					}
					else
					{
				
						$vozr = date('Y') - date ('Y', $arr3['birthday']);
						$month3 = (isset($arr3['last_ych']) AND !empty ($arr3['last_ych']))?($arr3['last_ych']+(86400*90)):'';
						if (($arr['pol']==$pol) AND ($vozr >= $arr['minim_vozr']) AND ($vozr <=$arr['max_vozr']) AND ($month3<=$arr['date_start']))
						{
						$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_zayavka (issled_id, dobrov_id ,status_issled,rez_ych,random, comment ) VALUES ('".$issled."','".$dobrov_id."', '".$status_issled."', '".$rez_ych."','".$random."','".$comment."')");
						if(!$insertSQL) die(mysqli_error($mysql));
						$zayavka_id= mysqli_insert_id($mysql);
							
						die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=3'>");
						}
						else 
						{
							$pol1 =  (isset($arr3['pol']) AND $arr3['pol']!=$arr['pol'])? 'по критерию «Пол»':'';
							$vozr1 =  (isset($vozr) AND ($vozr < $arr['minim_vozr']))? ' по критерию «Возраст»':'';
							$vozr2 =  (isset($vozr) AND  ($vozr >$arr['max_vozr']))? ' по критерию «Возраст»':'';
							$month03 =  (isset($month3) AND !empty($month3) AND ($month3>$arr['date_start']))? ' т. к промежуток между исследованиями должен составлять 3 месяца, вы сможете принять участие после ' .date ('d.m.Y', $month3): '';
							die(message_s ('Ошибка', 'К сожалению, Вы не прошли '.$pol1.' '.$vozr1.' '.$vozr2.' '.$month03.'  ','','', 'danger'));
							
	// 						echo $arr3['pol'].' => '.$arr['pol'].'<br>';
	// 						echo $vozr.' => '.$arr['minim_vozr'].'<br>';
	// 						echo $vozr.' => '.$arr['max_vozr'].'<br>';
	// 						echo $month3.' => '.$arr['date_start'].'<br>';
	// 						echo date('d.m.Y',$month3).' => '.date('d.m.Y',$arr['date_start']).'<br>';
	// 						die();
							
						}
					}
			}
				
		}
		else 
			{
				$SQL2= "SELECT * FROM CRM_issled WHERE issled_id='". intval($_GET['issled'])."' LIMIT 1";
				$IntSQL2=mysqli_query($mysql, $SQL2);
				if(!$IntSQL2) die (mysqli_error($mysql).' hgbhjghjg ');
				$arr=mysqli_fetch_assoc($IntSQL2);
				mysqli_free_result($IntSQL2);
				
				$snils1 = (isset($snils) AND !empty($snils))?' OR snils=".$snils." ':'';
				$SQL8= "SELECT * FROM CRM_dobrov WHERE fio='". $fio ."' '".$snils1."' LIMIT 1";
				$IntSQL8=mysqli_query($mysql, $SQL8);
				if(!$IntSQL8) die (mysqli_error($mysql));
				$pr=mysqli_fetch_assoc($IntSQL8);
				mysqli_free_result($IntSQL8);
                
                // Нужно отметить дублирование добровольца
                if (isset($pr['dobrov_id']) AND !empty($pr['dobrov_id']))
                {
                     $insertSQL = mysqli_query($mysql, "INSERT INTO CRM_dobrov (fio,pol,birthday,phone,snils,vk,rating,zan,povtor) VALUES ('".$fio."','".$pol."','".$birthday."','".$phone."','".$snils."','".$vk."','1','1','1')");
                        if(!$insertSQL) die(mysqli_error($mysql));
                        $dobrov_id= mysqli_insert_id($mysql);
                    
                    $insertSQL = mysqli_query($mysql, "INSERT INTO CRM_zayavka (issled_id, dobrov_id ,status_issled,rez_ych,random, comment ) VALUES ('".$issled."','".$dobrov_id."', '".$status_issled."', '".$rez_ych."','".$random."','".$comment."')");
                    if(!$insertSQL) die(mysqli_error($mysql));
                    $zayavka_id= mysqli_insert_id($mysql);

                    die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=3'>");
                }
                else 
                {
                    $vozr = date('Y') - date ('Y', $birthday);
                    if (($arr['pol']==$pol) AND ($vozr >= $arr['minim_vozr']) AND ($vozr <=$arr['max_vozr']))
                    {
                        $insertSQL = mysqli_query($mysql, "INSERT INTO CRM_dobrov (fio,pol,birthday,phone,snils,vk,rating,zan) VALUES ('".$fio."','".$pol."','".$birthday."','".$phone."','".$snils."','".$vk."','1','1')");
                        if(!$insertSQL) die(mysqli_error($mysql));
                        $dobrov_id= mysqli_insert_id($mysql);
                    
                    $insertSQL = mysqli_query($mysql, "INSERT INTO CRM_zayavka (issled_id, dobrov_id ,status_issled,rez_ych,random, comment ) VALUES ('".$issled."','".$dobrov_id."', '".$status_issled."', '".$rez_ych."','".$random."','".$comment."')");
                    if(!$insertSQL) die(mysqli_error($mysql));
                    $zayavka_id= mysqli_insert_id($mysql);

                    die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=3'>");
                    }
                    
                    else 
                    {
			$last_ych ='';
                    $insertSQL = mysqli_query($mysql, "INSERT INTO CRM_dobrov (fio,pol,birthday,phone,phone2,snils,vk,last_ych,rating, block, edit,zan, povtor ) VALUES ('".$fio."','".$pol."','".$birthday."','".$phone."','".$phone2."','".$snils."','".$vk."','".$last_ych."','1','0','0','0','0')");
                    if(!$insertSQL) die(mysqli_error($mysql).' код 1');
                    $dobrov_id= mysqli_insert_id($mysql);
			

			$pol =  (isset($pol) AND $pol!=$arr['pol'])? 'по критерию «Пол»':'';
			$vozr1 =  (isset($vozr) AND ($vozr < $arr['minim_vozr']))? ' по критерию «Возраст»':'';
			$vozr2 =  (isset($vozr) AND  ($vozr >$arr['max_vozr']))? ' по критерию «Возраст»':'';
 

			die(message_s ('Ошибка', 'К сожалению, Вы не прошли '.$pol.' '.$vozr1.' '.$vozr2.'  ','','', 'danger'));
                    }
				}
			}
	die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/register.php?issled=".$issled."&step=3&err=1'>");
	}
	
	?>
	<div class="container">
        <div class="row border-bottom white-bg dashboard-header">
            <div class="col-md-12">
                <?php 
                $SQL11 ="SELECT * FROM CRM_issled WHERE issled_id = '".$issled."' LIMIT 1";
                $nmSQL = mysqli_query($mysql,$SQL11 ) or die (mysql_error($mysql));
                $ra = mysqli_fetch_assoc($nmSQL);
                mysqli_free_result($nmSQL);
                ?>
                <h2><?php echo $ra['name']; ?></h2>
                <div class="wizard" id="stepwizard">
                    <div class="wizard-inner">
                    <div class="connecting-line" style="width:67%"></div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="<?php echo (isset($_GET['step']) AND $_GET['step'] == 1) ? 'active' : 'disabled'; ?>" role="presentation" style="width: 33%;">
                                <a href="#step1" title="" data-toggle="tab" aria-controls="step1" role="tab" data-original-title="Введите ваш контактный номер телефона">
                                <span class="round-tab"><i class="glyphicon glyphicon-phone"></i></span></a>
                            </li>
                            <li class="<?php echo (isset($_GET['step']) AND $_GET['step'] == 2) ? 'active' : 'disabled'; ?>" role="presentation" style="width: 33%;"><a href="#step2" title="" data-toggle="tab" aria-controls="step2" role="tab" data-original-title="Данные добровольца">
                                <span class="round-tab"><i class="glyphicon glyphicon-user"></i></span></a>
                            </li>
                            <li class="<?php echo (isset($_GET['step']) AND $_GET['step'] == 3) ? 'active' : 'disabled'; ?>" role="presentation" style="width: 33%;"><a href="#step3" title="" data-toggle="tab" aria-controls="step3" role="tab" data-original-title="Завершение регистрации">
                                <span class="round-tab"><i class="glyphicon glyphicon-ok"></i></span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
	<div class="container">
		<div class="row">
			<div class="middle-box text-center loginscreen animated fadeInDown">
			<?php
			if (isset ($_GET['step']) AND  $_GET['step']==1)
			{
			?>
			<p>Введите ваш контактный номер телефона</p>
			<form class="m-t" role="form" action="http://<?php echo $HTTP_HOST; ?>/register.php?issled=<?php echo $issled; ?>&step=1" method="post">
				<input name="issled" type="hidden" value="<?php echo $issled; ?>">
				
				<div class="form-group field-researchorderform-phone required has-error">
				<label class="control-label" for="researchorderform-phone">Телефон</label>
					<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></span><span class="input-group-addon">+7</span>
					<input data-mask="(999) 999-9999" type="text" id="researchorderform-phone" class="form-control" name="phone" value="" placeholder="(999) 999-99-99" aria-required="true" data-plugin-inputmask="inputmask_6401581c" aria-invalid="true"></div>
					<div class="help-block">Необходимо заполнить «Телефон».</div>
				</div>
				
				<button name="Add_1" type="submit" class="btn btn-primary block full-width m-b">Далее</button>
			</form>
			<?php 
			}
			elseif (isset ($_GET['step']) AND $_GET['step']==2)
			{
				if (isset($_GET['add']))
				{
				if (isset($_GET['edit_dobr']) AND is_numeric($_GET['edit_dobr']))
				{
				$SQL2= "SELECT * FROM CRM_dobrov WHERE phone='". $_SESSION['form']['phone']."' OR phone2='". $_SESSION['form']['phone']."' LIMIT 1";
				$IntSQL2=mysqli_query($mysql, $SQL2);
				if(!$IntSQL2) die (mysqli_error($mysql));
				$arr=mysqli_fetch_assoc($IntSQL2);
				mysqli_free_result($IntSQL2);
				}
			?>

			<h3><?php echo $settings_crm_about; ?></h3>
			<div class="alert alert-info">
				<?php echo isset($arr['dobrov_id']) ? 'Сверьте свои данные. Если всё верно, нажмите кнопку "Далее". Вы можете отправить запрос на изменение данных, нажав кнопку "Отредактировать данные"' : 'Мы не нашли ваш номер телефона в нашей базе.<br>Заполните, пожалуйста форму ниже для регистрации вас в системе в качестве добровольца.'; ?> 
			</div>
				<?php 
			
			?>
			<p>Введите ваш контактный номер телефона</p>
			<form class="m-t" role="form" action="http://<?php echo $HTTP_HOST; ?>/register.php?issled=<?php echo $issled; ?>&step=1" method="post">
				<input name="issled" type="hidden" value="<?php echo $issled; ?>">
				<?php echo isset($arr['dobrov_id']) ? '<input name="edit_dobr" type="hidden" value="'. $_GET['edit_dobr'].'" />' : ''; ?>
				<?php echo isset($arr['dobrov_id']) ? '<input name="dobrov_id" type="hidden" value="'.intval($arr['dobrov_id']).'" />' : ''; ?>
				<div class="form-group has-error">
					<label class="control-label">ФИО *</label> 
					<input type="text" name="fio" value="<?php echo isset($arr['fio']) ? $arr['fio'] : ''; ?>" placeholder="" class="form-control" required">
				</div>
				
				<div class="form-group  has-error"> 
					<label class="control-label">Пол</label>
					<select class="form-control" name="pol" required="">
						<option value="">Выбрать</option>
						<?php
						foreach ($arr_pol as $key =>$zn)
						{
						?>
						<option value="<?php echo $key; ?>" <?php echo (isset($arr['pol']) AND ($arr['pol']==$key))?'selected':''; ?> ><?php echo $zn;  ?></option>
						<?php	
						}
						?>	
					</select>
				</div>
					
				<div class="form-group has-error">
					<label class="control-label"> Дата рождения</label>
					<input class="form-control dataY" name="birthday" type="text" value="<?php echo (isset($arr['birthday']) AND !empty($arr['birthday'])) ? date("d.m.Y",$arr['birthday']) : ''; ?>"  required/>
				</div>	
				
				<div class="form-group has-error">
					<label for="phone" class="control-label"> Телефон *</label>
					<input data-mask="+7(999) 999-99-99" class="form-control" name="phone" type="text" value="<?php echo isset($arr['phone']) ? $arr['phone'] : $_SESSION['form']['phone']; ?>" placeholder="+7(999) 999-99-99" readonly/>
				</div>	
					
				<div class="form-group">
					<label for="snils" class="control-label"> СНИЛС *</label>
					<input data-mask="999-999-99 99" class="form-control" name="snils" type="text" value="<?php echo isset($arr['snils']) ? $arr['snils'] : ''; ?>" placeholder="999-999-99 99" autocomplete="nope" />
				</div>	
					
				<div class="form-groupr has-error"> 
					<label class="control-label">Профиль в соц. сети*</label>
					<input class="form-control" name="vk" type="text" value="<?php echo isset($arr['vk']) ? vk_linko($arr['vk']) : ''; ?>" required />
				</div>
										
				<?php echo isset($arr['dobrov_id']) ? '<input name="dobrov_id" type="hidden" value="'.$arr['dobrov_id'].'" />' : ''; ?>
				
				<div class="clearfix mtop"></div>
				
				<button name="Add_2" type="submit" class="btn btn-primary block full-width m-b">Далее</button>
			</form>
			<?php 
			}
			else
			{
			$SQL2= "SELECT * FROM CRM_dobrov WHERE phone='". $_SESSION['form']['phone']."' OR phone2='". $_SESSION['form']['phone']."' LIMIT 1";
				$IntSQL2=mysqli_query($mysql, $SQL2);
				if(!$IntSQL2) die (mysqli_error($mysql));
				$arr=mysqli_fetch_assoc($IntSQL2);
				mysqli_free_result($IntSQL2);

			?>
			<div class="alert alert-info">
				<p> Сверьте свои данные. Если всё верно, нажмите кнопку "Далее". Вы можете отправить запрос на изменение данных, нажав кнопку "Отредактировать данные".</p>
			</div>
            <form method="post">
		<?php echo isset($arr['dobrov_id']) ? '<input name="dobrov_id" type="hidden" value="'.intval($arr['dobrov_id']).'" />' : ''; ?>
			<table class="table table-hover">
		
							<tr>
								<th>ФИО</th>
								<td>
								<input type="text" name="fio" value="<?php echo isset($arr['fio']) ? $arr['fio'] : ''; ?>" placeholder="" class="form-control" required="" " readonly>
								</td>
							</tr>
							
							<tr>
								<th>Пол</th>
								<td>
								<?php
								foreach ($arr_pol as $key =>$zn)
								{
								?>
								<input type="text" name="pol" value="<?php echo (isset($arr['pol']) AND $arr['pol']==$key)? $zn : ''; ?>" placeholder="" class="form-control" required="" " readonly>
								<?php	
								}
								
								?>
								</td>
									
							</select> 
								</td>
							</tr>
							
							<tr>
								<th>Дата рождения</th>
								<td><input class="form-control datas" name="birthday" type="text" value="<?php echo (isset($arr['birthday']) AND !empty($arr['birthday'])) ? date("d.m.Y",$arr['birthday']) : ''; ?>" readonly/> </td>
							</tr>
							
							<tr>
								<th>Телефон</th>
								<td> <input   data-mask="+7(999) 999-99-99" class="form-control" name="phone" id="phone"  type="text" value="<?php echo isset($arr['phone']) ? $arr['phone'] : ''; ?>" placeholder="+7(999) 999-99-99" readonly/></td>
							</tr>
							
							<tr>
								<th>СНИЛС</th>
								<td> <input data-mask="999-999-99 99" class="form-control" name="snils" type="text" value="<?php echo isset($arr['snils']) ? $arr['snils'] : ''; ?>"  placeholder="999-999-99 99" readonly/></td>
							</tr>
							
							<tr>
								<th>Профиль в соц. сети</th>
								<td><input class="form-control" name="vk" type="text" value="<?php echo isset($arr['vk']) ? $arr['vk'] : ''; ?>"  readonly/></td>
							</tr>
			</table>
			<div class="clearfix mtop"></div>
			<p><a class="btn btn-sm btn-primary" href="?issled=<?php echo $issled;?>&step=2&edit_dobr=<?php echo $arr['dobrov_id']?>&add"><i class="fa fa-plus-square"></i>Отредактировать данные</a></p>
			<button name="Add_2" type="submit" class="btn btn-primary block full-width m-b">Далее</button>
			</form>
			<?php
			} // else
			?>
				
		<?php
			
			}
		
			elseif (isset ($_GET['step']) AND $_GET['step']==3)
			{
			$SQL2 ="SELECT * FROM CRM_issled WHERE issled_id = '".$issled."' ";
			$nSQL = mysqli_query($mysql,$SQL2 ) or die (mysql_error($mysql));
			$ra = mysqli_fetch_assoc($nSQL);
			mysqli_free_result($nSQL);
			?>
			<h2>Вы успешно зарегистрированы. Сообщение с приглашением придет вам в ВКонтакте. Для удобства добавьте в друзья:  <a href="https://vk.com/aakhokhlov" > https://vk.com/aakhokhlov</a></h2>
			<?php 
			}
			?>
			</div>
		</div>
	</div>

	<script src="js/jquery-3.1.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<!-- Input Mask-->
	<script src="js/plugins/jasny/jasny-bootstrap.min.js"></script>
		<!-- Data picker -->
	<script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>
	<script>
		$('.datas').datepicker({
			todayBtn: "linked",
			format: 'dd.mm.yyyy',
			language: 'ru',
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true
		});
		
		$('.dataY').datepicker({
			startView: 2,
			format: 'dd.mm.yyyy',
			language: 'ru',
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true
		});
	</script>
</body>

</html>
<?php 
// function vk_linko ($link)
// {
//     $e = explode('vk.com/',$link);
//     
//     return 'https://vk.com/'.(isset($e[1])?$e[1]:$e[0]);
// }
?>
