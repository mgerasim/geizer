<?require_once 'classes.php'?>
<?require_once 'menu.php'?>

<head>
	<title>Баланс пользователя</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
</head>

<body>
<h1>Баланс</h1>

<?$rest = $SMS4B->arBalance["Rest"]?>

<div style="background-color:#F0F0F0;width:150px">   
	<table cellpadding="3px">
		<tr>
			<td>Логин</td>
			<td><b><?=$SMS4B->getLogin()?></b></td>
		</tr>
		<tr>
			<td>Баланс</td>
			<td><b><?=round($rest,1)?></b> SMS</td>
		</tr>
	</table>	
</div>

<h3>Демонстрационый пример от сайта <a href = "http://www.sms4b.ru">http://www.sms4b.ru</a></h3>
</body>