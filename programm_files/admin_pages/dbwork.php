<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."inter.php?login'>");
	}
	

    
	$backsts = array('0' => 'Запланированный системой','1' => 'Сохранение -> Завка','2' => 'Сохранение -> другой результат', '3' => 'Удаление');

	if (isset($_GET['mess']))
	{
	?>
	<div class="alert alert-dismissible alert-success text-center">
		<strong>Успешно!</strong><br />
		<a class="btn btn-primary" href="?page=creator" class="alert-link">Хорошо</a>
	</div>	
	<?php
	}

	if(file_exists(ABSOLUTE__PATH__ . '/programm_files/backup.txt'))
	{
	$file = file(ABSOLUTE__PATH__ . '/programm_files/backup.txt');
	$set = explode('|',$file[0]);
	unset($file[0]);
		foreach ($file as $line)
		{
		$line = trim($line);
		$set_tables[$line] = $line;
		}
	}
	
	$set = isset($set)?$set:array('30','50','');
	
// 		echo '<pre>';
// 		print_r($set_tables);
// 		echo '</pre>';	
	
	if (isset($_POST['Submit_sett']))
	{
		$day = isset($_POST['day'])?intval($_POST['day']):30;
		$cntf = isset($_POST['cntf'])?intval($_POST['cntf']):30;
		$mail = isset($_POST['mail'])?trim($_POST['mail']):'';
		$saveses = $day.'|'.$cntf.'|'.$mail."\r\n";
		if (isset($_POST['table']))
		{
			foreach ($_POST['table'] as $i => $tables)
			{
			$saveses .= $tables."\r\n";
			}
		}
		$f = fopen(ABSOLUTE__PATH__ . '/programm_files/backup.txt', 'w');
		fputs ($f,$saveses);	
		fclose($f);
	die ("<meta http-equiv=refresh content='0; url=?page=dbwork&mes=0".(isset($_GET['dir'])?'&dir='.$_GET['dir']:'')."'>");
	}
	
	$dir_backup = ABSOLUTE__PATH__.'/programm_files/backup';
	deletefiledayDB ($dir_backup,$set[0]);
	deletefilecntDB ($dir_backup,$set[1]);
	
	if (isset($_GET['create']))
	{
	$set_tables = isset($set_tables)?$set_tables:false;
	$doBackupDB = backupDB(ABSOLUTE__PATH__.'/programm_files/backup/', 'bot_0_'.date('d-m-Y-H-i-s'),$set_tables); 
	
// 	$dir_backup = ABSOLUTE__PATH__.'/';
// 	$d = recursedir ($dir_backup);
// 	
// 	$files = array();
// 	foreach ($d as $k => $v)
// 	{
// 		if (is_array($v))
// 		{
// 			foreach ($v as $kk => $vv)
// 			{
// 				if (is_array($vv))
// 				{
// 					foreach ($vv as $kkk => $vvv)
// 					{
// 						if (is_array($vvv))
// 						{
// 						
// 						}
// 						else
// 							{
// 							$files[] = $dir_backup.'/'.$k.'/'.$kk.'/'.$vvv;
// 							//echo $dir_backup.'/'.$k.'/'.$kk.'/'.$vvv.'<br>';
// 							}
// 					}
// 				}
// 				else	
// 					{
// 					$files[] = $dir_backup.'/'.$k.'/'.$vv;
// 					//echo $dir_backup.'/'.$k.'/'.$vv.'<br>';
// 					}
// 			}
// 		}
// 		else
// 			{
// 			$files[] = $dir_backup.'/'.$v;
// 			//echo $dir_backup.'/'.$v.'<br>';
// 			}
// 	}
// 	
// 	shell_exec('cd '.ABSOLUTE__PATH__.'/programm_files/ && tar -cvf '.ABSOLUTE__PATH__.'/programm_files/backup/point/filesys/archive_'.date('d-m-Y-H-i-s').'.tar.gz ' . implode(' ', $files));
	
	die ("<meta http-equiv=refresh content='0; url=?page=dbwork&mes=0'>");
	}
	if (isset($_GET['restore_true']))
	{
	restoreDB (ABSOLUTE__PATH__.'/programm_files/backup/'.$_GET['restore_true']);
	die ("<meta http-equiv=refresh content='0; url=?page=dbwork&mes=0".(isset($_GET['dir'])?'&dir='.$_GET['dir']:'')."'>");
	}
	
	if (isset($_GET['del']))
	{
		$dir_backup = isset($_GET['dir'])?$dir_backup.'/'.$_GET['dir']:$dir_backup;
		if (file_exists($dir_backup.'/'.$_GET['del']) AND getExtension1($_GET['del']) == 'gz')
		{
		unlink($dir_backup.'/'.$_GET['del']);
		echo 'DEL!';
		}
	//die ("<meta http-equiv=refresh content='0; url=?page=dbwork&mes=0".(isset($_GET['dir'])?'&dir='.$_GET['dir']:'')."'>");
	}
	
	if (isset($_GET['save']))
	{
	copy($dir_backup.'/'.$_GET['save'], $dir_backup.'/point/'.$_GET['save']);
	die ("<meta http-equiv=refresh content='0; url=?page=dbwork&mes=0".(isset($_GET['dir'])?'&dir='.$_GET['dir']:'')."'>");
	}
