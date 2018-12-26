<?php
	if (!defined('ABSOLUTE__PATH__'))
	{
	header('Location: 404.php');
	exit;
	}
	
function repl_txt_sms ($txt)
{
	$txt = trim($txt);
	$txt = str_replace("\r\n", "", $txt);
	$txt = str_replace("№", "N", $txt);
	$txt = str_replace("ё", "$", $txt);
	$txt = str_replace("Ё", "$", $txt);
	$txt = str_replace("~", "$", $txt);

	return $txt;
}

    function privet()
    {
       $h = date("H");
       // от 5-и до 11-и утра, return возвращает Доброе утро
       if ($h>=5 && $h<=11)  return "Доброе утро!  ";
       // от 12-и до 16-и Здравствуйте
       if ($h>=12 && $h<=16) return "Добрый день! ";
       // от 17-и до 24-х часов Добрый вечер
       if ($h>=17 && $h<=24) return "Добрый вечер! ";
       // от 0 до 4-х утра Доброй ночи.
       if ($h>=0 && $h<=4)   return "Доброй ночи!  ";
    }
function curentDay ($mktime)
{
	$mounts = array ('01' => 'января','02' => 'февраля','03' => 'марта','04' => 'апреля','05' => 'мая','06' => 'июня','07' => 'июля','08' => 'августа','09' => 'сентября','10' => 'октября','11' => 'ноября','12' => 'декабря');

	if (!is_numeric($mktime))
	{
	$mktime = time();
	}

	if (date("Y-m-d",$mktime) == date("Y-m-d",time()))
	{
	$return = 'Сегодня';
	}
	elseif (date("Y-m-d",$mktime) == date("Y-m-d",(time()-86400)))
	{
	$return = 'Вчера';
	}
	else
		{
		$return = date("d",$mktime).' '.$mounts[date("m",$mktime)];
		}
	return $return;
}

function filesize_get($filesize)
{
   // Если размер переданного в функцию файла больше 1кб
   if($filesize > 1024)
   {
       $filesize = ($filesize/1024);
       // если размер файла больше одного килобайта
       // пересчитываем в мегабайтах
       if($filesize > 1024)
       {
            $filesize = ($filesize/1024);
           // если размер файла больше одного мегабайта
           // пересчитываем в гигабайтах
           if($filesize > 1024)
           {
               $filesize = ($filesize/1024);
               $filesize = round($filesize, 1);
               return $filesize." ГБ";

           }
           else
           {
               $filesize = round($filesize, 1);
               return $filesize." MБ";
           }

       }
       else
       {
           $filesize = round($filesize, 1);
           return $filesize." Кб";
       }

   }
   else
   {
       $filesize = round($filesize, 1);
       return $filesize." байт";
   }

}

function summa_replace ($sum)
{
	$sum = preg_replace ("/[^0-9.,]/","",$sum);
	return str_replace(',','.',$sum);
}

function getExtension1($filename) 
{
$e = explode(".", $filename);
return end($e);
}
		function downcounter($date,$time){
		    $time = $time >= time() ? time() : ($time <= $date?time():$time);
		    $check_time = strtotime($date) - $time;
		    if($check_time <= 0){
		        return '-';
		    }

		    $days = floor($check_time/86400);
		    $hours = floor(($check_time%86400)/3600);
		    $minutes = floor(($check_time%3600)/60);
		    //$seconds = $check_time%60; 

		    $str = '';
		    if($days > 0) $str .= declension($days,array('день','дня','дней')).' ';
		    if($hours > 0) $str .= declension($hours,array('час','часа','часов')).' ';
		    if($minutes > 0) $str .= declension($minutes,array('минута','минуты','минут')).' ';
		    //if($seconds > 0) $str .= declension($seconds,array('секунда','секунды','секунд'));

		    return !empty($str)?$str:'-';
		}

		function declension($digit,$expr,$onlyword=false){
		    if(!is_array($expr)) $expr = array_filter(explode(' ', $expr));
		    if(empty($expr[2])) $expr[2]=$expr[1];
		    $i=preg_replace('/[^0-9]+/s','',$digit)%100;
		    if($onlyword) $digit='';
		    if($i>=5 && $i<=20) $res=$digit.' '.$expr[2];
		    else
		    {
		        $i%=10;
		        if($i==1) $res=$digit.' '.$expr[0];
		        elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
		        else $res=$digit.' '.$expr[2];
		    }
		    return trim($res);
		}
