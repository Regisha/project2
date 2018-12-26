<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}

	$arr_type_pay = array (
	0 => array(1 => 'Платеж',2 => 'Выплата',3 => 'Другое')
	);
	
	mysqli_query($mysql,"CREATE TABLE IF NOT EXISTS MED_debet(
	id int auto_increment primary key 	COMMENT 'id',
	type_pay varchar(1) NOT NULL		COMMENT 'Тип оплаты',
	stspay varchar(2) NOT NULL 			COMMENT 'Статус оплаты',
	summ varchar(50) NOT NULL 			COMMENT 'Сумма оплаты',
	time varchar(12) NOT NULL 			COMMENT 'Дата оплаты',
	about TEXT NOT NULL 				COMMENT 'Комментарий',
	city_id  int(3) NOT NULL 			COMMENT 'id города 1 или 2',
	client_id int(3) NOT NULL 			COMMENT 'id клиента'
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Таблица Денег'");
	
	
		$time = isset($_GET['d1'])?strtotime($_GET['d1']):strtotime(date('d.m.Y'));	
		$d1 = isset($_GET['d1'])?strtotime($_GET['d1']):mktime (0, 0, 0, date('m',$time), 1, date('Y',$time));
		$d2 = isset($_GET['d2'])?strtotime($_GET['d2']):mktime (0, 0, 0, date('m',$time), date("t", mktime (0, 0, 0, date('m',$time), 1, date('Y',$time))), date('Y',$time));

		$dates = " AND ('".($d1)."' <= MED_debet.time AND '".($d2+86399)."' >= MED_debet.time)";
		$citysort = isset($_GET['citysort']) ? " AND MED_debet.city_id='".intval($_GET['citysort'])."'" : "";
		
		$arraySUM = array();
		$arrayX = array();
		$r = mysqli_query($mysql,"SELECT * FROM MED_debet WHERE id > '0' ".$dates." ".$citysort." ORDER BY MED_debet.time+0 DESC, MED_debet.id DESC");
			if(!$r) exit(mysqli_error($mysql));
			while	($hk=mysqli_fetch_assoc($r))
			{
			$arrayX[$hk['type_pay']][$hk['stspay']][$hk['id']] = $hk;
			$arraySUM[$hk['type_pay']][$hk['stspay']] = isset($arraySUM[$hk['type_pay']][$hk['stspay']])?$arraySUM[$hk['type_pay']][$hk['stspay']]+$hk['summ']:$hk['summ'];
			}
		mysqli_free_result($r);	
	
	if (isset($_POST['Sbmt_edit']))
	{
		if (!isset($_POST['stspay']))
		{
		die ("<meta http-equiv=refresh content='0; url=?page=money&err=stspay'>");
		}	
	$type_pay = intval($_POST['type_pay']);
	$stspay = intval($_POST['stspay']);
	$summ = mysqli_real_escape_string($mysql,summa_replace($_POST['summ']));
	$time = strtotime($_POST['time']);
	$about = mysqli_real_escape_string($mysql,$_POST['about']);

	$d1 = isset($_POST['d1'])?$_POST['d1']:date('d.m.Y',mktime (0, 0, 0, date('m',$time), 1, date('Y',$time)));
	$d2 = isset($_POST['d2'])?$_POST['d2']:date('d.m.Y',mktime (0, 0, 0, date('m',$time), date("t", mktime (0, 0, 0, date('m',$time), 1, date('Y',$time))), date('Y',$time)));
		
	$queryT = "UPDATE MED_debet SET type_pay='". $type_pay ."',stspay='". $stspay ."',summ='". $summ ."',time='". $time ."',about='". $about ."' WHERE id='".intval($_POST['id'])."' LIMIT 1";
	mysqli_query($mysql,$queryT) or trigger_error(mysqli_error($mysql)." in ".$queryT);
	die ("<meta http-equiv=refresh content='0; url=?page=money&d1=".$d1."&d2=".$d2."&see=true&tp=".$type_pay."&stspay=".$stspay."'>");
	}
	
	if (isset($_POST['Sbmt_start']))
	{
		if (!isset($_POST['stspay']))
		{
		die ("<meta http-equiv=refresh content='0; url=?page=money&err=stspay'>");
		}
	$type_pay = intval($_POST['type_pay']);
	$stspay = intval($_POST['stspay']);
	$summ = mysqli_real_escape_string($mysql,summa_replace($_POST['summ']));
	$time = strtotime($_POST['time']);
	$about = mysqli_real_escape_string($mysql,$_POST['about']);
	
	$query = "INSERT INTO MED_debet (type_pay,stspay,summ,time,about) VALUES('".$type_pay."','".$stspay."','".$summ."','".$time."','".$about."')";
	mysqli_query($mysql,$query) or die(mysqli_error($mysql));
	die ("<meta http-equiv=refresh content='0; url=?page=money'>");
	}
	
	if (isset($_GET['del']))
	{
	$queryD = "DELETE FROM MED_debet WHERE id='".mysqli_real_escape_string($mysql,$_GET['del'])."'";
	mysqli_query($mysql,$queryD) or die(mysqli_error($mysql));		

	die ("<meta http-equiv=refresh content='0; url=?page=money&d1=".$_GET['d1']."&d2=".$_GET['d2']."&see=true&tp=".$_GET['tp']."&stspay=".$_GET['stspay']."'>");
	}	
	
	if (isset($_GET['pay']))
	{
	$stspay = intval($_GET['stspay']);
	$d1 = isset($_GET['d1'])?$_GET['d1']:date('d.m.Y',mktime (0, 0, 0, date('m',$time), 1, date('Y',$time)));
	$d2 = isset($_GET['d2'])?$_GET['d2']:date('d.m.Y',mktime (0, 0, 0, date('m',$time), date("t", mktime (0, 0, 0, date('m',$time), 1, date('Y',$time))), date('Y',$time)));
		
	$queryT = "UPDATE MED_debet SET type_pay='0',time='". time() ."' WHERE id='".intval($_GET['pay'])."' LIMIT 1";
	mysqli_query($mysql,$queryT) or trigger_error(mysqli_error($mysql)." in ".$queryT);
	die ("<meta http-equiv=refresh content='0; url=?page=money&d1=".$d1."&d2=".$d2."'>");
	}

?>
	<div class="col-md-12">
	<div class="clearfix"></div>
	<br />
	<div class="clearfix"></div>
	
	<h2>Приход/Расход</h2>
	
	<a href="?page=money&type_pay=0" class="btn btn-<?php echo (isset($_GET['type_pay']) AND intval($_GET['type_pay']) == 0) ? 'success' : 'default' ; ?> btn-sm"><span class="glyphicon glyphicon-plus"></span> Добавить Приход</a>
	<a href="?page=money&type_pay=1" class="btn btn-<?php echo (isset($_GET['type_pay']) AND intval($_GET['type_pay']) == 1) ? 'success' : 'default' ; ?> btn-sm"><span class="glyphicon glyphicon-plus"></span> Добавить Расход</a>
	<a href="?page=money" class="btn btn-<?php echo isset($_GET['type_pay']) ? 'default' : 'success' ; ?> btn-sm"><span class="glyphicon glyphicon-ok"></span> Общая таблица</a>

	<div class="clearfix mtop"></div>
	
	<?php
	if (isset($_GET['err']))
	{
	?>
	<div class="alert alert-dismissible alert-danger">
		<h3 class="text-center text-danger">Ошибка</h3>
		<strong>Не передан Тип траты</strong><br /> 
		
		<p class="text-center">
			
			<a class="btn btn-primary" href="?page=money"> Хорошо </a>
		</p>
	</div>
	
	<div class="clearfix"></div>
	<?php
	}
	
	if (isset($_GET['delete']))
	{
	?>
	<div class="alert alert-dismissible alert-danger">
		<h3 class="text-center text-danger">Подтверждение удаления записи</h3>
		<strong>Выбранная запись будет удалена из базы</strong><br /> 
		
		<p class="text-center">
			
			<a class="btn btn-danger" href="?page=money&del=<?php echo $_GET['delete']; ?>&d1=<?php echo $_GET['d1']; ?>&d2=<?php echo $_GET['d2']; ?>&see=true&tp=<?php echo $_GET['tp']; ?>&stspay=<?php echo $_GET['stspay']; ?>"><span class="glyphicon glyphicon-remove"></span> Удалить </a>
			
			<a class="btn btn-primary" href="?page=money&d1=<?php echo $_GET['d1']; ?>&d2=<?php echo $_GET['d2']; ?>&see=true&tp=<?php echo $_GET['tp']; ?>&stspay=<?php echo $_GET['stspay']; ?>"> Отмена </a>
		</p>
	</div>
	
	<div class="clearfix"></div>
	<?php
	}	
	
	?>
	
	<?php
	if (isset($_GET['type_pay']))
	{
		if (isset($_GET['edit']))
		{
		$r = mysqli_query($mysql,"SELECT * FROM MED_debet WHERE id = '".intval($_GET['edit'])."' LIMIT 1");
		if(!$r) exit(mysqli_error($mysql));
		$arredit=mysqli_fetch_assoc($r);
		mysqli_free_result($r);
		}
	?>
	<div class="col-md-12 well">
		<h3><?php echo intval($_GET['type_pay']) == 0 ? 'Приход' : 'Расход' ; ?></h3>
		<form class="form-horizontal" action='' method='post'>
			<?php echo '<input name="type_pay" type="hidden" value="'.$_GET['type_pay'].'" />'; ?>
			<?php
			if (isset($arredit['type_pay']))
			{
			?>
			<div class="col-md-6">
				<div class="input-group">
					<label>
						<h3>
						<input <?php echo (isset($arredit['type_pay']) and $arredit['type_pay'] == 0) ? 'checked' : ''; ?> type="radio" name="type_pay" value="0"> 
						Приход
						</h3>
					</label>
				</div>
			</div>
			
			<div class="col-md-6">
				<div class="input-group">
					<label>
						<h3>
						<input <?php echo (isset($arredit['type_pay']) and $arredit['type_pay'] == 1) ? 'checked' : ''; ?> type="radio" name="type_pay" value="1"> 
						Расход
						</h3>
					</label>
				</div>
			</div>
			
			<div class="clearfix"></div>
			<?php
			}
			?>
			
			<div class="col-md-4">
				<select class="form-control input-sm notselect" id="stspay" name="stspay" >
					<option <?php echo !isset($arredit['stspay']) ? 'selected' : ''; ?> disabled>Тип траты</option>
					<?php
					foreach  ($arr_type_pay[0] as $k => $v)
					{
					?>
					<option <?php echo (isset($arredit['stspay']) and $arredit['stspay'] == $k) ? 'selected' : ''; ?> value="<?php echo $k; ?>"><?php echo $k; ?> - <?php echo $v; ?></option>
					<?php
					}
					?>
				</select>
			</div>	
			
			<div class="col-md-4">
				<input class="form-control input-sm" name="summ" type="text" value="<?php echo isset($arredit['summ']) ? $arredit['summ'] : ''; ?>" placeholder="Сумма"  />
			</div>
			
			<div class="col-md-4">
				<input class="form-control input-sm" name="time" type="text" value="<?php echo isset($arredit['time']) ? date('d.m.Y',$arredit['time']) : date('d.m.Y'); ?>" onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)"/>
			</div>
			
			<div class="clearfix mtop"></div>
			
			<div class="col-md-12 mtop">
				<textarea name="about" class="form-control" placeholder="Комментарий" rows="2"><?php echo isset($arredit['about']) ? $arredit['about'] : ''; ?></textarea>
			</div>

			<?php echo isset($arredit) ? '<input name="id" type="hidden" value="'.$arredit['id'].'" />' : ''; ?>
			<?php echo isset($_GET['d1']) ? '<input name="d1" type="hidden" value="'.$_GET['d1'].'" /><input name="d2" type="hidden" value="'.$_GET['d2'].'" />' : ''; ?>
		
			<div class="col-md-12 mtop text-center">
				<button type="submit" class="btn btn-primary" name="<?php echo isset($arredit) ? 'Sbmt_edit' : 'Sbmt_start'; ?>">Сохранить</button>
				
				<a class="btn btn-danger" href="?page=money&d1=<?php echo date('d.m.Y',$d1); ?>&d2=<?php echo date('d.m.Y',$d2); ?>">Отмена</a>

			</div>		
		</form>	
	</div>

	<div class="clearfix"></div>
	<?php
	}
	elseif (isset($_GET['see']))
	{
	?>
	<h3><a class="btn btn-primary" href="?page=money<?php echo isset($_GET['d1'])?'&d1='.$_GET['d1']:''; ?><?php echo isset($_GET['d2'])?'&d2='.$_GET['d2']:''; ?>" name="see"><span class="glyphicon glyphicon-arrow-left"></span> назад </a> <?php echo $arr_type_pay[0][$_GET['stspay']]; ?></h3>
		<table class="table table-hover">
			<tr>
				<th style="min-width:100px;">Сумма</th>
				<th>Дата</th>
				<th>Комментарий</th>
				<th>Управление</th>
			</tr>
			<?php
			$all_summ = 0;
			if (isset($arrayX[$_GET['tp']][$_GET['stspay']]))
			{
				foreach ($arrayX[$_GET['tp']][$_GET['stspay']] as $id => $vol)
				{
				$all_summ += $vol['summ'];
				?>
				<tr>
					<td><?php echo number_format($vol['summ'], 2, '.', ' '); ?></td>
					<td><?php echo date('d.m.Y',$vol['time']); ?></td>
					<td><?php echo $vol['about']; ?></td>
					<td>
						<a class="btn btn-success btn-sm" href="?page=money&edit=<?php echo $vol['id']; ?>&d1=<?php echo $_GET['d1']; ?>&d2=<?php echo $_GET['d2']; ?>&type_pay=<?php echo $_GET['tp']; ?>&stspay=<?php echo $_GET['stspay']; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
						<a class="btn btn-danger btn-sm" href="?page=money&delete=<?php echo $vol['id']; ?>&d1=<?php echo $_GET['d1']; ?>&d2=<?php echo $_GET['d2']; ?>&see=true&tp=<?php echo $_GET['tp']; ?>&stspay=<?php echo $_GET['stspay']; ?>"><span class="glyphicon glyphicon-remove"></span></a>
					</td>
				</tr>
				<?php
				}
			}
			?>
				<tr>
					<th><?php echo number_format($all_summ, 2, '.', ' '); ?></th>
					<th colspan="3"> &larr; Всего</th>
				</tr>
		</table>
	<?php
	}
	else
		{
		?>
		<div class="col-md-6 col-md-offset-3"><a name="date"></a>
			<table class="table table-condensed">
				<tr>
					<td>
						<a class="btn btn-sm btn-success" href="?page=money&d1=<?php echo date('d.m.Y',mktime (0, 0, 0, date('m',$time), 1, date('Y',$time)-1)); ?>&d2=<?php echo date('d.m.Y',mktime (0, 0, 0, date('m',$time), date("t", mktime (0, 0, 0, date('m',$time), 1, date('Y',$time)-1)), date('Y',$time)-1)); ?>#date"><?php echo date('Y',$time)-1; ?></a>
					</td>
					<td>
						<a class="btn btn-sm btn-primary" href="?page=money&d1=<?php echo date('d.m.Y',mktime (0, 0, 0, date('m',$time), 1, date('Y',$time))); ?>&d2=<?php echo date('d.m.Y',mktime (0, 0, 0, date('m',$time), date("t", mktime (0, 0, 0, date('m',$time), 1, date('Y',$time))), date('Y',$time))); ?>#date"><?php echo date('Y',$time); ?></a>
					</td>
					<td>
						<a class="btn btn-sm btn-success" href="?page=money&d1=<?php echo date('d.m.Y',mktime (0, 0, 0, date('m',$time), 1, date('Y',$time)+1)); ?>&d2=<?php echo date('d.m.Y',mktime (0, 0, 0, date('m',$time), date("t", mktime (0, 0, 0, date('m',$time), 1, date('Y',$time)+1)), date('Y',$time)+1)); ?>#date"><?php echo date('Y',$time)+1; ?></a>
					</td>
				</tr>
			</table>
		</div>
		
		<table class="table table-condensed">
			<tr>
			<?php
			foreach ($mounts as $m => $v)
			{
			?>
				<td>
					<a class="btn btn-sm btn-<?php echo (date('m',$time) == $m)?'primary':'success'; ?>" href="?page=money&d1=<?php echo date('d.m.Y',mktime (0, 0, 0, $m, 1, date('Y',$time))); ?>&d2=<?php echo date('d.m.Y',mktime (0, 0, 0, $m, date("t", mktime (0, 0, 0, $m, 1, date('Y',$time))), date('Y',$time))); ?>#date">
						<?php echo $v; ?>
					</a>
				</td>
			<?php
			}
			?>
			</tr>
		</table>
		<div class="clearfix"></div>
		<div class="col-md-6 col-md-offset-3 text-center">
		<?php
		foreach ($city as $k => $v)
		{
		?>
		<a class="label label-<?php echo (isset($_GET['citysort']) AND $_GET['citysort'] == $k)?'success':'default'; ?>" href="?page=money&citysort=<?php echo $k; ?>&d1=<?php echo date('d.m.Y',$d1); ?>&d2=<?php echo date('d.m.Y',$d2); ?>"><span class="glyphicon glyphicon-<?php echo (isset($_GET['citysort']) AND $_GET['citysort'] == $k)?'check':'unchecked'; ?>"></span> <?php echo $v; ?></a>
		<?php
		}
		if (isset($_GET['citysort']))
		{
		?>
		<a class="label label-danger" href="?page=money"><span class="glyphicon glyphicon-remove-sign"></span> Очистить</a>
		<?php
		}
		?>
		</div>
		<div class="clearfix mtop"><br></div>
		
		<div style="border-right:1px solid #ccc;" class="col-md-6">
			<h3 class="text-right">Приход</h3>
			<table class="table table-hover">
			<?php
			$itogo_D = 0;
			foreach  ($arr_type_pay[0] as $k => $v)
			{
			$itogo_D += (isset($arraySUM[0][$k])?$arraySUM[0][$k]:0);
			?>
				<tr>
					<td><a href="?page=money&d1=<?php echo date('d.m.Y',$d1); ?>&d2=<?php echo date('d.m.Y',$d2); ?>&see=true&tp=0&stspay=<?php echo $k; ?>#see"><?php echo $k; ?> - <?php echo $v; ?></a></td>
					<td><?php echo number_format((isset($arraySUM[0][$k])?$arraySUM[0][$k]:0), 2, '.', ' '); ?></td>
				</tr>
			<?php
			}
			?>
				<tr>
					<td class="text-right">Итого</td>
					<td><strong><?php echo number_format($itogo_D, 2, '.', ' '); ?></strong></td>
				</tr>
			</table>
		</div>
		
		<div class="col-md-6">
			<h3 class="text-left">Расход</h3>
			<table class="table table-hover">
			<?php
			$itogo_K = 0;
			foreach  ($arr_type_pay[0] as $k => $v)
			{
			$itogo_K += (isset($arraySUM[1][$k])?$arraySUM[1][$k]:0);
			?>
				<tr>
					<td><a href="?page=money&d1=<?php echo date('d.m.Y',$d1); ?>&d2=<?php echo date('d.m.Y',$d2); ?>&see=true&tp=1&stspay=<?php echo $k; ?>#see"><?php echo $k; ?> - <?php echo $v; ?></a></td>
					<td><?php echo number_format((isset($arraySUM[1][$k])?$arraySUM[1][$k]:0), 2, '.', ' '); ?></td>
				</tr>
			<?php
			}
			?>
				<tr>
					<td class="text-right">Итого</td>
					<td><strong><?php echo number_format($itogo_K, 2, '.', ' '); ?></strong></td>
				</tr>
			</table>
		</div>
		
		<div class="clearfix mtop"></div>
		
		<?php
		$ri = mysqli_query($mysql,"SELECT SUM(summ) FROM MED_debet WHERE type_pay='0' ".$citysort." ") OR exit(mysqli_error($mysql));
		$ro = mysqli_query($mysql,"SELECT SUM(summ) FROM MED_debet WHERE type_pay='1' ".$citysort." ") OR exit(mysqli_error($mysql));
		$incom = mysqli_fetch_array($ri, MYSQLI_NUM);
		$outcom = mysqli_fetch_array($ro, MYSQLI_NUM);
		mysqli_free_result($ri);
		mysqli_free_result($ro);
		?>
		
		<div class="col-md-6 col-md-offset-3">
			<table class="table table-hover">
				<tr>
					<td style="width:50%;">Баланс в этом месяце<br><small class="help-block">Приход-Расход</small></td>
					<td style="width:50%;">Текущий баланс<br><small class="help-block">Приход за все время-Расход за все время</small></td>
				</tr>
				<tr>
					<th><?php echo number_format($itogo_D-$itogo_K, 2, '.', ' '); ?></th>
					<th><?php echo number_format($incom[0]-$outcom[0], 2, '.', ' '); ?></th>
				</tr>
			</table>
		</div>
		<div class="clearfix mtop"></div>
		
		<div style="border-right:1px solid #ccc;" class="col-md-6">
			<table class="table table-hover">
				<tr>
					<th style="width:80px;">Сумма</th>
					<th>№ заявки</th>
					<th>№ клиента</th>
					<th>Дата</th>
					<th>Комментарий</th>
				</tr>
				<?php
// 				"SELECT * FROM MED_debet WHERE type_pay='0' ".$dates." ORDER BY MED_debet.id DESC"
				$SQL ="
						SELECT
							MED_debet.*
						FROM
							MED_debet	

						WHERE 
							MED_debet.type_pay='0' ".$dates."
						ORDER 
							BY MED_debet.id DESC";
// 				$SQL ="SELECT * FROM MED_debet WHERE type_pay='0' ".$dates." ORDER BY MED_debet.id DESC";

				$r = mysqli_query($mysql,$SQL);
					if(!$r) exit(mysqli_error($mysql));
					while	($hk=mysqli_fetch_assoc($r))
					{
					
					?>
					<tr style="font-size:12px;" class="<?php echo (isset ($_GET['znach']) AND $_GET['znach']== $hk['id'])?'success':'';?>">
						<td>
							<a name="<?php echo $hk['id']; ?>" href="?page=money&edit=<?php echo $hk['id']; ?>&d1=<?php echo date('d.m.Y',$d1); ?>&d2=<?php echo date('d.m.Y',$d2); ?>&type_pay=<?php echo $hk['type_pay']; ?>&stspay=<?php echo $hk['stspay']; ?>"><?php echo $hk['summ'] > 0 ? number_format($hk['summ'], 2, '.', ' ') : 0; ?></a>
						</td>
						<td>
							<?php
							if ($hk['zayavka_id'] > '0')
							{
							?>
							<a href="?page=zayavka&add=true&edit=<?php echo $hk['zayavka_id'];?>&city=<?php  echo $hk['city_id'];?>&client_id=<?php echo $hk['client_id']; ?>"> <?php echo $hk['zayavka_id'];?></a>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if ($hk['client_id'] > '0')
							{
							?>
							<a href="?page=client&add=true&edit=<?php echo $hk['client_id']; ?>"> <?php echo $hk['client_id'];?></a>
							<?php
							}
							?>
						</td>						
						<td><?php echo date('d.m.y',$hk['time']); ?></td>
						<td><?php echo $hk['about']; ?></td>
					</tr>
					<?php
					}
				mysqli_free_result($r);
				?>
			</table>
		</div>
		
		<div class="col-md-6">
			<table class="table table-hover">
				<tr>
					<th style="width:80px;">Сумма</th>
					<th>Дата</th>
					<th>Комментарий</th>
				</tr>
				<?php
				$r = mysqli_query($mysql,"SELECT * FROM MED_debet WHERE type_pay='1' ".$dates." ORDER BY MED_debet.id DESC");
					if(!$r) exit(mysqli_error($mysql));
					while	($hk=mysqli_fetch_assoc($r))
					{
					?>
					<tr style="font-size:12px;">
						<td><a href="?page=money&edit=<?php echo $hk['id']; ?>&d1=<?php echo date('d.m.Y',$d1); ?>&d2=<?php echo date('d.m.Y',$d2); ?>&type_pay=<?php echo $hk['type_pay']; ?>&stspay=<?php echo $hk['stspay']; ?>"><?php echo number_format($hk['summ'], 2, '.', ' '); ?></a></td>
						<td><?php echo date('d.m.Y',$hk['time']); ?></td>
						<td><?php echo $hk['about']; ?></td>
					</tr>
					<?php
					}
				mysqli_free_result($r);
				?>
			</table>
		</div>

	<?php
		}
	?>
	</div>