?>
	<div class="col-md-12">
	<h3>Резервные копии</h3>

	<a class="btn btn-success" href="?page=dbwork&create=1">Сделать резервную копию</a>
	<a class="btn btn-success" href="?page=dbwork&settings=1">Настройки</a>
	
	<div class="clearfix"></div>
	<br />
	<?php

	if (isset($_GET['mes']))
	{
	?>
	<div class="alert alert-info text-center col-md-8 col-md-offset-2" role="alert">
		<h3>Операция прошла успешно</h3>
		<a class="btn btn-success btn-sm" href="?page=dbwork">Хорошо</a>
	</div>
	<div class="clearfix"></div>
	<?php
	}
	
	if (isset($_GET['delete']))
	{
	$dir_backup1 = isset($_GET['dir'])?$dir_backup.'/'.$_GET['dir']:$dir_backup;
	?>
	<div class="alert alert-danger text-center" role="alert">
		<h4>Вы собираетесь удалить файл восстановления базы данных <strong><?php echo date('d.m.Y H:i',filemtime($dir_backup1.'/'.$_GET['delete'])); ?></strong></h4>
		<a class="btn btn-success btn-sm" href="?page=dbwork&del=<?php echo $_GET['delete']; ?><?php echo isset($_GET['dir'])?'&dir='.$_GET['dir']:''; ?>">Удалить</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="btn btn-danger btn-sm" href="?page=dbwork">Отменить</a>
	</div>
	<?php
	}
	if (isset($_GET['restore']))
	{
	$dir_backup1 = isset($_GET['dir'])?$dir_backup.'/'.$_GET['dir']:$dir_backup;
	?>
	<div class="alert alert-warning text-center" role="alert">
		<h4>Вы собираетесь восстановить файл восстановления базы данных от <strong><?php echo date('d.m.Y H:i',filemtime($dir_backup1.'/'.$_GET['restore'])); ?></strong></h4>
		<a class="btn btn-success btn-sm" href="?page=dbwork&restore_true=<?php echo $_GET['restore']; ?><?php echo isset($_GET['dir'])?'&dir='.$_GET['dir']:''; ?>">Восстановить</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="btn btn-danger btn-sm" href="?page=dbwork">Отменить</a>
	</div>
	<?php
	}


	if (isset($_GET['settings']))
	{
	?>
	<div class="alert alert-info col-md-8 col-md-offset-2">
		<form class="form-horizontal" method="POST">
			<fieldset>
				<legend>Настройки</legend>	
				<div class="form-group">
					<label for="page" class="col-lg-2 control-label">Дни</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="day" value="<?php echo $set[0]; ?>">
							<span class="help-block">Период хранения файлов восстановления в днях</span>
						</div>
					</label>	
				</div>
				
				<div class="form-group">
					<label for="page" class="col-lg-2 control-label">Файлы</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="cntf" value="<?php echo $set[1]; ?>">
							<span class="help-block">Общее кол-во файлов восстановления</span>
						</div>
					</label>	
				</div>
				
				<div class="form-group">
					<label for="page" class="col-lg-2 control-label">Электронка</label>
						<div class="col-lg-10">
							<input type="text" class="form-control" name="mail" value="<?php echo $set[2]; ?>">
							<span class="help-block">Укажите электронку, что бы система еще и на почту отсылала backup</span>
						</div>
					</label>	
				</div>				
				
				<div class="clearfix mtop"></div>
				<table class="table table-hover table-bordered mtop">
					<tr>
						<th>Таблица</th>
						<th><a href="javascript:void(0);" id="check_all">Выбор</a></th>
					</tr>
					<?php
					foreach (get_database_tables($mysql) AS $key => $vol)
					{
					?>
					<tr>
						<td><?php echo $vol; ?></td>
						<td><input <?php echo (isset($set_tables) AND isset($set_tables[$vol])) ? 'checked' : ''; ?> class="checkbox" type="checkbox" name="table[]" value="<?php echo $vol; ?>"></td>
					</tr>
					<?php
					}
					?>
				</table>				
				<div class="clearfix mtop"></div>

				<div class="form-group">
					<div class="col-lg-10 col-lg-offset-2">
						<button type="submit" class="btn btn-success btn-sm" name="Submit_sett"><span class="glyphicon glyphicon-ok"></span>  Сохранить настройки </button>
						
						<a class="btn btn-danger btn-sm" href="?page=dbwork">Закрыть</a>
					</div>
				</div>
				
			</fieldset>
		</form>	
	</div>
	<?php
	}
	?>
	<div class="clearfix mtop"></div>
	
	<ul class="breadcrumb">
		<li <?php echo !isset($_GET['dir'])?'class="active"':''; ?> ><?php echo !isset($_GET['dir'])?'Основной каталог':'<a href="?page=dbwork">Основной каталог</a>'; ?></li>
		<li <?php echo isset($_GET['dir'])?'class="active"':''; ?> ><?php echo isset($_GET['dir'])?'Важные файлы восстановления':'<a data-toggle="tooltip" data-placement="top" title="Из этой директории файлы возможно удалить только вручную" href="?page=dbwork&dir=point">Важные файлы восстановления</a>'; ?></li>
	</ul>
	
	<table class="table table-condensed table-hover">
		<tr>
			<th>Пользователь</th>
			<th>Описание</th>
			<th>Дата</th>
			<th>Размер</th>
			<th>Управление</th>
		</tr>
	<?php

		$diff = array('..', '.', '.htaccess','point','filesys');
		if (isset($_GET['dir']))
		{
		unset($diff[3]);
		$dir_backup = $dir_backup.'/'.$_GET['dir'];
		}
		$scanned_directory = array_diff(scandir($dir_backup), $diff);
		
		$new_array = array();
		foreach ($scanned_directory as $file)
		{
		$new_array[filemtime($dir_backup.'/'.$file)] = array('time' => filemtime($dir_backup.'/'.$file),'file' => $file);
		}
		unset($scanned_directory);
		rsort($new_array);
		//$scanned_directory = array_reverse($scanned_directory);
