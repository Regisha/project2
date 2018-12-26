<?php
	header("Content-Type:text/html;charset=utf-8");
	//ini_set('date.timezone', 'Asia/Yekaterinburg');
	setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
	$time_start = microtime(true);
	$orig_memory = (function_exists('memory_get_usage')?memory_get_usage():0);
	error_reporting(E_ALL);

	define('ABSOLUTE__PATH__',$_SERVER['DOCUMENT_ROOT']);
	session_start();

	$time_start = microtime(true);

	if (!isset($_SESSION['__ID__']))
	{
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/kpp.php?login');
		exit;
	}
	if ($_SERVER['HTTP_HOST'] !== 'crm.mysvami24.com')
	{
	exit ('Не зарегистрированный домен, ошибка.');
	}
	if (isset($_GET['exit']))
	{
		session_destroy();
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/kpp.php?login');
		exit;
	}

	define( '__PANEL__BOARD__', 1 );
	
	include_once(ABSOLUTE__PATH__.'/programm_files/mysql.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/functions.php');
	
	
	$user = array();
	$SQL = "SELECT id,login,pass,name,status FROM MED_worker";
	$B_user = mysqli_query($mysql,$SQL);
	if(!$B_user) exit(mysqli_error($mysql));
	while($usr=mysqli_fetch_assoc($B_user))
	{
	$user[$usr['id']] = $usr;
	}			
	mysqli_free_result($B_user);
	
	$super_admin = array(1,2);
	$city = array('1' => 'Москва','2' => 'СПб');
	$stat_zayavki = array(1 => 'Открыта', 2 => 'На паузе', 3 => 'Закрыта нами', 4 => 'Отказ клиента' );	
	$stat_clienta = array(1 => 'Следующая оплата', 2 => 'Закрыт', 3 => 'На паузе');	
	$analiz= array(1 => 'Hовые заказы', 2 => 'Закрытые нами', 3 => 'Платежей от новых клиентов за месяц');	
	$mounts = array ('01' => 'январь','02' => 'февраль','03' => 'март','04' => 'апрель','05' => 'май','06' => 'июнь','07' => 'июль','08' => 'август','09' => 'сентябрь','10' => 'октябрь','11' => 'ноябрь','12' => 'декабрь');
