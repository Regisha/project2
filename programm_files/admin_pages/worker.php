<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_worker(
	id int auto_increment primary key,
	name varchar(300) NOT NULL,
	login varchar(20) NOT NULL,
	pass varchar(32) NOT NULL,
	tel varchar(100) NOT NULL,
	mail varchar(100) NOT NULL,	
	settings text NOT NULL,
	time int(12) NOT NULL,
	status int(1) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Сотрудники'");	
	
	if (isset($_GET['delete']))
	{
	?>
	<div class="alert alert-dismissible alert-danger">
		<h3 class="text-center text-danger">Подтверждение удаления записи</h3>
		<strong>Выбранная запись будет удалена из базы</strong><br /> 
		<p>Осторожно, это может привести к последствиям, если данная запись уже использовалась в системе, тогда будет вызвана ошибка в той записи.</p>
		
		<p class="text-center">
			<input type="button" value=" Удалить " onclick="location.href = '?page=worker&del=<?php echo $_GET['delete']; ?>';return false;" class="btn btn-danger btn-sm"/>
			<input type="button" value=" Отмена " onclick="location.href = '?page=worker';return false;" class="btn btn-primary btn-sm"/>
		</p>
	</div>
	
	<div class="clearfix"></div>
	<?php
	}	
	
	if (isset($_GET['del']))
	{
		if ($_GET['del'] !== '1')
		{
		$queryT = "DELETE FROM MED_worker WHERE id='".$_GET['del']."'";
		mysqli_query($mysql,$queryT) or die(mysqli_error($mysql));	
		}
	die ("<meta http-equiv=refresh content='0; url=?page=worker#adds'>");
	}
	
	if (isset($_POST['Submit_pc']))
	{
		if (!isset($_POST['name']))
		{
		echo "<div class='alert alert-dismissible alert-warning text-center'>Не указано Имя! Нужно исправить.<br /><a class='btn btn-primary' onclick=\"history.back(); return false;\">Вернуться назад</a></div>";
		exit();
		}	
		
		if (!isset($_POST['login']) OR !isset($_POST['pass']))
		{
		echo "<div class='alert alert-dismissible alert-warning text-center'>Не указан Логин или Пароль! Нужно исправить.<br /><a class='btn btn-primary' onclick=\"history.back(); return false;\">Вернуться назад</a></div>";
		exit();
		}			
	
	$status = intval($_POST['status']);
	$name = mysqli_real_escape_string($mysql,$_POST['name']);
	$login = mysqli_real_escape_string($mysql,$_POST['login']);
	$pass = mysqli_real_escape_string($mysql,md5($_POST['pass']));
	$tel = mysqli_real_escape_string($mysql,trim($_POST['tel']));
	$mail = mysqli_real_escape_string($mysql,trim($_POST['mail']));
	$adress = isset($_POST['adress']) ? mysqli_real_escape_string($mysql,trim($_POST['adress'])) : '';
	
	$about = str_replace('\r\n', '<br />', trim($_POST['about']));
	$about = strip_tags($about, '<br>');
	$about = mysqli_real_escape_string($mysql,$about);
	
	$settings = $adress.'|'.$about;
	$settings = mysqli_real_escape_string($mysql,$settings);
	
	$queryU = "INSERT INTO MED_worker (name,login,pass,tel,mail,settings,time,status) VALUES('".$name."','".$login."','".$pass."','".$tel."','".$mail."','".$settings."','".time()."','".$status."')";
	mysqli_query($mysql,$queryU) or die(mysql_error($mysql));
	
	die ("<meta http-equiv=refresh content='0; url=?page=worker&mess=1'>");
	}	
	
	if (isset($_POST['Submit_pc_edit']))
	{
	$status = intval($_POST['status']);
	$name = mysqli_real_escape_string($mysql,$_POST['name']);
	$login = mysqli_real_escape_string($mysql,$_POST['login']);
	$pass = mysqli_real_escape_string($mysql,$_POST['pass']);
	$currentpass = mysqli_real_escape_string($mysql,$_POST['currentpass']);
	$pass = !empty($pass)?md5($pass):$currentpass;
	$tel = mysqli_real_escape_string($mysql,trim($_POST['tel']));
	$mail = mysqli_real_escape_string($mysql,trim($_POST['mail']));
	$adress = isset($_POST['adress']) ? mysqli_real_escape_string($mysql,trim($_POST['adress'])) : '';
	
	$about = str_replace('\r\n', '<br />', trim($_POST['about']));
	$about = strip_tags($about, '<br>');
	$about = mysqli_real_escape_string($mysql,$about);
	
	$settings = $adress.'|'.$about;
	$settings = mysqli_real_escape_string($mysql,$settings);
	
		if ($_POST['id'] !== '1')
		{
		$query_count = "UPDATE MED_worker SET name='".$name."',login='".$login."',pass='".$pass."',tel='".$tel."',mail='".$mail."',settings='".$settings."',status='".$status."' WHERE id='".$_POST['id']."' LIMIT 1";
		mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
		}
	die ("<meta http-equiv=refresh content='0; url=?page=worker'>");
	}
	?>
	
	<h3>Сотрудники</h3>
	<div class="clearfix"></div>
	<br />
	
	<div class="col-md-12"> 

		<a href="?page=worker&worker_add=true" class="btn btn-default">Добавить сотрудника</a>

		<a href="?page=worker" class="btn btn-primary">Сотрудники</a>
		<?php echo isset($_GET['q'])?'<a href="?page=worker" class="btn btn-danger">Очистить</a>':'';?>
	</div>
	
	<div class="clearfix"></div>
	<br />
	<div class="col-md-12">
	<?php
	
	if (isset($_GET['mess']) and $_GET['mess'] == 1)
	{
	?>
	<div class="alert alert-dismissible alert-success text-center">
		<strong>Запись добавлена!</strong><br />
		<a class="btn btn-primary" href="?page=worker" class="alert-link">Хорошо</a>
	</div>	
	<?php
	}	
	
	if (isset($_GET['worker_add']))
	{
		if (isset($_GET['edit']))
		{
		$id = is_numeric($_GET['edit'])?$_GET['edit']:die ("<meta http-equiv=refresh content='0; url=?page=worker&err=1'>");

		$SQL = "SELECT * FROM MED_worker WHERE id='".$id."' LIMIT 1";
		$B_user = mysqli_query($mysql,$SQL);
		if(!$B_user) exit(mysqli_error($mysql));
		$usr=mysqli_fetch_assoc($B_user);

			$set = explode('|',$usr['settings']);
			$adress = !empty($set[0])?$set[0]:'';
			$about = !empty($set[1])?str_replace('\\','',$set[1]):'';
		}
	?>
	<div class="col-md-12 well bs-component">
	<form class="form-horizontal" action='' method='post'>
		<fieldset>
			<legend>Поля выделенные <b class="text-danger">красным*</b>, обязательны к заполнению.</legend>

			<div class="form-group has-error">
				<label for="select" class="col-lg-2 control-label">Тип доступа*</label>
				<div class="col-lg-10">
					<select class="form-control" name="status" required>
						<option <?php echo !isset($usr['status']) ? 'selected' : ''; ?> disabled>Выбрать тип</option>
						<option <?php echo (isset($usr['status']) and $usr['status'] == 0) ? 'selected' : ''; ?> value="0">Полный</option>
						<option <?php echo (isset($usr['status']) and $usr['status'] == 1) ? 'selected' : ''; ?> value="1">Ограниченный</option>
					</select>
				</div>
			</div>
			
			<div class="form-group has-error">
				<label for="name" class="col-lg-2 control-label">Имя сотрудника *</label>
				<div class="col-lg-10">
					<input class="form-control" name="name" type="text" value="<?php echo isset($usr['name']) ? html_entity_decode(str_replace('\\"',"'",$usr['name'])) : ''; ?>" required placeholder="Имя или ФИО"/>
				</div>
			</div>

			<div class="well well-sm">
				<div class="form-group has-error">
					<label for="login" class="col-lg-2 control-label">Логин*</label>
					<div class="col-lg-10">
						<input class="form-control" name="login" type="text" value="<?php echo isset($usr['login']) ? $usr['login'] : ''; ?>" required placeholder="Логин для входа в систему"/>
					</div>
				</div>
				
				<div class="form-group has-error">
					<label for="pass" class="col-lg-2 control-label">Пароль</label>
					<div class="col-lg-10">
						<input class="form-control" name="pass" type="text" value="" placeholder="Пароль для входа в систему"/>
					</div>
				</div>
			</div>

			<div class="form-group overflow_dynamic">
			
				<label for="cname" class="col-lg-2 control-label">Контакты</label>
				<div class="col-lg-10">
					<input class="form-control phone" name="tel" type="text" value="<?php echo isset($usr['tel']) ? $usr['tel'] : ''; ?>" placeholder="+7(___)___-__-__"/>
					<input class="form-control" name="mail" type="text" value="<?php echo isset($usr['mail']) ? $usr['mail'] : ''; ?>" placeholder="Укажите e-mail" />
				</div>						
				<div class="clearfix"></div>
			</div>

			
			<div class="form-group">
				<label for="adress" class="col-lg-2 control-label">Адрес</label>
				<div class="col-lg-10">
					<input type="text" id="adress" value="<?php echo isset($adress) ? $adress : ''; ?>" name="adress" class="form-control input-sm" placeholder="Город, улица, дом/офис" />
					<span class="help-block">Если несколько адресов - разделите их точкой с запятой <strong>;</strong></span>
				</div>
			</div>
			
			<div class="form-group">
				<label for="text-count" class="col-lg-2 control-label">Дополнительный текст</label>
				<div class="col-lg-10">
					<textarea id="text-count" name="about" class="form-control" rows="3"><?php echo isset($about) ? $about : ''; ?></textarea>
					<span class="help-block">Укажите дополнительный текст, как справка (не обязательно)</span>
				</div>
			</div>
			
			<?php echo isset($usr['id'])?'<input type="hidden" name="id" value="'.$usr['id'].'">':''; ?>
			<?php echo isset($usr['id'])?'<input type="hidden" name="currentpass" value="'.$usr['pass'].'">':''; ?>
			
			<div class="form-group">
				<div class="col-lg-10 col-lg-offset-2">
					<p class="pull-left">
						<button type="submit" class="btn btn-primary" name="<?php echo isset($usr['id'])?'Submit_pc_edit':'Submit_pc'; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo isset($usr['id'])?'Изменить':'Сохранить'; ?></button>
					</p>
					<p class="pull-right">
						<?php echo isset($usr['id'])?'<a href="?page=worker&see='.$usr['id'].'" class="btn btn-success"><span class="glyphicon glyphicon-eye-open"></span> Просмотр</a>':''; ?>
						<a href="?page=worker" class="btn btn-danger"><span class="glyphicon glyphicon-off"></span> Закрыть</a>
					</p>					
				</div>
			</div>

		</fieldset>
		</form>	
	</div>
	<?php
	}
	
	elseif (isset($_GET['see']))
	{
	$id = is_numeric($_GET['see'])?$_GET['see']:die ("<meta http-equiv=refresh content='0; url=?page=worker&err=1'>");

		$SQL = "SELECT * FROM MED_worker WHERE id='".$id."' LIMIT 1";
		$B_user = mysqli_query($mysql,$SQL);
		if(!$B_user) exit(mysqli_error($mysql));
		$usr=mysqli_fetch_assoc($B_user);

		$set = explode('|',$usr['settings']);
		$adress = !empty($set[0])?$set[0]:' - ';
		$about = !empty($set[1])?$set[1]:'<s>Описания нет</s>';
	?>

	<div class="panel panel-<?php echo $usr['status'] == 0?'default':'warning'; ?>">
		<div class="panel-heading">
			<h3 class="panel-title">Пользователь №<?php echo $usr['id']; ?></h3>
		</div>
		<div class="panel-body">
		
			<div class="col-md-12">
			<h3 class="page-header">Общая информация</h3>
				<table class="table table-hover table-bordered">

					<tr>
						<td>Имя</td>
						<td><?php echo str_replace('\\','',html_entity_decode($usr['name'])); ?></td>
					</tr>
					<tr>
						<td>Тип</td>
						<td><?php echo $usr['status'] == 0?'Полный доступ':'Ограниченный доступ'; ?></td>
					</tr>					
					<tr>
						<td>Адрес</td>
						<td><a target="_blank" href="https://maps.yandex.ru/?text=<?php echo rawurlencode($adress); ?>"><?php echo $adress; ?></a></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo str_replace('\r\n','<br />',html_entity_decode(str_replace('\\','',$about))); ?></td>
					</tr>
				</table>
			</div>
			
			
			<div class="col-md-12">
			<h3 class="page-header">Контакты</h3>
				<table class="table table-hover table-bordered">
					<tr>
						<td><?php echo $usr['tel']; ?></td>
						<td><a data-toggle="tooltip" data-placement="top" title="написать письмо" href="mailto:<?php echo $usr['mail']; ?>"><?php echo $usr['mail']; ?></a></td>
					</tr>				
				</table>
			</div>
			
		</div>
		<div class="clearfix"></div>
		<p class="text-center ">
			<?php
			if (in_array($_SESSION['__ID__'],$super_admin))
			{
			?>
			<a href="?page=worker&worker_add=<?php echo $usr['id']; ?>&edit=<?php echo $usr['id']; ?>" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-pencil"></span> Редактировать</a>
			<?php
			}
			?>
			<a href="?page=worker" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-off"></span> Закрыть</a>
		</p>
	</div>	
	<?php
	}
	
	else
	{
	?>	
	<div class="col-md-12">
		<form class="form-horizontal" action='' method='get'>
			<input type="hidden" name="page" value="worker">
			<fieldset>
				<legend>Поиск</legend>

				<div class="form-group">

					<div class="col-md-4">
						<select class="form-control select" name="w">
							<option <?php echo (isset($_GET['w']) and $_GET['w'] == 0) ? 'selected' : ''; ?> value="0">Имя</option>
							<option <?php echo (isset($_GET['w']) and $_GET['w'] == 1) ? 'selected' : ''; ?> value="1">Телефон</option>
							<option <?php echo (isset($_GET['w']) and $_GET['w'] == 2) ? 'selected' : ''; ?> value="2">Почта</option>
						</select>
					</div>

					<div class="col-md-6">
						<input class="form-control" name="q" type="text" value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>" />
					</div>

					<div class="col-md-2">
						<button type="submit" class="btn btn-primary">Поиск</button>
					</div>
				</div>			
		</form>
		<div class="clearfix"></div>
	</div>
	
	<div class="clearfix mtop"><br></div>
	
	<table class="table mtop col-md-12">
		<tr>
			<th>Тип</th>
			<th>Имя</th>
			<th>Контакт</th>
			<th>Управление</th>
		</tr>
	<?php
	if (isset($_GET['q']))
	{
	$arr = array('name','tel','mail');
	$w = isset($_GET['w'])?$_GET['w']:'0';
	$q = intval($w) == 1 ? preg_replace ("/[^0-9\s]/","",trim($_GET['q'])) : urldecode(trim($_GET['q']));
	$w = intval($w) == 1 ? "REPLACE( REPLACE( REPLACE( ".$arr[intval($w)].", '-', '' ), '(', '' ), ')', '' )":$arr[intval($w)];
	$WHERE = " WHERE ".$w." LIKE '%".$q."%'";
	}
	
		$SQLs = "SELECT * FROM MED_worker ".(isset($WHERE)?$WHERE:''). " ";
		$result = mysqli_query($mysql, $SQLs) or  die(mysqli_error().' - MED_worker_NUM');
		$total_all = mysqli_num_rows($result);
		$lastgtforge = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
	
	$total_all = is_numeric($total_all)?$total_all:0;
		
	$p = isset($_GET['p'])?intval($_GET['p']):1;
	if($p < 1) {$p = 1;}
	$num_elements = 20;

	$num_pages = ceil($total_all / $num_elements);
	if ($p > $num_pages) $p = $num_pages;
	$start = ($p - 1) * $num_elements;
	$start = (int)str_replace('-','',$start);
	$start = $start < 0 ? 0 : $start;
	$num_elements < 0 ? 0 : $num_elements;	
	
	$SQL = "SELECT * FROM MED_worker ".(isset($WHERE)?$WHERE:''). " ORDER BY name LIMIT ".$start.", ".$num_elements;
	$B_user = mysqli_query($mysql,$SQL);
	if(!$B_user) exit(mysqli_error($mysql));
	while($usr=mysqli_fetch_assoc($B_user))
	{
		$set = explode('|',$usr['settings']);
		$adress = !empty($set[0])?$set[0]:'';
		$about = !empty($set[1])?$set[1]:'';
	?>
		<tr <?php echo $usr['status'] == 0?'':'class="warning"'; ?>>
			<td><?php echo $usr['status'] == 0?'Полный доступ':'Ограниченный доступ'; ?></td>
			<td><?php echo str_replace('\\','',html_entity_decode($usr['name'])); ?></td>
			<td>
				<?php echo $usr['tel']; ?><br />
				<a href="mailto:<?php echo $usr['mail']; ?>"><?php echo $usr['mail']; ?></a><br />
				<?php echo $adress; ?>
			</td>
			<td>
				<a data-toggle="tooltip" data-placement="top" title="Просмотр" class="btn btn-primary btn-sm" href="?page=worker&see=<?php echo $usr['id']; ?>"><span class="glyphicon glyphicon-eye-open"></span></a>

				<a data-toggle="tooltip" data-placement="top" title="Редактировать" class="btn btn-success btn-sm" href="?page=worker&worker_add=<?php echo $usr['id']; ?>&edit=<?php echo $usr['id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
				<a data-toggle="tooltip" data-placement="top" title="Удалить" class="btn btn-danger btn-sm" href="?page=worker&delete=<?php echo $usr['id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>

			</td>
		</tr>

	<?php 
	}			
	mysqli_free_result($B_user);
	?>
	</table>
	
	<?php
		/*
		if ($total_all >= $num_elements)
		{
		echo GetNav($p, $num_pages);
		}
		*/
	}
	?>
	</div>