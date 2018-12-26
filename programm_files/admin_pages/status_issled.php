<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}
	
    mysqli_query($mysql, "CREATE TABLE IF NOT EXISTS CRM_status_issled (
        status_issled_id int auto_increment primary key,
        name varchar(150) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Статусы исследований'");
	

	
	if (isset($_POST['Add_DB']))
	{
	$name= mysqli_real_escape_string($mysql,trim(strip_tags($_POST['name'])));
	
		if (isset($_POST['status_issled_id']))
		{
		$status_issled_id = intval($_POST['status_issled_id']);
		$query_count = "UPDATE CRM_status_issled SET name='".$name."' WHERE status_issled_id='".$status_issled_id."' LIMIT 1";
		mysqli_query($mysql,$query_count) or die('Ошибка обновления CRM_status_issled id:'.$status_issled_id);
		}
		else
			{
			$insertSQL = mysqli_query($mysql, "INSERT INTO CRM_status_issled (name) VALUES ('".$name."')");
			if(!$insertSQL) die(mysqli_error($mysql));
			$status_issled_id= mysqli_insert_id($mysql);
			}
			
	die ("<meta http-equiv=refresh content='0; url=?page=status_issled'>");
	}
	
	if (isset($_GET['del']))
	{
	$query = "DELETE FROM CRM_status_issled WHERE CRM_status_issled.status_issled_id='".intval($_GET['del'])."' LIMIT 1";
	mysqli_query($mysql,$query) or die(mysql_error());

	die ("<meta http-equiv=refresh content='0; url=?page=status_issled'>");
	}	
	
?>
	
	<div class="row border-bottom white-bg dashboard-header">
		<div class="col-md-12">
			<h2>Статусы исследований</h2>
			<p><a class="btn btn-sm btn-primary" href="?page=status_issled&add=true"><i class="fa fa-plus-square"></i> Добавить статус исследования </a></p>
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
						<strong>Выбранный статус исследования будет удален из базы</strong><br /> 
						<p>Это безопасно.</p>
						
						<p class="text-center">
							<a class="btn btn-sm btn-primary" href="?page=status_issled&del=<?php echo $_GET['delete']; ?>"><i class="fa fa-trash-o"></i> Удалить статус исследования</a>
							<a class="btn btn-sm btn-danger" href="?page=status_issled&see=<?php echo $_GET['delete']; ?>"><i class="fa fa-times"></i> Отменить</a>
						</p>
					</div>
					
					<div class="clearfix"></div>
					<?php
					}					
					elseif (isset($_GET['add']))
					{
						if (isset($_GET['edit']))
						{
							$result = mysqli_query($mysql, "SELECT	* FROM CRM_status_issled WHERE CRM_status_issled.status_issled_id='". intval($_GET['edit']) ."' LIMIT 1") or  die(trigger_error(mysqli_error($mysql).' - CRM_status_issled'));
							$arr = mysqli_fetch_assoc($result);
							mysqli_free_result($result);
						}	
					?>	
					<h5><?php echo isset($arr['status_issled_id']) ? 'Редактировать' : 'Добавить'; ?> статус исследования</h5>
					<form  action='' method='post' enctype='multipart/form-data'>
						<div class="form-group">
							<label class="control-label">Наименование</label> 
							<input type="text" name="name" value="<?php echo isset($arr['name']) ? $arr['name'] : ''; ?>" placeholder="" class="form-control" required="" ">
						</div>
                        
						<?php echo isset($arr['status_issled_id']) ? '<input name="status_issled_id" type="hidden" value="'.$arr['status_issled_id'].'" />' : ''; ?>
						
						<div class="col-lg-6 col-lg-offset-3 text-center">
							<button name="Add_DB" class="btn btn-sm btn-primary" type="submit"><strong>Сохранить</strong></button>
							<a class="btn btn-sm btn-danger" href="?page=status_issled"><i class="fa fa-times"></i> Закрыть</a>
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
								<th>Действия</th>
							</tr>
							<?php
							$SQL = "SELECT * FROM CRM_status_issled ";

							$r = mysqli_query($mysql,$SQL);
								if(!$r) exit(mysqli_error($mysql));
								while	($hk=mysqli_fetch_assoc($r))
								{
								?>
								<tr>
                                    <td> </td>
                                    <td><?php echo $hk['status_issled_id']; ?> </td>
									<td><?php echo $hk['name']; ?></td>
									<td>
										
										<a title="Редактировать" class="btn btn-success btn-sm" href="?page=status_issled&add=1&edit=<?php echo $hk['status_issled_id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a> 
										<a title="Удалить" class="btn btn-danger btn-sm" href="?page=status_issled&delete=<?php echo $hk['status_issled_id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
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

