<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_send_settings(
	id int auto_increment primary key,
	login varchar(20) NOT NULL,
	pass varchar(32) NOT NULL,
	about text NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='СМС Настройки информирования'");	
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_send_log(
	id int auto_increment primary key 			COMMENT 'id',
	admin_id int(3) NOT NULL 					COMMENT 'id Кто добавил',
	zayavka_id int(10) NOT NULL					COMMENT 'id заявки',
	time int(12) NOT NULL 						COMMENT 'Дата отправки',
	tel varchar(10) NOT NULL 					COMMENT 'Телефон',
	sms varchar(300) NOT NULL 					COMMENT 'Сообщение',
	status int(1) NOT NULL 						COMMENT 'Тип отправки 0 - вручную, >0 - автоматически'
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='СМС лог'");
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_send_options(
	id int auto_increment primary key 			COMMENT 'id',
	sms1 varchar(300) NOT NULL 					COMMENT 'Сообщение1',
	sms2 varchar(300) NOT NULL 					COMMENT 'Сообщение2',
	sms3 varchar(300) NOT NULL 					COMMENT 'Сообщение3',
	status int(1) NOT NULL 						COMMENT 'Статус 0 - откл, 1 - вкл'
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='СМС Шаблоны рассылки'");
	
// 	1. при смене статуса делаем запись в таблицу с пометкой о рассылке
// 	2. рассылаем и меняем пометку на разасланно
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_send_ochered(
	id int auto_increment primary key 			COMMENT 'id',
	admin_id int(3) NOT NULL 					COMMENT 'id Кто добавил',
	zayavka_id int(10) NOT NULL					COMMENT 'id заявки',
	time int(12) NOT NULL 						COMMENT 'Дата отправки',
	status_sms int(1) NOT NULL 					COMMENT 'Какой вариант рассылки sms1,sms2 или sms3',
	status int(1) NOT NULL 						COMMENT 'Пометка о рассылке 0 - не разасланно'
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='СМС очередь'");
	

	
	$r = mysqli_query($mysql,"SELECT * FROM MED_send_settings LIMIT 1");
	if(!$r) exit(mysqli_error());
	$smsc=mysqli_fetch_assoc($r);
	mysqli_free_result($r);	
	
	if(isset($smsc['login']))
	{
	include_once(ABSOLUTE__PATH__.'/programm_files/smsc_api.php');
	
		if (isset($_SESSION['balans']))
		{
			if ((time() - $_SESSION['balans']['mktime'] > 181 ))
			{
			$_SESSION['balans'] = array ('mktime' => time(), 'balans' => get_balance ());
			}
		}
		else
			{
			$_SESSION['balans'] = array ('mktime' => time(), 'balans' => get_balance ());
			}
	}
	
	if (isset($_SESSION['balans']))
	{
	$balance['color'] = $_SESSION['balans']['balans'] < 100 ? 'danger' : 'success';
	$balance['sum'] = $_SESSION['balans']['balans'];
	$balance['time'] = date("H:i", $_SESSION['balans']['mktime']);
	}
	
	if (isset($_POST['SBM_sms_option']))
	{
	$sms1 = mysqli_real_escape_string($mysql, trim($_POST['sms1']));
	$sms2 = mysqli_real_escape_string($mysql, trim($_POST['sms2']));
	$sms3 = mysqli_real_escape_string($mysql, trim($_POST['sms3']));
	$status = isset($_POST['status'])?1:0;
	
		if (isset($_POST['id']))
		{
		$query_count = "UPDATE MED_send_options SET sms1='". $sms1."',sms2='". $sms2 ."', sms3='". $sms3 ."', status='". $status ."' WHERE id='".intval($_POST['id'])."' LIMIT 1";
		mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
		}
		else
			{
			$query = "INSERT INTO MED_send_options (sms1,sms2,sms3,status) VALUES('".$sms1."','".$sms2."','".$sms3."','".$status."')";
			mysqli_query($mysql,$query) or die(mysqli_error());
			}
			
	die ("<meta http-equiv=refresh content='0; url=?page=send'>");
	}
		
	if (isset($_POST['Sbmt_start']))
	{
	$login = mysqli_real_escape_string($mysql, trim($_POST['login']));
	$pass = mysqli_real_escape_string($mysql, trim($_POST['pass']));
	$about = mysqli_real_escape_string($mysql, trim($_POST['about']));
	
		if (isset($_POST['id']))
		{
		$query_count = "UPDATE MED_send_settings SET login='". $login."',pass='". $pass ."', about='". $about ."' WHERE id='".intval($_POST['id'])."' LIMIT 1";
		mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
		}
		else
			{
			$query = "INSERT INTO MED_send_settings (login,pass,about) VALUES('".$login."','".$pass."','".$about."')";
			mysqli_query($mysql,$query) or die(mysqli_error());
			}
	
	die ("<meta http-equiv=refresh content='0; url=?page=send'>");
	}
	
	if (isset($_POST['Sbmt_sms']))
	{
	$message = repl_txt_sms($_POST['mes']);
	$params = '';
		if ((mb_strlen($_POST['tel']) !== 10) or (!is_numeric($_POST['tel'])))
		{
		?>
		<div class="alert alert-dismissible alert-danger">

				<h5>Вы ввели не 10-ти значный номер телефона или ошиблись при вводе.</h5>
				<p>Необходимо ввести правильный номер телефона в Российском стандарте, все символы, кроме цифр, запрещены<br />
				<b>Пример:</b><br />
				<span class="text-danger">(918)000-00-00 - не верно</span><br />
				<span class="text-danger">918 00 00 000 - не верно</span><br />
				<span class="text-danger"><b>8</b>9180000000 - не верно</span><br /><br />

				<span class="text-success">9180000000 - ВЕРНО!</span><br />

				<a class="btn btn-primary" href="?page=send">Хорошо</a>
		</div>
		<?php
		exit();
		}

	$tel = trim($_POST['tel']);
	$params .= "7".$tel.":".$message."\n";

	send_sms('','',0,0,0,0,translit($smsc['login']),"list=".urlencode($params));

	$queryt = "INSERT INTO MED_send_log (admin_id,zayavka_id,time,tel,sms,status) VALUES('".intval($_SESSION['__ID__'])."','0','".time()."','".mysqli_real_escape_string($mysql,$tel)."','".mysqli_real_escape_string($mysql,$message)."','0')";
	mysqli_query($mysql,$queryt) OR die(trigger_error(mysqli_error($mysql)." in ".$queryt));
	
	die ("<meta http-equiv=refresh content='0; url=?page=send&mess=1'>");
	}
	

	
	?>
	<div class="col-md-12">
	<h3 class="page-header">СМС-информирование</h3>	
	<?php
	if (isset($_GET['mess']) and $_GET['mess'] == 1)
	{
	?>
	<div class="alert alert-dismissible alert-success text-center">
		<strong>Ваше сообщение отправленно!</strong><br />
		<a class="btn btn-primary" href="?page=send" class="alert-link">Хорошо</a>
	</div>	
	<?php
	}
	elseif (isset($_GET['add']))
	{
		if (isset($_GET['edit']))
		{
		$r = mysqli_query($mysql,"SELECT * FROM MED_send_settings WHERE id='". (int)$_GET['edit'] ."' LIMIT 1");
			if(!$r) exit(mysqli_error());
			$arredit = mysqli_fetch_assoc($r);
		mysqli_free_result($r);
		}
	?>
	<div class="clearfix"></div>


	<div class="col-md-12 well bs-component">
	<a name="adds"></a>
	<form class="form-horizontal" enctype='multipart/form-data' action='' method='post'>
		<fieldset>
			<legend>Поля выделенные <b class="text-danger">красным*</b>, обязательны к заполнению.</legend>
		    
			<div class="form-group has-error">
				<label for="about" class="col-lg-2 control-label">Логин*</label>
				<div class="col-lg-10">
					<input class="form-control" name="login" type="text" value="<?php echo isset($arredit['login']) ? $arredit['login'] : ''; ?>" required />
				</div>
			</div>
			
			<div class="form-group has-error">
				<label for="pass" class="col-lg-2 control-label">Пароль*</label>
				<div class="col-lg-10">
					<input class="form-control" name="pass" type="text" value="<?php echo isset($arredit['pass']) ? $arredit['pass'] : ''; ?>" required />
				</div>
			</div>
			
			<div class="form-group">
				<label for="about" class="col-lg-2 control-label">Описание</label>
				<div class="col-lg-10">
					<textarea class="form-control" name="about" rows="2"  placeholder="Примечание"> <?php echo isset($arredit['about'])?html_entity_decode(html_entity_decode($arredit['about'])):'';?></textarea>
				</div>
			</div>
			
			<?php echo isset($arredit) ? '<input name="id" type="hidden" value="'.$arredit['id'].'" />' : ''; ?>
		
			<div class="form-group">
				<div class="col-lg-10 col-lg-offset-2">
					<button type="submit" class="btn btn-primary" name="Sbmt_start">Сохранить</button>
					
					<?php echo isset($arredit) ? '<input type="button" value=" Отмена " onclick="location.href = \'?page=send\';return false;" class="btn btn-danger"/>' : ''; ?>
				</div>
			</div>		

		</fieldset>
		</form>	
	</div>
	
	<?php
	}
	elseif(isset($_GET['send']))
	{
	?>
	<div class="clearfix"></div>

	<div class="col-md-12 well bs-component">
	<a name="adds"></a>
	<form class="form-horizontal" enctype='multipart/form-data' action='' method='post'>
		<fieldset>
			<legend>Поля выделенные <b class="text-danger">красным*</b>, обязательны к заполнению.</legend>
		    
			<div class="form-group has-error">
				<label for="tel" class="col-lg-4 control-label">Телефон (без +7 и 8)*</label>
				<div class="col-lg-8">
					<input class="form-control" name="tel" type="text" value="" placeholder="Номер телефона" required />
				</div>
			</div>
			
			<div class="form-group">
				<label for="mes" class="col-lg-2 control-label">Сообщение</label>
				<div class="col-lg-10">
					<textarea class="form-control" name="mes" rows="2"  placeholder="Коротенькое сообщение"></textarea>
				</div>
			</div>

		
			<div class="form-group">
				<div class="col-lg-10 col-lg-offset-2">
					<button type="submit" class="btn btn-primary" name="Sbmt_sms">Отправить</button>
					
					<a class="btn btn-danger btn-sm" href="?page=send">Отмена</a>
				</div>
			</div>		

		</fieldset>
		</form>	
	</div>
	
	<?php
	}
	else
		{
		$r = mysqli_query($mysql,"SELECT * FROM MED_send_options WHERE id='1' LIMIT 1");
		if(!$r) exit(mysqli_error());
		$sid = mysqli_fetch_assoc($r);
		mysqli_free_result($r);
		?>
	
		<div class="clearfix"></div>	
	
		<table class="table table-hover">
		
			<tr>
				<th>Логин</th>
				<th>Баланс</th>
				<th>Описание</th>
				<th>Операции</th>
			</tr>	
		<?php
			$i=0;
		$r = mysqli_query($mysql,"SELECT * FROM MED_send_settings");
			if(!$r) exit(mysqli_error());
			while	($hk=mysqli_fetch_assoc($r))
			{
			$sts_pp = true;
			?>
			<tr>
				<td>
					<form target="_blank" method="POST" id="form<?php echo $hk['login']; ?>" action="https://smsc.ru/login/">
						<input type="hidden" name="login" value="<?php echo $hk['login']; ?>">
						<input type="hidden" name="psw" value="<?php echo $hk['pass']; ?>">
						<input type="hidden" name="secure" value="on">
						<a title="Для быстрого перехода в Личн.Кабинет СМС-Центра" class="btn btn-primary btn-xs" name="ss" onclick="document.getElementById('form<?php echo $hk['login']; ?>').submit();return false;"><?php echo $hk['login']; ?></a>
					</form>
					
				</td>
				<td>
					<span class="text-<?php echo isset($balance) ? $balance['color'] : 'default' ; ?>"><?php echo isset($balance) ? $balance['sum'].' руб. в '.$balance['time']: '0' ; ?></span>
				</td>
				<td style="width:35%;"><?php echo $hk['about']; ?></td>
				<td>
					<a data-toggle="tooltip" data-placement="top" title="Отправить смс" class="btn btn-primary" href="?page=send&send=<?php echo $hk['id']; ?>"><span class="glyphicon glyphicon-envelope"></span></a>
					<a data-toggle="tooltip" data-placement="top" title="Редактировать" class="btn btn-success btn-sm" href="?page=send&edit=<?php echo $hk['id']; ?>&add=<?php echo $hk['id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
				</td>
			</tr>		
			<?php	
			$i++;
			}
		mysqli_free_result($r);	
		?>
		</table>
		
		<div class="clearfix"></div>
		
		<div class="col-md-12">
			<form class="form-horizontal" method="POST" role="form">	
				<h3>Разрешение отправки смс:</h3>
				<div class="form-group">
					<label><input <?php echo (isset($sid['status']) AND $sid['status'] == 1) ? 'checked="checked"' : ''; ?> type="checkbox" name="status"> <?php echo (isset($sid['status']) AND $sid['status'] == 1) ? '<span class="text-success">Рассылка разрешена</span>' : '<span class="text-danger">Рассылка запрещена</span>'; ?></span>
				</div>	
				
				<h3>1. отправляем клиенту смс " ваш заказ взят в работу" - если выбран статус заявки "Открыта"</h3>
				<div class="form-group">
					<textarea class="form-control" name="sms1" rows="2"><?php echo isset($sid['sms1'])?trim($sid['sms1']):'Уважаеймый(ая) [client] ваш заказ взят в работу.';?></textarea>
					<span class="help-block">Доступные параметры [client] - замениться на фио клиента</span>
				</div>	
				
				<h3>2. назначена сиделка на заказ</h3>
				<div class="form-group">
					<textarea class="form-control" name="sms2" rows="2"><?php echo isset($sid['sms2'])?trim($sid['sms2']):'На Ваш заказ назначена: [sidelka], [tel_sidelka], Ожидает вашего звонка. С уважением ООО Мы с Вами 24';?></textarea>
					<span class="help-block">Доступные параметры [sidelka] - замениться на фио сиделки, [tel_sidelka] - замениться на телефон сиделки</span>
				</div>	
				
				<h3>3. первая оплата: клиенту</h3>
				<div class="form-group">
					<textarea class="form-control" name="sms3" rows="2"><?php echo isset($sid['sms3'])?trim($sid['sms3']):'Уважаеймый(ая) [client], Вам необходимо внести оплату до [data]';?></textarea>
					<span class="help-block">Доступные параметры [client] - замениться на фио клиента, [data] - замениться на дату "за 1 день до дня оплаты"</span>
				</div>	
				
				<?php echo isset($sid['id']) ? '<input type="hidden" value="'.$sid['id'].'" name="id" />' : ''; ?>
				
				<div class="col-md-12">
					<button type="submit" class="btn btn-primary" name="SBM_sms_option"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
				</div>	
			<div class="clearfix"></div>
			</form>
		</div>
		<?php
		if (!isset($sts_pp))
		{
		?>
		<a data-toggle="tooltip" data-placement="top" title="Добавить" class="btn btn-success btn-lg" href="?page=send&add=1"><span class="glyphicon glyphicon-plus"></span></a>
		<?php
		}
		?>

		<div class="clearfix"></div>
		<?php
		}
		?>
	</div>