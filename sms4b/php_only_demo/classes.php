<?
require_once "options.php";
require_once "CSms4bBase.php";

error_reporting(E_ERROR);



if ($LOGIN == '' || $PASSWORD ==''):?>
	<h1 style="color:red">Не указаны логин и пароль для отправки SMS. Зайдите в файл options.php и введите логин и пароль.</h1>
	<p><b>Например</b></p>
	<div style="padding-left:15px">
	<p><b>Первоначально</b></p>
	<pre>
		#вам нужно настроить два параметра для доступа к отправке SMS
		$LOGIN = '';	// ваш логин для отправки SMS из ВНЕШНИХ ПРОГРАММ
		$PASSWORD = ''; // ваш пароль для отправки
	</pre>
	
	<p><b>Должно быть</b></p>
	<pre>
		#вам нужно настроить два параметра для доступа к отправке SMS
		$LOGIN = 'SMS_TEST';	// ваш логин для отправки SMS из ВНЕШНИХ ПРОГРАММ
		$PASSWORD = '1b013def'; // ваш пароль для отправки
	</pre>
	</div>
	<?exit();?>
<?endif;?>

<?
if (!is_object($SMS4B))
	$SMS4B = new Csms4bBase($LOGIN,$PASSWORD);
?>