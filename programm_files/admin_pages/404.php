<?php
	if (!defined('__PANEL__BOARD__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$_SERVER['HTTP_HOST']."/kpp.php'>");
	}
	
?>

  	<div class="col-md-12"> 
		<h3>404</h3>
		<p>Такой страницы не существует, Ваш запрос <strong><?php echo $_GET['page']; ?></strong> - если ошибка будет повторяться: обратитесь к <a href="?page=worker&see=1">разработчику</a>.</p>
	</div>
