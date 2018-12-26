<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}
	
    mysqli_query($mysql, "CREATE TABLE IF NOT EXISTS CRM_dobrov (
        dobrov_id int auto_increment primary key,
        fio varchar(150) NOT NULL,
        pol int(2) NOT NULL,
        birthday varchar(11) NOT NULL, 
        phone varchar(15) NOT NULL, 
	 phone2 varchar(15) NOT NULL, 
        snils varchar(15) NOT NULL,
        vk varchar(50) NOT NULL, 
        last_ych varchar(11) NOT NULL, 
        rating float(5) NOT NULL, 
        block int(1) NOT NULL,
        edit int(1) NOT NULL,
	zan int(5) NOT NULL,
        povtor int(1) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Добровольцы'");
	

	
	if (isset($_POST['Add_DB']))
	{
// 	look ($_POST);
	$fio = mysqli_real_escape_string($mysql,trim(strip_tags($_POST['fio'])));
	$pol = isset($_POST['pol'])?intval($_POST['pol']):0;
	$birthday = isset($_POST['birthday'])?strtotime($_POST['birthday']):'';
	$phone = isset($_POST['phone'])?tel_replace($_POST['phone']):'';
	$phone2 = isset($_POST['phone2'])?tel_replace($_POST['phone2']):'';
	$snils = isset($_POST['snils'])?trim($_POST['snils']):'';
	$vk = isset($_POST['vk'])?trim($_POST['vk']):'';
	$last_ych = isset($_POST['last_ych'])?strtotime($_POST['last_ych']):'';
	$rating = isset($_POST['rating'])?trim($_POST['rating']):'1';
	$block = isset($_POST['block'])?'1':'0';

	
			if (isset($_POST['dobrov_id']))
			{
			$dobrov_id = intval($_POST['dobrov_id']);
			$query_count = "UPDATE CRM_dobrov SET fio='".$fio."',pol='".$pol."',birthday='".$birthday."',phone='".$phone."',phone2='".$phone2."',snils='".$snils."',vk='".$vk."',last_ych='".$last_ych."', rating='".$rating."',block='".$block."'  WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
			mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
			
			}
			else
			{
			$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_dobrov (fio,pol,birthday,phone,snils,vk, last_ych, rating ,block,zan) VALUES ('".$fio."','".$pol."','".$birthday."','".$phone."','".$snils."','".$vk."','".$last_ych."','1','".$block."','0')");
			if(!$insertSQL) die(mysqli_error($mysql));
			$dobrov_id= mysqli_insert_id($mysql);
			}

        if (isset($_POST['return']))
        {
         die ("<meta http-equiv=refresh content='0; url=?page=issled&see=".$_GET['return']."'>");
        }
        else
        {
        die ("<meta http-equiv=refresh content='0; url=?page=dobrov'>");
        }
 
	}
	
	if (isset($_GET['del']))
	{
	$query = "DELETE FROM CRM_dobrov WHERE CRM_dobrov.dobrov_id='".intval($_GET['del'])."' LIMIT 1";
	mysqli_query($mysql,$query) or die(mysql_error());

	die ("<meta http-equiv=refresh content='0; url=?page=dobrov'>");
	}	
	
	if (isset($_POST['SBM_del']))
	{
		if (isset($_POST['delme']))
		{
			foreach($_POST['delme'] as $dobrov_id => $zn)
			{
			$queryT = "DELETE FROM CRM_dobrov WHERE CRM_dobrov.dobrov_id='".intval($dobrov_id)."' LIMIT 1 ";
			mysqli_query($mysql,$queryT) or die(mysqli_error($mysql));
			}
		}
	die ("<meta http-equiv=refresh content='0; url=?page=dobrov'>");	
	}
	
	
	if (isset($_POST['edit_dovrov'])) 
	{
		$dobrov_id = intval($_POST['dobrov_id']);
		$SQL1 = "SELECT * FROM CRM_dobrov_edit WHERE dobrov_id='".$dobrov_id."' LIMIT 1 ";
		$r1 = mysqli_query($mysql,$SQL1);
		if(!$r1) exit(mysqli_error($mysql));
		$hk2=mysqli_fetch_assoc($r1);
		mysqli_free_result($r1); 
		
		$query_count = "UPDATE CRM_dobrov SET fio='".$hk2['fio']."',pol='".$hk2['pol']."',birthday='".$hk2['birthday']."',phone='".$hk2['phone']."',snils='".$hk2['snils']."',vk='".$hk2['vk']."', edit='2'  WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
		mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
		
		die ("<meta http-equiv=refresh content='0; url=?page=dobrov&smena_status'>");
	}
	
	
	if (isset($_POST['edit_dovrov_otklon'])) 
	{
		$dobrov_id = intval($_POST['dobrov_id']);
			$query_count = "UPDATE CRM_dobrov SET edit='3'  WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
		mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
		
			die ("<meta http-equiv=refresh content='0; url=?page=dobrov&smena_status_otklon'>");
	}
	
	
	if (isset($_POST['add_povtor'])) 
	{
		$dobrov_id = intval($_POST['dobrov_id']);
			$query_count = "UPDATE CRM_dobrov SET povtor='0'  WHERE dobrov_id='".$dobrov_id."' LIMIT 1";
		mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
		
			die ("<meta http-equiv=refresh content='0; url=?page=dobrov'>");
	}
	
	
	if (isset($_POST['del_dubl'])) 
	{
        $dobrov_id = intval($_POST['dobrov_id']);
		$queryT = "DELETE FROM CRM_dobrov WHERE CRM_dobrov.dobrov_id='".intval($dobrov_id)."' LIMIT 1 ";
        mysqli_query($mysql,$queryT) or die(mysqli_error($mysql));
		
        die ("<meta http-equiv=refresh content='0; url=?page=dobrov'>");
	}
	
    if (isset($_POST['skleit'])) 
	{
//     look($_POST);
    $dobrov_id_original = intval($_POST['dobrov_id_original']);
    $dobrov_id_dubl =  intval($_POST['dobrov_id_dubl']);
    $query_count = "UPDATE CRM_dobrov SET phone2='".$_POST['phone2']."'  WHERE dobrov_id='".$dobrov_id_original."' LIMIT 1";
    mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_dobrov id:'.$dobrov_id);
    
        $queryT = "DELETE FROM CRM_dobrov WHERE CRM_dobrov.dobrov_id='".intval($dobrov_id_dubl)."' LIMIT 1 ";
        mysqli_query($mysql,$queryT) or die(mysqli_error($mysql));
        die ("<meta http-equiv=refresh content='0; url=?page=dobrov'>");
	}
	
?>
	
	<div class="row border-bottom white-bg dashboard-header">
		<div class="col-md-12">
			<h2>Добровольцы</h2>
			<p><a class="btn btn-sm btn-primary" href="?page=dobrov&add=true"><i class="fa fa-plus-square"></i> Добавить нового добровольца</a></p>
		</div>
	</div>
	
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<!--Это блок из 12-->
			<div class="col-md-12">
				<div class="ibox">
					<div class="ibox-content">	
					<?php
					if (isset($_GET['delete']))
					{
					?>
					<div class="alert alert-dismissible alert-danger col-lg-6 col-lg-offset-3">
						<h3 class="text-center text-danger">Подтверждение удаления</h3>
						<strong>Выбранный доброволец будет удален из базы</strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<a class="btn btn-sm btn-primary" href="?page=dobrov&del=<?php echo $_GET['delete']; ?>"><i class="fa fa-trash-o"></i> Удалить добровольца</a>
							<a class="btn btn-sm btn-danger" href="?page=dobrov"><i class="fa fa-times"></i> Отменить</a>
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
                    if (isset($_POST['delete']))
                    {
                        foreach ($_POST['delete'] as $id => $v)
                        {
                        echo '<input type="hidden" name="delme['.$id.']" value="'.$id.'">';
                        }
                    }
					?>
					<div class="alert alert-dismissible alert-danger col-lg-6 col-lg-offset-3">
						<h3 class="text-center text-danger">Подтверждение удаления</h3>
						<strong>Выбранные добровольцы будут удалены из базы</strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<button type="submit" name="SBM_del" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span> Удалить выбранные</button>
							<a class="btn btn-sm btn-danger" href="?page=dobrov"><i class="fa fa-times"></i> Отменить</a>
						</p>
					</div>
					</form>
					
					<div class="clearfix"></div>
					<?php
					}
					elseif (isset($_GET['smena_status']))
					{
					?>
					<div class="alert alert-dismissible alert-info col-lg-6 col-lg-offset-3">
						<h3 class="text-center text-info">Данные добровольца были успешно изменены. </h3>
						<strong>  </strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<a class="btn btn-sm btn-primary" href="?page=dobrov"><i class="fa fa-trash-o"></i> ОК</a>
						</p>
					</div>
					
					<div class="clearfix"></div>
					<?php
					}		
					elseif (isset($_GET['add']))
					{
						if (isset($_GET['edit']))
						{
							$result = mysqli_query($mysql, "SELECT	* FROM CRM_dobrov WHERE CRM_dobrov.dobrov_id='". intval($_GET['edit']) ."' LIMIT 1") or  die(trigger_error(mysqli_error($mysql).' - CRM_dobrov'));
							$arr = mysqli_fetch_assoc($result);
							mysqli_free_result($result);
						}	
					?>	
					<h5><?php echo isset($arr['dobrov_id']) ? 'Редактировать' : 'Добавить'; ?> исследование</h5>
					<form  action='' method='post' enctype='multipart/form-data'>
						<div class="form-group has-error">
							<label class="control-label">ФИО *</label> 
							<input type="text" name="fio" value="<?php echo isset($arr['fio']) ? $arr['fio'] : ''; ?>" placeholder="" class="form-control" required="" ">
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
                            <label class="control-label"> Дата рождения</label>
                                <input class="form-control datas" name="birthday" type="text" value="<?php echo(isset($arr['birthday']) AND !empty($arr['birthday'])) ? date("d.m.y",$arr['birthday']) : ''; ?>" />
                                                
                        </div>	
						
						
						<div class="form-group has-error">
                            <label for="phone" class="control-label"> Телефон 1*</label>
                                <input   data-mask="+7(999) 999-99-99" class="form-control" name="phone" id="phone"  type="text" value="<?php echo isset($arr['phone']) ? $arr['phone'] : ''; ?>" placeholder="+7(999) 999-99-99" />
                        </div>	
                        
                        <div class="form-group">
                            <label for="phone" class="control-label"> Телефон 2 (для дубля)</label>
                                <input   data-mask="+7(999) 999-99-99" class="form-control" name="phone2" id="phone2"  type="text" value="<?php echo isset($arr['phone2']) ? $arr['phone2'] : ''; ?>" placeholder="+7(999) 999-99-99" />
                        </div>	
                        
                        <div class="form-group has-error">
                            <label for="snils" class="control-label"> СНИЛС *</label>
                                <input data-mask="999-999-99 99" class="form-control" name="snils" type="text" value="<?php echo isset($arr['snils']) ? $arr['snils'] : ''; ?>"  placeholder="999-999-99 99"/>
                        </div>	
                        
                        <div class="form-group"> 
                            <label class="control-label">Профиль в соц. сети*</label>
                            <input class="form-control" name="vk" type="text" value="<?php echo isset($arr['vk']) ? $arr['vk'] : ''; ?>"  />
						</div>
						
						<div class="form-group">
                            <label class="control-label"> Последнее участие</label>
                                <input class="form-control datas" name="last_ych" type="text" value="<?php echo (isset($arr['last_ych']) AND !empty($arr['last_ych'])) ? date("d.m.y",$arr['last_ych']) : ''; ?>" />                 
                        </div>
						
						
						<div class="form-group">
                            <label class="control-label "> Рейтинг добровольца </label>
                                <input class="form-control" name="rating" type="text" value="<?php echo isset($arr['rating']) ? $arr['rating'] : ''; ?>" />
                        </div>
									
						<div class="form-group">
                            <label class="control-label"> Блокировать подачу заявок </label>
                                <input type="checkbox"  name="block"  class="select-on-check-all" <?php  echo (isset($arr['block']) AND $arr['block']==1) ? 'checked ' : '';?>>
                        </div>
                        
						<div class="clearfix mtop"></div>
						
						<?php echo isset($arr['dobrov_id']) ? '<input name="dobrov_id" type="hidden" value="'.$arr['dobrov_id'].'" />' : ''; ?>
						<?php echo isset($_GET['return']) ? '<input name="return" type="hidden" value="'.$_GET['return'].'" />' : ''; ?>
						
						<div class="col-lg-6 col-lg-offset-3 text-center">
							<button name="Add_DB" class="btn btn-sm btn-primary" type="submit"><strong>Сохранить</strong></button>
							<a class="btn btn-sm btn-danger" href="?page=dobrov"><i class="fa fa-times"></i> Закрыть без сохранения</a>
						</div>
						<div class="clearfix mtop"></div>
					</form>
					<?php
					}
					elseif (isset($_GET['see']) AND is_numeric($_GET['see']))

					{
					?>
					<table class="table table-hover">
                        <tr>
                            <th>#</th>
                            <th>Название исследования </th>
                            <th>Результат участия </th>
                        </tr>
                        
                        <?php
                            $i=1;
                            $SQL = "SELECT * FROM CRM_zayavka WHERE dobrov_id='".intval($_GET['see'])."' ";
                            $r = mysqli_query($mysql,$SQL);
                            if(!$r) exit(mysqli_error($mysql));
                            while ($hk=mysqli_fetch_assoc($r))
                            {
                            
                            $SQL8 = "SELECT * FROM CRM_status_z WHERE status_z_id='".$hk['rez_ych']."' ";
                            $r8 = mysqli_query($mysql,$SQL8);
                            if(!$r8) exit(mysqli_error($mysql));
                            $hk8=mysqli_fetch_assoc($r8);
                            mysqli_free_result($r8); 
                            $SQL1 = "SELECT * FROM CRM_issled WHERE issled_id='".$hk['issled_id']."' ";
                            $r1 = mysqli_query($mysql,$SQL1);
                            if(!$r1) exit(mysqli_error($mysql));
                            while ($hk1=mysqli_fetch_assoc($r1))
                            {
                            ?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td> <a href="?page=issled&see=<?php echo $hk1['issled_id']; ?>"><?php echo isset($hk1['name'])?$hk1['name']:''; ?></a> </td>
                                <td> <?php echo isset($hk8['name'])?$hk8['name']:''; ?> </td>
                        </tr>
                            <?php	
                            $i++;
                             
                            }
                            mysqli_free_result($r1); 
                            
                        }
                        mysqli_free_result($r);
                           	
                    ?>
                    <p><a class="btn btn-sm btn-primary" href="?page=dobrov"><i class="fa fa-chevron-circle-left"></i> Вернуться на страницу "Добровольцы"</a></p>
                    </table>
					<?php	
					}
					elseif (isset($_GET['smena']) AND is_numeric($_GET['smena']))
				
					{
					?>
					<div class="col-md-12">
						<div class="col-md-6">
						<h3> Данные добровольца из базы данных </h3>
						<?php 
						    $SQL1 = "SELECT * FROM CRM_dobrov WHERE dobrov_id='".intval($_GET['smena'])."' LIMIT 1 ";
                            $r1 = mysqli_query($mysql,$SQL1);
                            if(!$r1) exit(mysqli_error($mysql));
							$hk1=mysqli_fetch_assoc($r1);
                            mysqli_free_result($r1); 
						
						?>
						<table class="table table-hover">
							<tr>
								<th>ФИО</th>
								<td><?php echo isset($hk1['fio']) ? $hk1['fio'] : ''; ?> </td>
							</tr>
							<tr>
								<th>Пол</th>
								<td><?php
									foreach ($arr_pol as $key =>$zn)
									{
									?>
									<?php echo (isset($hk1['pol']) AND ($hk1['pol']==$key))?$zn:''; ?> 
									<?php	
									}
									?>	 
								</td>
							</tr>
							<tr>
								<th>Дата рождения</th>
								<td><?php echo (isset($hk1['birthday']) AND !empty($hk1['birthday'])) ? date("d.m.Y",$hk1['birthday']) : ''; ?> </td>
							</tr>
							<tr>
								<th>Телефон</th>
								<td><?php echo isset($hk1['phone']) ? $hk1['phone'] : ''; ?> </td>
							</tr>
							<tr>
								<th>СНИЛС</th>
								<td><?php echo isset($hk1['snils']) ? $hk1['snils'] : ''; ?> </td>
							</tr>
							<tr>
								<th>Профиль в соц. сети</th>
								<td><?php echo isset($hk1['vk']) ? vk_linko($hk1['vk']) : ''; ?> </td>
							</tr>
                        </table>
                        </div>
                        <div class= "col-md-6">
                        <?php 
						    $SQL1 = "SELECT * FROM CRM_dobrov_edit WHERE dobrov_id='".intval($_GET['smena'])."' LIMIT 1 ";
                            $r1 = mysqli_query($mysql,$SQL1);
                            if(!$r1) exit(mysqli_error($mysql));
							$hk2=mysqli_fetch_assoc($r1);
                            mysqli_free_result($r1); 
						
						?>
						<h3> Новые данные добровольца  </h3>
									<table class="table table-hover">
							<tr>
								<th>ФИО</th>
								<td><?php echo isset($hk2['fio']) ? $hk2['fio'] : ''; ?> </td>
							</tr>
							<tr>
								<th>Пол</th>
								<td><?php
									foreach ($arr_pol as $key =>$zn)
									{
									?>
									<?php echo (isset($hk2['pol']) AND ($hk2['pol']==$key))?$zn:''; ?> 
									<?php	
									}
									?>	 
								</td>
							</tr>
							<tr>
								<th>Дата рождения</th>
								<td><?php echo (isset($hk2['birthday']) AND !empty($hk2['birthday'])) ? date("d.m.Y",$hk2['birthday']) : ''; ?> </td>
							</tr>
							<tr>
								<th>Телефон</th>
								<td><?php echo isset($hk2['phone']) ? $hk2['phone'] : ''; ?> </td>
							</tr>
							<tr>
								<th>СНИЛС</th>
								<td><?php echo isset($hk2['snils']) ? $hk2['snils'] : ''; ?> </td>
							</tr>
							<tr>
								<th>Профиль в соц. сети</th>
								<td><?php echo isset($hk2['vk']) ? vk_linko($hk2['vk']) : ''; ?> </td>
							</tr>
                        </table>
                        
                        </div>
                        </div>
                        <form id="formsubmit" action="" method="POST" name="forma1">
                            <?php echo isset($_GET['smena']) ? '<input name="dobrov_id" type="hidden" value="'.$_GET['smena'].'" />' : ''; ?>
                        <p><button name="edit_dovrov" class="btn btn-sm btn-primary" type="submit"><strong><i class="fa fa-chevron-circle-left"></i> Принять изменения</strong></button></p>
                        <p><button name="edit_dovrov_otklon" class="btn btn-sm btn-danger" type="submit"><strong><i class="fa fa-chevron-circle-left"></i> Отклонить изменения</strong></button></p>
                
                        </form>
                        
                   
					<?php	
					}
					elseif (isset($_GET['dubl']) AND is_numeric($_GET['dubl']))
				
					{
					?>
					<div class="col-md-12">
						<div class="col-md-6">
						<h3> Данные дублера из базы данных </h3>
						<?php 
						    $SQLg4 = "SELECT * FROM CRM_dobrov WHERE dobrov_id='".intval($_GET['dubl'])."'  LIMIT 1 ";
                            $g4 = mysqli_query($mysql,$SQLg4);
                            if(!$g4) exit(mysqli_error($mysql));
							$hh1=mysqli_fetch_assoc($g4);
                            mysqli_free_result($g4); 
						
						?>
						<table class="table table-hover">
							<tr>
								<th>ФИО</th>
								<td><?php echo isset($hh1['fio']) ? $hh1['fio'] : ''; ?> </td>
							</tr>
							<tr>
								<th>Пол</th>
								<td><?php
									foreach ($arr_pol as $key =>$zn)
									{
									?>
									<?php echo (isset($hh1['pol']) AND ($hh1['pol']==$key))?$zn:''; ?> 
									<?php	
									}
									?>	 
								</td>
							</tr>
							<tr>
								<th>Дата рождения</th>
								<td><?php echo (isset($hh1['birthday']) AND !empty($hh1['birthday'])) ? date("d.m.Y",$hh1['birthday']) : ''; ?> </td>
							</tr>
							<tr>
								<th>Телефон</th>
								<td><?php echo isset($hh1['phone']) ? $hh1['phone'] : ''; ?> </td>
							</tr>
							<tr>
								<th>СНИЛС</th>
								<td><?php echo isset($hh1['snils']) ? $hh1['snils'] : ''; ?> </td>
							</tr>
							<tr>
								<th>Профиль в соц. сети</th>
								<td><?php echo isset($hh1['vk']) ? vk_linko($hh1['vk']) : ''; ?> </td>
							</tr>
                        </table>
                        </div>
                        <div class= "col-md-6">
                        <?php 
						    $SQL9 = "SELECT * FROM CRM_dobrov WHERE fio='".$hh1['fio']."' OR  snils='".$hh1['snils']."' LIMIT 1 ";
                            $r9 = mysqli_query($mysql,$SQL9);
                            if(!$r9) exit(mysqli_error($mysql));
							$h92=mysqli_fetch_assoc($r9);
                            mysqli_free_result($r9); 
						
						?>
						<h3> Данные существующего добровольца  </h3>
                        <table class="table table-hover">
							<tr>
								<th>ФИО</th>
								<td><?php echo isset($h92['fio']) ? $h92['fio'] : ''; ?> </td>
							</tr>
							<tr>
								<th>Пол</th>
								<td><?php
									foreach ($arr_pol as $key =>$zn)
									{
									?>
									<?php echo (isset($h92['pol']) AND ($h92['pol']==$key))?$zn:''; ?> 
									<?php	
									}
									?>	 
								</td>
							</tr>
							<tr>
								<th>Дата рождения</th>
								<td><?php echo (isset($h92['birthday']) AND !empty($h92['birthday'])) ? date("d.m.Y",$h92['birthday']) : ''; ?> </td>
							</tr>
							<tr>
								<th>Телефон</th>
								<td><?php echo isset($h92['phone']) ? $h92['phone'] : ''; ?> </td>
							</tr>
							<tr>
								<th>СНИЛС</th>
								<td><?php echo isset($h92['snils']) ? $h92['snils'] : ''; ?> </td>
							</tr>
							<tr>
								<th>Профиль в соц. сети</th>
								<td><?php echo isset($h92['vk']) ? vk_linko($h92['vk']) : ''; ?> </td>
							</tr>
                        </table>
                        
                        </div>
                        </div>
                        <form id="formsubmit" action="" method="POST" name="forma1">
                            <?php echo isset($_GET['dubl']) ? '<input name="dobrov_id_dubl" type="hidden" value="'.$_GET['dubl'].'" />' : ''; ?>
                             <?php echo isset($_GET['dubl']) ? '<input name="dobrov_id_original" type="hidden" value="'.$h92['dobrov_id'].'" />' : ''; ?>
                            <?php echo isset($_GET['dubl']) ? '<input name="phone2" type="hidden" value="'.$hh1['phone'].'" />' : ''; ?>
                        <p><button name="add_povtor" class="btn btn-sm btn-primary" type="submit"><strong><i class="fa fa-user-plus"></i> Принять дублера, как отдельного добровольца</strong></button>
                       <button name="skleit" class="btn btn-sm btn-warning" type="submit"><strong><i class="fa fa-user"></i> Склеить дублера с основным добровольцем</strong></button>
                       <button name="del_dubl" class="btn btn-sm btn-danger" type="submit"><strong><i class="fa fa-trash-o"></i> Удалить дублера</strong></button>
                       </p>
                       <a class="btn btn-sm btn-info" href="?page=dobrov"><i class="fa fa-chevron-circle-left"></i> Назад</a>
                      
                        </form>
                        
                   
					<?php	
					}
					else
						{
						if (isset($_GET['id_s']) OR isset($_GET['fio_s']) OR isset($_GET['birthday']) OR isset($_GET['phone']))
                        {              
                        $q1 = isset($_GET['id_s'])?trim($_GET['id_s']):'';
                        $q2 = isset($_GET['fio_s'])?trim($_GET['fio_s']):'';
                        $q3 = isset($_GET['birthday'])?strtotime($_GET['birthday']):'';
                        $q4 = isset($_GET['phone'])?tel_replace($_GET['phone']):'';
                        $q5 = isset($_GET['last_ych'])?strtotime($_GET['last_ych']):'';
                        $q6 = isset($_GET['next_ych'])?strtotime($_GET['next_ych']):'';
                        $q7 = isset($_GET['pol'])?intval($_GET['pol']):'';
                        $q8 = isset($_GET['vk'])?vk_linko($_GET['vk']):'';
                        }
                        $WHERE1 = (isset($q1) AND !empty($q1))?"AND dobrov_id LIKE '%".$q1."%'":'';
                        $WHERE2 = (isset($q2) AND !empty($q2))?"AND fio LIKE '%".$q2."%'":'';
                        $WHERE3 = (isset($q3) AND !empty($q3))?"AND birthday LIKE '%".$q3."%'":'';
                        $WHERE4 = (isset($q4) AND !empty($q4))?"AND phone LIKE '%".$q4."%'":'';
                        $WHERE5 = (isset($q5) AND !empty($q5))?"AND last_ych LIKE '%".$q5."%'":'';
						$WHERE6 = (isset($q6) AND !empty($q6))?"AND next_ych LIKE '%".$q6."%'":'';
						$WHERE7 = (isset($q7) AND !empty($q7))?"AND pol LIKE '%".$q7."%'":'';
						$WHERE8 = (isset($q8) AND !empty($q8))?"AND vk LIKE '%".$q8."%'":'';
						?>
						<form id="formsubmit" action="" method="GET" name="forma1">
                        <input type="hidden" name="page" value="dobrov">
						<table class="table table-hover">
							<tr>
								<th></th>
								<th>ID  <p><input style="width:25px;" type="text" class="form-control1" name="id_s"> </p> </th>
								<th>Рейтинг  <p><i class="fa fa-sort-numeric-desc" aria-hidden="true"></i>  </p></th>
								<th>ФИО <input type="text" class="form-control" name="fio_s"> </th>
								<th>Дата рождения <input  style="width:120px;"  type="text" class="form-control datas" name="birthday"></th>
								<th>Телефон <input style="width:115px;" type="text" class="form-control" name="phone"> </th>
								<th>Последнее участие <input type="text" class="form-control datas" name="last_ych"></th>
								<th>Следующее исследование <input type="text" class="form-control datas" name="next_ych"></th>
								<th>Пол 
									<select class="form-control chosen-select" name="pol" required>
										<option selected>Выбрать</option>
										<?php
										foreach ($arr_pol as $key =>$zn)
										{
										?>
										<option value="<?php echo $key; ?>" ><?php echo $zn; ?></option>
										<?php	
										}
										
										?>	
									</select> 
								</th>
								<th>Профиль в соц.сети  <input type="text" class="form-control" name="vk"> </th>
								<th>Действия <p><button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Искать</button></p></th>
						</form>		
							<form method="POST" role="form">	
							</tr>
							<?php
							$SQL = "SELECT * FROM CRM_dobrov WHERE dobrov_id>0 ".$WHERE1." ".$WHERE2." ".$WHERE3." ".$WHERE4." ORDER by rating  DESC";

							$r = mysqli_query($mysql,$SQL);
								if(!$r) exit(mysqli_error($mysql));
								while	($hk=mysqli_fetch_assoc($r))
								{
								?>
								<tr <?php echo $hk['block']==1?'class="danger"':''; ?> >
                                    <td><label> 
                                        <input type="checkbox" name="delete[<?php echo $hk['dobrov_id']; ?>]" value="1"/>
                                        </label>
                                    </td>
                                    <td><?php echo $hk['dobrov_id']; ?> </td>
                                    <td><?php echo $hk['rating']; ?> </td>
									<td><?php echo $hk['fio'].'<br>';  echo (isset($hk['snils']) AND !empty($hk['snils']))?'':'<span class="text-danger">снилc не указан</span>';?></td>
									<td><?php echo (isset($hk['birthday']) AND !empty($hk['birthday'])) ?date ("d.m.Y",$hk['birthday']):''; ?> </td>
									<td><?php echo $hk['phone']; ?><br><?php echo (isset($hk['phone2']) AND !empty($hk['phone2'])) ?$hk['phone2']:''; ?> </td>
									<td><?php echo (isset($hk['last_ych']) AND !empty($hk['last_ych'])) ?date ("d.m.Y",$hk['last_ych']):''; ?> </td>
									<td>
									<?php 
									$today = time();
									$sec = '86400';
									$day = '90';
									$months3 =(int)$sec * (int)$day ;
									 echo  (!empty($hk['last_ych'])) ?date ("d.m.Y",$hk['last_ych'] + $months3):'';
									?>
									</td>
									<td>
									<?php
                                    foreach ($arr_pol as $key =>$zn)
                                    {
                                    ?>
									<?php echo (isset($hk['pol']) AND $hk['pol']==$key)?$zn:''; ; ?> 
									<?php	
                                    }
                                    ?>
                                    </td>
									<td><a href="<?php echo $hk['vk']; ?>" target="_blank"> <?php echo $hk['vk']; ?></a> </td>
									<td>
									
										<a title="Просмотр" class="fa fa-calendar" href="?page=dobrov&see=<?php echo $hk['dobrov_id']; ?>"></a> 
																				
										<a title="Редактировать" class="fa fa-pencil" href="?page=dobrov&add=1&edit=<?php echo $hk['dobrov_id']; ?>"></a> 
																				
										<a title="Удалить" class="fa fa-trash-o" href="?page=dobrov&delete=<?php echo $hk['dobrov_id']; ?>"></a>
										<?php 
										if(isset($hk['edit']) AND $hk['edit']==1)
										{
										?>
										<a title="Заявка на смену данных" class="fa fa-window-restore" href="?page=dobrov&smena=<?php echo $hk['dobrov_id']; ?>"></a>
										<?php 
										}
										
										if(isset($hk['edit']) AND $hk['edit']==2)
										{
										?>
										<a title="Данные изменены" class="fa fa-address-card"></a>
										<?php 
										}
										
										if(isset($hk['edit']) AND $hk['edit']==3)
										{
										?>
										<a title="Данные были отклонены" class="fa fa-window-close"></a>
										<?php 
										}
										
                                        if(isset($hk['povtor']) AND $hk['povtor']==1)
                                        {
                                        
                                        ?>
                                        
                                        <a title="Дубликат" class="fa fa-exclamation-circle" href="?page=dobrov&dubl=<?php echo $hk['dobrov_id']; ?>"></a> 
                                        <?php 
                                        }
                                        ?>
										
									</td>
								</tr>
								
								
								<?php	
								}
							mysqli_free_result($r);	
							
							?>
						</table>
						<button type="submit" name="SBM_del1" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span> Удалить выбранные</button>
						</form>
						<div class="clearfix mtop"></div>
							
						<?php
						}
						?>
					</div>
				</div>
			</div>
			
        
		</div>
	</div>

