<?php
	header("Content-Type:text/html;charset=utf-8");
	setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
	$time_start = microtime(true);
	error_reporting(E_ALL);

	define('ABSOLUTE__PATH__',$_SERVER['DOCUMENT_ROOT']);
	include_once(ABSOLUTE__PATH__.'/programm_files/mysql.php');
	session_start();
	
	if (isset($_SESSION['__ID__']))
	{
	die ("<meta http-equiv=refresh content='0; url=rupka.php?'>");
	}
	
	if(isset($_POST['Submit_login']))
	{
	$login = mysqli_real_escape_string($mysql,trim($_POST['login']));	
		$SQLs = "SELECT * FROM MED_worker WHERE login='". $login ."' LIMIT 1";
		$result = mysqli_query($mysql, $SQLs) or  die(mysqli_error($mysql).' - MED_worker_ENTER');
		$usr = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		
		if (!isset($usr['id']))
		{
		?>
		<div class="alert alert-dismissible alert-danger">
			<h3 class="text-center text-danger">Ошибка</h3>
			<strong>Введенный логин не существует</strong><br /> 
		
			<p class="text-center">
				<input type="button" value=" Вернуться " onclick="location.href = 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/kpp.php';return false;" class="btn btn-primary btn-sm"/>
			</p>
		</div>
		<div class="clearfix"></div>
		<?php
		exit();
		}
		else
			{
			$pass = mysqli_real_escape_string($mysql,md5($_POST['pasw']));
				if ($pass !== $usr['pass'])
				{
				?>
				<div class="alert alert-dismissible alert-danger">
					<h3 class="text-center text-danger">Ошибка</h3>
					<strong>Введенный пароль не верен.</strong><br /> 
				
					<p class="text-center">
						<input type="button" value=" Вернуться " onclick="location.href = 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/kpp.php';return false;" class="btn btn-primary btn-sm"/>
					</p>
				</div>
				<div class="clearfix"></div>
				<?php
				$_SESSION['spam'] = isset($_SESSION['spam']) ? $_SESSION['spam'] + 1 : 1;
				$_SESSION['antispam'] = true;
				exit();
				}
			$_SESSION['__ID__'] = $usr['id'];
			die ("<meta http-equiv=refresh content='0; url=rupka.php?page=main'>");
			}
	}
	
	if 	(isset($_GET['login']))
	{
	echo "
	<div class='alert alert-dismissible alert-warning text-center'>
		<strong>Вы не авторизированны, пожалуйста авторизируйтесь.</strong>
	</div>";
	}

	if 	(
		(isset($_SESSION['spam'])) &&
		($_SESSION['spam'] >= 3) and
		(!isset($_SESSION['mktime']))
		)
	{
	$_SESSION['mktime'] = time() + (3600 * rand(1,5));
	}


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
	<!-- Style CSS -->
	<link href="css/cerulean_bootstrap.min.css" media="screen" rel="stylesheet">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>
<body>

<div class="container">
	<div class="row">
	<?php
	
	if 	(
		(isset($_SESSION['mktime'])) &&
		($_SESSION['mktime'] > time())
		)
	{
	echo "
	<div class='alert alert-dismissible alert-danger text-center'>
		<strong>Вы исчерпали все попытки ввести правильные логин и пароль.<br/> Вы заблокированны до ".date("H:i",$_SESSION['mktime'])." ".date("d-m-Y",$_SESSION['mktime']).".</strong><br />
		<a class='btn btn-primary' href='http://".$_SERVER['HTTP_HOST']."'>&larr; На сайт.</a>
	</div>";
	define('__PANEL__LOGIN__', 1 );
	}
	
	if (!defined('__PANEL__LOGIN__'))
	{
	?>
	<div class="col-md-8">
		
		<div class="col-md-6 col-md-offset-3">
			<h2 class="text-center page-header"><center><img class="img-responsive hidden-xs hidden-sm" src="2.png" alt="NEXT CRM" /></center>CRM ОРГАНИЗАЦИИ</h2>
		</div>
		
		<div class="clearfix"></div>
		
		<form class="form-horizontal" method="post" name="passform">
		<fieldset>
			<legend>Заполните необходимые данные</legend>
			
			<div class="form-group">
				<label for="inputlogin" class="col-lg-2 control-label">Логин</label>
				<div class="col-lg-10">
					<input name="login" class="form-control" id="inputlogin" placeholder="login" type="text">
				</div>
			</div>
			
			<div class="form-group">
				<label for="inputPassword" class="col-lg-2 control-label">Password</label>
				<div class="col-lg-10">
					<input name="pasw" class="form-control" id="inputPassword" placeholder="Password" type="password">
				</div>
			</div>

			<div class="form-group text-center">
			<div class="col-lg-10 col-lg-offset-2">
				<button name="Submit_login" type="submit" class="btn btn-primary">Вход</button>
			</div>
			</div>
		</fieldset>
		</form>		
	</div>
	<?php
	}
	?>
	</div>
</div>

</body>
</html>