// 		$order = 'asc';
// 		$s = sortt($array,$order);
		
			$d = 0;
		foreach ($new_array as $key => $vol)
		{
		$file = $vol['file'];
		$size = isset($size)?$size+filesize($dir_backup.'/'.$file):filesize($dir_backup.'/'.$file);
		$oo = explode('_',$file);

		?>
		<tr <?php echo date('d.m.Y',filemtime($dir_backup.'/'.$file)) == date('d.m.Y')?'class="success"':''; ?> >
			<td><?php echo isset($user[$oo[0]])?$user[$oo[0]]['name']:$oo[0]; ?></td>
			<td><?php echo isset($backsts[transnames($oo[1])])?$backsts[transnames($oo[1])]:$oo[1]; ?></td>
			<td><?php echo date('d.m.Y H:i',filemtime($dir_backup.'/'.$file)); ?></td>
			<td><?php echo format_size(filesize($dir_backup.'/'.$file)); ?></td>
			<td>
				<a title="Восстановить" data-toggle="tooltip" data-placement="top" class="btn btn-success btn-sm" href="?page=dbwork&restore=<?php echo $file; ?><?php echo isset($_GET['dir'])?'&dir='.$_GET['dir']:''; ?>"><span class="glyphicon glyphicon-ok"></span></a>
				
				<a title="Удалить" data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-sm" href="?page=dbwork&delete=<?php echo $file; ?><?php echo isset($_GET['dir'])?'&dir='.$_GET['dir']:''; ?>"><span class="glyphicon glyphicon-remove"></span></a>
				
				<?php echo !isset($_GET['dir'])?'<a title="Сохранить в важных файлах" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm" href="?page=dbwork&save='.$file.'"><span class="glyphicon glyphicon-floppy-saved"></span></a>':''; ?>
				
				<a target="_blank" title="Скачать к себе на компбютер" data-toggle="tooltip" data-placement="top" class="btn btn-info btn-sm" href="http://<?php echo $HTTP_HOST; ?>/programm_files/get_files.php?sql=<?php echo base64_encode($file); ?><?php echo isset($_GET['dir'])?'&dir='.$_GET['dir']:''; ?>"><span class="glyphicon glyphicon-floppy-save"></span></a>
			</td>
		</tr>
		<?php
			$d++;
		unset($file);
		}	
	?>
		<tr>
			<th colspan="3">Всего файлов <?php echo $d; ?></th>
			<th><?php echo isset($size)?format_size($size):0; ?></th>
			<th></th>
		</tr>
	</table>
	
	</div>
	<?php
	//shell_exec('mysql -u' . $config['db_username'] . ' -p' . $config['db_password'] . ' -h' . $config['db_hostname'] . ' --default-character-set=utf8 --force ' . $config['db_name'] . ' < '.ABSOLUTE__PATH__.'/programm_files/backup/08-11-2016_08-17-02.sql');
	
	//$doBackupDB = backupDB(ABSOLUTE__PATH__.'/programm_files/backup/', date('d-m-Y_H-i-s')); 

	//restoreDB (ABSOLUTE__PATH__.'/programm_files/backup/08-11-2016_08-17-02.sql');
// 	echo '<pre>';
// 	echo shell_exec('du -h '.ABSOLUTE__PATH__);
// 	echo '</pre>';



function recursedir ($dir)	
{
	$diff = array('..', '.', '.htaccess','images','backup','files');
	$scanned_directory = array_diff(scandir($dir), $diff);
	$res=array();
	$files=array();
	foreach ($scanned_directory as $v)
	{
		if (is_dir($dir.'/'.$v))
		{
		$res[$v] = recursedir ($dir.'/'.$v);
		}
		else
			{
			$res[] = $v;
			}
	}
	
	return $res;
}

function transnames($admin)
{
return str_replace('_','',translit($admin));
}
?>