function translit ($t)
{
	$t = trim($t);
	$t = mb_strtolower($t,'UTF-8');
	$t = str_replace('  ','_',$t);
	$t = str_replace(' ','_',$t);
	$t = str_replace('-','_',$t);

	$t = str_replace('а','a',$t);
	$t = str_replace('б','b',$t);
	$t = str_replace('в','v',$t);
	$t = str_replace('г','g',$t);
	$t = str_replace('д','d',$t);
	$t = str_replace('е','e',$t);
	$t = str_replace('ё','e',$t);
	$t = str_replace('ж','j',$t);
	$t = str_replace('з','z',$t);
	$t = str_replace('и','i',$t);
	$t = str_replace('й','y',$t);
	$t = str_replace('к','k',$t);
	$t = str_replace('л','l',$t);
	$t = str_replace('м','m',$t);
	$t = str_replace('н','n',$t);
	$t = str_replace('о','o',$t);
	$t = str_replace('п','p',$t);
	$t = str_replace('р','r',$t);
	$t = str_replace('с','s',$t);
	$t = str_replace('т','t',$t);
	$t = str_replace('у','u',$t);
	$t = str_replace('ф','f',$t);
	$t = str_replace('х','h',$t);
	$t = str_replace('ц','c',$t);
	$t = str_replace('ч','ch',$t);
	$t = str_replace('ш','sh',$t);
	$t = str_replace('щ','shch',$t);
	$t = str_replace('ъ','',$t);
	$t = str_replace('ы','y',$t);
	$t = str_replace('ь','',$t);
	$t = str_replace('э','e',$t);
	$t = str_replace('ю','yu',$t);
	$t = str_replace('я','ya',$t);

	$t = preg_replace('/[^0-9a-zA-Z_]/', '', $t);
	$t = trim($t);
	return $t;
}

function get_database_tables($mysql)
{
	$ret = array();
	$r = mysqli_query($mysql,"SHOW TABLES");
	if (mysqli_num_rows($r)>0)
	{
		while($row = mysqli_fetch_array($r, MYSQL_NUM))
		{
			$ret[] = $row[0];
		}
	}
	return $ret;
}

function check_send_sms ($mysql,$zayavka_id)
{
	$r = mysqli_query($mysql,"SELECT * FROM MED_send_ochered WHERE zayavka_id='".intval($zayavka_id)."' ");
	if(!$r) exit(mysqli_error($mysql));
	while	($hk=mysqli_fetch_assoc($r))
	{
	$smsc[$hk['status_sms']] = $hk;
	}
	mysqli_free_result($r);	
	
	return isset($smsc)?$smsc:false;
}
function tel_replace($tel)
{
	$tel = preg_replace('/[^0-9]/', '', $tel);
	$tel = mb_substr($tel,0,1) == '8' ? '7'.mb_substr($tel, 1) : (mb_substr($tel,0,1) == '9'?'7'.$tel:$tel);
	return $tel;
}

