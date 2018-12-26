<?php
	header("Content-Type:text/html;charset=utf-8");
	error_reporting(E_ALL);
	
	include_once('config.php');
	define('ABSOLUTE__PATH__',$DOCUMENR_ROOT);
	define('__PANEL__BOARD__',true);
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/functions.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/mysql.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/libmail.php');
	
	$user = array();
	$B_user = mysqli_query($mysql,"SELECT * FROM CRM_worker");
	if(!$B_user) exit(mysqli_error($mysql));
	while($usr=mysqli_fetch_assoc($B_user))
	{
	$user[$usr['id']] = $usr;
	}			
	mysqli_free_result($B_user);	
	
	if (isset($_POST['type']) AND $_POST['type'] == 'time')
	{
	echo date('d') .' '. rus_date('F',time()) .' '. date('Y') .' '. date('H:i:s');
	}
	
	if (isset($_REQUEST['type']) AND $_REQUEST['type'] == 'get_worker_status')
	{
	$worker_id = isset($_REQUEST['worker_id'])?intval($_REQUEST['worker_id']):0;		
		$SQL = "SELECT * FROM CRM_online_user WHERE worker_id='".$worker_id."' AND (time+1200) > '".time()."' LIMIT 1";
		$B_user = mysqli_query($mysql,$SQL);
		if(!$B_user) exit(mysqli_error($mysql));
		$exp=mysqli_fetch_assoc($B_user);
		mysqli_free_result($B_user);
		
		echo isset($exp['worker_id'])?'<span title="online" class="green"><i class="fa fa-circle" aria-hidden="true"></i></span>':'<span title="offline" class="text-danger"><i class="fa fa-circle" aria-hidden="true"></i></span>';
	}
	
	if (isset($_POST['type']) AND $_POST['type'] == 'online')
	{
	?>
			<table class="table table-hover">
				<tr>
					<th>Статус</th>
					<th>user</th>
					<th>Активность</th>
					<th>Где</th>
				</tr>
			<?php
			$SQL = "SELECT * FROM CRM_online_user";
			$B_user = mysqli_query($mysql,$SQL);
			if(!$B_user) exit(mysqli_error($mysql));
			while($exp=mysqli_fetch_assoc($B_user))
			{
				if (($exp['time']+2592000) > time())
				{
				?>
				<tr class="<?php echo (($exp['time']+1200) > time()) ? 'success' : 'danger'; ?>">
					<td><?php echo (($exp['time']+1200) > time()) ? '<b class="text-success">online</b>' : '<b class="text-danger">offline</b>'; ?></td>
					<td>
						<a href="?page=worker&see=<?php echo $exp['worker_id']; ?>">
							<?php echo isset($user[$exp['worker_id']]['name'])?$user[$exp['worker_id']]['name']:$exp['worker_id']; ?>
						</a>
					</td>
					<td><?php echo date('H:i, d.m.Y', $exp['time']); ?></td>
					<td>
						<a href="<?php echo !empty($exp['page']) ? $exp['page'] : '?page=404'; ?>">
							<?php echo !empty($exp['page']) ? $exp['page'] : '?page=404'; ?>
						</a>
					</td>
				</tr>
			<?php
				}
			}			
			mysqli_free_result($B_user);
			?>
			</table>
	<?php
	}
	
	if (isset($_REQUEST['type']) AND $_REQUEST['type'] == 'onlinejurnal')
	{
	$time = isset($_REQUEST['d1'])?strtotime($_REQUEST['d1']):strtotime(date('d.m.Y'));	
	$d1 = isset($_REQUEST['d1'])?strtotime($_REQUEST['d1']):mktime (0, 0, 0, date('m',$time), date('d',$time), date('Y',$time));
	$d2 = isset($_REQUEST['d2'])?strtotime($_REQUEST['d2']):mktime (0, 0, 0, date('m',$time), date('d',$time), date('Y',$time));
	$CRM_page_count = " AND ('".($d1)."' <= CRM_page_count.time AND '".($d2+86399)."' >= CRM_page_count.time)";	
	
		$SQL = "SELECT * FROM CRM_page_count WHERE worker_id>'0' ".$CRM_page_count." ORDER BY CRM_page_count.time+0 DESC";
		$B_user = mysqli_query($mysql,$SQL);
		if(!$B_user) exit(mysqli_error($mysql));
		while($exp=mysqli_fetch_assoc($B_user))
		{
		$array[] = $exp;
		}			
		mysqli_free_result($B_user);	
	?>
			<table class="table table-hover table-bordered">
				<tr>
					<th>Статус</th>
					<th>user</th>
					<th>Активность</th>
					<th>Где</th>
					<th>Популярность</th>
				</tr>
			<?php

			if (sizeof($array) > 0)
			{
				foreach ($array as $count => $exp)
				{
				?>
				<tr class="<?php echo (($exp['time']+30) > time()) ? 'success' : 'danger'; ?>">
					<td><?php echo (($exp['time']+30) > time()) ? '<b class="text-success">Только что</b>' : '<b class="text-danger">давно</b>'; ?></td>
					<td>
						<a href="?page=worker&see=<?php echo $exp['worker_id']; ?>">
							<span class="glyphicon glyphicon-user" style="color:#<?php echo substr(md5( (isset($user[$exp['worker_id']])?$user[$exp['worker_id']]['name']:$exp['worker_id'])), 0, 6); ?>;"></span> <?php echo $user[$exp['worker_id']]['name']; ?>
						</a>
					</td>
					<td><?php echo date('H:i, d.m.Y', $exp['time']); ?></td>
					<td>
						<a href="<?php echo !empty($exp['page']) ? $exp['page'] : '?page=404'; ?>">
							<?php echo !empty($exp['page']) ? substr($exp['page'], 0, 60) : '?page=404'; ?>
						</a>
					</td>
					<td><?php echo $exp['count']; ?></td>
				</tr>
				<?php
				}
			}
			?>
			</table>
	<?php
	}
	if (isset($_REQUEST['file']))
	{
		if (file_exists(ABSOLUTE__PATH__.'/tmp/'.base64_decode($_GET['file'])))
		{
		$e = getExtension1(base64_decode($_GET['file']));
		$content = file_get_contents(ABSOLUTE__PATH__.'/tmp/'.base64_decode($_GET['file']));
		header('Content-Type: '.$e[1].'; charset=utf-8');
		header("Content-Disposition: attachment; filename=".base64_decode($_GET['file']));
		echo $content;
		exit();
		}
		else
			{
			echo'
			<div class="modal-body">Файл не найден 01</div>
			<div class="modal-footer">
				<a  href="/">На сайт</a>
			</div>';			
			}
	}
?>