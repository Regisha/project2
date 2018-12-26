<?php
	if  (!defined ('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."inter.php?login'>");
	}
	

		// Сначала перечисляются все ID-шники потом Варчары
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_zayavka(
	zayavka_id int auto_increment primary key 	COMMENT 'id',
	admin_id int(3) NOT NULL 					COMMENT 'id Кто добавил',
	city_id  int(3) NOT NULL 					COMMENT 'id города 1 или 2',
	client_id  int(10) NOT NULL					COMMENT 'id клиента',	
	podopech_id  int(10) NOT NULL				COMMENT 'id подопечного',
	podopech_id1  int(10) NOT NULL				COMMENT 'id подопечного второго',
	date_zayavka varchar(12) NOT NULL 			COMMENT 'Дата заявки',
	sost_zayavki int(3) NOT NULL 				COMMENT 'Состояние/Тип',
	kom_zayavka  varchar(200) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Информация о заявке'");		
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_podopech (
	podopech_id int auto_increment primary key,
	admin_id int(1) NOT NULL,
	zayavka_id int(10) NOT NULL,	
	fio_podopech varchar(50) NOT NULL,
	gender int(1) NOT NULL,
	ves_podopech varchar(50) NOT NULL,
	sost_podopech varchar(150) NOT NULL,
	adres_podopech varchar(70) NOT NULL,
	grafik varchar(50) NOT NULL,
	yslov varchar(200) NOT NULL,
	status int(1) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Информация о подопечном'");
	
// 	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_svayz_podopech (
// 	podopech_id  int(10) NOT NULL				COMMENT 'id подопечного',
// 	zayavka_id int(10) NOT NULL
// 	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Информация о сиделке'");
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_sidelka (
	sidelka_id int auto_increment primary key,
	admin_id int(1) NOT NULL,
	zayavka_id int(10) NOT NULL,
	fio_sid varchar(50) NOT NULL,
	tel_sid varchar(50) NOT NULL,
	dop_sid varchar(150) NOT NULL,
	d1 varchar(12) NOT NULL 			COMMENT 'Дата начала работы',
	d2 varchar(12) NOT NULL 			COMMENT 'Дата окончания работы',
	status int(1) NOT NULL				COMMENT '0 - не работает, 1 сейчас работает'
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Информация о сиделке'");
	
	if (isset($_POST['SBM_new_client']))
	{
	$admin_id = mysqli_real_escape_string($mysql, $_SESSION['__ID__']);
	$city = trim($_POST['city']);
	$fio = isset($_POST['client_id_new'])?mysqli_real_escape_string($mysql,trim($_POST['client_id_new'])):'';
	$tel = isset($_POST['client_id_tel'])?mysqli_real_escape_string($mysql,trim($_POST['client_id_tel'])):'';
	
		$insertSQL = mysqli_query($mysql, "INSERT INTO MED_client (
		admin_id,
		status_client,
		fio_zakaz,
		happy_day,
		num_dog,
		tel_zakaz,
		pasport,
		pasport_kem,
		srok_dog1, 
		srok_dog2,
		zp_sid,
		srok_opl,
		data_opl,
		data_opl_sid, 
		sum_sid,
		agent,
		kyr,
		kom_klient) 
			VALUES
				(
				'".$admin_id."',
				'0',
				'".$fio."',
				'',
				'',
				'".$tel."', 
				'',
				'',
				'',
				'', 
				'',
				'',
				'".time()."',
				'',
				'',
				'',
				'',
				'.$kom_klient .')");
		if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
	$client_id = mysqli_insert_id($mysql);
	
	die ("<meta http-equiv=refresh content='0; url=?page=zayavka&city=".$city."&client_id=".$client_id."&add=true'>");
	}
	
	if (isset($_GET['sts_move']))
	{
	$e = explode('|',$_GET['sts_move']);
	$query_count = "UPDATE MED_zayavka SET sost_zayavki='" . intval($e[1]) . "' WHERE zayavka_id='".intval($e[0])."' LIMIT 1";
	mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
	request('http://'.$_SERVER['HTTP_HOST'].'/ajax.php?type=backup&backsts=2&user_id='.$_SESSION['__ID__']);
	die ("<meta http-equiv=refresh content='0; url=?page=zayavka'>");
	}
	
	if (isset($_POST['Sohr']))
	{
// 	echo '<pre>';
// 	print_r($_POST);
// 	echo '</pre>';
		//MED_client
		$status_client = intval($_POST['status_client']);
		$fio_zakaz = mysqli_real_escape_string($mysql, trim($_POST['fio_zakaz']));
		$happy_day = isset($_POST['happy_day'])?strtotime($_POST['happy_day']):time();
		$num_dog = mysqli_real_escape_string($mysql, trim($_POST['num_dog']));
		$tel_zakaz = mysqli_real_escape_string($mysql, trim($_POST['tel_zakaz']));
		$pasport= mysqli_real_escape_string($mysql, trim($_POST['pasport']));
		$pasport_kem= mysqli_real_escape_string($mysql, trim($_POST['pasport_kem']));
		$srok_dog1 = isset($_POST['srok_dog1'])?strtotime($_POST['srok_dog1']):time();
		$srok_dog2 = isset($_POST['srok_dog2'])?strtotime($_POST['srok_dog2']):time();
		$zp_sid = mysqli_real_escape_string($mysql, trim($_POST['zp_sid']));
		$data_opl = isset($_POST['data_opl'])?strtotime($_POST['data_opl']):time();
		$srok_opl = mysqli_real_escape_string($mysql, trim($_POST['srok_opl']));
		$data_opl_sid = isset($_POST['data_opl_sid'])?strtotime($_POST['data_opl_sid']):time();
		$sum_sid = mysqli_real_escape_string($mysql, trim($_POST['sum_sid']));
		$agent = mysqli_real_escape_string($mysql, trim($_POST['agent']));
		$kyr = mysqli_real_escape_string($mysql, trim($_POST['kyr']));
		$kom_klient= mysqli_real_escape_string($mysql, trim($_POST['kom_klient']));
		
		//MED_podopech
		$admin_id = mysqli_real_escape_string($mysql, $_SESSION['__ID__']);
		$fio_podopech= mysqli_real_escape_string($mysql, trim($_POST['fio_podopech']));
		$gender= intval($_POST['gender']);
		$ves_podopech = mysqli_real_escape_string($mysql, trim($_POST['ves_podopech']));
		$sost_podopech= mysqli_real_escape_string($mysql, trim($_POST['sost_podopech']));
		$adres_podopech= mysqli_real_escape_string($mysql, trim($_POST['adres_podopech']));
		$grafik = mysqli_real_escape_string($mysql, trim($_POST['grafik']));
		$yslov= mysqli_real_escape_string($mysql, trim($_POST['yslov']));
		
		//MED_podopech №2
		$fio_podopech1= isset($_POST['fio_podopech1'])?mysqli_real_escape_string($mysql, trim($_POST['fio_podopech1'])):'';
		$gender1= isset($_POST['gender1'])?intval($_POST['gender1']):'';
		$ves_podopech1 = isset($_POST['ves_podopech1'])?mysqli_real_escape_string($mysql, trim($_POST['ves_podopech1'])):'';
		$sost_podopech1= isset($_POST['sost_podopech1'])?mysqli_real_escape_string($mysql, trim($_POST['sost_podopech1'])):'';
		$adres_podopech1= isset($_POST['adres_podopech1'])?mysqli_real_escape_string($mysql, trim($_POST['adres_podopech1'])):'';
		$grafik1 = isset($_POST['grafik1'])?mysqli_real_escape_string($mysql, trim($_POST['grafik1'])):'';
		$yslov1= isset($_POST['yslov1'])?mysqli_real_escape_string($mysql, trim($_POST['yslov1'])):'';
		
// 		//MED_sidelka
// 		$admin_id = mysqli_real_escape_string($mysql, $_SESSION['__ID__']);
// 		$fio_sid= mysqli_real_escape_string($mysql, trim($_POST['fio_sid']));
// 		$tel_sid= mysqli_real_escape_string($mysql, trim($_POST['tel_sid']));
// 		$dop_sid= mysqli_real_escape_string($mysql, trim($_POST['dop_sid']));
		
		//MED_zayavka
		$city_id= mysqli_real_escape_string($mysql, trim($_POST['city']));	
		$date_zayavka = isset($_POST['date_zayavka'])?trim($_POST['date_zayavka']):date('d.m.Y');
		$date_hour = isset($_POST['date_hour'])?trim($_POST['date_hour']):date('H');
		$date_min = isset($_POST['date_min'])?trim($_POST['date_min']):date('i');
		
		$kom_zayavka= mysqli_real_escape_string($mysql, trim($_POST['kom_zayavka']));
		$date_zayavka = strtotime($date_zayavka.' '.$date_hour.':'.$date_min);
		
		$sost_zayavki= intval($_POST['sost_zayavki']);
		

		
		$client_id= intval($_POST['client_id']);
		
			if (isset($_POST['zayavka_id']))
			{
			$zayavka_id= intval($_POST['zayavka_id']);
			
			$podopech_id= intval($_POST['podopech_id']);	
			$podopech_id1= intval($_POST['podopech_id1']);	
			
			
			$query_count = "UPDATE MED_zayavka SET  date_zayavka='". $date_zayavka."', city_id='". $city_id."',sost_zayavki='". $sost_zayavki."',kom_zayavka='". $kom_zayavka."', client_id='". $client_id."', podopech_id='". $podopech_id."', podopech_id1='". $podopech_id1."' WHERE zayavka_id='".intval($_POST['zayavka_id'])."' LIMIT 1";
			mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
			
			$query_count = "UPDATE MED_podopech SET zayavka_id='". $zayavka_id."', fio_podopech='". $fio_podopech."', gender='". $gender."',ves_podopech='". $ves_podopech."', sost_podopech='". $sost_podopech."', adres_podopech='". $adres_podopech."', grafik='". $grafik."', yslov='". $yslov."'  WHERE podopech_id='".$podopech_id."' LIMIT 1";
			mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
			
			$query_count = "UPDATE MED_podopech SET zayavka_id='". $zayavka_id."', fio_podopech='". $fio_podopech1."', gender='". $gender1."',ves_podopech='". $ves_podopech1."', sost_podopech='". $sost_podopech1."', adres_podopech='". $adres_podopech1."', grafik='". $grafik1."', yslov='". $yslov1."'  WHERE podopech_id='".$podopech_id1."' LIMIT 1";
			mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
			
			$query_count = "UPDATE MED_client  SET zayavka_id='". $zayavka_id."',status_client='". $status_client."', fio_zakaz='". $fio_zakaz."', happy_day='".$happy_day."', num_dog='".$num_dog."',tel_zakaz='". $tel_zakaz."', pasport='". $pasport."', pasport_kem='". $pasport_kem."', srok_dog1='". $srok_dog1."', srok_dog2='". $srok_dog2."', zp_sid='". $zp_sid."', srok_opl='". $srok_opl."', data_opl='". $data_opl."',data_opl_sid='". $data_opl_sid."',sum_sid='". $sum_sid."', agent='". $agent."', kyr='". $kyr."', kom_klient='". $kom_klient."'  WHERE client_id='".$client_id."' LIMIT 1";
			mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
			
// 			$query_count = "UPDATE MED_sidelka  SET zayavka_id='". $zayavka_id."', fio_sid='". $fio_sid."',tel_sid='". $tel_sid."', dop_sid='". $dop_sid."'  WHERE sidelka_id='".intval($_POST['sidelka_id'])."' LIMIT 1";
// 			mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
			}
			else
				{
				$insertSQL = mysqli_query($mysql, "INSERT INTO MED_zayavka (admin_id,date_zayavka,city_id,sost_zayavki,kom_zayavka,client_id,podopech_id,podopech_id1) VALUES ('".$admin_id."','".$date_zayavka."','".$city_id."','".$sost_zayavki."','".$kom_zayavka."','".$client_id."','','')");
				if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
				$zayavka_id = mysqli_insert_id($mysql);
				
				$query_count = "UPDATE MED_client SET zayavka_id='". $zayavka_id."',status_client='". $status_client."', fio_zakaz='". $fio_zakaz."', happy_day='".$happy_day."', num_dog='".$num_dog."',tel_zakaz='". $tel_zakaz."', pasport='". $pasport."', pasport_kem='". $pasport_kem."', srok_dog1='". $srok_dog1."', srok_dog2='". $srok_dog2."', zp_sid='". $zp_sid."', srok_opl='". $srok_opl."', data_opl='". $data_opl."', data_opl_sid='". $data_opl_sid."',sum_sid='". $sum_sid."',agent='". $agent."', kyr='". $kyr."', kom_klient='". $kom_klient."' WHERE client_id='".$client_id."' LIMIT 1";
				mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
				
				$insertSQL = mysqli_query($mysql, "INSERT INTO MED_podopech (admin_id,zayavka_id,fio_podopech,gender,ves_podopech,sost_podopech,adres_podopech,grafik,yslov,status) VALUES ('".$admin_id."','".$zayavka_id."','".$fio_podopech."','".$gender."','".$ves_podopech."','".$sost_podopech."','".$adres_podopech."','".$grafik."','".$yslov."','1')");
				if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
				$podopech_id = mysqli_insert_id($mysql);
				
// 				$insertSQL = mysqli_query($mysql, "INSERT INTO MED_sidelka (admin_id,zayavka_id,fio_sid,tel_sid,dop_sid) VALUES ('".$admin_id."','".$zayavka_id."','".$fio_sid."','".$tel_sid."','".$dop_sid."')");
// 				if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
// 				$sidelka_id = mysqli_insert_id($mysql);
				
				$status = !empty($fio_podopech1) ? 1 : 0;
				
				$insertSQL = mysqli_query($mysql, "INSERT INTO MED_podopech (admin_id,zayavka_id,fio_podopech,gender,ves_podopech,sost_podopech,adres_podopech,grafik,yslov,status) VALUES ('".$admin_id."','".$zayavka_id."','".$fio_podopech1."','".$gender1."','".$ves_podopech1."','".$sost_podopech1."','".$adres_podopech1."','".$grafik1."','".$yslov1."','".$status."')");
				if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
				$podopech_id1 = mysqli_insert_id($mysql);
				
				$query_count = "UPDATE MED_zayavka SET podopech_id='". $podopech_id."', podopech_id1='". $podopech_id1."'  WHERE zayavka_id='".$zayavka_id."' LIMIT 1";
				mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
				}
		

			
		if (isset($_POST['fio_sid']))
		{
		$query_count = "UPDATE MED_sidelka SET status='0' WHERE zayavka_id='".$zayavka_id."' ";
		mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
		
			for ($i=0;$i<count($_POST['fio_sid']);$i++)
			{
				if (isset($_POST['sidelka_id'][$i]))
				{
				$query_count = "UPDATE MED_sidelka SET zayavka_id='". $zayavka_id."', fio_sid='". mysqli_real_escape_string($mysql, trim($_POST['fio_sid'][$i]))."',tel_sid='". mysqli_real_escape_string($mysql, trim($_POST['tel_sid'][$i]))."', dop_sid='". mysqli_real_escape_string($mysql, trim($_POST['dop_sid'][$i]))."',d2='".time()."',status='1'  WHERE sidelka_id='".intval($_POST['sidelka_id'][$i])."' LIMIT 1";
				mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
				}
				else
					{
					$insertSQL = mysqli_query($mysql, "INSERT INTO MED_sidelka (admin_id,zayavka_id,fio_sid,tel_sid,dop_sid,d1,d2,status) VALUES ('".$admin_id."','".$zayavka_id."','".mysqli_real_escape_string($mysql, trim($_POST['fio_sid'][$i]))."','".mysqli_real_escape_string($mysql, trim($_POST['tel_sid'][$i]))."','".mysqli_real_escape_string($mysql, trim($_POST['dop_sid'][$i]))."','".time()."','".time()."','1')");
					if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
					}
			}
		}
		

		
// 	echo '<pre>';
// 	print_r($check_send_sms);
// 	echo '</pre>';
// 	var_dump($check_send_sms);
	request('http://'.$_SERVER['HTTP_HOST'].'/ajax.php?type=backup&backsts=1&user_id='.$_SESSION['__ID__']);
	die ("<meta http-equiv=refresh content='0; url=?page=zayavka'>");
	}


	
	if (isset($_GET['delzayavkatrue']))
	{
		$queryT = "DELETE FROM MED_zayavka WHERE zayavka_id='".intval($_GET['delzayavkatrue'])."' LIMIT 1";
		mysqli_query($mysql,$queryT) or die(mysqli_error($mysql));
		request('http://'.$_SERVER['HTTP_HOST'].'/ajax.php?type=backup&backsts=3&user_id='.$_SESSION['__ID__']);
	die ("<meta http-equiv=refresh content='0; url=?page=zayavka'>");
	}

?>	
	<div class="col-md-12">
		<h3>Заявки</h3>
		<div class="clearfix"></div>
		<br />
		
			<a href="?page=zayavka" class="btn btn-default">Все заявки <span class="badge badge-inverse"></span></a>
			<?php
			if ($user[$_SESSION['__ID__']]['status'] == 0)
			{
			?>
			<a href="?page=zayavka&cityselect" class="btn btn-default">Добавить заявку</a>
			<?php
			}
			?>
			
		<div class="clearfix"></div>
		<br />

	<?php 
	if (isset($_GET['mess']))
	{
		if ($_GET['mess'] == 1)
		{
		?>
		<div class="alert alert-dismissible alert-success text-center">
			<strong>Вы не выбрали клиента!</strong><br />
			<a class="btn btn-primary" href="?page=zayavka&clientselect=&city=<?php echo $_GET['city']; ?>" class="alert-link">Хорошо</a>
		</div>	
		<?php
		}
		elseif ($_GET['mess'] == 'sms1')
		{
		$color = $_GET['status_sms'] == 1 ? 'success' : 'danger';
		$mess = $_GET['status_sms'] == 1 ? 'Ваше сообщение отправленно' : 'Сообщение не отправленно';
		?>
		<div class="alert alert-dismissible alert-<?php echo $color; ?> text-center">
			<strong><?php echo $mess; ?></strong><br />
			<a class="btn btn-primary" href="?page=zayavka&add=true&edit=<?php echo $_GET['edit']; ?>&city=<?php echo $_GET['city']; ?>&client_id=<?php echo $_GET['client_id']; ?>" class="alert-link">Хорошо</a>
		</div>	
		<?php
		}
		elseif ($_GET['mess'] == 'sms2')
		{
		$color = $_GET['status_sms'] == 1 ? 'success' : 'danger';
		$mess = $_GET['status_sms'] == 1 ? 'Ваше сообщение отправленно' : 'Сообщение не отправленно';
		?>
		<div class="alert alert-dismissible alert-<?php echo $color; ?> text-center">
			<strong><?php echo $mess; ?></strong><br />
			<a class="btn btn-primary" href="?page=zayavka&add=true&edit=<?php echo $_GET['edit']; ?>&city=<?php echo $_GET['city']; ?>&client_id=<?php echo $_GET['client_id']; ?>" class="alert-link">Хорошо</a>
		</div>	
		<?php
		}
	}
	elseif (isset($_GET['delzayavka']))
	{
	?>
	<div class="alert alert-dismissible alert-danger">
		<h3 class="text-center text-danger">Подтверждение удаления заявки</h3>
		<strong>Выбранная заявка №<?php echo $_GET['delzayavka']; ?> будет удалена из базы</strong><br /> 
		<p>Это безопасно.</p>
		
		<p class="text-center">
			<input type="button" value=" Удалить " onclick="location.href = '?page=zayavka&delzayavkatrue=<?php echo $_GET['delzayavka']; ?>&city=<?php echo $_GET['city']; ?>&client_id=<?php echo $_GET['client_id']; ?>';return false;" class="btn btn-danger btn-sm"/>
			<input type="button" value=" Отмена " onclick="location.href = '?page=zayavka&add&edit=<?php echo $_GET['delzayavka']; ?>&city=<?php echo $_GET['city']; ?>&client_id=<?php echo $_GET['client_id']; ?>&add=true';return false;" class="btn btn-primary btn-sm"/>
		</p>
	</div>
	
	<div class="clearfix"></div>
	<?php
	}
	elseif (isset($_GET['cityselect']))
	{
	?>
	<h3> Для оформления заявки необходимо выбрать город</h3>
	<?php
		foreach ($city as $key => $gorod)
		{
		?>
		<div class="col-md-6 well well-sm">
			<h5><a href="?page=zayavka&clientselect=&city=<?php echo $key; ?>" class="btn btn-sm btn-block btn-success"><?php echo $gorod; ?></a></h5>
		</div>
		
		<?php
		}
		?>
	<?php	
	}
	elseif ((isset($_GET['clientselect'])))
	{
	?>
	<form id="client_id" class="form-horizontal" method="GET" role="form">
		<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
		<input type="hidden" name="city" value="<?php echo $_GET['city']; ?>"/>
		<input type="hidden" name="add" value="true"/>
		<div class="col-md-12 alert alert-warning mtop">
		
			<div class="form-group has-error">
				<h3> Для оформления заявки необходимо выбрать клиента из списка или добавить нового</h3>
				<div class="col-lg-12">
					<select class="form-control search" name="client_id" required>
						<option selected disabled>Выбрать</option>
						<option value="0"><b >Добавить нового</b></option>
						<?php 
						$result = mysqli_query($mysql, "SELECT * FROM MED_client ORDER BY fio_zakaz ASC") or  die(mysqli_error($mysql).' - MED_client');
						if(!$result) exit(mysqli_error($mysql));
						while ($dt = mysqli_fetch_assoc($result))
						{
						?>
						<option <?php echo (isset($arr['client_id']) and $arr['client_id'] == $dt['client_id']) ? 'selected' : ''; ?> value="<?php echo $dt['client_id']; ?>"><?php echo $dt['fio_zakaz']; ?></option>
						<?php 
						}
						?>
					</select>
				</div>
			</div>	
			
			<div class="col-md-12">
				<button class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Выбрать</button>
				<a href="?page=zayavka" class="btn btn-danger"><span class="glyphicon glyphicon-off"></span> Отмена</a>
			</div>
			
		</div>
	</form>	
	
	<form id="client_id_new" class="form-horizontal hidden" method="POST" role="form">
		<div class="form-group has-error">
		<label for="menu" class="col-lg-2 control-label">Клиент</label>
			<div class="col-lg-10">
				
				<div class="col-md-12">
					<div class="col-md-5">
						<input class="form-control" name="client_id_new" type="text" value="" placeholder="ФИО"/>
					</div>
					<div class="col-md-5">
						<input class="form-control" name="client_id_tel" type="text" value="" placeholder="+7"/>
					</div>
					<div class="col-md-2"><a class="mybutton m5 btn btn-danger btn-sm" href="javascript:void(0)" id="close_client_id_new"><span class="glyphicon glyphicon-remove"></span></a></div>
				</div>
				
			</div>
		</div>
		
		<div class="col-md-12">
			<input type="hidden" name="city" value="<?php echo $_GET['city']; ?>"/>
			<button type="submit" name="SBM_new_client" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
			<a href="?page=zayavka" class="btn btn-danger"><span class="glyphicon glyphicon-off"></span> Отмена</a>
		</div>
	</form>
<?php	
	}
	elseif (isset($_GET['add']) )
	{
		if (!isset($_GET['client_id']))
		{
		die ("<meta http-equiv=refresh content='0; url=?page=zayavka&clientselect=&city=".$_GET['city']."&mess=1'>");
		}
		
		if (isset($_GET['edit']))
		{
		$result = mysqli_query($mysql, "SELECT * FROM MED_zayavka WHERE zayavka_id='". intval($_GET['edit']) ."' LIMIT 1") or  die(mysqli_error($mysql).' - MED_zayavka');
		if(!$result) exit(mysqli_error($mysql));
		$arr = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		
		$result = mysqli_query($mysql, "SELECT * FROM MED_podopech WHERE  podopech_id='".  intval($arr['podopech_id'])."' LIMIT 1") or  die(mysqli_error($mysql).' - MED_zayavka');
		if(!$result) exit(mysqli_error($mysql));
		$pod = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		
		$result = mysqli_query($mysql, "SELECT * FROM MED_podopech WHERE  podopech_id='".  intval($arr['podopech_id1'])."' LIMIT 1") or  die(mysqli_error($mysql).' - MED_zayavka');
		if(!$result) exit(mysqli_error($mysql));
		$pod1 = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		}
		
		$result = mysqli_query($mysql, "SELECT * FROM MED_client WHERE client_id='". intval($_GET['client_id'])."' LIMIT 1") or  die(mysqli_error($mysql).' - MED_zayavka');
		if(!$result) exit(mysqli_error($mysql));
		$cln = mysqli_fetch_assoc($result);
		mysqli_free_result($result);	

	?>		
	<h3>Добавить/редактировать заявку</h3> 
	<form class="form-horizontal" method="POST" role="form">
		<input type="hidden" name="client_id" value="<?php echo $_GET['client_id']; ?>"/>
		
		<?php
		if (isset($arr['zayavka_id']))
		{
		echo '<input type="hidden" name="zayavka_id" value="'.$arr['zayavka_id'].'"/>
			<input type="hidden" name="podopech_id" value="'.$arr['podopech_id'].'"/>
			<input type="hidden" name="podopech_id1" value="'.$arr['podopech_id1'].'"/>';
			
			$check_send_sms = check_send_sms ($mysql,$arr['zayavka_id']);
			
			if (isset($_GET['sms']))
			{
				if ($_GET['sms'] == '1')
				{
					if ($arr['sost_zayavki'] == '1' AND ($check_send_sms == false OR !isset($check_send_sms[1])))
					{
					$insertSQL = mysqli_query($mysql, "INSERT INTO MED_send_ochered (admin_id,zayavka_id,time,status_sms,status) VALUES ('".intval($_SESSION['__ID__'])."','".$arr['zayavka_id']."','".time()."','1','0')");
					if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
					die ("<meta http-equiv=refresh content='0; url=?page=zayavka&add=true&edit=".$_GET['edit']."&city=".$_GET['city']."&client_id=".$_GET['client_id']."&mess=sms1&status_sms=1'>");
					}
					else
						{
						die ("<meta http-equiv=refresh content='0; url=?page=zayavka&add=true&edit=".$_GET['edit']."&city=".$_GET['city']."&client_id=".$_GET['client_id']."&mess=sms1&status_sms=0'>");
						}
				}
				if ($_GET['sms'] == '2')
				{
					if ($check_send_sms == false OR !isset($check_send_sms[2]))
					{
					$insertSQL = mysqli_query($mysql, "INSERT INTO MED_send_ochered (admin_id,zayavka_id,time,status_sms,status) VALUES ('".intval($_SESSION['__ID__'])."','".$arr['zayavka_id']."','".time()."','2','0')");
					if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
					die ("<meta http-equiv=refresh content='0; url=?page=zayavka&add=true&edit=".$_GET['edit']."&city=".$_GET['city']."&client_id=".$_GET['client_id']."&mess=sms2&status_sms=1'>");
					}
					else
						{
						die ("<meta http-equiv=refresh content='0; url=?page=zayavka&add=true&edit=".$_GET['edit']."&city=".$_GET['city']."&client_id=".$_GET['client_id']."&mess=sms2&status_sms=0'>");
						}
				}
			}
		}
		
		?>
		<div class="pull-right">
		<?php
		if (isset($arr['zayavka_id']))
		{
		?>
		<a href="?page=zayavka&add=true&edit=<?php echo $arr['zayavka_id']; ?>&city=<?php echo $_GET['city']; ?>&client_id=<?php echo $_GET['client_id']; ?>&sms=1" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-envelope"></span> 1. СМС клиенту</a>
		
		<a href="?page=zayavka&add=true&edit=<?php echo $arr['zayavka_id']; ?>&city=<?php echo $_GET['city']; ?>&client_id=<?php echo $_GET['client_id']; ?>&sms=2" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-envelope"></span> 2. СМС клиенту</a>
		
		<a href="?page=zayavka&delzayavka=<?php echo $arr['zayavka_id']; ?>&city=<?php echo $_GET['city']; ?>&client_id=<?php echo $_GET['client_id']; ?>" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Удалить заявку</a>
		<?php
		}
		?>
		</div>
		<div class="clearfix"></div>
		
		<div class="col-md-12 well well-sm mtop">
			<h3>Информация о Заявке</h3>
			
			<div class="form-group">
				<label for="sost_zayavki" class="col-lg-3 control-label">Статус заявки: </label>
				<div class="col-lg-8">
					<select class="form-control" name="sost_zayavki" required>
						<?php 
						
						foreach ($stat_zayavki as $k => $v)
						{
						?>
						<option <?php echo (isset($arr['sost_zayavki']) and $arr['sost_zayavki'] == $k) ? 'selected' : ''; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
						<?php
						}	
						?>
					</select>
				</div>
			</div>
			
			<div class="clearfix"></div>

			<div class="form-group">
				<div class="col-lg-6">
					<label for="date_zayavka" class="col-lg-6 control-label">Дата оформления заявки</label>
					<div class="col-lg-6">
						<input class="form-control" name="date_zayavka" type="text"  value="<?php echo isset($arr['date_zayavka'])?date('d.m.Y',$arr['date_zayavka']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
					</div>
				</div>
				
				<div class="col-lg-6">
				
					<div class="col-lg-5">
						<select class="form-control" name="date_hour" id="date_hour" required>
						<?php
							for ($i=0;$i<24;$i++)
							{
							?>
							<option <?php echo (isset($arr['date_zayavka']) AND date('G',$arr['date_zayavka']) == $i)?'selected':( (!isset($arr['date_zayavka']) AND date('G') == $i)?'selected':''); ?> value="<?php echo $i; ?>"><?php echo $i; ?> ч.</option>
							<?php	
							}
							?>
						</select>
					</div>	

					<div class="col-lg-5">
						<select class="form-control" name="date_min" id="date_min" required>
							<?php
							for ($i=0;$i<60;$i++)
							{
							?>
							<option value="<?php echo $i; ?>"><?php echo $i; ?> мин.</option>
							<?php	
							}
							?>
						</select>
					</div>
				</div>	
	
			</div>
			
			<div class="form-group">
				<label for="dop_sid" class="col-lg-3 control-label"> Комментарий</label>
				<div class="col-lg-8">
					<textarea class="form-control" name="kom_zayavka" rows="2"  placeholder="Комментарий к заявке"> <?php echo isset($arr['kom_zayavka'])?html_entity_decode(html_entity_decode($arr['kom_zayavka'])):'';?></textarea>
				</div>
			</div>
		</div>
		
		<div class="clearfix"></div>
		
		<div class="col-md-12 alert alert-warning mtop">
			<h3>Информация о заказчике (клиенте)</h3>
				<div class="form-group">
				<label for="status_client" class="col-lg-3 control-label">  Статус клиента: </label>
				<div class="col-lg-8">
					<select class="form-control" name="status_client" required>
						<?php 
						foreach ($stat_clienta as $k => $v)
						{
						?>
						<option <?php echo (isset($cln['status_client']) and $cln['status_client'] == $k) ? 'selected' : ''; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
						<?php
						}	
						?>
					</select>
				</div>
			</div>
				
				<div class="form-group has-error">
					<label for="fio_zakaz" class="col-lg-3 control-label">ФИО заказчика</label>
					<div class="col-lg-8">
					
						<input class="form-control" name="fio_zakaz" type="text" value="<?php echo isset($cln['fio_zakaz']) ? $cln['fio_zakaz'] : ''; ?>"   placeholder="Введите ФИО заказчика" required />
						<a  href="?page=client&add=true&edit=<?php echo isset($cln['client_id']) ? $cln['client_id'] : ''; ?>"> Перейти на страницу клиента: <?php echo isset($cln['fio_zakaz']) ? $cln['fio_zakaz'] : '';?>  </a>
					</div>
				</div>
				
				<div class="form-group">
				<label for="happy_day" class="col-lg-3 control-label">Дата рождения</label>
				<div class="col-lg-8">
					<input class="form-control" name="happy_day" type="text"  value="<?php echo (isset($cln['happy_day']) AND !empty($cln['happy_day']) )?date('d.m.Y',$cln['happy_day']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				</div>
			</div>
			
			<div class="form-group">
				<label for="num_dog" class="col-lg-3 control-label">Номер договора</label>
				<div class="col-lg-8">
					<input class="form-control" name="num_dog" type="text" value="<?php echo isset($cln['num_dog']) ? $cln['num_dog'] : ''; ?>"   placeholder="Введите № договора"  />
				</div>
			</div>
				
				
				
				<div class="form-group has-error">
					<label for="tel_zakaz" class="col-lg-3 control-label">Телефон заказчика</label>
					<div class="col-lg-8">
						<input class="form-control" name="tel_zakaz" type="text" value="<?php echo isset($cln['tel_zakaz']) ? $cln['tel_zakaz'] : ''; ?>"  placeholder="Введите № телефона"/>
					</div>
				</div>
				
				<div class="form-group">
					<label for="pasport" class="col-lg-3 control-label"> Паспортные данные </label>
					<div class="col-lg-8">
						<input class="form-control" name="pasport" type="text"  value="<?php echo isset($cln['pasport']) ? $cln['pasport'] : ''; ?>" placeholder="серия и номер " />
					</div>
				</div>
				
				<div class="form-group">
					<label for="pasport_kem" class="col-lg-3 control-label"> Паспорт выдан </label>
					<div class="col-lg-8">
						<input class="form-control" name="pasport_kem" type="text"  value="<?php echo isset($cln['pasport_kem']) ? $cln['pasport_kem'] : ''; ?>"  placeholder="дата выдачи и кем выдан"   />
					</div>
				</div>
				
				
				<div class="form-group">
					<label for="srok_dog" class="col-lg-3 control-label"> Срок действия договора </label>
					<div class="col-lg-3">
						<input class="form-control" name="srok_dog1" type="text"  value="<?php echo (isset($cln['srok_dog1']) AND !empty($cln['srok_dog1']) )?date('d.m.Y',$cln['srok_dog1']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				
					</div>
					<div class="col-lg-3">
						<input class="form-control" name="srok_dog2" type="text"  value="<?php echo (isset($cln['srok_dog2']) AND !empty($cln['srok_dog2']) )?date('d.m.Y',$cln['srok_dog2']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
					</div>
				</div>	
				
				<div class="form-group">
					<label for="zp_sid" class="col-lg-3 control-label"> Заработная плата для сиделки </label>
					<div class="col-lg-8">
						<input class="form-control" name="zp_sid" type="text" value="<?php echo isset($cln['zp_sid']) ? $cln['zp_sid'] : ''; ?>" placeholder="Введите сумму "  />
					</div>
				</div>
				
				<div class="form-group">
				<label for="srok_opl" class="col-lg-3 control-label"> Следующая оплата клиента </label>
				<div class="col-lg-8">
					<input class="form-control" name="data_opl" type="text"  value="<?php echo (isset($cln['data_opl']) AND !empty($cln['data_opl']) )?date('d.m.Y',$cln['data_opl']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				</div>
				
			</div>
			
			<div class="form-group">
				<label for="srok_opl" class="col-lg-3 control-label"> Сумма оплаты от клиента </label>	
				<div class="col-lg-8">
					<input class="form-control" name="srok_opl" type="text" value="<?php echo isset($cln['srok_opl']) ? $cln['srok_opl'] : ''; ?>"  placeholder="6000 предоплата "  />
				</div>
			</div>
			
			<div class="form-group">
				<label for="data_opl_sid" class="col-lg-3 control-label"> Следующая оплата сиделке </label>
				<div class="col-lg-8">
					<input class="form-control" name="data_opl_sid" type="text"  value="<?php echo (isset($cln['data_opl_sid']) AND !empty($cln['data_opl_sid']) )?date('d.m.Y',$cln['data_opl_sid']):date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
				</div>
				
			</div>
			
			<div class="form-group">
				<label for="sum_sid" class="col-lg-3 control-label"> Сумма оплаты сиделке </label>	
				<div class="col-lg-8">
					<input class="form-control" name="sum_sid" type="text" value="<?php echo isset($cln['sum_sid']) ? $cln['sum_sid'] : ''; ?>"  placeholder="1500 рублей "  />
				</div>
			</div>
			
				<div class="form-group">
					<label for="agent" class="col-lg-3 control-label"> Агентское вознаграждение </label>
					<div class="col-lg-3">
						<input class="form-control" name="agent" type="text" value="<?php echo isset($cln['agent']) ? $cln['agent'] : ''; ?>"  placeholder="0 " />
					</div>
				
					<label for="kyr" class="col-lg-3 control-label"> Курьерские </label>
					<div class="col-lg-2">
						<input class="form-control" name="kyr" type="text"  value="<?php echo isset($cln['kyr']) ? $cln['kyr'] : ''; ?>"  placeholder="0 "/>
					</div>
				</div>
				
				<div class="form-group">
					<label for="kom_klient" class="col-lg-3 control-label"> Комментарий к клиенту </label>
					<div class="col-lg-8">
									
						<textarea class="form-control" name="kom_klient" rows="2"> <?php echo isset($cln['kom_klient'])?html_entity_decode(html_entity_decode($cln['kom_klient'])):'';?></textarea>
					</div>
				</div>
				
		</div>	
		
		<div class="col-md-1"></div>
	
		<div class="col-md-12 alert alert-warning mtop">
			<h3>Информация о подопечном #1</h3>
				<div class="form-group">
				<label for="fio_podopech" class="col-lg-3 control-label"> ФИО подопечного </label>
					<div class="col-lg-8">
						<input class="form-control" name="fio_podopech" type="text"  value="<?php echo isset($pod['fio_podopech']) ? $pod['fio_podopech'] : ''; ?>"  placeholder="Введите информацию о подопечном"  />
					</div>
				</div>	
			
				<div class="form-group">		
					<div class="form-group has-error">
						<label for="gender" class="col-lg-3 control-label" > Пол подопечного </label>
						<div class="col-lg-3">
							<select class="form-control" name="gender"  required>
								<option <?php echo (isset($pod['gender']) and $pod['gender'] == 0) ? 'selected' : ''; ?> value="0">Жен</option>
								<option <?php echo (isset($pod['gender']) and $pod['gender'] == 1) ? 'selected' : ''; ?> value="1">Муж</option>
							</select>
						</div>
					</div>
					
						<label for="ves_podopech" class="col-lg-3 control-label"> Вес подопечного </label>
						<div class="col-lg-3">
							<input class="form-control" name="ves_podopech" type="text" value="<?php echo isset($pod['ves_podopech']) ? $pod['ves_podopech'] : ''; ?>"   placeholder="Введите вес"/>
						</div>
				</div>			
			
				<div class="form-group has-error">
					<label for="sost_podopech" class="col-lg-3 control-label"> Состояние - заболевания </label>
					<div class="col-lg-8">
						<input class="form-control" name="sost_podopech" type="text" value="<?php echo isset($pod['sost_podopech']) ? $pod['sost_podopech'] : ''; ?>"  placeholder="Введите информацию о подопечном" required />
					</div>
				</div>	
			
				<div class="form-group has-error">
					<label for="city" class="col-lg-3 control-label"> Город</label>
					<div class="col-lg-2">
						<select class="form-control" name="city" required>
							<option <?php echo (isset($pod['city']) and $pod['city'] == 1) ? 'selected' : (  (isset($_GET['city']) and $_GET['city'] == 1)? 'selected'  :''); ?> value="1">Москва</option>
							<option <?php echo (isset($pod['city']) and $pod['city'] == 2) ? 'selected' : (  (isset($_GET['city']) and $_GET['city'] == 2)? 'selected'  :''); ?> value="2">СПб</option>
						</select>
					</div>
				
					<label for="adres_podopech" class="col-lg-2 control-label"> Улица / дом </label>
						<div class="col-lg-4">
							<input class="form-control" name="adres_podopech" type="text"  value="<?php echo isset($pod['adres_podopech']) ? $pod['adres_podopech'] : ''; ?>"  placeholder="Введите адрес подопечного" required />
						</div>
				</div>	
			
				<div class="form-group has-error">
					<label for="grafik" class="col-lg-3 control-label"> График ухода за больным </label>
					<div class="col-lg-8">
						<input class="form-control" name="grafik" type="text" value="<?php echo isset($pod['grafik']) ? $pod['grafik'] : ''; ?>"  placeholder="Введите график работы: приходящая" required />
					</div>
				</div>
				
				<div class="form-group has-error">
					<label for="yslov" class="col-lg-3 control-label"> Условия </label>
					<div class="col-lg-8">
						<textarea class="form-control" name="yslov" rows="2"  placeholder="опишите детально условия работы"> <?php echo isset($pod['yslov'])?html_entity_decode(html_entity_decode($pod['yslov'])):'';?></textarea>
					</div>
				</div>
			</div>	
			
			<?php
			if (!isset($pod1['fio_podopech']) OR empty($pod1['fio_podopech']))
			{
			?>
			<div id="btn_podopech" class="col-md-6 col-md-offset-3">
				<span class="btn btn-warning add_podopech"><span class="glyphicon glyphicon-plus"></span> Добавить еще подопечного</span>
			</div>
			<?php
			}
			?>
			
			<div id="add_podopech" class="col-md-12 alert alert-warning mtop <?php echo (isset($pod1['fio_podopech']) AND !empty($pod1['fio_podopech'])) ? '' : 'hidden'; ?>">
			<?php
			if (!isset($pod1['fio_podopech']) OR empty($pod1['fio_podopech']))
			{
			?>
			<p class="pull-right"><span class="btn btn-danger add_podopech_close"><span class="glyphicon glyphicon-remove"></span></span></p>
			<?php
			}
			?>
			<h3>Информация о подопечном #2</h3>
				<div class="form-group">
					<label for="fio_podopech" class="col-lg-3 control-label"> ФИО подопечного </label>
					<div class="col-lg-8">
						<input class="form-control" name="fio_podopech1" type="text"  value="<?php echo isset($pod1['fio_podopech']) ? $pod1['fio_podopech'] : ''; ?>" placeholder="Введите информацию о подопечном"  />
					</div>
				</div>	
			
				<div class="form-group">		
					<div class="form-group">
						<label for="gender1" class="col-lg-3 control-label" > Пол подопечного </label>
						<div class="col-lg-3">
							<select class="form-control" name="gender1"  >
								<option <?php echo (isset($pod1['gender']) and $pod1['gender'] == 0) ? 'selected' : ''; ?> value="0">Жен</option>
								<option <?php echo (isset($pod1['gender']) and $pod1['gender'] == 1) ? 'selected' : ''; ?> value="1">Муж</option>
							</select>
						</div>
					</div>
					
						<label for="ves_podopech1" class="col-lg-3 control-label"> Вес подопечного </label>
						<div class="col-lg-3">
							<input class="form-control" name="ves_podopech1" type="text" value="<?php echo isset($pod1['ves_podopech']) ? $pod1['ves_podopech'] : ''; ?>"   placeholder="Введите вес"/>
						</div>
				</div>			
			
				<div class="form-group">
					<label for="sost_podopech1" class="col-lg-3 control-label"> Состояние - заболевания </label>
					<div class="col-lg-8">
						<input class="form-control" name="sost_podopech1" type="text" value="<?php echo isset($pod1['sost_podopech']) ? $pod1['sost_podopech'] : ''; ?>"  placeholder="Введите информацию о подопечном"  />
					</div>
				</div>	
			
				<div class="form-group">
					<label for="city1" class="col-lg-3 control-label"> Город</label>
					<div class="col-lg-2">
						<select class="form-control" name="city1" >
							<option <?php echo (isset($pod1['city']) and $pod1['city'] == 1) ? 'selected' : (  (isset($_GET['city']) and $_GET['city'] == 1)? 'selected'  :''); ?> value="1">Москва</option>
							<option <?php echo (isset($pod1['city']) and $pod1['city'] == 2) ? 'selected' : (  (isset($_GET['city']) and $_GET['city'] == 2)? 'selected'  :''); ?> value="2">СПб</option>
						</select>
					</div>
				
					<label for="adres_podopech1" class="col-lg-2 control-label"> Улица / дом </label>
						<div class="col-lg-4">
							<input class="form-control" name="adres_podopech1" type="text"  value="<?php echo isset($pod1['adres_podopech']) ? $pod1['adres_podopech'] : ''; ?>"  placeholder="Введите адрес подопечного" />
						</div>
				</div>	
			
				<div class="form-group">
					<label for="grafik1" class="col-lg-3 control-label"> График ухода за больным </label>
					<div class="col-lg-8">
						<input class="form-control" name="grafik1" type="text" value="<?php echo isset($pod1['grafik']) ? $pod1['grafik'] : ''; ?>"  placeholder="Введите график работы: приходящая" />
					</div>
				</div>
				
				<div class="form-group">
					<label for="yslov1" class="col-lg-3 control-label"> Условия </label>
					<div class="col-lg-8">
						<textarea class="form-control" name="yslov1" rows="2"  placeholder="опишите детально условия работы"> <?php echo isset($pod1['yslov'])?html_entity_decode(html_entity_decode($pod1['yslov'])):'';?></textarea>
					</div>
				</div>
			</div>	
			
		
		
			
			<?php
			/*
			<div class="col-md-12 alert alert-success mtop">
				<p class="pull-right"><span class="btn btn-danger remove_sidelka"><span class="glyphicon glyphicon-remove"></span></span></p>
				<h3>Информация о сиделке</h3>
				<div class="form-group has-error">
					<label for="fio_sid" class="col-lg-3 control-label"> ФИО сиделки</label>
					<div class="col-lg-8">
						<input class="form-control" name="fio_sid" type="text"  value="<?php echo isset($sid['fio_sid']) ? $sid['fio_sid'] : ''; ?>"  placeholder="Введите информацию о подопечном"  />
					</div>
				</div>	

				<div class="form-group">
					<label for="tel_sid" class="col-lg-3 control-label"> Телефон </label>
					<div class="col-lg-8">
						<input class="form-control" name="tel_sid" type="text" value="<?php echo isset($sid['tel_sid']) ? $sid['tel_sid'] : ''; ?>"  placeholder="Введите график работы: приходящая"  />
					</div>
				</div>
				
				<div class="form-group">
					<label for="dop_sid" class="col-lg-3 control-label"> Дополнительное поле </label>
					<div class="col-lg-8">
						<textarea class="form-control" name="dop_sid" rows="2"  placeholder="опишите детально условия работы"> <?php echo isset($sid['dop_sid'])?html_entity_decode(html_entity_decode($sid['dop_sid'])):'';?></textarea>
					</div>
				</div>	
			</div> 
			*/
			?>
			
		<div class="form-group overflow_dynamic_dop">
		<?php
		$h=0;
			if (isset($arr['zayavka_id']))
			{
			$h=1;
    		$result = mysqli_query($mysql, "SELECT * FROM MED_sidelka WHERE zayavka_id='" . $arr['zayavka_id'] . "'") or  die(mysqli_error($mysql).' - CRM_SNAB_tovar_EDIT');
    			if(!$result) exit(mysqli_error($mysql));
    			while($sid=mysqli_fetch_assoc($result))
    			{
					if ($sid['status'] == 1)
					{
					?>
					<div id="fp<?php echo $h; ?>" class="col-md-12 alert alert-success mtop">
						<p class="pull-right"><span class="btn btn-danger remove_sidelka" onclick="deaddoppay('<?php echo $h; ?>');"><span class="glyphicon glyphicon-remove"></span></span></p>
						<h3>Информация о сиделке</h3>
						<input type="hidden" name="sidelka_id[]" value="<?php echo $sid['sidelka_id']; ?>"/>
						<div class="form-group has-error">
							<label for="fio_sid" class="col-lg-3 control-label"> ФИО сиделки</label>
							<div class="col-lg-8">
								<input class="form-control" name="fio_sid[]" type="text"  value="<?php echo isset($sid['fio_sid']) ? $sid['fio_sid'] : ''; ?>"  placeholder="Введите информацию о подопечном"  />
							</div>
						</div>	

						<div class="form-group">
							<label for="tel_sid" class="col-lg-3 control-label"> Телефон </label>
							<div class="col-lg-8">
								<input class="form-control" name="tel_sid[]" type="text" value="<?php echo isset($sid['tel_sid']) ? $sid['tel_sid'] : ''; ?>"  placeholder="Введите график работы: приходящая"  />
							</div>
						</div>
						
						<div class="form-group">
							<label for="dop_sid" class="col-lg-3 control-label">Комментарий к сиделке </label>
							<div class="col-lg-8">
								<textarea class="form-control" name="dop_sid[]" rows="2"  placeholder="опишите детально условия работы"> <?php echo isset($sid['dop_sid'])?html_entity_decode(html_entity_decode($sid['dop_sid'])):'';?></textarea>
							</div>
						</div>
						
						
					</div> 
								
					<div class="clearfix mtop"></div>
					<?php
					$h++;
					}
					else
						{
						?>
						<div class="col-md-12 alert alert-warning mtop">

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
    			}
    		mysqli_free_result($result);
			
			}
		?>
		</div>
		
		<div class="form-group">
			<label for="cname" class="col-lg-2 control-label"></label>
			<div class="col-lg-9 text-right">
				<input name="counter1" class="counter1" value="<?php echo $h; ?>" type="hidden" />
				<a href="#" class="btn btn-warning btn-sm add_sidelka">Добавить сиделку</a>
				<span class="help-block"> </span>
			</div>
			<div class="col-lg-1"></div>
		</div>	
			
	
		<div class="col-md-12">
			<button type="submit" class="btn btn-primary" name="Sohr"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
			<a href="?page=zayavka" class="btn btn-danger"><span class="glyphicon glyphicon-off"></span> Закрыть</a>
		</div>	
		
		<div class="clearfix"></div>
		
	</form>
	<?php 
	}
	else
		{
		
		foreach ($city as $k => $v)
		{
		?>
		<a class="label label-<?php echo (isset($_GET['citysort']) AND $_GET['citysort'] == $k)?'success':'default'; ?>" href="?page=zayavka&citysort=<?php echo $k; ?><?php echo (isset($_GET['sort'])?'&sort='.$_GET['sort']:''); ?><?php echo (isset($_GET['d1'])?'&d1='.$_GET['d1'].'&d2='.$_GET['d2']:''); ?>"><span class="glyphicon glyphicon-<?php echo (isset($_GET['citysort']) AND $_GET['citysort'] == $k)?'check':'unchecked'; ?>"></span> <?php echo $v; ?></a>
		<?php
		}
		foreach ($stat_zayavki as $k => $v)
		{
		?>
		<a class="label label-<?php echo (isset($_GET['sort']) AND $_GET['sort'] == $k)?'success':'default'; ?>" href="?page=zayavka&sort=<?php echo $k; ?><?php echo (isset($_GET['citysort'])?'&citysort='.$_GET['citysort']:''); ?><?php echo (isset($_GET['d1'])?'&d1='.$_GET['d1'].'&d2='.$_GET['d2']:''); ?>"><span class="glyphicon glyphicon-<?php echo (isset($_GET['sort']) AND $_GET['sort'] == $k)?'check':'unchecked'; ?>"></span> <?php echo $v; ?></a>
		<?php
		}
		if (isset($_GET['citysort']) OR isset($_GET['sort']))
		{
		?>
		<a class="label label-danger" href="?page=zayavka"><span class="glyphicon glyphicon-remove-sign"></span> Очистить</a>
		<?php
		}
		
		?>

		<form class="form-horizontal mtop" action='' method='get'>
			<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
			<?php
			
			echo isset($_GET['sort'])?'<input type="hidden" name="sort" value="'.$_GET['sort'].'">':'';
			echo isset($_GET['citysort'])?'<input type="hidden" name="citysort" value="'.$_GET['citysort'].'">':'';
			
			?>
			<div class="form-group">
			<div class="col-md-4">
			<input autocomplete="off" class="form-control input-sm" name="d1" type="text" value="<?php echo isset($_GET['d1'])?$_GET['d1']:''; ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this);" />
			</div>
			
			<div class="col-md-4">
			<input autocomplete="off" class="form-control input-sm" name="d2" type="text" value="<?php echo isset($_GET['d2'])?$_GET['d2']:''; ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this);" />
			</div>
			
			<div class="col-md-2">
			<button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-search"></span> Поиск</button>
			</div>

			
			</div>
		
		</form>

      <div class="clearfix mtop"></div>
		

		
		
		<?php
		$time = isset($_GET['d1'])?strtotime($_GET['d1']):strtotime(date('d.m.Y'));  
		$d1 = isset($_GET['d1'])?strtotime($_GET['d1']):mktime (0, 0, 0, date('m',$time), date('d',$time), date('Y',$time));
		$d2 = isset($_GET['d2'])?strtotime($_GET['d2']):mktime (0, 0, 0, date('m',$time), date('d',$time), date('Y',$time));
		$t = date("t", $time);
		$MAG_naklad_base_dates = isset($_GET['d1'])? "AND ('".($d1)."' <= MED_zayavka.date_zayavka  AND '".($d2+86399)."' >= MED_zayavka.date_zayavka)": "";
		$plat_dates = isset($_GET['d1'])? "AND ('".($d1)."' <= MED_oplata.date_plat  AND '".($d2+86399)."' >= MED_oplata.date_plat)": "";
		$SQLs = "SELECT * FROM MED_zayavka";
		$result = mysqli_query($mysql, $SQLs) or  die(mysqli_error().' - MED_zayavka');
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
		
		$citysort = isset($_GET['citysort']) ? " AND MED_zayavka.city_id='".intval($_GET['citysort'])."'" : "";
		
		$sostsort = isset($_GET['sort']) ? " AND MED_zayavka.sost_zayavki='".intval($_GET['sort'])."'" : '';
				$SQL1 = "
					SELECT 
							MED_zayavka.zayavka_id,
							MED_zayavka.city_id,
							MED_zayavka.client_id,
							MED_zayavka.sost_zayavki,
							MED_zayavka.date_zayavka,
							MED_client.zp_sid,
							MED_client.fio_zakaz,
							MED_client.srok_dog1,
							MED_client.srok_dog2,
							MED_client.tel_zakaz,
							MED_podopech.gender,
							MED_podopech.grafik,
							MED_podopech.sost_podopech
					FROM 
						MED_zayavka 
					LEFT JOIN  
						MED_client
					ON 
						MED_zayavka.client_id=MED_client.client_id
					LEFT JOIN  
						MED_podopech
					ON 
						MED_zayavka.podopech_id=MED_podopech.podopech_id 
					WHERE
						MED_zayavka.sost_zayavki ='1' 
						".$citysort." ".$sostsort." ".$MAG_naklad_base_dates." ";
					$r3 = mysqli_query($mysql,$SQL1);
					if(!$r3) exit(mysqli_error());
					$arr3=mysqli_num_rows($r3);
					mysqli_free_result($r3);
		?>
		<div class="well well-sm mtop">
			<h4>Статистика на этой странице</h4>
			
			<div class="col-md-3">
				<p><strong> Новые заказы: <?php echo$arr3; ?></strong> </p>
			</div>
	
	
	<!-- 		Вывод заказов по статусу "На паузе"	 -->
			<?php
			$SQL3 = "
					SELECT 
							MED_zayavka.zayavka_id,
							MED_zayavka.city_id,
							MED_zayavka.client_id,
							MED_zayavka.sost_zayavki,
							MED_zayavka.date_zayavka,
							MED_client.zp_sid,
							MED_client.fio_zakaz,
							MED_client.srok_dog1,
							MED_client.srok_dog2,
							MED_client.tel_zakaz,
							MED_podopech.gender,
							MED_podopech.grafik,
							MED_podopech.sost_podopech
					FROM 
						MED_zayavka 
					LEFT JOIN  
						MED_client
					ON 
						MED_zayavka.client_id=MED_client.client_id
					LEFT JOIN  
						MED_podopech
					ON 
						MED_zayavka.podopech_id=MED_podopech.podopech_id 
					WHERE
						MED_zayavka.sost_zayavki ='2' 
						".$citysort." ".$sostsort." ".$MAG_naklad_base_dates." ";
					$r5 = mysqli_query($mysql,$SQL3);
					if(!$r5) exit(mysqli_error());
					$row_cnt1 = mysqli_num_rows($r5);
					mysqli_free_result($r5);
			?>
			<div class="col-md-3">
				<p><strong>На паузе: <?php  echo $row_cnt1; ?><br></strong></p>
			</div>
	
<!-- 		Вывод заказов по статусу "Закратые нами"	 -->
			<?php
			$SQL2 = "
					SELECT 
							MED_zayavka.zayavka_id,
							MED_zayavka.city_id,
							MED_zayavka.client_id,
							MED_zayavka.sost_zayavki,
							MED_zayavka.date_zayavka,
							MED_client.zp_sid,
							MED_client.fio_zakaz,
							MED_client.srok_dog1,
							MED_client.srok_dog2,
							MED_client.tel_zakaz,
							MED_podopech.gender,
							MED_podopech.grafik,
							MED_podopech.sost_podopech
					FROM 
						MED_zayavka 
					LEFT JOIN  
						MED_client
					ON 
						MED_zayavka.client_id=MED_client.client_id
					LEFT JOIN  
						MED_podopech
					ON 
						MED_zayavka.podopech_id=MED_podopech.podopech_id 
					WHERE
						MED_zayavka.sost_zayavki ='3' 
						".$citysort." ".$sostsort." ".$MAG_naklad_base_dates." ";
					$r4 = mysqli_query($mysql,$SQL2);
					if(!$r4) exit(mysqli_error());
					$row_cnt = mysqli_num_rows($r4);
					mysqli_free_result($r4);
			?>
			<div class="col-md-3">
				<p><strong>Закрытые нами: <?php  echo $row_cnt; ?><br></strong></p>
			</div>
<!--  -->


<!-- 		Вывод заказов по статусу "Отказ клиента"	 -->
			<?php
			$SQL2 = "
					SELECT 
							MED_zayavka.zayavka_id,
							MED_zayavka.city_id,
							MED_zayavka.client_id,
							MED_zayavka.sost_zayavki,
							MED_zayavka.date_zayavka,
							MED_client.zp_sid,
							MED_client.fio_zakaz,
							MED_client.srok_dog1,
							MED_client.srok_dog2,
							MED_client.tel_zakaz,
							MED_podopech.gender,
							MED_podopech.grafik,
							MED_podopech.sost_podopech
					FROM 
						MED_zayavka 
					LEFT JOIN  
						MED_client
					ON 
						MED_zayavka.client_id=MED_client.client_id
					LEFT JOIN  
						MED_podopech
					ON 
						MED_zayavka.podopech_id=MED_podopech.podopech_id 
					WHERE
						MED_zayavka.sost_zayavki ='4' 
						".$citysort." ".$sostsort." ".$MAG_naklad_base_dates." ";
					$r6 = mysqli_query($mysql,$SQL2);
					if(!$r6) exit(mysqli_error());
					$row_cnt8 = mysqli_num_rows($r6);
					mysqli_free_result($r6);
			?>
			<div class="col-md-3">
				<p><strong>Отказ клиента: <?php  echo $row_cnt8; ?><br></strong></p>
			</div>
<!--  Подсчет платежей  от новых клиентов за месяц -->
			<div class="col-md-12">
			<?php 
			$result = mysqli_query($mysql,"SELECT  SUM(sum_plat) AS sum FROM MED_oplata  WHERE 
						date_plat>='".$d1."' AND
						date_plat<='".$d2."' AND
						vid_plat='0' ");
			if(!$result) exit(mysqli_error($mysql));
			while ($arr = mysqli_fetch_assoc($result))
			{
			?>
				
				<p><strong>Платежей от клиентов</strong> c <?php echo date('d.m.y',$d1); ?> по <?php echo date('d.m.y',$d2); ?>: <strong><?php echo number_format($arr['sum'], 2, '.', ' ').' руб.'; ?></strong></p>
			<?php	
			}
			mysqli_free_result($result);
			?>
			</div>
			
			
			
			<div class="clearfix mtop"></div>
		</div>
		<?php
		
		
		
			$SQL = "
					SELECT 
							MED_zayavka.zayavka_id,
							MED_zayavka.city_id,
							MED_zayavka.client_id,
							MED_zayavka.sost_zayavki,
							MED_zayavka.date_zayavka,
							MED_client.zp_sid,
							MED_client.fio_zakaz,
							MED_client.srok_dog1,
							MED_client.srok_dog2,
							MED_client.tel_zakaz,
							MED_podopech.gender,
							MED_podopech.grafik,
							MED_podopech.sost_podopech
					FROM 
						MED_zayavka 
					LEFT JOIN  
						MED_client
					ON 
						MED_zayavka.client_id=MED_client.client_id
					LEFT JOIN  
						MED_podopech
					ON 
						MED_zayavka.podopech_id=MED_podopech.podopech_id 
					WHERE
						MED_zayavka.zayavka_id>'0'
						".$citysort." ".$sostsort." ".$MAG_naklad_base_dates." ";
				
			$r = mysqli_query($mysql,$SQL."ORDER	BY MED_zayavka.zayavka_id DESC LIMIT ".$start.", ".$num_elements);
			if(!$r) exit(mysqli_error());
			while	($arr=mysqli_fetch_assoc($r))
			{
			$status_CHECK = isset($status_CHECK)?$status_CHECK+1:1;
			$SQL = "SELECT * FROM MED_sidelka WHERE zayavka_id='". $arr['zayavka_id'] ."' AND status='1' LIMIT 1";
			$rs = mysqli_query($mysql, $SQL) or  die(mysqli_error().' - MED_sidelka');
			$sid = mysqli_fetch_assoc($rs);
			mysqli_free_result($rs);
			?>
			<div class="col-md-12 list-group-item">
				<div class="col-md-5">
					<h4><a class="" href="?page=zayavka&add=true&edit=<?php echo $arr['zayavka_id']; ?>&city=<?php echo $arr['city_id']; ?>&client_id=<?php echo $arr['client_id']; ?>">Заявка №<?php echo $arr['zayavka_id']; ?></a></h4>
						<p class="pull-left">
							ФИО заказчика: <?php echo $arr['fio_zakaz']; ?> <br>
							Телефона заказчика: <?php echo $arr['tel_zakaz']; ?> <br>
							Срок действия договора: c <?php echo date('d.m.Y',$arr['srok_dog1']); ?> по <?php echo date('d.m.Y',$arr['srok_dog2']); ?> <br>
						</p>
				</div>	
				
				<div class="col-md-5">
						<p class="pull-left">
							Пол подопечного: <?php echo (isset($arr['gender']) AND $arr['gender'] == 0) ? 'Жен' : 'Муж'; ?> <br>
							График: <?php echo isset($arr['grafik']) ? $arr['grafik'] : ''; ?> <br>
							Состояние: <?php echo isset($arr['sost_podopech']) ? $arr['sost_podopech'] : ''; ?> <br>
							Сиделка: <?php echo isset($sid['fio_sid']) ? '<span class="text-success">назначена</span>' : '<span class="text-danger">НЕ назначена</span>'; ?> <br>
						</p>
				</div>	

				<div class="col-md-2">
					<p class="pull-right">
						Статус заявки:  <br>
						<form method="GET" id="foobar<?php echo $arr['zayavka_id']; ?>">
						<input type="hidden" name="page" value="zayavka"/>
						<select class="form-control" name="sts_move" onChange="this.form.submit();">
							<?php 
							foreach ($stat_zayavki as $k => $v)
							{
							?>
							<option <?php echo (isset($arr['sost_zayavki']) AND $arr['sost_zayavki'] == $k) ? 'selected' : ''; ?>  value="<?php echo $arr['zayavka_id']; ?>|<?php echo $k; ?>"><?php echo $v; ?></option>
							<?php
							}	
							?>
						</select>
						</form>
						<br>
						 Город: <?php echo (isset($arr['city_id']) AND $arr['city_id'] == 1) ? 'Москва' : 'СПб'; ?> <br>
					</p>
				</div>	
				<div class="clearfix"></div>
			</div>
			<?php
			unset($arr);
			}
			mysqli_free_result($r);
			?>
			
			<div class="clearfix mtop"><br></div>
			<div class="clearfix mtop"><br></div>
			<?php
			if ($total_all >= $num_elements and (isset($status_CHECK) AND $status_CHECK >= $num_elements) )
			{
			echo GetNav($p, $num_pages);
			}
			?>
			
		<div class="clearfix"></div>
		<?php 
		}
		?>
