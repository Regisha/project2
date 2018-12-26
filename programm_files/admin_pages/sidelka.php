<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php'>");
	}
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_sidelka (
	sidelka_id int auto_increment primary key,
	admin_id int(1) NOT NULL,
	zayavka_id int(10) NOT NULL,
	fio_sid varchar(50) NOT NULL,
	tel_sid varchar(50) NOT NULL,
	dop_sid varchar(150) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Информация о сиделке'");
	
	if (isset($_POST['SBM_add']))
	{
	$fio_sid= mysqli_real_escape_string($mysql, trim($_POST['fio_sid']));
	$tel_sid= mysqli_real_escape_string($mysql, trim($_POST['tel_sid']));
	$dop_sid= mysqli_real_escape_string($mysql, trim($_POST['dop_sid']));

	$query_count = "UPDATE MED_sidelka  SET fio_sid='". $fio_sid."',tel_sid='". $tel_sid."', dop_sid='". $dop_sid."'  WHERE sidelka_id='".intval($_POST['sidelka_id'])."' LIMIT 1";
	mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
			
	die ("<meta http-equiv=refresh content='0; url=?page=sidelka'>");
	}
	
	if (isset($_GET['del']))
	{
		if (intval($_GET['del']) > 1)
		{
		$query = "DELETE FROM MED_sidelka  WHERE sidelka_id='" .  intval($_GET['del']) . "' LIMIT 1";
		mysqli_query($mysql,$query) or die(mysqli_error());
		}
	die ("<meta http-equiv=refresh content='0; url=?page=sidelka'>");
	}	
?>

  	<div class="col-md-12">
		<h3>Сиделки</h3>
		<div class="clearfix mtop"><br></div>
	<?php
	if (isset($_GET['delete']))
	{
	?>
	<div class="alert alert-dismissible alert-danger col-md-8 col-md-offset-2">
		<h3 class="text-center text-danger">Подтверждение удаления сиделки #<?php echo $_GET['delete']; ?></h3>
		<p class="text-danger">ОСТОРОЖНО! Сиделка закреплена за определенной заявкой, удалении сиделки может привести к ошибке в заявке.</p>
		<p class="text-center">
			<a class="btn btn-danger btn-sm" href="?page=sidelka">Отмена</a>
			<a class="btn btn-success" href="?page=sidelka&del=<?php echo $_GET['delete']; ?>"><span class="glyphicon glyphicon-trash"></span> Удалить</a>
		</p>
	</div>
	<div class="clearfix"></div>
	<?php
	}
	elseif (isset($_GET['add']))
	{
		if (isset($_GET['edit']))
		{
		$SQL = "SELECT * FROM MED_sidelka WHERE sidelka_id='". intval($_GET['edit']) ."' LIMIT 1";
		$r = mysqli_query($mysql, $SQL) or  die(mysqli_error().' - MED_sidelka');
		$sid = mysqli_fetch_assoc($r);
		mysqli_free_result($r);
		}
		
	?>
	<div class="col-md-12 alert alert-warning mtop">
		<h3>Информация о сиделке</h3>
		<form class="form-horizontal" method="POST" role="form">
			<input type="hidden" name="sidelka_id" value="<?php echo $_GET['edit']; ?>"/>
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
		
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" name="SBM_add" class="btn btn-primary">Сохранить</button>
					<a class="btn btn-sm btn-danger" href="?page=sidelka">Отменить</a>
				</div>
			</div>
		
		</form>
	<div class="clearfix"></div>
	</div>

	<div class="clearfix"></div>
	<?php
	}
	else
		{
		$SQLs = "SELECT * FROM MED_sidelka";
		$result = mysqli_query($mysql, $SQLs) or  die(mysqli_error().' - MED_sidelka');
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
		
		foreach ($stat_clienta as $k => $v)
		{
		?>
		<a class="label label-<?php echo (isset($_GET['sort']) AND $_GET['sort'] == $k)?'success':'default'; ?>" href="?page=sidelka&sort=<?php echo $k; ?>"><span class="glyphicon glyphicon-<?php echo (isset($_GET['sort']) AND $_GET['sort'] == $k)?'check':'unchecked'; ?>"></span> <?php echo $v; ?></a>
		<?php
		}
		?>
		<a class="label label-<?php echo (isset($_GET['work']) AND $_GET['work'] == 0)?'success':'default'; ?>" href="?page=sidelka&work=0"><span class="glyphicon glyphicon-<?php echo (isset($_GET['work']) AND $_GET['work'] == 0)?'check':'unchecked'; ?>"></span> Свободные</a>
		<a class="label label-<?php echo (isset($_GET['work']) AND $_GET['work'] == 1)?'success':'default'; ?>" href="?page=sidelka&work=1"><span class="glyphicon glyphicon-<?php echo (isset($_GET['work']) AND $_GET['work'] == 1)?'check':'unchecked'; ?>"></span> В работе</a>
		<?php
		if (isset($_GET['sort']) OR isset($_GET['q']) OR isset($_GET['work']))
		{
		?>
		<a class="label label-danger" href="?page=sidelka"><span class="glyphicon glyphicon-remove-sign"></span> Очистить</a>
		<?php
		}
		?>
		<div class="clearfix mtop"><br></div>
		
		<form class="form-horizontal" action='' method='get'>
			<input type="hidden" name="page" value="sidelka">
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
		
		<table class="table table-hover mtop">
			<tr>
				<th><span class="text-success">Где закреплена</span>/<span class="text-warning">Где работала</span></th>
				<th>Статус клиента</th>
				<th>ФИО</th>
				<th>Тел</th>
				<th>Управление</th>
			</tr>
			<?php
			$WHERE = '';
			if (isset($_GET['q']))
			{
			$arr = array('MED_sidelka.fio_sid','MED_sidelka.tel_sid');
			$w = isset($_GET['w'])?$_GET['w']:'0';
			$q = intval($w) == 1 ? preg_replace ("/[^0-9\s]/","",trim($_GET['q'])) : urldecode(trim($_GET['q']));
			$w = intval($w) == 1 ? "REPLACE( REPLACE( REPLACE( ".$arr[intval($w)].", '-', '' ), '(', '' ), ')', '' )":$arr[intval($w)];
			$WHERE = " AND ".$w." LIKE '%".$q."%'";
			}
	
			$sort = isset($_GET['sort']) ? intval($_GET['sort']) : implode(',',array_flip($stat_clienta));
			$work = isset($_GET['work']) ? " AND MED_sidelka.status='".intval($_GET['work'])."'" : '';
				$SQL = "
					SELECT 
							MED_sidelka.sidelka_id,
							MED_sidelka.fio_sid,
							MED_sidelka.tel_sid,
							MED_sidelka.status,
							MED_zayavka.zayavka_id,
							MED_zayavka.city_id,
							MED_zayavka.client_id,
							MED_zayavka.sost_zayavki,
							MED_client.status_client
					FROM 
						MED_sidelka 
					LEFT JOIN  
						MED_zayavka
					ON 
						MED_zayavka.zayavka_id=MED_sidelka.zayavka_id
					LEFT JOIN  
						MED_client
					ON 
						MED_zayavka.client_id=MED_client.client_id
					WHERE 
						MED_client.status_client IN(".$sort.")
						".$WHERE." ".$work."
					ORDER
						BY MED_sidelka.sidelka_id DESC LIMIT ".$start.", ".$num_elements;
						
			$r = mysqli_query($mysql,$SQL);
				if(!$r) exit(mysqli_error($mysql));
				while	($hk=mysqli_fetch_assoc($r))
				{
				$status_CHECK = true;
				?>
				<tr>
					<td><?php echo $hk['status'] == 1 ? '<span class="text-success">Сейчас в заявке №'.$hk['zayavka_id'].'</span>':'<span class="text-warning">В заявке №'.$hk['zayavka_id'].'</span>'; ?></td>
					<td><?php echo $stat_clienta[$hk['status_client']]; ?> </td>
					<td><?php echo $hk['fio_sid']; ?> </td>
					<td><?php echo $hk['tel_sid']; ?></td>
					<td>
						<a class="btn btn-sm btn-success" href="?page=sidelka&add=true&edit=<?php echo $hk['sidelka_id']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
						<a class="btn btn-sm btn-danger" href="?page=sidelka&delete=<?php echo $hk['sidelka_id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
				<?php
				}
			mysqli_free_result($r);
			?>
		</table>
		<?php
			if ($total_all >= $num_elements and isset($status_CHECK))
			{
			echo GetNav($p, $num_pages);
			}
		}
		?>
	</div>
