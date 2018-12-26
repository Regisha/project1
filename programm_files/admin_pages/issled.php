<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}

    mysqli_query($mysql, "CREATE TABLE IF NOT EXISTS CRM_issled (
        issled_id int auto_increment primary key,
        name varchar(150) NOT NULL,
        date_start varchar(11) NOT NULL,
        date_stop varchar(11) NOT NULL,
        pol int(2) NOT NULL,
        minim_vozr int(3) NOT NULL, 
        max_vozr int(3) NOT NULL, 
        centr int(2) NOT NULL,
        status int(2) NOT NULL,
        comment varchar(5000) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Исследования'");
	
	if (isset($_POST['Add_DB']))
	{
	$name = mysqli_real_escape_string($mysql,trim(strip_tags($_POST['name'])));
	$date_start = isset($_POST['date_start'])?strtotime($_POST['date_start']):'';
	$date_stop = isset($_POST['date_stop'])?strtotime($_POST['date_stop']):'';
	$pol = isset($_POST['pol'])?intval($_POST['pol']):0;
	$minim_vozr = isset($_POST['minim_vozr'])?intval($_POST['minim_vozr']):0;
	$max_vozr = isset($_POST['max_vozr'])?intval($_POST['max_vozr']):0;
	$centr = isset($_POST['centr'])?intval($_POST['centr']):0;
	$status = isset($_POST['status'])?intval($_POST['status']):0;
	$comment = isset($_POST['comment']) ? mysqli_real_escape_string($mysql, trim($_POST['comment'])) : '';

			if (isset($_POST['issled_id']))
			{
				$issled_id = intval($_POST['issled_id']);
				$query_count = "UPDATE CRM_issled SET name='".$name."',date_start='".$date_start."',date_stop='".$date_stop."',pol='".$pol."',minim_vozr='".$minim_vozr."',max_vozr='".$max_vozr."',centr='".$centr."',status='".$status."',comment='".$comment."' WHERE issled_id='".$issled_id."' LIMIT 1";
				mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_issled id:'.$issled_id);
			
				$result = mysqli_query($mysql, "SELECT	* FROM CRM_zayavka WHERE CRM_zayavka.issled_id='". intval($_POST['issled_id'])."' ") or  die(trigger_error(mysqli_error($mysql).' - CRM_zayavka'));
				 while ($arr = mysqli_fetch_assoc($result))
				{
				$query_count = "UPDATE CRM_zayavka SET status_issled='".$status."' WHERE  zayavka_id='".$arr['zayavka_id']."' ";
				mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_issled id:'.$issled_id);
				}
				mysqli_free_result($result);
	
			}
		else
			{
				$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_issled (name,date_start,date_stop,pol,minim_vozr,max_vozr,centr,status,comment) VALUES ('".$name."','".$date_start."','".$date_stop."','".$pol."','".$minim_vozr."','".$max_vozr."','".$centr."','".$status."','".$comment."')");
				if(!$insertSQL) die(mysqli_error($mysql));
				$issled_id= mysqli_insert_id($mysql);
			}
			
			$result = mysqli_query($mysql, "SELECT	issled_id, dobrov_id  FROM CRM_zayavka WHERE CRM_zayavka.status_issled='3' AND CRM_zayavka.rez_ych='8'  ") or  die(trigger_error(mysqli_error($mysql).' - CRM_zayavka'));
			while ($arr = mysqli_fetch_assoc($result)) 
			{
				if (isset($arr['issled_id']) AND !empty($arr['issled_id']))
				{
				$result1 = mysqli_query($mysql, "SELECT	* FROM CRM_issled WHERE CRM_issled.issled_id='".$arr['issled_id']."' LIMIT 1") or  die(trigger_error(mysqli_error($mysql).' - CRM_dobrov'));
				$arr1 = mysqli_fetch_assoc($result1);
				mysqli_free_result($result1);
				
				$query_count = "UPDATE CRM_dobrov SET last_ych='".$arr1['date_stop']."', zan='0' WHERE CRM_dobrov.dobrov_id='".$arr['dobrov_id']."' LIMIT 1";
				mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
				}	
			}
			mysqli_free_result($result);

		if (isset($_POST['return']))
			{
			die ("<meta http-equiv=refresh content='0; url=?page=issled&see=".$_GET['return']."'>");
			}
			else
			{
			die ("<meta http-equiv=refresh content='0; url=?page=issled'>");
			}
		}
	
		if (isset($_GET['del']))
		{
		$query = "DELETE FROM CRM_issled WHERE CRM_issled.issled_id='".intval($_GET['del'])."' LIMIT 1";
		mysqli_query($mysql,$query) or die(mysql_error());

		die ("<meta http-equiv=refresh content='0; url=?page=issled'>");
		}	
	
		if (isset($_GET['dob_del']))
		{
	// 	look($_GET);
		$query = "DELETE FROM CRM_zayavka WHERE CRM_zayavka.issled_id='".intval($_GET['see1'])."'  AND CRM_zayavka.dobrov_id='".intval($_GET['dob_del'])."' LIMIT 1";
		mysqli_query($mysql,$query) or die(mysql_error());

		die ("<meta http-equiv=refresh content='0; url=?page=issled&see=".$_GET['see1']."'>");
	}
	
		if (isset($_POST['SBM_del']))
		{
			if (isset($_POST['delme']))
			{
				foreach ($_POST['delme'] as $id => $v)
				{
				$queryT = "DELETE FROM CRM_issled WHERE CRM_issled.issled_id='".intval($id)."' LIMIT 1";
				mysqli_query($mysql,$queryT) or die(mysqli_error($mysql));
				}
			}

// 		die ("<meta http-equiv=refresh content='0; url=?page=issled'>");
		}
	
	if (isset($_POST['DEL_check_dobrov']))
	{
		if (isset($_POST['del_dobr']))
		{
			foreach ($_POST['del_dobr'] as $id => $v)
			{
			$queryT = "DELETE FROM CRM_zayavka WHERE CRM_zayavka.issled_id='".intval($_POST['issled_id'])."' AND  CRM_zayavka.dobrov_id='".intval($id)."' LIMIT 1";
			mysqli_query($mysql,$queryT) or die(mysqli_error($mysql));
			}
		}

	die ("<meta http-equiv=refresh content='0; url=?page=issled&see=".intval($_POST['issled_id'])."'>");
	}
	

	if (isset($_POST['Add_correction']))
	{
	$fio = mysqli_real_escape_string($mysql,trim(strip_tags($_POST['fio'])));
	$rez_ych =  isset($_POST['rez_ych'])?intval($_POST['rez_ych']):'1';
	$random =  isset($_POST['random'])?trim($_POST['random']):'';
	$comment = isset($_POST['comment']) ? mysqli_real_escape_string($mysql, trim($_POST['comment'])) : '';

		if (isset($_POST['issled_id']) AND isset($_POST['dobrov_id']))
		{
			$issled_id = intval($_POST['issled_id']);
			$dobrov_id = intval($_POST['dobrov_id']);
			$query_count = "UPDATE CRM_zayavka SET  rez_ych='".$rez_ych."',random='".$random."',comment='".$comment."' WHERE issled_id='".$issled_id."' AND dobrov_id='".$dobrov_id."'   LIMIT 1";
			mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_zayavka id:'.$issled_id);
			$rating = '1';
			$result = mysqli_query($mysql, "SELECT	* FROM CRM_zayavka WHERE CRM_zayavka.dobrov_id='".$dobrov_id."' ") or  die(trigger_error(mysqli_error($mysql).' - CRM_zayavka'));
			while ($arr = mysqli_fetch_assoc($result))
			{
				$result1 = mysqli_query($mysql, "SELECT	* FROM CRM_status_z WHERE CRM_status_z.status_z_id='".$arr['rez_ych']."' ") or  die(trigger_error(mysqli_error($mysql).' - CRM_status_z'));
				while ($arr1 = mysqli_fetch_assoc($result1))
				{
					$rating+=$arr1['reit'];
					$query_count = "UPDATE CRM_dobrov SET rating='".$rating."'  WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
					mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
				}
			}
			mysqli_free_result($result);
		}
		
		$result = mysqli_query($mysql, "SELECT	* FROM CRM_zayavka WHERE CRM_zayavka.status_issled='3' AND CRM_zayavka.rez_ych='8' ") or  die(trigger_error(mysqli_error($mysql).' - CRM_zayavka'));
		while (  $arr = mysqli_fetch_assoc($result)) 
		{
			if (isset($arr['issled']) AND !empty($arr['issled']))
			{
			$result1 = mysqli_query($mysql, "SELECT	* FROM CRM_issled WHERE CRM_issled.issle_id='".$arr['issled_id']."' LIMIT 1") or  die(trigger_error(mysqli_error($mysql).' - CRM_dobrov'));
			$arr1 = mysqli_fetch_assoc($result1);
			mysqli_free_result($result1);
			
			$query_count = "UPDATE CRM_dobrov SET last_ych='".$arr1['date_stop']."' WHERE dobrov_id='".$arr['dobrov_id']."' LIMIT 1";
			mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
			}
		}
		mysqli_free_result($result);
		die ("<meta http-equiv=refresh content='0; url=?page=issled&see=".$issled_id."'>");

	}
	
	if (isset($_POST['ADD_dobrov_v_issled']))
	{
		if(isset($_POST['add']))
		{
			foreach  ($_POST['add'] as $dobrov_id => $zn)
			{
			$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_zayavka (issled_id, dobrov_id ,status_issled,rez_ych,random, comment ) VALUES ('".intval($_GET['number'])."','".$dobrov_id."', '1', '1','','')");
			if(!$insertSQL) die(mysqli_error($mysql));
			$zayavka_id= mysqli_insert_id($mysql);
			
			$query_count = "UPDATE CRM_dobrov SET zan='1' WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
            mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
			}
		}
			die ("<meta http-equiv=refresh content='0; url=?page=issled&add_dobrov_ok=".$_GET['number']."'>");
	}
	
	if (isset($_POST['ADD_FILE']))
	{
// 		$_FILES['uploadfile']['name'] - имя файла до его отправки на сервер, например, pict.gif;
// 		$_FILES['uploadfile']['size'] - размер принятого файла в байтах;
// 		$_FILES['uploadfile']['type'] - MIME-тип принятого файла (если браузер смог его определить), например: image/gif, image/png, image/jpeg, text/html;
// 		$_FILES['uploadfile']['tmp_name'] (так мы назвали поле загрузки файла) - содержит имя файла во временном каталоге, например: /tmp/phpV3b3qY;
// 		$_FILES['uploadfile']['error'] 
// 	look($_POST);
// 	look($_FILES);
		if (isset($_FILES['file']))
		{
		//copy($_FILES['file']['tmp_name'],ABSOLUTE__PATH__.'/tmp/asdasdasd.csv');
		$file = $_FILES['file']['tmp_name']; 
			$i=0;
			foreach (file($_FILES['file']['tmp_name']) as $line)
			{
				if ($i>0)
				{
				echo $line.'<br>';
				$e = explode(';',$line);
				look($e);
				
				$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_dobrov (fio,pol,birthday,phone,snils,vk, last_ych, rating ,block,zan,povtor) VALUES ('".$e['1']."','0','".$e['2']."','".$e['3']."','".$e['4']."','','','1','0','0','0')");
				if(!$insertSQL) die(mysqli_error($mysql));
				$dobrov_id= mysqli_insert_id($mysql);
				
				$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_zayavka SET  issled_id='".$_GET['id_is']."', dobrov_id='".$dobrov_id."',status_issled = '1', rez_ych='1' ");
				if(!$insertSQL) die(mysqli_error($mysql));
				$dobrov_id= mysqli_insert_id($mysql);
				}
			$i++;
			}
		}
	}

	
?>
	
	<div class="row border-bottom white-bg dashboard-header">
		<div class="col-md-12">
			<h2>Исследования</h2>
			<p><a class="btn btn-sm btn-primary" href="?page=issled&add=true"><i class="fa fa-plus-square"></i> Добавить новое исследование</a></p>
		</div>
		
	</div>
	
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<!--Это блок = из 12-->
			<div class="col-md-12">
				<div class="ibox">
					<div class="ibox-content">	
					<?php
					if (isset($_GET['delete']))
					{
					?>
					<div class="alert alert-dismissible alert-danger col-lg-6 col-lg-offset-3">
						<h3 class="text-center text-danger">Подтверждение удаления</h3>
						<strong>Выбранное  исследование будет удалено из базы</strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<a class="btn btn-sm btn-primary" href="?page=issled&del=<?php echo $_GET['delete']; ?>"><i class="fa fa-trash-o"></i> Удалить исследование</a>
							<a class="btn btn-sm btn-danger" href="?page=issled"><i class="fa fa-times"></i> Отменить</a>
						</p>
					</div>
					
					<div class="clearfix"></div>
					<?php
					}
					
					if (isset($_POST['SBM_del1']))
					{
					?>
					<form method="POST">
					<?php
                    if (isset($_POST['del']))
                    {
                        foreach ($_POST['del'] as $id => $v)
                        {
                        echo '<input type="hidden" name="delme['.$id.']" value="'.$id.'">';
                        }
                    }
					?>
					<div class="alert alert-dismissible alert-danger col-lg-6 col-lg-offset-3">
						<h3 class="text-center text-danger">Подтверждение удаления</h3>
						<strong>Выбранное  исследование будет удалено из базы</strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<button type="submit" name="SBM_del" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span> Удалить выбранные</button>
							<a class="btn btn-sm btn-danger" href="?page=issled"><i class="fa fa-times"></i> Отменить</a>
						</p>
					</div>
					</form>
					
					<div class="clearfix"></div>
					<?php
					}
					
					if (isset($_GET['add_dobrov_ok']))
					{
					?>
					<div class="alert alert-dismissible alert-info col-lg-6 col-lg-offset-3">
						<h3 class="text-center text-danger"> Добавление добровольца(ев) в исследование успешно завершено.</h3>
						<strong></strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<a class="btn btn-sm btn-primary" href="?page=issled&see=<?php echo $_GET['add_dobrov_ok']; ?>"><i class="fa fa-trash-o"></i> Ок</a>

						</p>
					</div>
					
					<div class="clearfix"></div>
					<?php
					}
					
					if (isset($_GET['dobrov_del']))
					{
					?>
					<div class="alert alert-dismissible alert-danger col-lg-6 col-lg-offset-3">
						<h3 class="text-center text-danger">Подтверждение удаления</h3>
						<strong>Выбранный доброволец будет удален из данного исследования</strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<a class="btn btn-sm btn-primary" href="?page=issled&see1=<?php echo $_GET['see1']; ?>&dob_del=<?php echo $_GET['dobrov_del']; ?>"><i class="fa fa-trash-o"></i> Удалить добровольца из исследования</a>
							<a class="btn btn-sm btn-danger" href="?page=issled&see=<?php echo $_GET['see1']; ?>"><i class="fa fa-times"></i> Отменить</a>
						</p>
					</div>
					
					
					<?php
					}
					
					
					elseif (isset($_GET['add']))
					{
						if (isset($_GET['edit']))
						{
							$result = mysqli_query($mysql, "SELECT	* FROM CRM_issled WHERE CRM_issled.issled_id='". intval($_GET['edit']) ."' LIMIT 1") or  die(trigger_error(mysqli_error($mysql).' - CRM_issled'));
							$arr = mysqli_fetch_assoc($result);
							mysqli_free_result($result);
						}	
					?>	
					<h5><?php echo isset($arr['issled_id']) ? 'Редактировать' : 'Добавить'; ?> исследование</h5>
					<form  action='' method='post' enctype='multipart/form-data'>
						<div class="form-group has-error">
							<label class="control-label">Название</label> 
							<input type="text" name="name" value="<?php echo isset($arr['name']) ? $arr['name'] : ''; ?>" placeholder="" class="form-control" required="" ">
						</div>

						<div class="form-group">
						<label class="control-label"> Начало</label>
							<input class="form-control datas" name="date_start" type="text" value="<?php echo isset($arr['date_start']) ? date("d.m.y",$arr['date_start']) : ''; ?>" />
									
						</div>	
						
						<div class="form-group">
						<label for="name" class="control-label">Окончание</label>
							<input class="form-control datas" name="date_stop" type="text" value="<?php echo isset($arr['date_stop']) ? date("d.m.y",$arr['date_stop']) : ''; ?>" />
						</div> 
                        
						 <div class="form-group"> 
						<label class="control-label">Пол</label>
							<p> 		
							<select class="form-control chosen-select" name="pol" required>
								<option selected>Выбрать</option>
								<?php
								foreach ($arr_pol as $key =>$zn)
								{
								?>
								<option value="<?php echo $key; ?>" <?php echo (isset($arr['pol']) AND ($arr['pol']==$key))?'selected':''; ?> ><?php echo $zn; ?></option>
								<?php	
								}
								
								?>	
							</select>
						</div>
						
						<div class="form-group">
						<label for="name" class="control-label">Минимальный возраст</label>
							<input class="form-control" name="minim_vozr" type="text" value="<?php echo isset($arr['minim_vozr']) ? $arr['minim_vozr'] : ''; ?>" />
						</div>	
                        
						<div class="form-group">
						<label for="name" class="control-label">Максимальный возраст</label>
							<input class="form-control" name="max_vozr" type="text" value="<?php echo isset($arr['max_vozr']) ? $arr['max_vozr'] : ''; ?>" />
						</div>	
                        
						<div class="form-group has-error"> 
						<label class="control-label">Исследовательский центр *</label>
							<p> 		
							<select class="form-control chosen-select" name="centr" required>
								<option selected>Выбрать</option>
								<?php
								$SQL2 ="SELECT * FROM CRM_centr";
								$nSQL = mysqli_query($mysql,$SQL2 ) or die (mysql_error($mysql));
								while ($ra = mysqli_fetch_assoc($nSQL))
								{
								?>
								<option value="<?php echo $ra['centr_id']; ?>" <?php echo (isset($arr['centr']) AND ($arr['centr']==$ra['centr_id']))?'selected':''; ?> ><?php echo $ra['name']; ?></option>
								<?php	
								}
								
								?>	
							</select>
						</div>
						
						<div class="form-group  has-error"> 
						<label class="control-label">Статус исследования *</label>
							<p> 		
							<select class="form-control chosen-select" name="status" required>
								<option selected>Выбрать</option>
								<?php
								$SQL2 ="SELECT * FROM CRM_status_issled";
								$nSQL = mysqli_query($mysql,$SQL2 ) or die (mysql_error($mysql));
								while ($st = mysqli_fetch_assoc($nSQL))
								{
								?>
								<option value="<?php echo $st['status_issled_id']; ?>" <?php echo (isset($arr['status']) AND ($arr['status']==$st['status_issled_id']))?'selected':''; ?> ><?php echo $st['name']; ?></option>
								<?php	
								}
								
								?>	
							</select>
						</div>
						
						<div class="form-group">
							<label class="control-label">Комментарий </label>
							<textarea name="comment" class="form-control" rows="7"><?php echo isset($arr['comment'])?$arr['comment']:'';?> </textarea>
						</div>
						
						<div class="clearfix mtop"></div>
						
						<?php echo isset($arr['issled_id']) ? '<input name="issled_id" type="hidden" value="'.$arr['issled_id'].'" />' : ''; ?>
						<?php echo isset($_GET['return']) ? '<input name="return" type="hidden" value="'.$_GET['return'].'" />' : ''; ?>
						
						<div class="col-lg-6 col-lg-offset-3 text-center">
							<button name="Add_DB" class="btn btn-sm btn-primary" type="submit"><strong>Сохранить</strong></button>
							<a class="btn btn-sm btn-danger" href="?page=issled"><i class="fa fa-times"></i> Закрыть</a>
						</div>
						<div class="clearfix mtop"></div>
					</form>
					<?php
					}
					  elseif (isset($_GET['see']) AND is_numeric($_GET['see']))
					{ 
					 $SQL5= "SELECT * FROM CRM_issled WHERE issled_id='".intval($_GET['see']) ."' ";
					$IntSQL5=mysqli_query($mysql, $SQL5);
					if(!$IntSQL5) die (mysqli_error($mysql));
					$arr5=mysqli_fetch_assoc($IntSQL5);
					?> 
					<h2><i class="fa fa-list" aria-hidden="true"></i> <?php echo $arr5['name'];?> </h2>
					
				
					<a>
					<div class="panel-group" id="accordion">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"  class="js-add-volunteer-btn add-volunteer-btn btn btn-info" > <span class="glyphicon glyphicon-info-sign"></span> </a>
									<a class="js-edit-research-btn add-volunteer-btn btn btn-warning js-modal-form" href="?page=issled&add=1&edit=<?php echo $_GET['see'];?>&return=<?php echo $_GET['see'];?>" title="Редактировать"><span class="glyphicon glyphicon-edit"></span></a>
									 <a class="js-remove-research-btn add-volunteer-btn btn btn-danger" href="" title="Удалить" data-confirm="Вы уверены, что хотите удалить?" data-method="post"><span class="glyphicon glyphicon-trash"></span></a>

<!--смысл поняла? я только класс меняла,в чем смысл? ну присмотрись это обязательный класс что ли linkos  да он модаль и активирует...ппцок в рупке смотре внизу линкос -->
									<a   data-link="http://<?php echo $HTTP_HOST; ?>/register.php?issled=<?php echo $arr5['issled_id']; ?>&step=1" class="btn btn-primary linkos" data-toggle="modal" data-target=".bs-example-modal-lg"><span class="glyphicon glyphicon-share"></span>Ссылка </a>
									<a class="js-add-volunteer-btn js-modal-grid-loader add-volunteer-btn btn btn-default" href="?page=issled&add_dobrov=1<?php echo isset($_GET['see'])?'&number='.$_GET['see']:''; ?>"><span class="glyphicon glyphicon-plus"></span> Добавить добровольца</a>
									<a class="js-research-import research-import btn btn-default js-modal-form" href="dobrov.txt" download><span class="glyphicon glyphicon-floppy-open"></span> Выгрузить добровольцев</a> 
									<div class="btn-group">
									<a class="js-research-import research-import btn btn-default js-modal-form" href="?page=issled&id_is=<?php echo intval($_GET['see']); ?>&addfile" ><span class="glyphicon glyphicon-floppy-open"></span>Загрузить добровольцев из CSV</a> 
									
								</h4>
							</div>
							
							<div id="collapseOne" class="panel-collapse collapse in">
								<div class="panel-body">
									
									<table class="table table-hover">
										<tr>
											<th>Начало</th>
											 <td> <?php echo (isset($arr5['date_start']) AND !empty($arr5['date_start'])) ? date('d.m.Y',$arr5['date_start']) : ''; ?>
										</tr>
										
										<tr>
											<th>Окончание</th>
											 <td> <?php echo (isset($arr5['date_stop']) AND !empty($arr5['date_stop'])) ? date('d.m.Y',$arr5['date_stop']) : ''; ?>
										</tr>

										
										<tr>
											<th>Пол</th>
											<td>
											<?php
											foreach ($arr_pol as $key =>$zn)
											{
											?>
											<?php echo (isset($arr5['pol']) AND $arr5['pol']==$key)? $zn : ''; ?>
											<?php	
											}
											?>
											</td>
												
										</select> 
											</td>
										</tr>
										
										<tr>
											<th>Минимальный возраст</th>
											 <td> <?php echo isset($arr5['minim_vozr'])?$arr5['minim_vozr'] : ''; ?>
										</tr>
										
										<tr>
											<th>Максимальный возраст</th>
											 <td> <?php echo isset($arr5['max_vozr'])?$arr5['max_vozr'] : ''; ?>
										</tr>
										
										<tr>
											<th>Исследовательский центр</th>
											<td>
											
											<?php 
											$SQL2 ="SELECT * FROM CRM_centr";
											$nSQL = mysqli_query($mysql,$SQL2 ) or die (mysql_error($mysql));
											while ($ra = mysqli_fetch_assoc($nSQL))
											{
											echo (isset($arr5['centr']) AND $arr5['centr'] ==$ra['centr_id']) ? $ra['name'] : ''; 
											}
											?>
										</tr>
										
										<tr>
											<th>Статус исследования</th>
											<td><?php 
											$SQL2 ="SELECT * FROM CRM_status_issled";
											$nSQL = mysqli_query($mysql,$SQL2 ) or die (mysql_error($mysql));
											while ($st = mysqli_fetch_assoc($nSQL))
											{
											
											echo ( isset($arr5['status']) AND $arr5['status']==$st['status_issled_id'])  ? $st['name'] : ''; 
											}
											?>
										</tr>
									</table>
								</div>
							</div>
						</div>
				
           
                    
                   
                    </div>
                    <form id="formsubmit" action="" method="post" name="forma1">
                    <input type="hidden" name="page" value="issled">
                    <?php echo isset($_GET['see']) ? '<input name="issled_id" type="hidden" value="'.$_GET['see'].'" />' : '';                     ?>
                    
					<table class="table table-hover">
						<tr>
							<th><input type="checkbox" class="select-on-check-all" name="selection_all" value=""></th>
							<th>ID</th>
							<th>ФИО</th>
							<th>Дата рождения</th>
							<th>Телефон</th>
							<th>Профиль в соц. сети</th>
							<th>Результат участия</th>
							<th>Действия</th>
							
						</tr>	
					<?php

					$csv = 'dobrov_id;fio;birthday;phone;snils'."\r\n";
					$SQL= "SELECT * FROM CRM_zayavka WHERE issled_id='".intval($_GET['see']) ."' ";
					$IntSQL=mysqli_query($mysql, $SQL);
					if(!$IntSQL) die (mysqli_error($mysql));
					while ($arr=mysqli_fetch_assoc($IntSQL))
					{
					$SQL3= "SELECT * FROM CRM_dobrov WHERE dobrov_id='".intval($arr['dobrov_id']) ."' ";
					$IntSQL3=mysqli_query($mysql, $SQL3);
					if(!$IntSQL3) die (mysqli_error($mysql));
					while ($dt=mysqli_fetch_assoc($IntSQL3))
					{
					$arr_dob[$dt['dobrov_id']] = $dt;
						
					$csv .= $dt['dobrov_id'].';'. $dt['fio'].';'.$dt['birthday'].';'.$dt['phone'].';'.$dt['snils']."\r\n";
?>
					<tr>
						<td><label> 
                                <input type="checkbox" name="del_dobr[<?php echo $arr_dob[$dt['dobrov_id']]['dobrov_id']; ?>]" value="1"/>
                            </label>
                        </td>
						<td><?php echo $arr_dob[$dt['dobrov_id']]['dobrov_id']; ?> </td>
						<td><?php echo  $arr_dob[$dt['dobrov_id']]['fio']; ?></td>
						<td><?php echo (isset($arr_dob[$dt['dobrov_id']]['birthday']) AND !empty($arr_dob[$dt['dobrov_id']]['birthday']))? date("d.m.Y",$arr_dob[$dt['dobrov_id']]['birthday']):''; ?></td>
						<td><?php echo  $arr_dob[$dt['dobrov_id']]['phone']; ?></td>
						<td><a href="<?php echo  $arr_dob[$dt['dobrov_id']]['vk']; ?>" target="_blank"> <?php echo  $arr_dob[$dt['dobrov_id']]['vk']; ?></a>  </td>
						<?php
						
						$SQL2= "SELECT * FROM CRM_zayavka WHERE  issled_id='".intval($_GET['see']) ."'  AND dobrov_id='".intval($arr_dob[$dt['dobrov_id']]['dobrov_id']) ."' LIMIT 1";
						$IntSQL2=mysqli_query($mysql, $SQL2);
						if(!$IntSQL2) die (mysqli_error($mysql));
						while ($arr2=mysqli_fetch_assoc($IntSQL2))
						{								
						$SQL4= "SELECT * FROM CRM_status_z WHERE status_z_id='".intval($arr2['rez_ych']) ."' LIMIT 1";
						$IntSQL4=mysqli_query($mysql, $SQL4);
						if(!$IntSQL4) die (mysqli_error($mysql));
						while ($dt1=mysqli_fetch_assoc($IntSQL4))
						{
						$arr_rez[$dt1['status_z_id']] = $dt1;
						?>
						<td> <?php echo $arr_rez[$arr2['rez_ych']] ['name']; ?></td>
						<?php 
						}
						}
						?>
						<td>
						<?php 
							if (isset($arr5['status']) AND $arr5['status']==3)
							{
								echo '';
							}
							else 
							{
						?>
							<a title="Редактировать" class="fa fa-pencil" href="?page=issled&see_issled=<?php echo intval($_GET['see']); ?>&correction=<?php echo $arr_dob[$dt['dobrov_id']]['dobrov_id']; ?>"></a> 
							
							<a title="Удалить" class="fa fa-trash-o" href="?page=issled&see1=<?php echo intval($_GET['see']); ?>&dobrov_del=<?php echo $arr_dob[$dt['dobrov_id']]['dobrov_id']; ?>"></a> 
							<?php 
							}
							?>
							<a title="Доброволец" class="fa fa-user" href="?page=dobrov&add=1&edit=<?php echo $arr_dob[$dt['dobrov_id']]['dobrov_id']; ?>&return=<?php echo intval($_GET['see']); ?>"></a> 
							<?php 
							$SQL7= "SELECT * FROM CRM_dobrov WHERE CRM_dobrov.dobrov_id='".intval($arr['dobrov_id'])."' AND CRM_dobrov.povtor='1' ";
							$IntSQL7=mysqli_query($mysql, $SQL7);
							if(!$IntSQL7) die (mysqli_error($mysql));
							while ($dtp=mysqli_fetch_assoc($IntSQL7))
							{
                            
							?>
							
							<a title="Дубликат" class="fa fa-exclamation-circle" href="?page=dobrov&dubl=<?php echo $dtp['dobrov_id']; ?>"></a> 
							<?php 
							}
							?>
							
						</td>
					</tr>
				
				<div class="clearfix mtop"></div>
					
					<?
					}
					mysqli_free_result($IntSQL3);
					
					}

					mysqli_free_result($IntSQL);
					$f1= fopen('tmp/dobrov.csv', 'w');
					fputs($f1, $csv);
					fclose($f1);
					?>
					</table>
					
                        <button type="submit" name="DEL_check_dobrov" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span> Удалить выбранные</button>
                   </form>
					<?php 
					}
					
					elseif (isset($_GET['correction']) AND is_numeric($_GET['correction']))
					{
					$SQL= "SELECT * FROM CRM_zayavka WHERE issled_id='".intval($_GET['see_issled']) ."' AND dobrov_id='".intval($_GET['correction']) ."'  LIMIT 1 ";
					$IntSQL=mysqli_query($mysql, $SQL);
					if(!$IntSQL) die (mysqli_error($mysql));
					$arr=mysqli_fetch_assoc($IntSQL);
					{
					$SQL3= "SELECT * FROM CRM_dobrov WHERE dobrov_id='".intval($arr['dobrov_id']) ."' ";
					$IntSQL3=mysqli_query($mysql, $SQL3);
					if(!$IntSQL3) die (mysqli_error($mysql));
					while ($dt=mysqli_fetch_assoc($IntSQL3))
					{
					$arr_dob[$dt['dobrov_id']] = $dt;
					
					$SQL2= "SELECT * FROM CRM_issled WHERE issled_id='".intval($arr['issled_id']) ."' ";
					$IntSQL0=mysqli_query($mysql, $SQL2);
					if(!$IntSQL0) die (mysqli_error($mysql));
					while ($ao=mysqli_fetch_assoc($IntSQL0))
					{
					$arr_is[$ao['issled_id']] = $ao;
					?>
					<form  action='' method='post' enctype='multipart/form-data'>
                        <?php echo isset($_GET['correction']) ? '<input name="dobrov_id" type="hidden" value="'.$_GET['correction'].'" />' : ''; ?>
                        <?php echo isset($_GET['see_issled']) ? '<input name="issled_id" type="hidden" value="'.$_GET['see_issled'].'" />' : ''; ?>
						<div class="form-group has-error">
							<label class="control-label">Название</label> 
							<input type="text" name="name" value="<?php echo isset($arr_is[$arr['issled_id']]['name']) ? $arr_is[$arr['issled_id']]['name'] : ''; ?>" placeholder="" class="form-control" required="" " readonly>
						</div>

						<div class="form-group has-error">
							<label class="control-label">ФИО *</label> 
							<input type="text" name="fio" value="<?php echo isset($arr_dob[$arr['dobrov_id']]['fio']) ? $arr_dob[$arr['dobrov_id']]['fio'] : ''; ?>" placeholder="" class="form-control" required="" " readonly>
						</div>
						
						<div class="form-group  has-error"> 
						<label class="control-label"> Результат участия </label>
							<p> 		
							<select class="form-control chosen-select" name="rez_ych" required>
								<option selected>Выбрать</option>
								<?php
								$SQL2 ="SELECT * FROM CRM_status_z";
								$nSQL = mysqli_query($mysql,$SQL2 ) or die (mysql_error($mysql));
								while ($st = mysqli_fetch_assoc($nSQL))
								{
								?>
								<option value="<?php echo $st['status_z_id']; ?>" <?php echo (isset($arr['rez_ych']) AND ($arr['rez_ych']==$st['status_z_id']))?'selected':''; ?> ><?php echo $st['name']; ?></option>
								<?php	
								}
								
								?>	
							</select>
						</div>
						
						<div class="form-group">
							<label class="control-label">Рандомизационный номер</label> 
							<input type="text" name="random" value="<?php echo isset($arr['random']) ? $arr['random'] : ''; ?>" placeholder="" class="form-control"  " >
						</div>
						
						<div class="form-group">
							<label class="control-label">Комментарий </label>
							<textarea name="comment" class="form-control" rows="3"><?php echo isset($arr['comment'])?$arr['comment']:'';?> </textarea>
						</div>
						
						<div class="col-lg-6 col-lg-offset-3 text-center">
							<button name="Add_correction" class="btn btn-sm btn-primary" type="submit"><strong>Сохранить</strong></button>
							<a class="btn btn-sm btn-danger" href="?page=issled"><i class="fa fa-times"></i> Закрыть</a>
						</div>
						<div class="clearfix mtop"></div>
						
						
					</form>	
					
					<?php
					}
					mysqli_free_result($IntSQL);
					}
					mysqli_free_result($IntSQL3);
					}
					
					mysqli_free_result($IntSQL0);
					}
					elseif (isset($_GET['addfile']))
					{
					?>
					 <form enctype="multipart/form-data" method="post">
						<p>Загрузите ваш файл на сервер</p>
						<p><input type="file" name="file" ></p>


						 <div class="form-group"> 
							<label class="control-label">Кодировка</label>
								<p> 		
								<select class="form-control chosen-select" name="cod" required>
									<option selected>Выбрать</option>
									<?php
									$arr_cod = array("Windows-1251","UTF-8","Windows-1252" );
									foreach ($arr_cod as $key =>$zn)
									{
									?>
									<option value="<?php echo $key; ?>" ><?php echo $zn; ?></option>
									<?php	
									}
									
									?>	
								</select>
						</div>

						<button type="submit" name="ADD_FILE" class="btn btn-info btn-md"><span class="fa fa-plus-square"></span> Добавить файл</button>
					</form>

					<?php
					}
					
					elseif (isset($_GET['add_dobrov']))
					{ 
					?>

						<table class="table table-hover">
						<h3> <?php echo mb_strtolower('Выберите добровольцев из списка'); ?> </h3>
							<tr>
								<th><input type="checkbox" /></th>
								<th>ID <input type="text" class="form-control" name="id_s" style="width:5%;"> </th>
								<th>ФИО <input type="text" class="form-control" name="fio_s"> </th>
								<th>Дата рождения <input type="text" class="form-control datas" name="birthday"></th>
								<th>Телефон <input type="text" class="form-control" name="phone"> </th>
								<th>Последнее участие <input type="text" class="form-control datas" name="last_ych"></th>
							</tr>
					
							<form method="POST" role="form">	
							 <input type="hidden" name="page" value="dobrov">
							<?php echo isset($_GET['number']) ? '<input name="issled_id" type="hidden" value="'.$_GET['number'].'" />' : ''; ?>
							<?php
								$SQL = "SELECT * FROM CRM_dobrov WHERE zan=0";
								$r = mysqli_query($mysql,$SQL);
								if(!$r) exit(mysqli_error($mysql));
								while ($hk=mysqli_fetch_assoc($r))
								{
								
								?>
								<tr <?php echo $hk['block']==1?'class="danger"':''; ?>>
                                    <td><label> 
										<input type="checkbox" name="add[<?php echo $hk['dobrov_id']; ?>]" value="1"/>
										</label>
									</td>
                                    <td><?php echo $hk['dobrov_id']; ?> </td>
									<td><?php echo $hk['fio']; ?></td>
									<td><?php echo date ("d.m.Y",$hk['birthday']); ?></td>
									<td><?php echo $hk['phone']; ?></td>
									<td> </td>
									
								</tr>
								
								<?php	
							}
							mysqli_free_result($r);	
							?>
						</table>
						<button type="submit" name="ADD_dobrov_v_issled" class="btn btn-info btn-md"><span class="fa fa-plus-square"></span> Добавить выбранные</button>
						</form>
						<div class="clearfix mtop"></div>
						<?php
					}
					else
						{
							if (isset($_GET['uncheck']))
							{
							unset($_GET['status']);
							}
							
							if (isset($_GET['id_s']) OR isset($_GET['name_s']) OR isset($_GET['date_start_s']) OR isset($_GET['date_stop_s']))
							{              
							$q1 = isset($_GET['id_s'])?trim($_GET['id_s']):'';
							$q2 = isset($_GET['name_s'])?trim($_GET['name_s']):'';
							$q3 = isset($_GET['date_start_s'])?strtotime($_GET['date_start_s']):'';
							$q4 = isset($_GET['date_stop_s'])?strtotime($_GET['date_stop_s']):'';
							}
                        $WHERE1 = (isset($q1) AND !empty($q1))?"AND issled_id LIKE '%".$q1."%'":'';
                        $WHERE2 = (isset($q2) AND !empty($q2))?"AND name LIKE '%".$q2."%'":'';
                        $WHERE3 = (isset($q3) AND !empty($q3))?"AND date_start LIKE '%".$q3."%'":'';
                        $WHERE4 = (isset($q4) AND !empty($q4))?"AND date_stop LIKE '%".$q4."%'":'';
                        
                        $r4 = mysqli_query($mysql,"SELECT * FROM CRM_status_issled");
                        if(!$r4) exit(mysqli_error($mysql));
                        while ($hk4=mysqli_fetch_assoc($r4))
                        {
						?>
                        <a class="label label-<?php echo (isset($_GET['status']) AND $_GET['status'] == $hk4['status_issled_id'])?'success':'default'; ?> m4" href="?page=issled&status=<?php echo $hk4['status_issled_id'];?><?php echo (isset($_GET['status']) AND $_GET['status'] == $hk4['status_issled_id'])?'&uncheck='.$hk4['status_issled_id']:''; ?><?php echo (isset($_GET['date_start_s'])?'&date_start_s='.$_GET['date_start_s'].'&date_stop_s='.$_GET['date_stop_s']:''); ?><?php echo (isset($_GET['name_s'])?'&name_s='.$_GET['name_s']:''); ?>"><span class="glyphicon glyphicon-<?php echo (isset($_GET['status']) AND $_GET['status'] == $hk4['status_issled_id'])?'check':'unchecked'; ?>"></span> <?php echo $hk4['name'];?></a>
						<?php 
						}
						mysqli_free_result($r4);	
						?>
							
                        <?php echo isset($_GET['status'])?'<a class="label label-danger" href="?page=issled"><span class="glyphicon glyphicon-remove"></span> Все исследования </a>':''; ?>
                        <div class="clearfix"></div>
                        <br>
						<table class="table table-hover">
							<form id="formsubmit" action="" method="GET" name="forma1">
							<input type="hidden" name="page" value="issled">
							<tr>
								<th> </th>
								
								<th>ID <input type="text" class="form-control" name="id_s" id="id_s">  </th>
					
								<th>Название <input type="text" class="form-control" name="name_s"></th>
								<th>Начало 
                                    <div id="researchsearch-startdate-kvdate" class="input-group  date">
                                        <span class="input-group-addon kv-date-picker" title="Выбрать дату">
                                            <i class="glyphicon glyphicon-calendar kv-dp-icon"></i>
                                        </span> 
                                        <input type="text" id="researchsearch-startdate" class="form-control krajee-datepicker datas" name="date_start_s" data-datepicker-source="researchsearch-startdate-kvdate" data-datepicker-type="2" data-krajee-kvdatepicker="kvDatepicker_1643d6f1">
                                    </div>
                                </th>
								<th>Окончание
								<div id="researchsearch-startdate-kvdate" class="input-group  date">
                                        <span class="input-group-addon kv-date-picker" title="Выбрать дату">
                                            <i class="glyphicon glyphicon-calendar kv-dp-icon"></i>
                                        </span> 
                                        <input type="text" class="form-control datas" name="date_stop_s" autocomplete="off">
                                    </div>
								</th>
								<th>Действия <p><button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Искать</button></p></th>
							</tr>
							</form>
							
							<form method="POST" role="form">
							<?php
							$i = 0;
							$status = isset($_GET['status'])?" AND status='". intval($_GET['status']) ."'":" ";
							$SQL = "SELECT * FROM CRM_issled WHERE issled_id>0  ".$status ." ".$WHERE1." ".$WHERE2." ".$WHERE3." ".$WHERE4."  ";
								
							$r = mysqli_query($mysql,$SQL);
								if(!$r) exit(mysqli_error($mysql));
								while	($hk=mysqli_fetch_assoc($r))
								{
								?>
								<tr <?php 
										echo (isset($hk['date_stop']) AND ($hk['date_stop']>time()) AND ($hk['status']==1 ))?'class="success"':''; 
										echo (isset($hk['date_stop']) AND ($hk['date_stop']<time()) AND ($hk['status']==1 ))?'class="danger"':''; 
										echo (isset($hk['date_stop']) AND ($hk['date_stop']<time()) AND ($hk['status']==2 ))?'class="warning"':''; 
								?>>
									<td>
										<label> 
										<?php 
											if (isset($hk['status']) AND $hk['status'] ==3 )
											{
												echo '';
											}
											else 
											{
										?>
										<input type="checkbox" name="del[<?php echo $hk['issled_id']; ?>]" value="1"/>
										<?php
										}
										?>
										
											
										</label>
									</td>
									<td style="width:15%;"><?php echo $hk['issled_id']; ?> </td>
									<td><?php echo $hk['name']; ?></td>
									<td><?php echo date ("d.m.Y",$hk['date_start']); ?></td>
									<td><?php echo  date ("d.m.Y",$hk['date_stop']); ?></td>
									<td>
                                        
										<a title="Просмотр" class="fa fa-tasks" href="?page=issled&see=<?php echo $hk['issled_id']; ?>"></a> 
										<a title="Редактировать" class="fa fa-pencil" href="?page=issled&add=1&edit=<?php echo $hk['issled_id']; ?>"></a> 
										<?php 
											if ($hk['status'] ==3)
											{
												echo '';
											}
											else 
											{
										?>
										<a title="Удалить" class="fa fa-trash-o" href="?page=issled&delete=<?php echo $hk['issled_id']; ?>"></a>
										<a class="fa fa-share-square-o linkos" data-link="http://<?php echo $HTTP_HOST; ?>/register.php?issled=<?php echo $hk['issled_id']; ?>&step=1" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg"></a>
										<?php
										}
										?>
									</td>
								</tr>
								<?php	
								$i++;
								}
							mysqli_free_result($r);	
							
							?>
							<tr>
								<td colspan="6">
									<button type="submit" name="SBM_del1" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span> Удалить выбранные</button>
								</td>
							</tr>
							</form>
						</table>
						
						<div class="clearfix mtop"></div>
							
						<?php
						}
						?>
					</div>
				</div>
			</div>
		

		</div>
	</div>



	<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">

				<div class="panel panel-default" style="margin-bottom: 0px;">
					<div class="panel-heading">
						Заголовок <small class="pull-right"><button type="button" class="btn btn-danger btn-xs" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button></small>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="control-label">Ссылка на исследование</label> 
							<input id="linkos" type="text" class="form-control">
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
