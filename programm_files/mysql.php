<?php
	if (!defined('ABSOLUTE__PATH__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}

	$hostDB = '';
	$userDB = '';
	$passDB = '';
	$baseDB = '';
	
	$config['db_username'] = $userDB;
	$config['db_password'] = $passDB;
	$config['db_hostname'] = $hostDB;
	$config['db_name'] = $baseDB;	
	
	$mysql = mysqli_connect($hostDB,$userDB,$passDB,$baseDB);
            
	mysqli_set_charset($mysql, "utf8");

	if (!$mysql) { 
	printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error()); 
	exit; 
	}

?>