?>
<!doctype html>
<!--[if lt IE 7 ]><html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]><html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]><html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]><html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="en" class="no-js"> <!--<![endif]-->
<head>
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta charset="utf-8">
	<title>ООО Мы с Вами 24</title>
	<!-- Style CSS -->
	<link href="css/cerulean_bootstrap.min.css" media="screen" rel="stylesheet">
	<link href="js/Easy-Searchable-Filterable-Select-List-with-jQuery/jquery.searchableSelect.css" media="screen" rel="stylesheet">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
 
	
	<style>
	.hover:hover {
	background:#F6D9A3;
	}
    .m5 {margin-top:2px; margin-bottom:2px;}
    .m4 {padding-left:2px; padding-right:2px;}	
    .mtop {margin-top:5px;padding-top:5px;}
    .hr {border:1px solid #2FA4E7;}
	table td a.atd{width:100%;height:100%;display:inline-block;color:white;}
	a.atd:hover {color:black;}
table {
  overflow: hidden;
}

td, th {
  padding: 10px;
  position: relative;
  outline: 0;
}

body:not(.nohover) tbody tr:hover {
  background-color: #F9F9F9;
}

td:hover::after,
thead th:not(:empty):hover::after,
td:focus::after,
thead th:not(:empty):focus::after { 
  content: '';  
  height: 10000px;
  left: 0;
  position: absolute;  
  top: -5000px;
  width: 100%;
  z-index: -1;
}

td:hover::after,
th:hover::after {
  background-color: #F9F9F9;
}

td:focus::after,
th:focus::after {
  background-color: lightblue;
}

/* Focus stuff for mobile */
td:focus::before,
tbody th:focus::before {
  background-color: lightblue;
  content: '';  
  height: 100%;
  top: 0;
  left: -5000px;
  position: absolute;  
  width: 10000px;
  z-index: -1;
}
textarea.form-control {
    min-height: 30px;
}
.horizontal {
  overflow: auto;
  max-height: 150px;
}
.panel {
    margin-bottom: 2px;
}
        input[type=checkbox] {
		display: inline-block;
		width: 24px;
		height: 24px;
		background-position: 0 0;
		background-repeat: no-repeat;
		line-height: 24px;
		cursor: pointer;
        }
	</style>
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Навигация</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<div class="nav navbar-nav">
					<ul class="nav navbar-nav">
						<li><a href="?page=main">Главная</a></li>
						<li><a href="?page=zayavka" class=" <?php echo $_GET['page'] == 'zayavka'?'active':''; ?>">
						<span class="glyphicon glyphicon-list"></span>Заявки</a></li>
						<li><a href="?page=client" class=" <?php echo $_GET['page'] == 'client'?'active':''; ?>">
						<span class="glyphicon glyphicon-user"></span>Клиенты</a></li>
						<li><a href="?page=sidelka" class=" <?php echo $_GET['page'] == 'sidelka'?'active':''; ?>"><span class="text-success glyphicon glyphicon-user"></span>Сиделки</a></li>
						
						<li><a href="?page=money" class=" <?php echo $_GET['page'] == 'money'?'active':''; ?>"><span class="glyphicon glyphicon-usd"></span>Касса</a></li>
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="100" data-close-others="false" role="button" aria-expanded="false">Настройки <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li <?php echo (isset($_GET['page']) AND $_GET['page']=='worker')?'class="active"':''; ?>><a href="?page=worker">Пользователи системы</a></li>
								<li <?php echo (isset($_GET['page']) AND $_GET['page']=='send')?'class="active"':''; ?>><a href="?page=send" class=" <?php echo $_GET['page'] == 'send'?'active':''; ?>">Настройки информирования</a></li>
								<li <?php echo (isset($_GET['page']) AND $_GET['page']=='dbwork')?'class="active"':''; ?>><a href="?page=dbwork" class=" <?php echo $_GET['page'] == 'dbwork'?'active':''; ?>"><span class="glyphicon glyphicon-floppy-saved"></span>Резервное копирование</a></li>
								
								<li <?php (in_array($_SESSION['__ID__'],$super_admin))?'hidden':''; ?>>
									<a target="_blank" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/programm_files/php_admin.php?server=<?php echo $hostDB; ?>&username=<?php echo $userDB; ?>&password=<?php echo $passDB; ?>&db=<?php echo $baseDB; ?>">
										<span class="glyphicon glyphicon-tasks"></span>
										База данных
									</a>
								</li>
							</ul>
						</li>
						
						<li><a target="_blank" href="/"><span class="glyphicon glyphicon-globe"></span> На сайт</a></li>
						<li><a href="?exit"><span class="glyphicon glyphicon-off"></span> Выход</a></li>
						
					</ul>
				</div>
			</div>
		</div>
	</nav>

	<div class="container">
		<div class="page-header">
			<div class="row"></div>
		</div>		
	<div class="row">
		<?php
		/*
		?>
		<div class="col-md-2">
			<div class="row text-center"> 
				<h2 class="page-header"><img class="img-responsive hidden-xs hidden-sm" src="2.png" alt="Code CMS" /><br> </h2>
				<p><a href="?page=worker&see=<?php echo $_SESSION['__ID__']; ?>"><?php echo privet().'<br>'. $user[$_SESSION['__ID__']]['name']; ?></a></p>
			</div>
			<div class="row">
				<div class="list-group">
					<a href="?page=main" class="list-group-item <?php echo $_GET['page'] == 'main'?'active':''; ?>">
						<span class="glyphicon glyphicon-home"></span>
						Главная
					</a>
					<a href="?page=zayavka" class="list-group-item <?php echo $_GET['page'] == 'zayavka'?'active':''; ?>">
						<span class="glyphicon glyphicon-list"></span>
						Заявки
					</a>
					
					<a href="?page=client" class="list-group-item <?php echo $_GET['page'] == 'client'?'active':''; ?>">
						<span class="glyphicon glyphicon-user"></span>
						Клиенты
					</a>
					
					<a href="?page=sidelka" class="list-group-item <?php echo $_GET['page'] == 'sidelka'?'active':''; ?>">
						<span class="text-success glyphicon glyphicon-user"></span>
						Сиделки
					</a>
					
					<a href="?page=money" class="list-group-item <?php echo $_GET['page'] == 'money'?'active':''; ?>">
						<span class="glyphicon glyphicon-usd"></span>
						Касса
					</a>
					
					<a href="?page=send" class="list-group-item <?php echo $_GET['page'] == 'send'?'active':''; ?>">
						<span class="glyphicon glyphicon-envelope"></span>
						Настройки информирования
					</a>
					
					<a href="?page=worker" class="list-group-item <?php echo $_GET['page'] == 'worker'?'active':''; ?>">
						<span class="glyphicon glyphicon-user"></span>
						Пользователи
					</a>
					
					<a href="?page=dbwork" class="list-group-item <?php echo $_GET['page'] == 'dbwork'?'active':''; ?>">
						<span class="glyphicon glyphicon-floppy-saved"></span>
						Резервное копирование
					</a>					

					<?php
					if (in_array($_SESSION['__ID__'],$super_admin))
					{
					?>
					<a target="_blank" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/programm_files/php_admin.php?server=<?php echo $hostDB; ?>&username=<?php echo $userDB; ?>&password=<?php echo $passDB; ?>&db=<?php echo $baseDB; ?>" class="list-group-item">
						<span class="glyphicon glyphicon-tasks"></span>
						База данных
					</a>
					<?php
					}
					?>
					<a href="?exit=true" class="list-group-item">
						<span class="glyphicon glyphicon-off"></span>
						Выход
					</a>
				</div>
			</div>
		</div><!-- col-md-2	-->
		*/
		?>
		
		
		<div class="col-md-12">	
		<div class="row">	
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
			include_once(ABSOLUTE__PATH__.'/programm_files/admin_pages/main.php');
			}
		
	isset($mysql)?mysqli_close($mysql):'';
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	
	$memory = (function_exists('memory_get_usage')?memory_get_usage():0);
    $memory = $memory - $orig_memory;	
 

	?>
		</row>
		</div><!-- col-md-10-->
	</div>
