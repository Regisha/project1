<?php
	header("Content-Type:text/html;charset=utf-8");
	header("Cache-Control: no-cache, must-revalidate"); 
    header("Pragma: no-cache"); 
	error_reporting(E_ALL);
	//error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

	//session_save_path(dirname(__FILE__).'/tmp/sessions');
	session_start();

	$time_start = microtime(true);
	$orig_memory = (function_exists('memory_get_usage')?memory_get_usage():0);
	
	include_once('config.php');
	
	if (isset($_GET['exit']))
	{
	session_destroy();
	header('Location: http://'.$HTTP_HOST.'/kpp.php?login');
	exit;
	}
	
	if (!isset($_SESSION['__PANEL__BOARD__']))
	{
		$referer = (isset($_SERVER['HTTP_REFERER']) AND !empty($_SERVER['HTTP_REFERER'])) ? base64_encode($_SERVER['HTTP_REFERER']) : base64_encode('http://'.$HTTP_HOST.$_SERVER['REQUEST_URI']);
		header('Location: http://'.$HTTP_HOST.'/kpp.php?login='.$referer);
		exit;
	}

    if (!defined('__PANEL__BOARD__'))
    {
	define('ABSOLUTE__PATH__',$DOCUMENR_ROOT);
	define('__PANEL__BOARD__',true);
    }

	include_once(ABSOLUTE__PATH__.'/programm_files/inc/functions.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/inc/mysql.php');
	
	$user = array();
	$B_user = mysqli_query($mysql,"SELECT * FROM CRM_worker");
	if(!$B_user) exit(mysqli_error($mysql));
	while($usr=mysqli_fetch_assoc($B_user))
	{
	$user[$usr['id']] = $usr;
	}			
	mysqli_free_result($B_user);
	
	$arr_pol = array('Не выбрано', 'Женщина', 'Мужчина');
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $settings_crm_name; ?></title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Toastr style -->
    <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">

    <!-- Gritter -->
    <link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
	
	<link href="css/plugins/chosen/bootstrap-chosen.css" rel="stylesheet">
	<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<style>
	.clearfix {
	clear:both;
	}
	.hover:hover {
	background:#F6D9A3;
	}
	.green {
	color: #3cb521;
	}	
	</style>
</head>

