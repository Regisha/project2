<?php 
// 	header("Content-Type:text/xml;charset=utf-8");
// 	ini_set("max_execution_time", "3600");
// 	set_time_limit (3600);
// 	error_reporting(E_ALL);
	define('ABSOLUTE__PATH__',$_SERVER['DOCUMENT_ROOT']);
	define('SITE__PATH__','http://'.$_SERVER['HTTP_HOST']);
	define( '__PANEL__BOARD__', 1 );
	

	$on_l=fopen(ABSOLUTE__PATH__.'/test.srz', 'w');
	fputs($on_l,date('d.m.Y H:i:s'));
	fclose($on_l);
	
	include_once(ABSOLUTE__PATH__.'/programm_files/mysql.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/functions.php');
	

	$r = mysqli_query($mysql,"SELECT * FROM MED_send_options LIMIT 1");
	if(!$r) exit(mysqli_error());
	$smsso=mysqli_fetch_assoc($r);
	mysqli_free_result($r);	
	
	if (isset($smsso['status']) AND $smsso['status'] == 1)
	{
	$r = mysqli_query($mysql,"SELECT * FROM MED_send_settings LIMIT 1");
	if(!$r) exit(mysqli_error());
	$smsc=mysqli_fetch_assoc($r);
	mysqli_free_result($r);	
	
	include_once(ABSOLUTE__PATH__.'/programm_files/smsc_api.php');
	
	$SQL = "
			SELECT 
				MED_send_ochered.*,
				MED_sidelka.fio_sid,
				MED_sidelka.tel_sid,
				MED_client.fio_zakaz,
				MED_client.tel_zakaz,
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
			WHERE
				MED_send_ochered.status='0'
			GROUP
				BY MED_send_ochered.id";
		$params = '';				
		$r = mysqli_query($mysql,$SQL);
		if(!$r) exit(mysqli_error($mysql));
		while	($hk=mysqli_fetch_assoc($r))
		{
		$sms = isset($smsso['sms'.$hk['status_sms']])?$smsso['sms'.$hk['status_sms']]:$hk['status_sms'];
		$sms = str_replace('[sidelka]',$hk['fio_sid'],$sms);
		$sms = str_replace('[tel_sidelka]',$hk['tel_sid'],$sms);
		$sms = str_replace('[client]',$hk['fio_zakaz'],$sms);
		
		$sms = str_replace('[data]',(date('d.m.Y',($hk['data_opl']+86400))),$sms);
		
		$tel = tel_replace($hk['tel_zakaz']);
		$params .= $tel.":".$sms."\n";
		
		send_sms('','',0,0,0,0,translit($smsc['login']),"list=".urlencode($params));	
		
		$query_count = "UPDATE MED_send_ochered SET status='1' WHERE MED_send_ochered.id='".$hk['id']."' LIMIT 1";
		mysqli_query($mysql,$query_count) or trigger_error(mysqli_error($mysql)." in ".$query_count);
		}
	mysqli_free_result($r);
	
// 		if (!empty($params))
// 		{
// 		send_sms('','',0,0,0,0,translit($smsc['login']),"list=".urlencode($params));	
// 		}
	}
	
	$on_l=fopen(ABSOLUTE__PATH__.'/test_sms_send.srz', 'w');
	fputs($on_l,date('d.m.Y H:i:s'));
	fclose($on_l);
	
?>

