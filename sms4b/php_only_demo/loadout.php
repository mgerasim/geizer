<style type="text/css">
	.table {border:1px dotted silver; margin-top:30px;}
	.td {font-size:10px;	font-weight: bold; background-color: #F5F5F5;}
	.tdn {vertical-align: center; padding-bottom: 15px; border:1px dotted silver; padding:0 10px 0 10px; width:110px;}
	.tdnt {vertical-align: center; padding-bottom: 15px; border:1px dotted silver; padding:0 10px 0 10px; width:550px;}
</style>  
<?require_once 'classes.php';
require_once 'menu.php';?>

<p>В этом примере дата последнего считывания sms хранится в переменной сессии. Правильным решением при реализации будет хранение даты в базе данных. Считываются входящие sms с даты приёма последней считанной sms.</p>

<?$props = array( 	"SessionId" => $SMS4B->GetSID(),
					//флаг выгрузки sms
					"Flags"		=> "0",
					/*"Flags"	=> "1",*/
				);

$arMassiv = array();

$i = 1000;
$k = 0;

if ($_REQUEST["LoadOut"])
{
	while ($i != 0)
	{
		$props["ChangesFrom"] = $_SESSION["ChangesFrom"];
		$arMassiv = $SMS4B->GetSOAP("LoadSMS",$props);

		foreach ($arMassiv as $arIndexx):
			$_SESSION["ChangesFrom"] = $arIndexx["M"];
		endforeach;
  		
		$lastDate = $_SESSION["ChangesFrom"];

		$arrLastModifiedInc = explode('.', $lastDate);
		if ($arrLastModifiedInc[0])
		{
			$millisecondsIn = $arrLastModifiedInc[1];
			if ($millisecondsIn == '997')
			{
				$lastDate = date("Y-m-d H:i:s.001", MakeTimeStamp($arrLastModifiedInc[0], "YYYY-MM-DD HH:MI:SS") + 1);
			}
			else if ($millisecondsIn == '998')
			{
				$lastDate = date("Y-m-d H:i:s.002", MakeTimeStamp($arrLastModifiedInc[0], "YYYY-MM-DD HH:MI:SS") + 1);
			}
			else if ($millisecondsIn == '999')
			{
				$lastDate = date("Y-m-d H:i:s.003", MakeTimeStamp($arrLastModifiedInc[0], "YYYY-MM-DD HH:MI:SS") + 1);
			}
			else
			{
				$lastDate = $arrLastModifiedInc[0].'.'.sprintf('%03s', intval($arrLastModifiedInc[1]) + 4);
			}
		}
		$_SESSION["ChangesFrom"] = $lastDate;
        
		if (empty($arMassiv))
        {
            break;
        }
        
        $Massive[$k] = $arMassiv;
        
		$k++;
		$i--;	
	}
	$_SESSION["SMS"] = $Massive;
}?>

<form action="loadout.php" style="margin-top:30px;">
	<input type="submit" name="LoadOut" value="Загрузить входящие sms">
</form> 

<?
$Massive = '';
$arSmsses = '';
$Massive = array();
$arSmsses = array();

$Massive = $_SESSION["SMS"];
 
foreach($Massive as $arIndex):?>
	<?$arIndex = array_reverse($arIndex);
	$arSmsses = array_merge($arIndex, $arSmsses);?>
<?endforeach;?>

<?
$num = 15;
$page = $_GET['page'];
$sms = count($arSmsses);

$total = intval(($sms - 1) / $num) + 1;
$page = intval($page);

if(empty($page) or $page < 0) $page = 1; 
if($page > $total) $page = $total;

$start = $page * $num - $num;
$c = $start;
$numb = '';
$numb = 0;
   
while ($numb != 16)
{
	if (empty($arSmsses[$c]))
    {
        break;
    }
    
    $postrow[] = $arSmsses[$c];
	$c++;
	$numb++; 
}?> 

<?if (count($_SESSION["SMS"]) >= 1):?>
	<div style="background-color:#F0F0F0;margin-top: 7px; width:1100px;">
		<table class="table">
			<thead>
				<tr>
					<td class="td">Отправитель</td>
					<td class="td">Получатель</td>
					<td class="td">Время отправки</td>
					<td class="td">Части</td>
					<td class="td">Текст</td>
				</tr>
			</thead>
			<tbody>
				<?for($i = 0; $i < $numb; $i++):?>
				<tr> 
					<td class="tdn"><?=$postrow[$i]["S"]?></td>
					<td class="tdn"><?=$postrow[$i]["D"]?></td>
					<td class="tdn"><?=$postrow[$i]["M"]?></td>
					<td class="tdn"><?=$postrow[$i]["A"]?></td>
					<td class="tdnt"><?=$SMS4B->decode($postrow[$i]["B"], $postrow[$i]["E"])?></td>
				</tr>
				<?endfor;?>
			</tbody>
		</table>
	</div>
<?else:?>
	<p>Новых входящих сообщений нет или загрузка входящих SMS в данной сессии еще не производилась.</p>
<?endif;?>

<?//Проверяем нужны ли стрелки назад
if ($page != 1) $pervpage = '<a href=loadout.php?page=1>Первая</a> | <a href=loadout.php?page='. ($page - 1) .'>Предыдущая</a> | ';
//Проверяем нужны ли стрелки вперед
if ($page != $total) $nextpage = ' | <a href=loadout.php?page='. ($page + 1) .'>Следующая</a> | <a href=loadout.php?page=' .$total. '>Последняя</a>';

//Находим две ближайшие станицы
if($page - 5 > 0) $page5left = ' <a href=loadout.php?page='. ($page - 5) .'>'. ($page - 5) .'</a> | ';
if($page - 4 > 0) $page4left = ' <a href=loadout.php?page='. ($page - 4) .'>'. ($page - 4) .'</a> | ';
if($page - 3 > 0) $page3left = ' <a href=loadout.php?page='. ($page - 3) .'>'. ($page - 3) .'</a> | ';
if($page - 2 > 0) $page2left = ' <a href=loadout.php?page='. ($page - 2) .'>'. ($page - 2) .'</a> | ';
if($page - 1 > 0) $page1left = '<a href=loadout.php?page='. ($page - 1) .'>'. ($page - 1) .'</a> | ';

if($page + 5 <= $total) $page5right = ' | <a href=loadout.php?page='. ($page + 5) .'>'. ($page + 5) .'</a>';
if($page + 4 <= $total) $page4right = ' | <a href=loadout.php?page='. ($page + 4) .'>'. ($page + 4) .'</a>';
if($page + 3 <= $total) $page3right = ' | <a href=loadout.php?page='. ($page + 3) .'>'. ($page + 3) .'</a>';
if($page + 2 <= $total) $page2right = ' | <a href=loadout.php?page='. ($page + 2) .'>'. ($page + 2) .'</a>';
if($page + 1 <= $total) $page1right = ' | <a href=loadout.php?page='. ($page + 1) .'>'. ($page + 1) .'</a>';

//если страниц больше одной - выводим меню
if ($total > 1)
{
	echo "<br />";
	echo $pervpage.$page5left.$page4left.$page3left.$page2left.$page1left.'<b>'.$page.'</b>'.$page1right.$page2right.$page3right.$page4right.$page5right.$nextpage;
}?>

<br /><br />