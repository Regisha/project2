<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php?login'>");
	}
	

	?>
	
	<div class="col-md-12">
	<h3 class="page-header">Главная</h3>
		<div class="col-md-8">
		<?php
		if (in_array($_SESSION['__ID__'],$super_admin))
		{
		?>
			<h3>Активность пользователей</h3>
			<table class="table table-hover">
				<tr>
					<th>Статус</th>
					<th>user</th>
					<th>Активность</th>
					<th>Где</th>
				</tr>
			<?php
			foreach (file(ABSOLUTE__PATH__.'/programm_files/online.srz') as $line)
			{
			$line = trim($line);
			$exp = explode ('|',$line);
				if (($exp[1]+2592000) > time())
				{
				?>
				<tr class="<?php echo (($exp[1]+1200) > time()) ? 'success' : 'danger'; ?>">
					<td><?php echo (($exp[1]+1200) > time()) ? '<b class="text-success">online</b>' : '<b class="text-danger">offline</b>'; ?></td>
					<td>
						<a href="?page=worker&see=<?php echo $exp[0]; ?>">
							<?php echo isset($user[$exp[0]])?$user[$exp[0]]['name']:$exp[0]; ?>
						</a>
					</td>
					<td><?php echo date('H:i, d.m.Y', $exp[1]); ?></td>
					<td>
						<a href="<?php echo !empty($exp[2]) ? $exp[2] : '?page=main'; ?>">
							<?php echo !empty($exp[3]) ? $exp[3] : $exp[2]; ?>
						</a>
					</td>
				</tr>
			<?php
				}
			}
			?>
			</table>
		<?php
		}
		?>
		</div>
		
		<div class="col-md-4">
			
			<script>var calendru_c='';var calendru_mc='';var calendru_dc='';var calendru_c_all='';var calendru_n_l=1;var calendru_n_s=0;var calendru_n_d=0;var calendru_i_f=1;var calendru_show_names = 0;</script><script src=http://www.calend.ru/img/export/informer_new_theme1u.js?></script>
			<div class="clearfix"></div>
		</div>
		
		<div class="clearfix"></div>
		
		<div class="col-md-4">
			<h3><small>Дата и Время на сервере:</small> <?php echo date('d.m.Y - H:i'); ?></h3>
			<div class="clearfix"></div>
		</div>
		
		<div class="col-md-8">
			<h3>Завтра необходимо оплатить</h3>
			<table class="table table-hover mtop">
				<tr>
					<th>№ Заявки</th>
					<th>Клиент</th>
					<th>Дата</th>
				</tr>
				<?php
				$SQL = "SELECT * FROM MED_client WHERE data_opl>='" .(strtotime(date('d.m.Y'))+86400). "' AND data_opl<='" .(strtotime(date('d.m.Y'))+(86400*2)). "'";
							
				$r = mysqli_query($mysql,$SQL);
					if(!$r) exit(mysqli_error($mysql));
					while	($arr=mysqli_fetch_assoc($r))
					{
						if ($arr['zayavka_id'] > 0)
						{
							$check_send_sms = check_send_sms ($mysql,$arr['zayavka_id']);
							if ($check_send_sms == false OR !isset($check_send_sms[3]))
							{
							$insertSQL = mysqli_query($mysql, "INSERT INTO MED_send_ochered (admin_id,zayavka_id,time,status_sms,status) VALUES ('".intval($_SESSION['__ID__'])."','".$arr['zayavka_id']."','".time()."','3','0')");
							if(!$insertSQL) die(trigger_error(mysqli_error($mysql)." in ".$insertSQL));
							}
						}
					?>
					<tr>
						<td><?php echo $arr['zayavka_id'] == 0 ? 'Не в заявке' : $arr['zayavka_id']; ?></td>
						<td><a href="?page=client&add=true&edit=<?php echo $arr['client_id']; ?>"><?php echo $arr['fio_zakaz']; ?></a></td>
						<td><?php echo date('d.m.Y',$arr['data_opl']); ?></td>
					</tr>
					<?php
					}
				mysqli_free_result($r);
				?>
			</table>
		</div>
		
		<div class="clearfix mtop"></div>
		<?php
		$r = mysqli_query($mysql,"SELECT * FROM MED_send_options LIMIT 1");
		if(!$r) exit(mysqli_error());
		$smsc=mysqli_fetch_assoc($r);
		mysqli_free_result($r);	
		?>
		<h3>Запланированная отправка смс <small><?php echo (isset($smsc['status']) AND $smsc['status'] == 1) ? '<span class="text-success">Рассылка разрешена</span>' : '<span class="text-danger">Рассылка запрещена</span>'; ?></small></h3>
		<table class="table table-hover mtop">
			<tr>
				<th>№ Заявки</th>
				<th>СМС</th>
				<th>Статус рассылки</th>
				<th>Дата</th>
			</tr>
			<?php
				$SQL = "
					SELECT 
							MED_send_ochered.*,
							MED_sidelka.fio_sid,
							MED_sidelka.tel_sid,
							MED_client.fio_zakaz,
							MED_client.data_opl
					FROM 
						MED_send_ochered 
					LEFT JOIN  
						MED_sidelka
					ON 
						MED_send_ochered.zayavka_id=MED_sidelka.zayavka_id AND MED_sidelka.status='1'
					LEFT JOIN  
						MED_client
					ON 
						MED_send_ochered.zayavka_id=MED_client.zayavka_id
					GROUP
						BY MED_send_ochered.id
					ORDER
						BY MED_send_ochered.id DESC,MED_send_ochered.status+0 DESC LIMIT 50";
						
			$r = mysqli_query($mysql,$SQL);
				if(!$r) exit(mysqli_error($mysql));
				while	($hk=mysqli_fetch_assoc($r))
				{
				$sms = isset($smsc['sms'.$hk['status_sms']])?$smsc['sms'.$hk['status_sms']]:$hk['status_sms'];
				$sms = str_replace('[sidelka]',$hk['fio_sid'],$sms);
				$sms = str_replace('[tel_sidelka]',$hk['tel_sid'],$sms);
				$sms = str_replace('[client]',$hk['fio_zakaz'],$sms);
				$sms = str_replace('[data]',date('d.m.Y',$hk['data_opl']+(86400*30)),$sms);
				?>
				<tr>
					<td><?php echo $hk['zayavka_id']; ?></td>
					<td><?php echo $sms; ?></td>
					<td><?php echo $hk['status'] == 0 ? '<span class="text-danger">Ожидает</span>' : '<span class="text-success">Разосланно</span>'; ?></td>
					<td><?php echo date('d.m.Y H:i',$hk['time']); ?></td>
				</tr>
				<?php
				}
			mysqli_free_result($r);
			?>
		</table>
	</div>