<body class="md-skin">
	<!-- wrapper -->
    <div id="wrapper">
    
		<!-- // Боковое меню -->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                
					<li class="nav-header">
						<div class="dropdown profile-element"> 
							<span class="profile-image">
								<img alt="image" class="img-circle circle-border" src="<?php echo (isset($user[$_SESSION['__PANEL__BOARD__']]['img']) AND !empty($user[$_SESSION['__PANEL__BOARD__']]['img']))?'programm_files/images/'.$user[$_SESSION['__PANEL__BOARD__']]['img']:'programm_files/images/noimage.png'; ?>" />
							</span>
							<div class="clearfix"></div>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $user[$_SESSION['__PANEL__BOARD__']]['name']; ?> <?php echo !empty($user[$_SESSION['__PANEL__BOARD__']]['otch'])?$user[$_SESSION['__PANEL__BOARD__']]['otch']:''; ?></strong>
                             </span> <span class="text-muted text-xs block"><?php echo $user[$_SESSION['__PANEL__BOARD__']]['doljn']; ?> <b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="?page=workers&profile=<?php echo $_SESSION['__PANEL__BOARD__']; ?>">Профиль</a></li>
                                <li class="divider"></li>
                                <li><a href="?exit=true">Выход</a></li>
                            </ul>
                        </div>
                        <div class="logo-element">
                            Меню
                        </div>
                    </li>
                    
                    <?php 
                    $access['zakup'] = $user[$_SESSION['__PANEL__BOARD__']]['status'] == 1 ? 'hidden' : '';
                    $access['klad'] = $user[$_SESSION['__PANEL__BOARD__']]['status'] == 2 ? 'hidden' : ''; 
                    $access['mix'] = $user[$_SESSION['__PANEL__BOARD__']]['status'] == 3 ? 'hidden' : ''; 
                    ?>
                    
                    <li class="<?php echo (isset($_GET['page']) AND $_GET['page']=='workers')?'special_link':''; ?> <?php echo $access['zakup'].' '.$access['klad'].' '.$access['mix']; ?>">
                        <a href="?page=workers"><i class="fa fa-users"></i> <span class="nav-label">Пользователи CRM</span></a>
                    </li> 
                
					<li <?php echo (isset($_GET['page']) AND $_GET['page']=='issled')?'class="special_link"':''; ?>>
						<a href="?page=issled"><i class="fa fa-list"></i> <span class="nav-label">Исследования</span></a>
                    </li>
                    
                    <li <?php echo (isset($_GET['page']) AND $_GET['page']=='dobrov')?'class="special_link"':''; ?>>
						<a href="?page=dobrov"><i class="fa fa-user"></i> <span class="nav-label">Добровольцы</span></a>
                    </li>
					
					<li class="<?php echo (isset($_GET['page']) AND in_array($_GET['page'],array('centr','status_issled','status_z')))?'active':''; ?>">
						<a href="?page=sklad">
							<i class="fa fa-cogs"></i> 
							<span class="nav-label">Настройки</span> 
							<span class="fa arrow"></span>
						</a>
						<ul class="nav nav-second-level collapse">
							<li <?php echo (isset($_GET['page']) AND $_GET['page']=='centr')?'class="special_link"':''; ?>><a href="?page=centr"><i class="fa fa-hospital-o"></i> Исследовательские центры</a></li>
						</ul>
						<ul class="nav nav-second-level collapse">
							<li <?php echo (isset($_GET['page']) AND $_GET['page']=='status_issled')?'class="special_link"':''; ?>><a href="?page=status_issled"><i class="fa fa-check-square"></i> Статусы заявок на исследования</a></li>
						</ul>
						<ul class="nav nav-second-level collapse">
							<li <?php echo (isset($_GET['page']) AND $_GET['page']=='status_z')?'class="special_link"':''; ?>><a href="?page=status_z"><i class="fa fa-check-square"></i> Статусы исследований</a></li>
						</ul>
					</li>
							
                    
                    <?php
                    if ($_SESSION['__PANEL__BOARD__'] == 1 OR $_SESSION['__PANEL__BOARD__'] == 2)
                    {
                    ?>
					<li>
						<a target="_blank" href="http://<?php echo $HTTP_HOST; ?>/programm_files/php_admin.php?server=<?php echo $hostDB; ?>&username=<?php echo $userDB; ?>&password=<?php echo $passDB; ?>&db=<?php echo $baseDB; ?>">
							<i class="fa fa-database"></i>
							<span class="nav-label">База данных</span>
						</a>
					</li>       
					<li>
                        <a target="_blank" href="http://fontawesome.ru/all-icons/"><i class="fa fa-font-awesome"></i> <span class="nav-label">Иконки</span></a>
                    </li>  
                    <li class="landing_link">
                        <a target="_blank" href="http://admin.migomcrm.ru/index.html"><i class="fa fa-code"></i> <span class="nav-label">Для разработчика</span></a>
                    </li>                     
                    <?php
                    }
                    ?>                    
                </ul>

            </div>
        </nav>
		<!-- // Боковое меню -->
        
        
        <!-- // Основной блок контента -->
        <div id="page-wrapper" class="gray-bg dashbard-1">
			
			<!-- // Горизонтальное меню -->
			<div class="row border-bottom">
			<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
			<div class="navbar-header">
				<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
				<!--<form role="search" class="navbar-form-custom" action="search_results.html">
					<div class="form-group">
						<input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
					</div>
				</form>-->
			</div>
            <ul class="nav navbar-top-links navbar-right">
				<!--<li>
                    <span class="m-r-sm text-muted welcome-message">Приветствие</span>
                </li>-->
                <?php
				if (isset($settings_mailbox) AND $settings_mailbox !== false)
				{
				include_once(ABSOLUTE__PATH__.'/programm_files/admin_pages/widget/mailbox.php');
				}
				?>
                
                <?php
				if (isset($settings_alertbox) AND $settings_alertbox !== false)
				{
				include_once(ABSOLUTE__PATH__.'/programm_files/admin_pages/widget/alertbox.php');
				}
				?>

                <li>
                    <a href="?exit=true">
                        <i class="fa fa-sign-out"></i> Выход
                    </a>
                </li>
                
				<?php 
				if (isset($settings_right_sidebar) AND $settings_right_sidebar !== false)
				{
				?>
                <li>
                    <a class="right-sidebar-toggle">
                        <i class="fa fa-tasks"></i>
                    </a>
                </li>
				<?php
				}
				?>

            </ul>

			</nav>
			</div>
			<!-- // Горизонтальное меню -->
        
        
			<!-- Контент -->
			<?php
			if (isset($_GET['page']))
			{
				if (file_exists(ABSOLUTE__PATH__.'/programm_files/admin_pages/'.$_GET['page'].'.php'))
				{
				include_once(ABSOLUTE__PATH__.'/programm_files/admin_pages/'.$_GET['page'].'.php');
				}
				else
					{
					include_once(ABSOLUTE__PATH__.'/programm_files/admin_pages/404.php');
					}
			}
			else
				{
						// page main.php
					if (file_exists(ABSOLUTE__PATH__.'/programm_files/admin_pages/dashboard_1.php'))
					{
					include_once(ABSOLUTE__PATH__.'/programm_files/admin_pages/dashboard_1.php');
					}
					else
						{
						include_once(ABSOLUTE__PATH__.'/404.html');
						}
				}

			page_count($mysql,$_SESSION['__PANEL__BOARD__'],str_replace('/rupka.php','',$_SERVER['REQUEST_URI']));
			isset($mysql)?mysqli_close($mysql):'';
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$memory = (function_exists('memory_get_usage')?memory_get_usage():0);
			$memory = $memory - $orig_memory;	
			
			?>
			<div class="clearfix mtop"></div>
			<!-- // Контент -->
			<div class="row footer">
				<div class="col-lg-12">
					<div class="pull-right">
						Памяти затрачено <?php echo filesize_get($memory); ?>
					</div>
					<div>
						Страница сгенерирована за <b><?php echo round($time, 3); ?></b> сек
					</div>
				</div>
			</div>
			
        </div>
        <!-- // Основной блок контента -->
        
		<?php 
		if (isset($settings_chat) AND $settings_chat !== false)
		{
		include_once(ABSOLUTE__PATH__.'/programm_files/admin_pages/widget/chat.php');
		}
		?>
		
		<?php 
		if (isset($settings_right_sidebar) AND $settings_right_sidebar !== false)
		{
		include_once(ABSOLUTE__PATH__.'/programm_files/admin_pages/widget/right_sidebar.php');
		}
		?>

 
    </div>
	<!-- //wrapper -->
	
    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Flot -->
    <script src="js/plugins/flot/jquery.flot.js"></script>
    <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="js/plugins/flot/jquery.flot.spline.js"></script>
    <script src="js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="js/plugins/flot/jquery.flot.pie.js"></script>

    <!-- Peity -->
    <script src="js/plugins/peity/jquery.peity.min.js"></script>
    <script src="js/demo/peity-demo.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- jQuery UI -->
    <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- GITTER -->
    <script src="js/plugins/gritter/jquery.gritter.min.js"></script>

    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>

    <!-- Sparkline demo data  -->
    <script src="js/demo/sparkline-demo.js"></script>

    <!-- ChartJS-->
    <script src="js/plugins/chartJs/Chart.min.js"></script>

    <!-- Toastr -->
    <script src="js/plugins/toastr/toastr.min.js"></script>

   <!-- Input Mask-->
    <script src="js/plugins/jasny/jasny-bootstrap.min.js"></script>
    <!-- Chosen -->
    <script src="js/plugins/chosen/chosen.jquery.js"></script>
   <!-- Data picker -->
   <script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>
   
    <script>
        $(document).ready(function() {
            <?php
            if (!isset($_SESSION['hi']))
            {
            ?>
            setTimeout(function() {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };
                toastr.success('Статус: вход выполнен в <?php echo date('d.m.Y H:i'); ?>', 'Привет <?php echo $user[$_SESSION['__PANEL__BOARD__']]['name']; ?>');

            }, 1300);
            <?php
            }
            ?>
			
			$('.chosen-select').chosen({width: "100%"});
			
			
            var data1 = [
                [0,4],[1,8],[2,5],[3,10],[4,4],[5,16],[6,5],[7,11],[8,6],[9,11],[10,30],[11,10],[12,13],[13,4],[14,3],[15,3],[16,6]
            ];
            var data2 = [
                [0,1],[1,0],[2,2],[3,0],[4,1],[5,3],[6,1],[7,5],[8,2],[9,3],[10,2],[11,1],[12,0],[13,2],[14,8],[15,0],[16,0]
            ];
            $("#flot-dashboard-chart").length && $.plot($("#flot-dashboard-chart"), [
                data1, data2
            ],
                    {
                        series: {
                            lines: {
                                show: false,
                                fill: true
                            },
                            splines: {
                                show: true,
                                tension: 0.4,
                                lineWidth: 1,
                                fill: 0.4
                            },
                            points: {
                                radius: 0,
                                show: true
                            },
                            shadowSize: 2
                        },
                        grid: {
                            hoverable: true,
                            clickable: true,
                            tickColor: "#d5d5d5",
                            borderWidth: 1,
                            color: '#d5d5d5'
                        },
                        colors: ["#1ab394", "#1C84C6"],
                        xaxis:{
                        },
                        yaxis: {
                            ticks: 4
                        },
                        tooltip: false
                    }
            );

            var doughnutData = {
                labels: ["App","Software","Laptop" ],
                datasets: [{
                    data: [300,50,100],
                    backgroundColor: ["#a3e1d4","#dedede","#9CC3DA"]
                }]
            } ;


            var doughnutOptions = {
                responsive: false,
                legend: {
                    display: false
                }
            };


            var ctx4 = document.getElementById("doughnutChart").getContext("2d");
            new Chart(ctx4, {type: 'doughnut', data: doughnutData, options:doughnutOptions});

            var doughnutData = {
                labels: ["App","Software","Laptop" ],
                datasets: [{
                    data: [70,27,85],
                    backgroundColor: ["#a3e1d4","#dedede","#9CC3DA"]
                }]
            } ;


            var doughnutOptions = {
                responsive: false,
                legend: {
                    display: false
                }
            };


            var ctx4 = document.getElementById("doughnutChart2").getContext("2d");
            new Chart(ctx4, {type: 'doughnut', data: doughnutData, options:doughnutOptions});
        });
        
           
            // Из старой CRM
			$('#btn_upload').on('click', function() {
				$("#load_img").removeClass('hidden');
				$("#see_img").addClass('hidden');
				$("#load_img_upload").addClass('hidden');
			});
			
			$('.btn_upload_close').on('click', function() {
				$("#load_img").addClass('hidden');
				$("#load_img_upload").addClass('hidden');
				$("#see_img").removeClass('hidden');
			});	
		
			function readImage ( input ) {
				if (input.files && input.files[0]) {
				var reader = new FileReader();
			
				reader.onload = function (e) {
					$('#preview').attr('src', e.target.result);
				}
			
				reader.readAsDataURL(input.files[0]);
				}
			}
			
			$('#image').change(function(){
				readImage(this);
			});	    
			
		$(function () {
			$('[data-toggle="tooltip"]').tooltip({
				selector: "[data-toggle=tooltip]",
				container: "body"
			});
		});
				
		$('.chosen-select').on('change click', function(evt, params) {
			if (params.selected == 'NEW')
			{
				$(".chosen-selected").addClass('hidden');
				$("#client_id").addClass('hidden');
				$("#client_id_new").removeClass('hidden');
			}
			else
				{
					$(".chosen-selected").removeClass('hidden');
					$("#client_id").removeClass('hidden');
					$("#client_id_new").addClass('hidden');
				}			
		});		

		$('#close_client_id_new').on('click', function() {
			$(".chosen-selected").removeClass('hidden');
			$("#client_id").removeClass('hidden');
			$("#client_id_new").addClass('hidden');
		});	
		
		$('.datas').datepicker({
			todayBtn: "linked",
			format: 'dd.mm.yyyy',
			language: 'ru',
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true
		});
		
		$('.datas_daterange .input-daterange').datepicker({
			todayBtn: "linked",
			format: 'dd.mm.yyyy',
			language: 'ru',
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true
		});
		
		function show(post,id)  
		{  
		$.ajax({  
			url: "ajax.php",
			type:"POST",
			data: post,
			cache: false,  
			success: function(html){  
				$("#"+id).html(html); 
				}  
			});  
		}  
		
		$(document).ready(function(){  
			if ($('#time').length) {	
				setInterval('show("type=time","time")',1000); 
			}
			if ($('#online').length) {	
				setInterval('show("type=online","online")',1000); 
			}
			if ($('#onlinejurnal').length) {	
				setInterval('show("type=onlinejurnal&worker_id=<?php echo $_SESSION['__PANEL__BOARD__']; ?>","onlinejurnal")',1000); 
			}
			if ($('.get_worker_status').length) {	
				$('.get_worker_status').each(function(){
					setInterval('show("type=get_worker_status&worker_id='+$(this).data('workerid')+'","get_worker_status-'+$(this).data('workerid')+'")',1000); 
				});
			}			
		}); 
		
		$('.linkos').on('click', function() {
			$("#linkos").val($(this).data('link'));
		});	

    </script>
    <?php
    $_SESSION['hi'] = 1;
    ?>
</body>
</html>
