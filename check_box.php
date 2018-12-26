<?php 
//	header("Content-Type:text/xml;charset=utf-8");
// error_reporting(E_ALL);
// 	
	if (file_exists('checkbox.txt'))
	{
	$f = file('checkbox.txt');
	}
	
	if (isset($_POST['SBM_box']))
	{
	$checkbox = '';
		for ($d=0;$d<3;$d++)
		{
			if (isset($_POST['ch'][$d]))
			{
			$checkbox .= '1'."\r\n";
			}
			else
				{
				$checkbox .= '0'."\r\n";
				}
		}
	$save=fopen('checkbox.txt', 'w');
	fputs($save,$checkbox);
	fclose($save);
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/check_box.php'>");
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>CHECKBOX</title>
	<style>
	.green {
	background:green;
	}
	.red {
	background:red;
	}
	</style>
</head>
<body>
	<form action="" method="post" class="form">
		<table class="table">
			<tr>
			<?php
				for ($d=0;$d<3;$d++)
				{
				?>
				<td class="<?php echo (isset($f) AND $f[$d] == 1)?'green':'red'; ?>" align="center"><label for="checkbox"><input <?php echo (isset($f) AND $f[$d] == 1)?'checked="checked"':''; ?> type="checkbox" name="ch[<?php echo $d; ?>]">ch<?php echo $d; ?></label></td>
				<?php
				}
				?>
			</tr>
		</table>
		<p>
			<button type="submit" class="button" name="SBM_box">Сохранить</button>
		</p>
	</table>
</body>
</html>