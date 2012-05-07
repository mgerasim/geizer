<?require_once 'classes.php';
require_once 'menu.php';

?><p>В этом премере дата последнего считывания sms хранится в переменной сессии. Правильным решением при реализации будет хранение даты в базе данных. Считывается 5 входящих sms с даты приёма последней считанной sms.</p><?

$props = array( "SessionID" => $SMS4B->GetSID(),
				"StartChanges"  => /*$_SESSION["StartChanges"]*/"",
				);


if ($_REQUEST["LoadOut"]):
//загружаем входящие сообщения
$arMassiv = array();
$arMassiv = $SMS4B->GetSOAP("LoadIn",$props);?>



<table style="border:1px dotted silver; margin-top:30px;">
	<thead>
		<tr>
			<td style= "font-size:10px;	font-weight: bold; background-color: #F5F5F5;">Отправитель</td>
			<td style= "font-size:10px;	font-weight: bold; background-color: #F5F5F5;">Получатель</td>
			<td style= "font-size:10px;	font-weight: bold; background-color: #F5F5F5;">Время отправки</td>
			<td style= "font-size:10px;	font-weight: bold; background-color: #F5F5F5;">Части</td>
			<td style= "font-size:10px;	font-weight: bold; background-color: #F5F5F5;">Текст</td>
		</tr>
	</thead>
	<tbody>
		<?foreach($arMassiv as $arIndex):
		$_SESSION["StartChanges"] = $arIndex["Moment"];?>
			<tr>
				<td style= "vertical-align: top; padding-bottom: 15px; border:1px dotted silver; padding:5px 10px 0px 10px; width:150px;"><?=$arIndex["Source"]?></td>
				<td style= "vertical-align: top; padding-bottom: 15px; border:1px dotted silver; padding:5px 10px 0px 10px; width:150px;"><?=$arIndex["Destination"]?></td>
				<td style= "vertical-align: top; padding-bottom: 15px; border:1px dotted silver; padding:5px 10px 0px 10px; width:150px;"><?=$arIndex["Moment"]?></td>
				<td style= "vertical-align: top; padding-bottom: 15px; border:1px dotted silver; padding:5px 10px 0px 10px; width:150px;"><?=$arIndex["Total"]?></td>
				<td style= "vertical-align: top; padding-bottom: 15px; border:1px dotted silver; padding:5px 10px 0px 10px; width:150px;"><?=$SMS4B->decode($arIndex["Body"], $arIndex["Coding"])?></td>
			</tr>
		<?endforeach;?>
 	</tbody>
</table>
<?endif;?>

<form action="#" style="margin-top:30px;">
<input type="submit" name="LoadOut" value="Загрузить входящие sms">
</form>