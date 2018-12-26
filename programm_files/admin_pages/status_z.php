<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}
	
    mysqli_query($mysql, "CREATE TABLE IF NOT EXISTS CRM_status_z (
        status_z_id int auto_increment primary key,
        name varchar(150) NOT NULL,
        reit varchar(150) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Статусы заявок на исследования'");
	

	
	if (isset($_POST['Add_DB']))
	{
	$name= mysqli_real_escape_string($mysql,trim(strip_tags($_POST['name'])));
	$reit = isset($_POST['reit'])?trim($_POST['reit']):0;
	
		if (isset($_POST['status_z_id']))
		{
		$status_z_id = intval($_POST['status_z_id']);
		$query_count = "UPDATE CRM_status_z SET name='".$name."', reit='".$reit."' WHERE status_z_id='".$status_z_id."' LIMIT 1";
		mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_status_z id:'.$status_z_id);
		}
		else
			{
			$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_status_z (name,reit) VALUES ('".$name."', '".$reit."')");
			if(!$insertSQL) die(mysqli_error($mysql));
			$status_z_id= mysqli_insert_id($mysql);
			}
			
	die ("<meta http-equiv=refresh content='0; url=?page=status_z'>");
	}
	
	if (isset($_GET['del']))
	{
	$query = "DELETE FROM CRM_status_z WHERE CRM_status_z.status_z_id='".intval($_GET['del'])."' LIMIT 1";
	mysqli_query($mysql,$query) or die(mysql_error());

	die ("<meta http-equiv=refresh content='0; url=?page=status_z'>");
	}	
	
?>
	
	<div class="row border-bottom white-bg dashboard-header">
		<div class="col-md-12">
			<h2>Статусы заявок на исследования</h2>
			<p><a class="btn btn-sm btn-primary" href="?page=status_z&add=true"><i class="fa fa-plus-square"></i> Добавить статус заявки на исследования </a></p>
		</div>
	</div>
	
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<!--Это блок = 8 блокам из 12-->
			<div class="col-sm-8">
				<div class="ibox">
					<div class="ibox-content">	
					<?php
					if (isset($_GET['delete']))
					{
					?>
					<div class="alert alert-dismissible alert-danger col-lg-6 col-lg-offset-3">
						<h3 class="text-center text-danger">Подтверждение удаления</h3>
						<strong>Выбранный статус заявки на исследование будет удален из базы</strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<a class="btn btn-sm btn-primary" href="?page=status_z&del=<?php echo $_GET['delete']; ?>"><i class="fa fa-trash-o"></i> Удалитьстатус заявки </a>
							<a class="btn btn-sm btn-danger" href="?page=status_z&see=<?php echo $_GET['delete']; ?>"><i class="fa fa-times"></i> Отменить</a>
						</p>
					</div>
					
					<div class="clearfix"></div>
					<?php
					}					
					elseif (isset($_GET['add']))
					{
						if (isset($_GET['edit']))
						{
							$result = mysqli_query($mysql, "SELECT	* FROM CRM_status_z WHERE CRM_status_z.status_z_id='". intval($_GET['edit']) ."' LIMIT 1") or  die(trigger_error(mysqli_error($mysql).' - CRM_status_z'));
							$arr = mysqli_fetch_assoc($result);
							mysqli_free_result($result);
						}	
					?>	
					<h5><?php echo isset($arr['status_z_id']) ? 'Редактировать' : 'Добавить'; ?> статус заявки на исследование</h5>
					<form  action='' method='post' enctype='multipart/form-data'>
						<div class="form-group">
							<label class="control-label">Наименование</label> 
							<input type="text" name="name" value="<?php echo isset($arr['name']) ? $arr['name'] : ''; ?>" placeholder="" class="form-control" required="" ">
						</div>
						
						<div class="form-group">
							<label class="control-label">Рейтинг</label> 
							<input type="text" name="reit" value="<?php echo isset($arr['reit']) ? $arr['reit'] : ''; ?>" placeholder="" class="form-control" required="" ">
						</div>
                        
						<?php echo isset($arr['status_z_id']) ? '<input name="status_z_id" type="hidden" value="'.$arr['status_z_id'].'" />' : ''; ?>
						
						<div class="col-lg-6 col-lg-offset-3 text-center">
							<button name="Add_DB" class="btn btn-sm btn-primary" type="submit"><strong>Сохранить</strong></button>
							<a class="btn btn-sm btn-danger" href="?page=status_z"><i class="fa fa-times"></i> Закрыть</a>
						</div>
						<div class="clearfix mtop"></div>
					</form>
					<?php
					}
					else
						{
						?>
						<table class="table table-hover">
							<tr>
								<th></th>
								<th>ID</th>
								<th>Наименование</th>
								<th>Рейтинг</th>
								<th>Действия</th>
							</tr>
							<?php
							$SQL = "SELECT * FROM CRM_status_z ";

							$r = mysqli_query($mysql,$SQL);
								if(!$r) exit(mysqli_error($mysql));
								while	($hk=mysqli_fetch_assoc($r))
								{
								?>
								<tr>
                                    <td> </td>
                                    <td><?php echo $hk['status_z_id']; ?> </td>
									<td><?php echo $hk['name']; ?></td>
									<td><?php echo $hk['reit']; ?></td>
									<td>
										
										<a title="Редактировать" class="btn btn-success btn-sm" href="?page=status_z&add=1&edit=<?php echo $hk['status_z_id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a> 
										<a title="Удалить" class="btn btn-danger btn-sm" href="?page=status_z&delete=<?php echo $hk['status_z_id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
									</td>
								</tr>
								<?php	
								}
							mysqli_free_result($r);	
							
							?>
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