</div>

	<div class="col-md-12"> 
		<div class="row text-center">
			<p class="footer_text">Страница сгенерирована за <?php echo round($time, 3); ?> сек. | Памяти затрачено <?php echo filesize_get($memory); ?> </p>
		</div>
	</div>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/bootstrap.js"></script>	
	<script src="js/calendar_ru2.js"></script>
	
	<script src="js/Easy-Searchable-Filterable-Select-List-with-jQuery/jquery.searchableSelect.js"></script>
	<script type="text/javascript">
  
	if ($('select.search').length) {	
	$('select.search').searchableSelect();
	}
	
	$(document).ready(function() {
	
		$("#client_id").change(function()
		{
			if ($("#client_id option:selected").val() == '0')
			{
			$("#client_id").addClass('hidden');
			$("#client_id_new").removeClass('hidden');
			}
			else
				{
				$("#client_id").removeClass('hidden');
				$("#client_id_new").addClass('hidden');
				}
		});
		
		$('.searchable-select-item').click(function () {
		var client_id = $(this).data("value");
		if (client_id == '0')
		{
			$("#client_id").addClass('hidden');
			$("#client_id_new").removeClass('hidden');
		}
		else
			{
				$("#client_id").removeClass('hidden');
				$("#client_id_new").addClass('hidden');
			}
		});
		
		$('#close_client_id_new').on('click', function() {
			$(".searchable-select").removeClass('hidden');
			$("#client_id").removeClass('hidden');
			$("#client_id_new").addClass('hidden');
		});	
		
		
		$('.add_podopech').click(function(){
			$("#add_podopech").removeClass('hidden');
			$("#btn_podopech").addClass('hidden');
		});	
		
		$('.add_podopech_close').click(function(){
			$("#add_podopech").addClass('hidden');
			$("#btn_podopech").removeClass('hidden');
		});	
		
		$('#sms_send').click(function(){
			$("#smsform").removeClass('hidden');
			$("#sms_close").removeClass('hidden');
			$("#sms_send").addClass('hidden');
		});	
		
		$('#sms_close').click(function(){
			$("#smsform").addClass('hidden');
			$("#sms_close").addClass('hidden');
			$("#sms_send").removeClass('hidden');
		});	
		
	});	
	
		$('.add_sidelka').click(function(){
			var num = parseInt($('.counter1').last().val())+1;
			$('.counter1').val(num);
			
					var htmls = '<div id="fp'+num+'" class="col-md-12 alert alert-success mtop">'+
						'<p class="pull-right"><span class="btn btn-danger remove_sidelka" onclick="deaddoppay('+num+');"><span class="glyphicon glyphicon-remove"></span></span></p>'+
						'<div class="form-group has-error">'+
							'<label for="fio_sid" class="col-lg-3 control-label">ФИО сиделки</label>'+
							'<div class="col-lg-8">'+
								'<input class="form-control" name="fio_sid[]" type="text" value="" placeholder="ФИО"  />'+
							'</div>'+
						'</div>'+
						'<div class="form-group">'+
							'<label for="tel_sid" class="col-lg-3 control-label">Телефон</label>'+
							'<div class="col-lg-8">'+
								'<input class="form-control" name="tel_sid[]" type="text" value=""  placeholder="+7..."  />'+
							'</div>'+
						'</div>'+
						'<div class="form-group">'+
							'<label for="dop_sid" class="col-lg-3 control-label">Примечание</label>'+
							'<div class="col-lg-8">'+
								'<input class="form-control" name="dop_sid[]" type="text" value=""  placeholder=""  />'+
							'</div>'+
						'</div>'+
					'</div><div class="clearfix mtop"></div>';
					
			$(htmls).appendTo('.overflow_dynamic_dop');
			return false;
		});	
		
		function deaddoppay (id)
		{
		$('#fp'+id).remove();
		}
		
	</script>