function repl_str ($str)
{
	$str = trim($str);
	$str = str_replace('|','',$str);
	$str = str_replace('\\','',$str);
	$str = htmlspecialchars($str, ENT_QUOTES, "utf-8");
	return $str;
}
function clearTXT ($text)
{
	$text = trim($text);
	$text = preg_replace('/<img.*?src="(.*?)".*?>/im','<img src="$1" class="img-responsive" />',$text);
	$text = preg_replace('~<script[^>]*>.*?</script>~si', '', $text);
	$text = strip_tags($text, '<table><tr><th><td><img><br><p><ul><li>');
	$f = preg_replace("'<table[^>]*?>'si", '<table class="table marktab">', $text);
	/*$f = preg_replace("'<tr[^>]*?>'si", '<tr>', $f);
	$f = preg_replace("'<th[^>]*?>'si", '<th>', $f);
	$f = preg_replace("'<td[^>]*?>'si", '<td>', $f);*/
	$f = preg_replace("'<p[^>]*?>'si", '<p>', $f);
	$f = preg_replace("'<ul[^>]*?>'si", '<ul>', $f);
	$f = preg_replace("'<li[^>]*?>'si", '<li>', $f);
	$f = str_replace('\"', '"', $f);
	return $f;
}
function GetNav($p, $num_pages){

	if (isset($_GET['page']) and $_GET['page'] == 'client')
	{
	$fulllink = '?page=client';
	$and = '&p=';
	}
	elseif (isset($_GET['page']) and $_GET['page'] == 'sidelka')
	{
	$fulllink = '?page=sidelka';
	$and = '&p=';
	}
	elseif (isset($_GET['page']) and $_GET['page'] == 'zayavka')
	{
	$d1 = isset($_GET['d1'])?'&d1='.$_GET['d1'].'&d2='.$_GET['d2']:'';
	$citysort = isset($_GET['citysort'])?'&citysort='.$_GET['citysort']:'';
	$sort = isset($_GET['sort'])?'&sort='.$_GET['sort']:'';
	$fulllink = '?page=zayavka'.$d1.$citysort.$sort;
	$and = '&p=';
	}
	else
		{
		$fulllink = '';
		$and = '';
		}
  if($p > 3){
    $first_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.'1"><span>1</span></a></div> ';
  }
  else{
    $first_page = '';
  }
  if($p < ($num_pages - 2)){
    $last_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.$num_pages.'"> <span>'.$num_pages.'</span></a></div> ';
  }
  else{
    $last_page = '';
  }
  if($p > 1){
    $prev_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p - 1).'"> <span> < </span></a></div> ';
  }
  else{
    $prev_page = '';
  }
  if($p < $num_pages){
    $next_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p + 1).'"> <span>Следующая &rarr; </span></a></div> ';
  }
  else{
    $next_page = '';
  }
  if($p - 2 > 0){
    $prev_2_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p - 2).'"><span>'.($p - 2).'</span></a></div> ';
  }
  else{
    $prev_2_page = '';
  }
  if($p - 1 > 0){
    $prev_1_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p - 1).'"><span>'.($p - 1).'</span></a></div> ';
  }
  else{
    $prev_1_page = '';
  }
  if($p + 2 <= $num_pages){
    $next_2_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p + 2).'"><span>'.($p + 2).'</span></a></div> ';
  }
  else{
    $next_2_page = '';
  }
  if($p + 1 <= $num_pages){
    $next_1_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p + 1).'"><span>'.($p + 1).'</span></a></div> ';
  }
  else{
    $next_1_page = '';
  }
  
  $nav = "<div class='btn-toolbar' role='toolbar'><div class='btn-group'><a class='btn btn-default' href='#'>&uarr;</a></div>".$first_page.$prev_2_page.$prev_1_page."<div class='btn-group btn btn-default active'><span>".$p."</span></div>".$next_1_page.$next_2_page.$next_page."</div>";

  return $nav;
}

function format_size($size){
    
    $mod = 1024;
    $units = array('Б', 'КБ', 'МБ', 'ГБ', 'ТБ', 'ПБ');   
    for ($i = 0; $size > $mod; $i++)   
        $size /= $mod;

    return round($size, 2) . " " . $units[$i];
}
function restoreDB ($pathANDfile)
{
	global $config;
	$command = 'mysql -u' . $config['db_username'] . ' -p' . $config['db_password'] . ' -h' . $config['db_hostname'] . ' --default-character-set=utf8 --force ' . $config['db_name'] . ' < '.$pathANDfile;
	$return = shell_exec($command);
	return $return;
}
function backupDB($backup_folder, $backup_name, $array=false)
{
    global $config;
    $fullFileName = $backup_folder . '/' . $backup_name . '.sql.gz';
    $command = 'mysqldump -h' . $config['db_hostname'] . ' -u' . $config['db_username'] . ' -p' . $config['db_password'] . ' ' . $config['db_name'] . ' '.( $array!==false ? implode(' ',$array) : '' ).' | gzip -c > ' . $fullFileName;
    shell_exec($command);
    return $fullFileName;
}
function deletefiledayDB ($dir_backup,$delday)
{
	$scanned_directory = array_diff(scandir($dir_backup), array('..', '.', '.htaccess','point'));
	foreach ($scanned_directory as $file)
	{
		if (file_exists($dir_backup.'/'.$file) AND getExtension1($file) == 'gz' AND (time() - filemtime($dir_backup.'/'.$file) > (86400*$delday) ))
		{
		unlink($dir_backup.'/'.$file);
		}
	}
	return;
}
function deletefilecntDB ($dir_backup,$delcnt)
{
	$scanned_directory = array_diff(scandir($dir_backup), array('..', '.', '.htaccess','point'));
	$scanned_directory = array_reverse($scanned_directory);
	$h=0;
	foreach ($scanned_directory as $file)
	{
		if (file_exists($dir_backup.'/'.$file) AND getExtension1($file) == 'gz' AND ($h>=$delcnt))
		{
		unlink($dir_backup.'/'.$file);
		}
	$h++;
	}
	return;
}
function request($url)
{
 $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url );
  curl_setopt($ch, CURLOPT_HEADER, 0); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
?>
