<?php
	header("Content-Type:text/html;charset=utf-8");
	error_reporting(E_ALL);
	session_start();

	
    if (!defined('__PANEL__BOARD__'))
    {
	define('ABSOLUTE__PATH__',$_SERVER['DOCUMENT_ROOT']);
	define( '__PANEL__CORE__PATH__', ABSOLUTE__PATH__.'/programm_files' );
	define('__PANEL__BOARD__',true);
    }

		// Задействовать файл безопасности
	include_once(ABSOLUTE__PATH__.'/programm_files/functions.php');
	include_once(ABSOLUTE__PATH__.'/programm_files/mysql.php');	
	

	
	if (isset($_REQUEST['type']) AND $_REQUEST['type'] == 'backup')
	{
	$backsts = isset($_GET['backsts'])?intval($_GET['backsts']):0;
	$user_id = isset($_GET['user_id'])?intval($_GET['user_id']):'bot';
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
			$set_tables = isset($set_tables)?$set_tables:false;
			$date = date('d-m-Y-H-i-s');
			$doBackupDB = backupDB(ABSOLUTE__PATH__.'/programm_files/backup/', $user_id.'_'.$backsts.'_'.$date,$set_tables); 	
			}
			
		if (isset($set[2]) AND !empty($set[0]))
		{
		// пример использования
		$file = $doBackupDB; // файл
		$mailTo = trim($set[2]); // кому
		$from = trim($set[2]); // от кого
		$subject = "Backup file ".$date; // тема письма
		$message = "Файл от ".$date; // текст письма
		$r = sendMailAttachment($mailTo, $from, $subject, $message, $file); // отправка письма c вложением
		//echo ($r)?'Письмо отправлено':'Ошибка. Письмо не отправлено!';
		//$r = sendMailAttachment($mailTo, $from, $subject, $message); // отправка письма без вложения
		//echo ($r)?'Письмо отправлено':'Ошибка. Письмо не отправлено!';
		}
	
	}
	
	if (isset($_POST['type']) AND $_POST['type'] == 'time')
	{
	echo date('d') .' '. rus_date('F',time()) .' '. date('Y') .' '. date('H:i:s');
	}
	

/**
* Отправка письма с вложением
* @param string $mailTo
* @param string $from
* @param string $subject
* @param string $message
* @param string|bool $file - не обязательный параметр, путь до файла
* 
* @return bool - результат отправки
*/
 
function sendMailAttachment($mailTo, $from, $subject, $message, $file = false){
    $separator = "---"; // разделитель в письме
    // Заголовки для письма
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: $from\nReply-To: $from\n"; // задаем от кого письмо
    $headers .= "Content-Type: multipart/mixed; boundary=\"$separator\""; // в заголовке указываем разделитель
    // если письмо с вложением
    if($file){
        $bodyMail = "--$separator\n"; // начало тела письма, выводим разделитель
        $bodyMail .= "Content-type: text/html; charset='utf-8'\n"; // кодировка письма
        $bodyMail .= "Content-Transfer-Encoding: quoted-printable"; // задаем конвертацию письма
        $bodyMail .= "Content-Disposition: attachment; filename==?utf-8?B?".base64_encode(basename($file))."?=\n\n"; // задаем название файла
        $bodyMail .= $message."\n"; // добавляем текст письма
        $bodyMail .= "--$separator\n";
        $fileRead = fopen($file, "r"); // открываем файл
        $contentFile = fread($fileRead, filesize($file)); // считываем его до конца
        fclose($fileRead); // закрываем файл
        $bodyMail .= "Content-Type: application/octet-stream; name==?utf-8?B?".base64_encode(basename($file))."?=\n"; 
        $bodyMail .= "Content-Transfer-Encoding: base64\n"; // кодировка файла
        $bodyMail .= "Content-Disposition: attachment; filename==?utf-8?B?".base64_encode(basename($file))."?=\n\n";
        $bodyMail .= chunk_split(base64_encode($contentFile))."\n"; // кодируем и прикрепляем файл
        $bodyMail .= "--".$separator ."--\n";
    // письмо без вложения
    }else{
        $bodyMail = $message;
    }
    $result = mail($mailTo, $subject, $bodyMail, $headers); // отправка письма
    return $result;
}
?>