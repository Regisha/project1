<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS CRM_worker(
	id int auto_increment primary key 	COMMENT 'user id',
	mail varchar(20) NOT NULL 			COMMENT 'Логин он же @mail',
	pass varchar(32) NOT NULL 			COMMENT 'Пароль',
	family varchar(50) NOT NULL 		COMMENT 'Фамилия',
	name varchar(50) NOT NULL 			COMMENT 'Имя',
	otch varchar(50) NOT NULL 			COMMENT 'Отчество',
	adress varchar(500) NOT NULL 		COMMENT 'Адрес',
	tel varchar(20) NOT NULL 			COMMENT 'Телефон',
	doljn varchar(50) NOT NULL 			COMMENT 'Должность',
	about TEXT NOT NULL 				COMMENT 'О себе',
	img varchar(300) NOT NULL 			COMMENT 'Аватарка',
	status int(1) NOT NULL 				COMMENT 'Статус 0-Полный, 1-Ограниченный...'
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Пользователи CRM'");	
	
	$arr_status = array('Полные','Ограниченные');
	
	if (isset($_POST['Sbmt_start']))
	{
// 	echo '<pre>';
// 	print_r($_POST);
// 	echo '</pre>';
// 	echo '<pre>';
// 	print_r($_FILES);
// 	echo '</pre>';
// 	exit();
	$family = mysqli_real_escape_string($mysql, trim($_POST['family']));
	$name = mysqli_real_escape_string($mysql, trim($_POST['name']));
	$otch = isset($_POST['otch']) ? mysqli_real_escape_string($mysql, trim($_POST['otch'])) : '';
	
	$tel = isset($_POST['tel']) ? mysqli_real_escape_string($mysql, tel_replace($_POST['tel'])) : '';
	$adres = isset($_POST['adres']) ? mysqli_real_escape_string($mysql, trim($_POST['adres'])) : '';
	$doljn = isset($_POST['doljn']) ? mysqli_real_escape_string($mysql, trim($_POST['doljn'])) : '';
	$about = isset($_POST['about']) ? mysqli_real_escape_string($mysql, trim($_POST['about'])) : '';
	
	$mail = mysqli_real_escape_string($mysql,$_POST['mail']);
	$pass = mysqli_real_escape_string($mysql,$_POST['pass']);
	$currentpass = isset($_POST['currentpass']) ? mysqli_real_escape_string($mysql,$_POST['currentpass']) : $pass;
	$pass = !empty($pass)?md5($pass):$currentpass;
	
	$status =  intval($_POST['status']);
	$IMG = isset($_POST['tecimg'])?mysqli_real_escape_string($mysql,$_POST['tecimg']):'';
		
		if (isset($_FILES['foto']) AND !empty($_FILES['foto']['name']))
		{
		$ext = getExtension1($_FILES['foto']['name']);
		$RuName = str_replace('.'.$ext,'',$_FILES['foto']['name']);
		$img = md5($RuName.time()).'.'.$ext;
		copy ($_FILES['foto']['tmp_name'], $temp_dir.$img) or die ("Ошибка загрузки фото - ".$_FILES['foto']['name']);
		resizer_image($temp_dir.$img, 256, 256);
		$IMG = $img;
		}	
	
		if (isset($_POST['worker_id']))
		{
		$worker_id = intval($_POST['worker_id']);
		$query_count = "UPDATE CRM_worker SET mail='".$mail."',pass='".$pass."',family='".$family."',name='".$name."',otch='".$otch."',adress='".$adres."',tel='".$tel."',doljn='".$doljn."',about='".$about."',img='".$IMG."',status='".$status."' WHERE id='".$worker_id."' LIMIT 1";
		mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_worker id:'.$worker_id);
		}
		else
			{
			$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_worker (mail,pass,family,name,otch,adress,tel,doljn,about,img,status) VALUES ('".$mail."','".$pass."','".$family."','".$name."','".$otch."','".$adres."','".$tel."','".$doljn."','".$about."','".$IMG."','".$status."')");
			if(!$insertSQL) die(mysqli_error($mysql));
			$worker_id = mysqli_insert_id($mysql);
			}
	die ("<meta http-equiv=refresh content='0; url=?page=workers'>");		
	}
	
	if (isset($_GET['del']))
	{
	$query = "DELETE FROM CRM_worker WHERE CRM_worker.id='".intval($_GET['del'])."' LIMIT 1";
	mysqli_query($mysql,$query) or die(mysql_error());

	die ("<meta http-equiv=refresh content='0; url=?page=workers'>");
	}	
	
?>
	
	<div class="row border-bottom white-bg dashboard-header">
		<div class="col-md-12">
			<h2>Пользователи CRM</h2>
			<p><a class="btn btn-sm btn-primary" href="?page=workers&add=true"><i class="fa fa-user-plus"></i> Добавить пользователя</a></p>
		</div>
	</div>
	
	<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
	<?php
	if (isset($_GET['delete']))
	{
	?>
	<div class="alert alert-dismissible alert-danger col-lg-6 col-lg-offset-3">
		<h3 class="text-center text-danger">Подтверждение удаления</h3>
		<strong>Выбранный пользователь будет удален из базы</strong><br /> 
		<p>Это безопасно.</p>
		
		<p class="text-center">
			<a class="btn btn-sm btn-primary" href="?page=workers&del=<?php echo $_GET['delete']; ?>"><i class="fa fa-trash-o"></i> Удалить пользователя</a>
			<a class="btn btn-sm btn-danger" href="?page=workers&profile=<?php echo $_GET['delete']; ?>"><i class="fa fa-times"></i> Отменить</a>
		</p>
	</div>
	
	<div class="clearfix"></div>
	<?php
	}	
	elseif (isset($_GET['add']))
	{
		if (isset($_GET['edit']))
		{
			$result = mysqli_query($mysql, "SELECT	* FROM CRM_worker WHERE CRM_worker.id='". intval($_GET['edit']) ."' LIMIT 1") or  die(trigger_error(mysqli_error($mysql).' - CRM_worker_EDIT'));
			$arr = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
		}	
	?>		
		<form  action='' method='post' enctype='multipart/form-data'>
		<div class="col-lg-8">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Добавить нового пользователя CRM</h5>
					<small class="pull-right"><a class="btn btn-xs btn-danger" href="?page=workers"><i class="fa fa-times"></i></a></small>
				</div>
				<div class="ibox-content">
					<div class="row">
					
						<div class="col-sm-6 b-r">
							<h4>Личные данные</h4>
								<div class="form-group has-error">
									<label class="control-label">Фамилия</label> 
									<input type="text" name="family" value="<?php echo isset($arr['family']) ? $arr['family'] : ''; ?>" placeholder="Иванов" class="form-control" required="">
								</div>
								<div class="form-group has-error">
									<label class="control-label">Имя</label> 
									<input type="text" name="name" value="<?php echo isset($arr['name']) ? $arr['name'] : ''; ?>" placeholder="Иван" class="form-control" required="">
								</div>
								<div class="form-group">
									<label>Отчество</label> 
									<input type="text" name="otch" value="<?php echo isset($arr['otch']) ? $arr['otch'] : ''; ?>" placeholder="Иванович" class="form-control">
								</div>
								<div class="form-group">
									<label>Телефон</label> 
									<input data-mask="+7(999) 999-9999" type="text" name="tel" value="<?php echo isset($arr['tel']) ? $arr['tel'] : ''; ?>" placeholder="+7" class="form-control">
								</div>
								<div class="form-group">
									<label class="control-label">Адрес</label>
									<textarea name="adres" class="form-control" rows="2"><?php echo isset($arr['adress']) ? $arr['adress'] : ''; ?></textarea>
								</div>
								
								<div class="clearfix mtop"></div>
								<div class="hr-line-dashed"></div>
								
								<h4>Другие данные</h4>
								<div class="form-group">
									<label class="control-label">Права доступа</label>
									<select class="form-control" name="status" required>
										<option <?php echo (isset($arr['status']) AND $arr['status'] == 1) ? 'selected' : ''; ?> value="1">Ограниченные</option>
										<option <?php echo (isset($arr['status']) AND $arr['status'] == 0) ? 'selected' : ''; ?> value="0">Полные</option>
									</select>
								</div>
								<div class="form-group">
									<label>Должность</label> 
									<input type="text" name="doljn" value="<?php echo isset($arr['doljn']) ? $arr['doljn'] : ''; ?>" placeholder="Арт-директор" class="form-control">
								</div>                                    
								<div class="form-group">
									<label class="control-label">О себе (примечание)</label>
									<textarea name="about" class="form-control" rows="2"><?php echo isset($arr['about']) ? $arr['about'] : ''; ?></textarea>
								</div>                                    
						</div>
						
						<div class="col-sm-6">
							<h4>Фото</h4>
							<div id="see_img" class="col-md-12">
								<div class="col-md-6 col-md-offset-3">
									<?php echo isset($arr['img']) ? '<a href="'.$img_dir.$arr['img'].'" title=""><img class="center-block img-responsive" src="'.$img_dir.$arr['img'].'" alt=""></a>' : '<img class="center-block img-responsive" src="programm_files/images/noimage.png" alt="">'; ?>
									<div class="clearfix"></div>
									<a id="btn_upload" href="javascript:void(0)" class="btn btn-default btn-block mtop btn-lg">Загрузить</a>
								</div>
							</div>
							
							<div id="load_img" class="col-md-12 hidden">
								<div class="pull-right clearfix"><a id="btn_upload_close" href="javascript:void(0)" class="btn btn-danger btn-xs btn_upload_close">Отменить</a></div>
								<div id="load_img_upload1">
									<div class="col-md-12 text-center">
										<div id="image-preview" style="min-height:32px;">
											<img width="256" class="img-thumbnail" id="preview" src="<?php echo (isset($arr['img']) AND !empty($arr['img']))?$img_dir.$arr['img']:'programm_files/images/noimage.png'; ?>" alt="">
										</div>
										<input type="file" name="foto" id="image">
										<div class="clearfix mtop"></div>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							
							<div class="clearfix mtop"></div>
							<div class="hr-line-dashed"></div>
							
							<div class="alert alert-info">
								<h4>Доступы</h4>
								<div class="form-group has-error">
									<label class="control-label">Электронный адрес e-mail</label> 
									<input type="text" name="mail" value="<?php echo isset($arr['mail']) ? $arr['mail'] : ''; ?>" placeholder="@mail" class="form-control" required="">
								</div>
								<div class="form-group">
									<label class="control-label">Пароль</label> 
									<input type="text" name="pass" placeholder="***xxx***" class="form-control">
								</div>
							</div>	
						</div>
						
						<div class="clearfix mtop"></div>
						
						<?php echo isset($arr['img']) ? '<input name="tecimg" type="hidden" value="'.$arr['img'].'" />' : ''; ?>
						<?php echo isset($arr['id']) ? '<input name="worker_id" type="hidden" value="'.$arr['id'].'" />' : ''; ?>    
						<?php echo isset($arr['pass']) ? '<input name="currentpass" type="hidden" value="'.$arr['pass'].'" />' : ''; ?> 
						
						<div class="col-lg-6 col-lg-offset-3 text-center">
							<button name="Sbmt_start" class="btn btn-sm btn-primary" type="submit"><strong>Сохранить</strong></button>
							<a class="btn btn-sm btn-danger" href="?page=workers"><i class="fa fa-times"></i> Закрыть</a>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		</form>
		
		<div class="col-lg-4">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Помощь</h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<ul>
							<li class="text-danger">Обязательно к заполению</li>
							<li>Ваш электронный адрес (@mail) это логин</li>
							<li>Загрузите изображение, если оно отсутствует</li>
							<li>Заполните как можно больше полей</li>
						</ul>
						<?php
						if (isset($arr['id']))
						{
						?>
						<div class="clearfix mtop"></div>
						<div class="hr-line-dashed"></div>						
						
						<div class="p-md">
							<a title="Удалить" class="btn btn-danger btn-sm" href="?page=workers&delete=<?php echo $arr['id']; ?>"><span class="glyphicon glyphicon-trash"></span> Удалить пользователя</a>
							
							<a title="Просмотр профиля" class="btn btn-info btn-sm" href="?page=workers&profile=<?php echo $arr['id']; ?>"><span class="glyphicon glyphicon-eye-open"></span> Просмотр профиля</a> 
						</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>            
		</div>

	<?php
	}
	else
		{
		$status = isset($_GET['status'])?intval($_GET['status']):0;
		$SQL_status = isset($_GET['status'])?" WHERE CRM_worker.status='".$status."'" :'';
		?>

		<div class="row">
			<div class="col-sm-8">
				<div class="ibox">
					<div class="ibox-content">
			
						<div class="tab-content">
						<table class="table table-hover mtop">
							<tr>
								<th></th>
								<th></th>
								<th>ФИО</th>
								<th class="hidden-xs">Тел</th>
								<th>Права доступа</th>
								<th class="hidden-xs">Операции</th>
							</tr>	
							<?php
							$arr_id = array();
							$SQLs = "SELECT * FROM CRM_worker ".$SQL_status." ORDER BY CRM_worker.name";
							$result = mysqli_query($mysql, $SQLs) or  die(mysqli_error($mysql).' - CRM_worker');
							while( $hk = mysqli_fetch_assoc($result) )
							{ 
							$arr_id[$hk['id']] = $hk['id'];
							?>
							<tr <?php echo (isset($_GET['profile']) AND $_GET['profile'] == $hk['id']) ? 'class="active"' : ''; ?>>
								<td class="hidden-xs"><div id="get_worker_status-<?php echo $hk['id']; ?>" class="get_worker_status" data-workerid="<?php echo $hk['id']; ?>"></div></td>
								<td class="client-avatar">
									<img class="img-circle" id="preview" src="<?php echo (isset($hk['img']) AND !empty($hk['img']))?$img_dir.$hk['img']:'programm_files/images/noimage.png'; ?>" alt="">
								</td>
								<td class="text-left">
									<a data-toggle="tab" href="#contact-<?php echo $hk['id']; ?>" class="client-link"><?php echo $hk['family']; ?> <?php echo $hk['name']; ?> <?php echo !empty($hk['otch'])?$hk['otch']:''; ?></a>
								</td>
								<td class="hidden-xs"><?php echo $hk['tel']; ?></td>
								<td><?php echo $arr_status[$hk['status']]; ?></td>
								<td class="hidden-xs">
									<a data-toggle="tab" title="Просмотр" class="btn btn-info btn-sm" href="#contact-<?php echo $hk['id']; ?>"><span class="glyphicon glyphicon-eye-open"></span></a> 
									<a title="Редактировать" class="btn btn-success btn-sm" href="?page=workers&add=1&edit=<?php echo $hk['id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a> 
									<a title="Удалить" class="btn btn-danger btn-sm" href="?page=workers&delete=<?php echo $hk['id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
								</td>
							</tr>
							<?php
							}
							mysqli_free_result($result);
							?>
						</table>
						</div>
						
					</div>
				</div>			
			</div>
			
			<div class="col-sm-4">
				<div class="ibox ">

					<div class="ibox-content">
						<div class="tab-content">
						<?php
						if (sizeof($arr_id) > 0)
						{
							$SQLs = "SELECT * FROM CRM_worker WHERE CRM_worker.id IN(".implode(',',$arr_id).") ORDER BY CRM_worker.name";
							$result = mysqli_query($mysql, $SQLs) or  die(mysqli_error($mysql).' - CRM_worker');
							while( $hk = mysqli_fetch_assoc($result) )
							{ 
							?>
							<div id="contact-<?php echo $hk['id']; ?>" class="tab-pane <?php echo (isset($_GET['profile']) AND $_GET['profile'] == $hk['id']) ? 'active' : ''; ?>">
								<div class="row m-b-lg text-center">
									<div class="col-lg-12 navy-bg text-center">
										<h2><?php echo $hk['family']; ?> <?php echo $hk['name']; ?> <?php echo !empty($hk['otch'])?$hk['otch']:''; ?></h2>

										<div class="m-b-md">
											<img alt="image" class="img-circle circle-border m-b-md" src="<?php echo (isset($hk['img']) AND !empty($hk['img']))?$img_dir.$hk['img']:'programm_files/images/noimage.png'; ?>">
										</div>

										<?php echo !empty($hk['doljn'])?'<p>'.$hk['doljn'].'</p>':''; ?>
									</div>
								</div>
								<div class="client-detail">
									<div class="text-center mtop">	
										<a class="btn btn-success btn" href="?page=workers&add=1&edit=<?php echo $hk['id']; ?>"><span class="glyphicon glyphicon-pencil"></span> Редактировать</a> 
										<a class="btn btn-danger btn" href="?page=workers&delete=<?php echo $hk['id']; ?>"><span class="glyphicon glyphicon-trash"></span> Удалить</a>
									</div>
									
									<div class="clearfix mtop"></div>
									
									<strong>Остальные данные</strong>

									<ul class="list-group clear-list">
										<li class="list-group-item fist-item">
											<span class="pull-right"><?php echo $hk['tel']; ?></span>
											Телефон
										</li>
										<li class="list-group-item">
											<span class="pull-right"><?php echo $hk['mail']; ?></span>
											e-mail
										</li>                                            
										<li class="list-group-item">
											<span class="pull-right"><?php echo !empty($hk['adress'])?$hk['adress']:' - '; ?></span>
											Адрес
										</li>
									</ul>
									<strong>Примечание</strong>
									<p><?php echo !empty($hk['about'])?$hk['about']:' - '; ?></p>
								</div>
							</div>
							<?php
							}
							mysqli_free_result($result);
						}
						?>
						</div>
					</div>
				</div>
			</div>				
			
		</div>
		<?php
		}
		?>
	</div>
	</div>
	
