<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS CRM_page_count(
	worker_id int(4) NOT NULL,
	hash varchar(32) NOT NULL,
	page varchar(1000) NOT NULL,
	time int(12) NOT NULL,
	count int(12) NOT NULL,
	KEY `worker_id` (`worker_id`),
	KEY `hash` (`hash`),
	KEY `time` (`time`)
	) CHARACTER SET utf8 COLLATE utf8_general_ci");
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS CRM_online_user(
	worker_id int(4) NOT NULL,
	page varchar(1000) NOT NULL,
	time int(12) NOT NULL
	) CHARACTER SET utf8 COLLATE utf8_general_ci");
?>
	<div class="row border-bottom white-bg dashboard-header">
		<div class="col-md-12">
			<h2>Заголовок</h2>
			<p>Пространство</p>
		</div>
	</div>
	
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<!--Это блок = 8 блокам из 12-->
			<div class="col-sm-8">
				<div class="ibox">
					<div class="ibox-content">	
						<h3>Подзаголовок</h3>
						<p>Описание, управление</p>
						
						<div class="col-md-12 <?php echo (isset($user[$_SESSION['__PANEL__BOARD__']]) AND $user[$_SESSION['__PANEL__BOARD__']]['status'] !== '0')?'hidden':''; ?>">
							<h3>Активность пользователей по состоянию на <span id="time"></span></h3>
							<div id="online"><img src="css/3.GIF" alt="loading...." /></div>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			
			<!--Если надо то можно использовать тут далее еще 4 блока из 12-->
			<div class="col-sm-4">
				<div class="ibox">
					<div class="ibox-content">	
						<h3>Подзаголовок</h3>
						<p>Описание, управление</p>
					</div>
				</div>
			</div>
		</div>
	</div>