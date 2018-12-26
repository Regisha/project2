<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php'>");
	}
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_client(
		client_id int auto_increment primary key,
		admin_id int(1) NOT NULL,
		zayavka_id int(10) NOT NULL,	
		status_client int(2) NOT NULL,
		fio_zakaz varchar(50) NOT NULL,
		happy_day varchar(50) NOT NULL,
		num_dog varchar(200) NOT NULL,
		tel_zakaz varchar(20) NOT NULL,
		pasport varchar(20) NOT NULL,
		pasport_kem varchar(80) NOT NULL,
		srok_dog1 varchar(50) NOT NULL,
		srok_dog2 varchar(50) NOT NULL,
		zp_sid varchar(20) NOT NULL,
		srok_opl varchar(20) NOT NULL,
		data_opl varchar(12) NOT NULL,
		data_opl_sid  varchar(12) NOT NULL,
		sum_sid varchar(20) NOT NULL,
		agent varchar(20) NOT NULL,
		kyr varchar(20) NOT NULL,
		kom_klient  varchar(150) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Клиенты'");	
	
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_oplata(
	oplata_id int auto_increment primary key,
	client_id int(10) NOT NULL,
	admin_id int(1) NOT NULL,
	date_plat varchar(50) NOT NULL,
	vid_plat int(1) NOT NULL,
	sum_plat varchar(10) NOT NULL,
	status_plat varchar(50) NOT NULL,   
	zayavka_id int(3) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Оплата'");



	if (isset($_POST['Sbmt_sms']))
	{
		$r = mysqli_query($mysql,"SELECT * FROM MED_send_settings LIMIT 1");
		if(!$r) exit(mysqli_error());
		$smsc=mysqli_fetch_assoc($r);
		mysqli_free_result($r);	
		include_once(ABSOLUTE__PATH__.'/programm_files/smsc_api.php');
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

				<a class="btn btn-primary" href="?page=client&add=true&edit=<?php echo $_POST['client_id']; ?>">Хорошо</a>
		</div>
		<?php
		exit();
		}

	$tel = trim($_POST['tel']);
	$params .= "7".$tel.":".$message."\n";

	send_sms('','',0,0,0,0,translit($smsc['login']),"list=".urlencode($params));

	$queryt = "INSERT INTO MED_send_log (admin_id,zayavka_id,time,tel,sms,status) VALUES('".intval($_SESSION['__ID__'])."','0','".time()."','".mysqli_real_escape_string($mysql,$tel)."','".mysqli_real_escape_string($mysql,$message)."','0')";
	mysqli_query($mysql,$queryt) OR die(trigger_error(mysqli_error($mysql)." in ".$queryt));
	
	die ("<meta http-equiv=refresh content='0; url=?page=client&add=true&edit=".$_POST['client_id']."&mess=1'>");
	}
	
	if (isset($_POST['SBM_add']))
	{
		$admin_id = mysqli_real_escape_string($mysql, $_SESSION['__ID__']);
		$zayavka_id = isset($_POST['zayavka_id '])?mysqli_real_escape_string($mysql, trim($_POST['zayavka_id '])):0;
		$status_client = mysqli_real_escape_string($mysql, trim($_POST['status_client']));
		$fio_zakaz = mysqli_real_escape_string($mysql, trim($_POST['fio_zakaz']));
		
		$happy_day = isset($_POST['happy_day'])?strtotime($_POST['happy_day']):time();
		$num_dog = mysqli_real_escape_string($mysql, trim($_POST['num_dog']));
		
		$tel_zakaz = mysqli_real_escape_string($mysql, trim($_POST['tel_zakaz']));
		$pasport= mysqli_real_escape_string($mysql, trim($_POST['pasport']));
		$pasport_kem= mysqli_real_escape_string($mysql, trim($_POST['pasport_kem']));
		$srok_dog1 = isset($_POST['srok_dog1'])?strtotime($_POST['srok_dog1']):time();
		$srok_dog2 = isset($_POST['srok_dog2'])?strtotime($_POST['srok_dog2']):time();
		$zp_sid = mysqli_real_escape_string($mysql, trim($_POST['zp_sid']));
		$srok_opl = mysqli_real_escape_string($mysql, trim($_POST['srok_opl']));
		$data_opl = isset($_POST['data_opl'])?strtotime($_POST['data_opl']):time();
		
		$sum_sid= mysqli_real_escape_string($mysql, trim($_POST['sum_sid']));
		$data_opl_sid= isset($_POST['data_opl_sid'])?strtotime($_POST['data_opl_sid']):time();
		
		$agent = mysqli_real_escape_string($mysql, trim($_POST['agent']));
		$kyr = mysqli_real_escape_string($mysql, trim($_POST['kyr']));
		$kom_klient = mysqli_real_escape_string($mysql, trim($_POST['kom_klient']));
		
	
		if (isset($_POST['client_id']))
		{
		$query_count = "UPDATE MED_client SET zayavka_id='". $zayavka_id."',status_client='". $status_client ."', fio_zakaz='". $fio_zakaz ."', tel_zakaz='". $tel_zakaz ."', happy_day='".$happy_day."', num_dog='".$num_dog."', pasport='". $pasport."', pasport_kem='". $pasport_kem."', srok_dog1='". $srok_dog1."', srok_dog2='". $srok_dog2."', zp_sid='". $zp_sid ."', srok_opl='". $srok_opl."', data_opl='". $data_opl."', data_opl_sid='". $data_opl_sid."', sum_sid='". $sum_sid."', agent='". $agent."', kyr='". $kyr."', kom_klient='". $kom_klient."' WHERE client_id='".intval($_POST['client_id'])."' LIMIT 1";
		mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
		$client_id = intval($_POST['client_id']);
		
		
		
// 		$query_count = "UPDATE MED_debet SET client_id='". $_GET['edit']"  LIMIT 1";
// 		mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
		
		}
		else
			{
			$insertSQL = mysqli_query($mysql, "INSERT INTO MED_client (admin_id,zayavka_id,status_client,fio_zakaz,tel_zakaz,happy_day,num_dog,pasport,pasport_kem,srok_dog1, srok_dog2,zp_sid,srok_opl,data_opl,data_opl_sid, sum_sid,agent,kyr,kom_klient) 
			VALUES 	
				('".$admin_id."','".$zayavka_id."','".$status_client."','".$fio_zakaz."','". $tel_zakaz ."','".$happy_day."','". $num_dog ."', '". $pasport ."','".$pasport_kem."','".$srok_dog1."','".$srok_dog2."', '". $zp_sid ."','".$srok_opl."','". $data_opl."','". $data_opl_sid."','". $sum_sid."','".$agent."','".$kyr."', '".$kom_klient."')");
			if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
			
			$client_id = mysqli_insert_id($mysql);
			}
			
	die ("<meta http-equiv=refresh content='0; url=?page=client&add=true&edit=".$client_id."&mess=2'>");
	}
	
	if (isset($_POST['add_plata']))
	{
		$client_id = intval($_POST['client_id']);
		$admin_id = mysqli_real_escape_string($mysql, $_SESSION['__ID__']);
		$date_plat = isset($_POST['date_plat'])?strtotime($_POST['date_plat']):time();
		$vid_plat = mysqli_real_escape_string($mysql, trim($_POST['vid_plat']));
		$sum_plat = mysqli_real_escape_string($mysql, summa_replace($_POST['sum_plat']));
		$status_plat = mysqli_real_escape_string($mysql, trim($_POST['status_plat']));
		$city_id = intval($_POST['city_id']);
		$zayavka_id = isset($_POST['zayavka_id'])?intval($_POST['zayavka_id']):0;
		
		$insertSQL = mysqli_query($mysql, "INSERT INTO MED_oplata (client_id,admin_id,date_plat,vid_plat,sum_plat,status_plat) VALUES('".$client_id."','".$admin_id."','". $date_plat."','".$vid_plat."','".$sum_plat."', '".$status_plat."')");
		if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
		
		$oplata_id = mysqli_insert_id($mysql);
		
		$query = "INSERT INTO MED_debet (type_pay,stspay,summ,time,about,city_id,oplata_id,client_id,zayavka_id) VALUES('".$vid_plat."','".($vid_plat+1)."','".$sum_plat."','".$date_plat."','".$status_plat."','".$city_id."','".$oplata_id."','".$client_id."','".$zayavka_id."')";
		mysqli_query($mysql,$query) or die(mysqli_error($mysql));

	die ("<meta http-equiv=refresh content='0; url=?page=client&add=true&edit=".$client_id."&mess=3'>");	
	}	
	
		
	
	
	
	if (isset($_GET['del']))
	{
		$query = "DELETE FROM MED_client  WHERE client_id='" .  intval($_GET['del']) . "' LIMIT 1";
		mysqli_query($mysql,$query) or die(mysqli_error());
		
	die ("<meta http-equiv=refresh content='0; url=?page=client'>");
	}	
	
	if (isset($_GET['del_opl']))
	{
		$query = "DELETE FROM MED_oplata  WHERE MED_oplata.oplata_id='" .  intval($_GET['del_opl']) . "' LIMIT 1";
		mysqli_query($mysql,$query) or die(mysqli_error());
		
		$query = "DELETE FROM MED_debet  WHERE MED_debet.oplata_id='" .  intval($_GET['del_opl']) . "' LIMIT 1";
		mysqli_query($mysql,$query) or die(mysqli_error());
		
	die ("<meta http-equiv=refresh content='0; url=?page=client&add=true&edit=".$_GET['edit']."'>");
	}	
?>

  	<div class="col-md-12">
		<h3>Клиенты</h3>
		<a class="btn btn-primary" href="?page=client&add=true"><span class="glyphicon glyphicon-plus"></span> Добавить клиента</a>
		<div class="clearfix mtop"><br></div>
	<?php
	if (isset($_GET['mess']) and $_GET['mess'] == 3)
	{
	?>
	<div class="alert alert-dismissible alert-success text-center">
		<strong>Оплата добавлена успешно</strong><br />
		<a class="btn btn-primary" href="?page=client&add=true&edit=<?php echo $_GET['edit']; ?>" class="alert-link">Хорошо</a>
	</div>	
	<?php
	}
	if (isset($_GET['mess']) and $_GET['mess'] == 2)
	{
	?>
	<div class="alert alert-dismissible alert-success text-center">
		<strong>Клиент добавлен/Отредактирован!</strong><br />
		<a class="btn btn-primary" href="?page=client&add=true&edit=<?php echo $_GET['edit']; ?>" class="alert-link">Хорошо</a>
	</div>	
	<?php
	}
	if (isset($_GET['mess']) and $_GET['mess'] == 1)
	{
	?>
	<div class="alert alert-dismissible alert-success text-center">
		<strong>Ваше сообщение отправленно!</strong><br />
		<a class="btn btn-primary" href="?page=client&add=true&edit=<?php echo $_GET['edit']; ?>" class="alert-link">Хорошо</a>
	</div>	
	<?php
	}
	elseif (isset($_GET['delete']))
	{
	?>
	<div class="alert alert-dismissible alert-danger col-md-8 col-md-offset-2">
		<h3 class="text-center text-danger">Подтверждение удаления клиента #<?php echo $_GET['delete']; ?></h3>
		<p class="text-center">
			<a class="btn btn-danger btn-sm" href="?page=client">Отмена</a>
			<a class="btn btn-success" href="?page=client&del=<?php echo $_GET['delete']; ?>"><span class="glyphicon glyphicon-trash"></span> Удалить</a>
		</p>
	</div>
	<div class="clearfix"></div>
	<?php
	}
	elseif (isset($_GET['delete_opl']))
	{
	?>
	<div class="alert alert-dismissible alert-danger col-md-8 col-md-offset-2">
		<h3 class="text-center text-danger">Подтверждение удаления оплаты #<?php echo $_GET['delete_opl']; ?></h3>
		<p class="text-center">
			<a class="btn btn-danger btn-sm" href="?page=client&add=true&edit=<?php echo $_GET['edit']; ?>">Отмена</a>
			<a class="btn btn-success" href="?page=client&add=true&edit=<?php echo $_GET['edit']; ?>&del_opl=<?php echo $_GET['delete_opl']; ?>"><span class="glyphicon glyphicon-trash"></span> Удалить</a>
		</p>
	</div>
	<div class="clearfix"></div>
	<?php
	}
	elseif (isset($_GET['history']))
	{
	?>
	<a class="btn btn-success btn-sm" href="?page=client&add=true&edit=<?php echo $_GET['edit'];?>"><span class="glyphicon glyphicon-arrow-left"></span> Назад в карточку клиента</a>
	<table class="table table-hover mtop">
		<tr>
			<th> Заявка №</th>
			<th>Дата оформления заявки</th>
			<th>Состояние заявки</th>
		
		</tr>
		<?php 
		$SQL="SELECT * FROM MED_zayavka WHERE client_id='" .  intval($_GET['edit']) . "' ORDER BY MED_zayavka.date_zayavka+0 DESC";
		$r = mysqli_query($mysql, $SQL) or  die(mysqli_error().' - FIRM_menu_cat');
		while ($arr7 = mysqli_fetch_assoc($r))
		{
		?> 
		<tr >
			<td> <a href="?page=zayavka&add=true&edit=<?php echo $arr7['zayavka_id']; ?>&city=<?php echo $arr7['city_id'];?>&client_id=<?php echo $arr7['client_id'];?>"> <?php echo $arr7['zayavka_id']; ?></a></td>
			<td><?php echo date('d.m.Y',$arr7['date_zayavka']);?> </td>
			<td> <?php echo $stat_zayavki[$arr7['sost_zayavki']]; ?> </td>
		</tr>

		<?php
		}
		mysqli_free_result($r);
		?>
		</table>
		
	<?php
	}	
	elseif (isset($_GET['add']))
	{
		if (isset($_GET['edit']))
		{
				$SQL = "
					SELECT 
							MED_zayavka.city_id,
							MED_client.*
					FROM 
						MED_client 
					LEFT JOIN  
						MED_zayavka
					ON 
						MED_zayavka.client_id=MED_client.client_id
					WHERE MED_client.client_id='". intval($_GET['edit']) ."' LIMIT 1";
						
		//$SQL = "SELECT * FROM MED_client WHERE client_id='". intval($_GET['edit']) ."' LIMIT 1";
		$r = mysqli_query($mysql, $SQL) or  die(mysqli_error().' - FIRM_menu_cat');
		$arr = mysqli_fetch_assoc($r);
		mysqli_free_result($r);
		
		$SQL="SELECT * FROM MED_zayavka WHERE client_id='" .  intval($_GET['edit']) . "' ORDER BY MED_zayavka.date_zayavka+0 DESC";
		$r = mysqli_query($mysql, $SQL) or  die(mysqli_error().' - FIRM_menu_cat');
		$all_zayav = mysqli_num_rows($r);
		mysqli_free_result($r);		
		?>
	
		<a class="btn btn-success btn-sm" href="?page=client&add=true&edit=<?php echo $arr['client_id'];?>&history">Заявки клиента (<?php echo $all_zayav; ?>)</a>
		<p class="pull-right"><button id="sms_send" class="btn btn-primary">Отправить смс</button></p>
		

	</table>
	<?php
	}
	?>
	
	<div id="smsform" class="col-md-12 well hidden">
		<p class="pull-right"><button id="sms_close" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></button></p>
		<form class="form-horizontal" enctype='multipart/form-data' action='' method='post'>
		<?php echo isset($arr['client_id'])?'<input type="hidden" name="client_id" value="'.$arr['client_id'].'"/>':''; ?>
		<fieldset>
			<legend>Поля выделенные <b class="text-danger">красным*</b>, обязательны к заполнению.</legend>
		    
			<div class="form-group has-error">
				<label for="tel" class="col-lg-4 control-label">Телефон (без +7 и 8)*</label>
				<div class="col-lg-8">
					<input class="form-control" name="tel" type="text" value="<?php echo isset($arr['tel_zakaz']) ? tel_replace($arr['tel_zakaz']) : ''; ?>" placeholder="Номер телефона" required />
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
					
					<button id="sms_close" class="btn btn-danger">Отмена</button>
				</div>
			</div>		

		</fieldset>
		</form>	
	</div>
	
	<div class="clearfix mtop"></div>
	
	<div class="col-md-12 alert alert-warning mtop">
		<h3>Информация о клиенте <small><?php echo (isset($arr['city_id']) and isset($city[$arr['city_id']])) ? $city[$arr['city_id']] : ''; ?></small></h3>
		<form class="form-horizontal" method="POST" role="form">	
			<?php echo isset($arr['client_id'])?'<input type="hidden" name="client_id" value="'.$arr['client_id'].'"/><input type="hidden" name="city_id" value="'.$arr['city_id'].'"/>':''; ?>
		
			<div class="form-group">
				<label for="status_client" class="col-lg-3 control-label">  Статус клиента: </label>
				<div class="col-lg-8">
					<select class="form-control" name="status_client" required>
						<?php
						foreach ($stat_clienta as $k => $v)
						{
						?>
						<option <?php echo (isset($arr['status_client']) and $arr['status_client'] == $k) ? 'selected' : ''; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
						<?php
						}
						?>
					</select>
				</div>
			</div>			
			
			<div class="form-group has-error">
				<label for="fio_zakaz" class="col-lg-3 control-label">ФИО заказчика</label>
				<div class="col-lg-8">
					<input class="form-control" name="fio_zakaz" type="text" value="<?php echo isset($arr['fio_zakaz']) ? $arr['fio_zakaz'] : ''; ?>"   placeholder="Введите ФИО заказчика" required />
				</div>
			</div>
		
		
			<div class="form-group">
				<label for="happy_day" class="col-lg-3 control-label">Дата рождения</label>
				<div class="col-lg-8">
					<input class="form-control" name="happy_day" type="text"  value="<?php echo (isset($arr['happy_day']) AND !empty($arr['happy_day']) )?date('d.m.Y',$arr['happy_day']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				</div>
			</div>
			
			<div class="form-group">
				<label for="num_dog" class="col-lg-3 control-label">Номер договора</label>
				<div class="col-lg-8">
					<input class="form-control" name="num_dog" type="text" value="<?php echo isset($arr['num_dog']) ? $arr['num_dog'] : ''; ?>"   placeholder="Введите № договора"  />
				</div>
			</div>
			
			<div class="form-group">
				<label for="tel_zakaz" class="col-lg-3 control-label">Телефон</label>
				<div class="col-lg-8">
					<input class="form-control" name="tel_zakaz" type="text" value="<?php echo isset($arr['tel_zakaz']) ? $arr['tel_zakaz'] : ''; ?>"   placeholder="Введите № телефона"  />
				</div>
			</div>
				
			<div class="form-group">
				<label for="pasport" class="col-lg-3 control-label"> Паспортные данные </label>
				<div class="col-lg-8">
					<input class="form-control" name="pasport" type="text"  value="<?php echo isset($arr['pasport']) ? $arr['pasport'] : ''; ?>" placeholder="серия и номер "  />
				</div>
			</div>
				
			<div class="form-group">
				<label for="pasport_kem" class="col-lg-3 control-label"> Паспорт выдан </label>
				<div class="col-lg-8">
					<input class="form-control" name="pasport_kem" type="text"  value="<?php echo isset($arr['pasport_kem']) ? $arr['pasport_kem'] : ''; ?>"  placeholder="дата выдачи и кем выдан" />
				</div>
			</div>
				
			<div class="form-group">
				<label for="srok_dog" class="col-lg-3 control-label"> Срок действия договора </label>
				<div class="col-lg-3">
					<input class="form-control" name="srok_dog1" type="text"  value="<?php echo isset($arr['srok_dog1'])?date('d.m.Y',$arr['srok_dog1']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				</div>
				<div class="col-lg-3">
					<input class="form-control" name="srok_dog2" type="text"  value="<?php echo isset($arr['srok_dog2'])?date('d.m.Y',$arr['srok_dog2']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				</div>
			</div>	
				
			<div class="form-group">
				<label for="zp_sid" class="col-lg-3 control-label"> Заработная плата для сиделки </label>
				<div class="col-lg-8">
					<input class="form-control" name="zp_sid" type="text" value="<?php echo isset($arr['zp_sid']) ? $arr['zp_sid'] : ''; ?>" placeholder="Введите сумму " />
				</div>
			</div>
				
			<div class="form-group">
				<label for="srok_opl" class="col-lg-3 control-label"> Следующая оплата клиента </label>
				<div class="col-lg-8">
					<input class="form-control" name="data_opl" type="text"  value="<?php echo (isset($arr['data_opl']) AND !empty($arr['data_opl']) )?date('d.m.Y',$arr['data_opl']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				</div>
				
			</div>
			
			<div class="form-group">
				<label for="srok_opl" class="col-lg-3 control-label"> Сумма оплаты от клиента </label>	
				<div class="col-lg-8">
					<input class="form-control" name="srok_opl" type="text" value="<?php echo isset($arr['srok_opl']) ? $arr['srok_opl'] : ''; ?>"  placeholder="6000 предоплата "  />
				</div>
			</div>
			
			<div class="form-group">
				<label for="data_opl_sid" class="col-lg-3 control-label"> Следующая оплата сиделке </label>
				<div class="col-lg-8">
					<input class="form-control" name="data_opl_sid" type="text"  value="<?php echo (isset($arr['data_opl_sid']) AND !empty($arr['data_opl_sid']) )?date('d.m.Y',$arr['data_opl_sid']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				</div>
				
			</div>
			
			<div class="form-group">
				<label for="sum_sid" class="col-lg-3 control-label"> Сумма оплаты сиделке </label>	
				<div class="col-lg-8">
					<input class="form-control" name="sum_sid" type="text" value="<?php echo isset($arr['sum_sid']) ? $arr['sum_sid'] : ''; ?>"  placeholder="1500 рублей "  />
				</div>
			</div>
			
			
			<div class="form-group">
				<label for="agent" class="col-lg-3 control-label"> Агентское вознаграждение </label>
				<div class="col-lg-3">
					<input class="form-control" name="agent" type="text" value="<?php echo isset($arr['agent']) ? $arr['agent'] : ''; ?>"  placeholder="0 " />
				</div>
				
				<label for="kyr" class="col-lg-3 control-label"> Курьерские </label>
				<div class="col-lg-2">
					<input class="form-control" name="kyr" type="text"  value="<?php echo isset($arr['kyr']) ? $arr['kyr'] : ''; ?>"  placeholder="0 " />
				</div>
			</div>	
			
			
			<div class="form-group">
				<label for="kom_klient" class="col-lg-3 control-label"> Комментарий к клиенту</label>
				<div class="col-lg-8">
					<textarea  class="form-control" name="kom_klient" rows="4"> <?php echo isset($arr['kom_klient'])?html_entity_decode(html_entity_decode($arr['kom_klient'])):'';?> </textarea>
				</div>
			</div>
			
			<div class="form-group">
				<div class="col-lg-offset-2 col-lg-10">
					<button type="submit" name="SBM_add" class="btn btn-primary">Сохранить</button>
				</div>
			</div>
			
		</form>
		<div class="clearfix"></div>
	</div>
	

	
	
	<?php
	if (isset($arr['zayavka_id']))
	{
		$SQL="SELECT * FROM MED_zayavka WHERE client_id='" .  $arr['client_id'] . "' AND sost_zayavki='1' LIMIT 1";
		$r1 = mysqli_query($mysql, $SQL) or  die(mysqli_error().' - MED_zayavka');
		$asd = mysqli_fetch_assoc($r1);
		mysqli_free_result($r1);			
	?>
	<div class="clearfix mtop"></div>
	
	<div class="col-md-12 alert alert-default mtop">
		<form class="form-horizontal" method="POST" role="form">
			<?php echo isset($arr['client_id'])?'<input type="hidden" name="client_id" value="'.$arr['client_id'].'"/><input type="hidden" name="city_id" value="'.$arr['city_id'].'"/>':''; ?>
			<?php echo isset($asd['zayavka_id'])?'<input type="hidden" name="zayavka_id" value="'.$asd['zayavka_id'].'"/>':''; ?>
			<div class="col-md-12">
				<h4 align="center"> Форма для добавления платежа в систему </h4>
				<table class="table table-hover">
					<tr>
						<th>Дата</th>
						<th>Вид</th>
						<th>Сумма</th>
						<th>Статус</th>
					</tr>
					
					<tr>
						<td><input class="form-control" name="date_plat" type="text"  value="<?php echo date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /> </td>
						<td> 
							<select class="form-control" name="vid_plat" required>
								<option disabled>Выбрать </option>
								<option value="0">Платёж</option>
								<option  value="1">Выплата</option>
							</select>	
						</td>
							<td> <input class="form-control" name="sum_plat" type="text"  value=" "  placeholder="сумма " /> </td>
							<td> <input class="form-control" name="status_plat" type="text"  value=" "  placeholder="" /> </td>
						</tr>
				</table>
				
				<div class="form-group">
					<div class="col-md-6 col-md-offset-3">
						<button type="submit" name="add_plata" class="btn btn-primary">Добавить платеж</button>
					</div>
				</div>
		
			</div>
			<div class="col-md-12">
				<div class="col-md-6">
					<h4> История платежей клиентов </h4>
					<table class="table table-hover">
						<tr>
							<th>Дата</th>
							<th>Сумма</th>
							<th>Статус</th>
							<th>Удалить</th>
						</tr>
						<?php 
						
						$SQL ="
						SELECT
							MED_debet.*
						FROM
							MED_debet	
						WHERE 
							MED_debet.type_pay='0' AND MED_debet.client_id='". intval($_GET['edit']) ."' 
						ORDER BY
							MED_debet.time+0 DESC";
						$itigo_pay = 0;
// 						$SQL = "SELECT * FROM MED_debet WHERE client_id='". intval($_GET['edit']) ."' AND type_pay='0'";
						$r = mysqli_query($mysql, $SQL) or  die(mysqli_error().' - FIRM_menu_cat');
						while ($abb = mysqli_fetch_assoc($r))
						{
						//http://crm.mysvami24.com/rupka.php?page=money&d1=01.06.2018&d2=30.06.2018#date
						$time =$abb['time'];	
						$d1 = isset($_GET['d1'])?strtotime($_GET['d1']):mktime (0, 0, 0, date('m',$time), 1, date('Y',$time));
						$d2 = isset($_GET['d2'])?strtotime($_GET['d2']):mktime (0, 0, 0, date('m',$time), date("t", mktime (0, 0, 0, date('m',$time), 1, date('Y',$time))), date('Y',$time));
						$itigo_pay = $itigo_pay + $abb['summ']; 
						?>
						<tr>
							<td><a href="?page=money&edit=<?php echo $abb['id'];?>&type_pay=<?php echo $abb['type_pay'];?>&stspay=<?php echo $abb['stspay'];?>"> <?php echo date('d.m.Y',$abb['time']); ?>   </a></td>
							<td><a href="?page=money&znach=<?php echo $abb['id'];?>&d1=<?php echo date('d.m.Y',$d1);?>&d2=<?php echo date('d.m.Y',$d2);?>#<?php echo $abb['id'];?>"><?php echo $abb['summ']; ?></a></td>
							<td><?php echo $abb['about'] ; ?></td>
							<td> <a class="btn btn-xs btn-danger" href="?page=client&add=true&edit=<?php echo $_GET['edit']; ?>&delete_opl=<?php echo $abb['oplata_id']; ?>"><span class="glyphicon glyphicon-trash"></span></a></td>
						</tr>
						<?php 
						unset($time,$d1,$d2);
						}
						?>
						<tr>
							<th>Итого:</th>
							<th colspan="3"><?php echo $itigo_pay; ?></th>
						</tr>
					</table>	
				</div>
						
				<div class="col-md-6">
					<h4> История выплат сиделкам </h4>
					<table class="table table-hover">
						<tr>
							<th>Дата</th>
							<th>Сумма</th>
							<th>Статус</th>
							<th>Удалить</th>
						</tr>
						<?php 
						$itigo_incom = 0;
						$SQL = "SELECT * FROM MED_oplata WHERE client_id='". intval($_GET['edit']) ."' AND  vid_plat='1' ORDER BY MED_oplata.date_plat+0 DESC";
						$r = mysqli_query($mysql, $SQL) or  die(mysqli_error().' - FIRM_menu_cat');
						while ($abb = mysqli_fetch_assoc($r))
						{
						$itigo_incom = $itigo_incom + $abb['sum_plat'];
						?>
						<tr>
							<td><?php echo date('d.m.Y',$abb['date_plat']); ?></td>
							<td><?php echo $abb['sum_plat']; ?></td>
							<td><?php echo $abb['status_plat']; ?></td>
							<td> <a class="btn btn-xs btn-danger" href="?page=client&add=true&edit=<?php echo $_GET['edit']; ?>&delete_opl=<?php echo $abb['oplata_id']; ?>"><span class="glyphicon glyphicon-trash"></span></a></td>
						</tr>
						<?php 
						}
						?>
						<tr>
							<th>Итого:</th>
							<th colspan="3"><?php echo $itigo_incom; ?></th>
						</tr>
					</table>	
				</div>
			</div>
			


		</form>
	</div>
	<?php
	}
	?>	
	<div class="clearfix mtop"></div>
	
	<?php
	if (isset($arr['zayavka_id']))
	{
	?>
	<div class="col-md-12 well well-sm mtop">
	<?php
    	$result = mysqli_query($mysql, "SELECT * FROM MED_sidelka WHERE MED_sidelka.zayavka_id='".intval($arr['zayavka_id'])."'") or  die(mysqli_error($mysql).' - CRM_SNAB_tovar_EDIT');
    	if(!$result) exit(mysqli_error($mysql));
    	while($sid=mysqli_fetch_assoc($result))
    	{
		?>
		<div class="col-md-12 alert alert-<?php echo $sid['status'] == 1 ? 'success' : 'warning'; ?> mtop">
			<p class="pull-right"><a href="?page=sidelka&add=true&edit=<?php echo $sid['sidelka_id']; ?>" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span></a></p>
			<div class="form-group">
				<label for="dop_sid" class="col-lg-3 control-label">Сатус</label>
				<div class="col-lg-8">
					<p><?php echo $sid['status'] == 1 ? 'Сейчас работает' : 'работала ранее'; ?></p>
				</div>
			</div>

			<div class="form-group has-error">
				<label for="fio_sid" class="col-lg-3 control-label"> ФИО сиделки</label>
				<div class="col-lg-8">
					<p><?php echo $sid['fio_sid']; ?></p>
				</div>
			</div>	

			<div class="form-group">
				<label for="tel_sid" class="col-lg-3 control-label"> Телефон </label>
				<div class="col-lg-8">
					<p><?php echo $sid['tel_sid']; ?></p>
				</div>
			</div>
							
			<div class="form-group">
				<label for="dop_sid" class="col-lg-3 control-label"> Дополнительное поле </label>
				<div class="col-lg-8">
					<p><?php echo $sid['dop_sid']; ?></p>
				</div>
			</div>
			
			<div class="clearfix"></div>
							
			<div class="form-group">
				<label for="dop_sid" class="col-lg-3 control-label"> Работала с по </label>
				<div class="col-lg-8">
					<div class="col-lg-6">
						<?php echo date('d.m.Y',$sid['d1']); ?>
					</div>
					<div class="col-lg-6">
						<?php echo date('d.m.Y',$sid['d2']); ?>
					</div>
				</div>
			</div>
							
			</div> 
		<div class="clearfix mtop"></div>
		<?php
    	}
	mysqli_free_result($result);
	?>
	</div>
	<?php
	}
	?>
		

	<?php
	}
	else
		{
		$SQLs = "SELECT * FROM MED_client";
		$result = mysqli_query($mysql, $SQLs) or  die(mysqli_error().' - MED_client');
		$total_all = mysqli_num_rows($result);
		mysqli_free_result($result);
		
			if(!isset($_GET['p']))
			{
			$p = 1;
			}
			else
				{
				$p =  intval($_GET['p']);
				if($p < 1) {$p = 1;}
				}
			$num_elements = 50;

		$num_pages = ceil(($total_all>0?$total_all:50) / $num_elements);
		if ($p > $num_pages) $p = $num_pages;
		$start = ($p - 1) * $num_elements;
		$start = (int)str_replace('-','',$start);
		$start = $start < 0 ? 0 : $start;
		$num_elements < 0 ? 0 : $num_elements;
		?>
		<a class="label label-<?php echo (isset($_GET['sort']) AND $_GET['sort'] == 0)?'success':'default'; ?>" href="?page=client&sort=0<?php echo (isset($_GET['citysort'])?'&citysort='.$_GET['citysort']:''); ?>"><span class="glyphicon glyphicon-<?php echo (isset($_GET['sort']) AND $_GET['sort'] == 0)?'check':'unchecked'; ?>"></span> Не в заявке</a>
		<?php
		foreach ($city as $k => $v)
		{
		?>
		<a class="label label-<?php echo (isset($_GET['citysort']) AND $_GET['citysort'] == $k)?'success':'default'; ?>" href="?page=client&citysort=<?php echo $k; ?><?php echo (isset($_GET['sort'])?'&sort='.$_GET['sort']:''); ?>"><span class="glyphicon glyphicon-<?php echo (isset($_GET['citysort']) AND $_GET['citysort'] == $k)?'check':'unchecked'; ?>"></span> <?php echo $v; ?></a>
		<?php
		}
		foreach ($stat_clienta as $k => $v)
		{
		?>
		<a class="label label-<?php echo (isset($_GET['sort']) AND $_GET['sort'] == $k)?'success':'default'; ?>" href="?page=client&sort=<?php echo $k; ?><?php echo (isset($_GET['citysort'])?'&citysort='.$_GET['citysort']:''); ?>"><span class="glyphicon glyphicon-<?php echo (isset($_GET['sort']) AND $_GET['sort'] == $k)?'check':'unchecked'; ?>"></span> <?php echo $v; ?></a>
		<?php
		}
		if (isset($_GET['sort']) OR isset($_GET['q']) OR isset($_GET['citysort']))
		{
		?>
		<a class="label label-danger" href="?page=client"><span class="glyphicon glyphicon-remove-sign"></span> Очистить</a>
		<?php
		}
		?>
		<div class="clearfix mtop"><br></div>
		
		<form class="form-horizontal" action='' method='get'>
			<input type="hidden" name="page" value="client">
			<fieldset>
				<legend>Поиск</legend>

				<div class="form-group">

					<div class="col-md-4">
						<select class="form-control select" name="w">
							<option <?php echo (isset($_GET['w']) and $_GET['w'] == 0) ? 'selected' : ''; ?> value="0">Имя</option>
							<option <?php echo (isset($_GET['w']) and $_GET['w'] == 1) ? 'selected' : ''; ?> value="1">Телефон</option>
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
		
		<div class="clearfix mtop"></div>
		
			<?php
			$WHERE = '';
			if (isset($_GET['q']))
			{
			$arr = array('MED_client.fio_zakaz','MED_client.tel_zakaz');
			$w = isset($_GET['w'])?$_GET['w']:'0';
			$q = intval($w) == 1 ? preg_replace ("/[^0-9\s]/","",trim($_GET['q'])) : urldecode(trim($_GET['q']));
			$w = intval($w) == 1 ? "REPLACE( REPLACE( REPLACE( ".$arr[intval($w)].", '-', '' ), '(', '' ), ')', '' )":$arr[intval($w)];
			$WHERE = " AND ".$w." LIKE '%".$q."%'";
			}
				
			$citysort = isset($_GET['citysort']) ? " AND MED_zayavka.city_id='".intval($_GET['citysort'])."'" : "";
			$sort = isset($_GET['sort']) ? intval($_GET['sort']) : implode(',',array_flip($stat_clienta));
			
				$SQL = "
					SELECT 
							MED_zayavka.zayavka_id,
							MED_zayavka.city_id,
							MED_zayavka.client_id,
							MED_zayavka.sost_zayavki,
							MED_client.zp_sid,
							MED_client.fio_zakaz,
							MED_client.srok_dog1,
							MED_client.srok_dog2,
							MED_client.tel_zakaz,
							MED_client.status_client,
							MED_client.data_opl,
							MED_client.data_opl_sid
					FROM 
						MED_client 
					LEFT JOIN  
						MED_zayavka
					ON 
						MED_zayavka.client_id=MED_client.client_id
					WHERE
						MED_client.status_client IN(".$sort.") ".$WHERE."
						".$citysort." 
					ORDER
						BY MED_zayavka.zayavka_id DESC LIMIT ".$start.", ".$num_elements;
			
			$r = mysqli_query($mysql,$SQL);
				if(!$r) exit(mysqli_error());
				while	($hk=mysqli_fetch_assoc($r))
				{
				$status_CHECK = true;
				$SQL_s = "SELECT * FROM MED_sidelka WHERE zayavka_id='". $hk['zayavka_id'] ."' AND status='1' LIMIT 1";
				$rs = mysqli_query($mysql, $SQL_s) or  die(mysqli_error().' - MED_sidelka');
				$sid = mysqli_fetch_assoc($rs);
				mysqli_free_result($rs);
				?>
				<div class="col-md-12 list-group-item">
					<div class="col-md-5">
						<h4><a href="?page=client&add=true&edit=<?php echo $hk['client_id']; ?>"><?php echo $hk['fio_zakaz']; ?></a></h4>
							<p class="pull-left">
								Тел: <?php echo $hk['tel_zakaz']; ?><br>
								Город: <?php echo (isset($hk['city_id']) and isset($city[$hk['city_id']])) ? $city[$hk['city_id']] : 'Не указан'; ?>
							</p>
					</div>	
					
					<div class="col-md-5">
							<p class="pull-left">
								Статус клиента: <?php echo isset($stat_clienta[$hk['status_client']])?$stat_clienta[$hk['status_client']]:'Не в заявке'; ?><br>
								Следующая оплата клиента: <?php echo !empty($hk['data_opl']) ? date('d.m.Y',$hk['data_opl']) : 'неизвестно'; ?><br>
								Следующая оплата сиделке: <?php echo !empty($hk['data_opl_sid']) ? date('d.m.Y',$hk['data_opl_sid']) : 'неизвестно'; ?><br>

							</p>
					</div>	

					<div class="col-md-2">
						<p class="pull-right">
							<a class="btn btn-sm btn-success" href="?page=client&add=true&edit=<?php echo $hk['client_id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
							<a class="btn btn-sm btn-danger" href="?page=client&delete=<?php echo $hk['client_id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
						</p>
					</div>	
					<div class="clearfix"></div>
				</div>
				<?php
				unset($sid);
				}
			mysqli_free_result($r);
			?>
		<?php
			if ($total_all >= $num_elements and isset($status_CHECK))
			{
			echo GetNav($p, $num_pages);
			}
		}
		?>
	</div>