</body>
</html>

<?php 

	$fileonline = file(ABSOLUTE__PATH__.'/programm_files/online.srz');
			if (isset($titlepanel))
			{
			$gde = $titlepanel;
			}
			else
				{
				$gde = str_replace('/rupka.php','',$_SERVER['REQUEST_URI']);
				}
			$saveline = '';
			foreach($fileonline as $online)
			{
			$exl=explode("|",$online);

				if ($exl[0] == $_SESSION['__ID__'])
				{
				$saveline .= $_SESSION['__ID__']."|".time()."|".str_replace('/rupka.php','',$_SERVER['REQUEST_URI'])."|".$gde."|\r\n";
				$save_status = true;
				}
				else
					{
					$save_status2 = true;
					$saveline .= $exl[0]."|".$exl[1]."|".$exl[2]."|".$exl[3]."|\r\n";
					}
			}

			if (isset($save_status))
			{
			$on_l=fopen(ABSOLUTE__PATH__.'/programm_files/online.srz', 'w');
			$saves = $saveline;
			}
			elseif (!isset($save_status) or !isset($save_status2))
				{
				$on_l=fopen(ABSOLUTE__PATH__.'/programm_files/online.srz', 'a+');
				$saves = $_SESSION['__ID__']."|".time()."|".str_replace('/rupka.php','',$_SERVER['REQUEST_URI'])."|".$titlepanel."|\r\n";
				}
			fputs($on_l,$saves);
			fclose($on_l);
	
?>
