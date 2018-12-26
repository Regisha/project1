<?php
	if (!defined('ABSOLUTE__PATH__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/index.php'>");
	}

	$config['db_username'] = ;
	$config['db_password'] = ;
	$config['db_hostname'] = ;
	$config['db_name'] = ;

	$mysql = mysqli_connect($hostDB,$userDB,$passDB,$baseDB);

	mysqli_set_charset($mysql, "utf8");

	if (!$mysql) {
	printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error());
	exit;
	}

?>
