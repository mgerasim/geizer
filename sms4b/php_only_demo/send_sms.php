<?require_once 'classes.php';
require_once 'menu.php';
#echo "<pre>";print_r($_SESSION);echo "</pre>";
#echo "<pre>";print_r($_REQUEST);echo "</pre>";
$MESS ['BAD_DATE_FORMAT'] = "Неправильный формат даты в секции отложенной отправки";
$MESS ['DATE_IS_TO_BIG'] = "Вы выбрали слишком большую дату для отложеной отправки. Дата не может быть больше 10 дней от текущего времени";
$MESS ['BAD_DATE_FORMAT_FOR_ACTUAL_DATE'] = "Неправильный формат даты актуальности доставки";
$MESS ['ERROR_IN_DATE'] = "Дата актуальности доставки меньше текущей даты";
$MESS ['ERROR_IN_DATE_1'] = "Дата актуальности доставки должна быть больше даты начала рассылки не менее чем на 15 минут";
$MESS ['ERROR_IN_DATE_2'] = "Вы выбрали слишком большую дату для актуальности отправки. Дата не может быть больше 14 дней от текущего времени";
$MESS ['BAD_FORMAT_FOR_INTERVAL'] = "Неправильный формат интервала разрешенной отправки";
$MESS ['SENDING_DENIED'] = "Запрещена отправка на номера, которые незарегистрированы на персонал. На них сообщения доставлены не будут";

error_reporting(E_ERROR);?>

<?if($SMS4B->LastError == '' && $SMS4B->GetSOAP("AccountParams",array("SessionID" => $SMS4B->GetSID())) === true )
{
	if (!$_REQUEST['apply'])
	{
		unset($_SESSION["checking_f5"]);
	}
	
	if (isset($_REQUEST["checking_f5"]) && $_REQUEST["checking_f5"] == $_SESSION["checking_f5"])
	{
		echo "<div style = \"color:red\">Повторная отправка формы запрещена. Операция обновления заблокирована</div>";
		echo "<a href = \"".""."\">Вернуться к форме отправки SMS</a>";
		return;
	}
	
	if ($_REQUEST['apply'])
	{
		$_SESSION["checking_f5"] = $_REQUEST["checking_f5"];
	}
	
	if($SMS4B->arBalance["Rest"] < 0.1)
	{
		$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
		$arResult["RESULT_MESSAGE"]["MESSAGE"] = GetMessage("NO_MESSAGES").'<br>';
		$arResult["CAN_SEND"] = "N";
	}
	else
	{

		$arResult["BALANCE"] = $SMS4B->arBalance["Rest"];
		$arResult["ADRESSES"] = $SMS4B->arBalance["Addresses"];

		if ($_REQUEST['apply'])
		{
			//take data entered by user
			$sender = stripslashes($_REQUEST["sender_number"]);
			//take all addresses, it is may be multy send
			$destination = $SMS4B->parse_numbers($_REQUEST["destination_number"]);
			$message = $_REQUEST["message"];
			
			//need message about sending?
			$request = ($_POST["reply"] == "on")? 0 : 1;

			if ($sender == "")
			{
				$errors[] = "Не указан номер отправителя";	
			}
			if (count($destination) == 0)
			{
				$errors[] = "Не указано нe одного номера получателя";
			}
			if ($message == "")
			{
				$errors[] = "Не указан текст сообщения";
			}
			
			if (!in_array($sender,$arResult["ADRESSES"]))
			{
				$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
				$arResult["RESULT_MESSAGE"]["MESSAGE"] = "Проверьте заполненность полей формы";
				
				if (count($destination) == 0 && $_REQUEST["destination_number"] <> '')
				{
					$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
					$arResult["RESULT_MESSAGE"]["MESSAGE"] = "Не указаны номера получателей";	
				}
			}
			
			//checking begin of the send
			if (!isset($_REQUEST["BEGIN_SEND_AT"]) || $_REQUEST["BEGIN_SEND_AT"] == '' )
			{
				$startUp = "";
			}
			else
			{
				$startUp = $SMS4B->GetFormatDateForSmsForm($_REQUEST["BEGIN_SEND_AT"]);
				
				//checking date
				if ($startUp == -1)
				{
					$errors[] = $MESS['BAD_DATE_FORMAT'];
				}
				
				$timestampStartUp = $SMS4B->GetTimeStamp($_REQUEST["BEGIN_SEND_AT"]);
				$currTimeStamp = time();
				
				if ($timestampStartUp < $currTimeStamp)
				{
					$timestampStartUp = $currTimeStamp;
					$startUp = date("Ymd H:i:s",time()+1);
				}
				
				//chosen date couldn't be better for 10 days
				$timeX = $timestampStartUp - (86400*10);
				if ($timeX > $currTimeStamp)
				{
					$errors[] = $MESS['DATE_IS_TO_BIG'];	
				}
								
			}
				
			//checking actual date for send
			if (!isset($_REQUEST["DATE_ACTUAL"]) || $_REQUEST["DATE_ACTUAL"] == '')
			{
				$dateActual = "";
			}
			else
			{
				$dateActual = $SMS4B->GetFormatDateForSmsForm($_REQUEST["DATE_ACTUAL"]);
				
				if ($dateActual == -1)
				{
					$errors[] = $MESS['BAD_DATE_FORMAT_FOR_ACTUAL_DATE'];
				}
				
				$timestampDateActual = $SMS4B->GetTimeStamp($_REQUEST["DATE_ACTUAL"]);
				
				//getting current time
				$currTimeStamp = time();
				
				if ($timestampDateActual < $currTimeStamp)
				{
					$errors[] = $MESS['ERROR_IN_DATE'];
				}
				
				if ($startUp != "")
				{
					$timeX = $timestampDateActual - 900;
					if ($timeX < $timestampStartUp)
					{
						$errors[] = $MESS['ERROR_IN_DATE_1'];	
					}
				}
				
				$timeX = $timestampDateActual-(86400*14);
				if ($timeX > $currTimeStamp)
				{
					$errors[] = $MESS['ERROR_IN_DATE_2'];	
				} 	
			}
			
			//checking period
			if (!isset($_REQUEST["DATE_FROM_NS"]) || !isset($_REQUEST["DATE_TO_NS"]) || $_REQUEST["DATE_FROM_NS"] == "" ||  $_REQUEST["DATE_TO_NS"] == "" )
			{
				$period = ""; 
			}
			else
			{   
				$formedLeftPart = '';
				$formedRightPart = '';
				
				$dateFromNS = htmlspecialchars($_REQUEST["DATE_FROM_NS"]);
				$dateToNS 	= htmlspecialchars($_REQUEST["DATE_TO_NS"]);
				
				if (ord($dateFromNS) >= 65 && ord($dateFromNS) <= 88 && ord($dateToNS) >= 65 && ord($dateToNS) <= 88)
				{
					//this is left part
					if ($dateToNS == 'X')
					{
						$formedLeftPart = 'A';	
					}
					else
					{
						$formedLeftPart = chr(ord($dateToNS)+1);
					}
					//this is right part
					if ($dateFromNS == 'A')
					{
						$formedRightPart = 'X';	
					}
					else
					{
						$formedRightPart = chr(ord($dateFromNS)-1);
					}
					
					$period = $formedLeftPart.$formedRightPart;
				}
				else
				{
					$errors[] = 'Неправильный интервал ночной отправки';
				}
			}
			
			if (count($errors) == 0) 
			{
				$result = $SMS4B->SendSmsPack($message, $destination, htmlspecialchars($sender), $startUp, $dateActual, $period);
								
				$arResult["RESULT_MESSAGE"]["TYPE"] = "OK";
				$arResult["WAS_SEND"] = (empty($result["SEND"]) ? "0" : $result["SEND"]);
				$arResult["NOT_SEND"] = (empty($result["NOT_SEND"]) ? "0" : $result["NOT_SEND"]);
			}
		}
	}
}
else
{
	$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
	$arResult["RESULT_MESSAGE"]["MESSAGE"] = $SMS4B->LastError. "Проверьте настройки в классе.";
	$arResult["CAN_SEND"] = "N";
}
?>

<head>
	<title>Отправка SMS</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
	<script src="script.js"></script>
</head>

<body>

<h1>Форма отправки SMS</h1>

<?
if (count($errors) > 0)
{
	foreach($errors as $arIndex)
	{
		echo "<div style='color:red'>".$arIndex."</div>";	
	}
	
	$strError = $arResult["RESULT_MESSAGE"]["MESSAGE"];
	$dest = htmlspecialchars($_POST["destination_number"]);
	$sender = htmlspecialchars($_POST["sender_number"]);
	$mess = htmlspecialchars($_POST["message"]);
	$date = htmlspecialchars($_POST["DATE"]);
}

if ($_REQUEST['apply'] && $arResult["RESULT_MESSAGE"]["TYPE"] == "OK")
{
	if ($arResult["WAS_SEND"] != 0):
		echo "<div style='color:green'>"."Удачно отправлено - " . $arResult["WAS_SEND"]." sms."."</div>";
	endif;
	if ($arResult["NOT_SEND"] != 0): 
		echo "<div style='color:red'>"."Не отправлено на " . $arResult["NOT_SEND"]." номеров. "."</div>";
	endif;	
}
?>

<div style="background-color:#F0F0F0;margin-top: 15px;width:800px;">
<form name = "form1" method="POST" action="#">
	<table cellpadding="9px">
		<tr>
			<td style="vertical-align: text-top;"><b>Имя отправителя</b></td>
			<td>                                   
				<select name="sender_number">
						<?foreach ($arResult["ADRESSES"] as $arIndex):?>
							<?if ($arIndex == "" || $arIndex == " "):?>
								<?continue;?>
							<?endif;?>
							<option value = '<?=$arIndex?>'
								<?if ($sender == $arIndex):?> selected <?endif;?>><?=$arIndex?>
							</option>
						<?endforeach;?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td style="vertical-align: text-top;"><b>Адрес получателя</b></td>
			<td>
				<div class = "counters">Количество набраных номеров <span class = "fontColor" id = 'freeNumbers'>0</span></div>
				<div style = "clear:both"></div> 
				<textarea name = "destination_number" cols = "50" rows = "4"
				id = "freeNums"
				wrap = "off"
				onKeyUp = "return this.onkeypress()"
				onkeypress="getTelNumber('freeNums', 'freeNumbers');Counters('message','free-text-length', 'free-part-size', 'free-parts', 'free-need-sms', 'freeNums', 'freeNumbers')" 
				><?=($arResult["RESULT_MESSAGE"]["TYPE"] == "ERROR" ? $dest : '')?></textarea>
			</td>
		</tr>

		<tr>
			<td style="vertical-align: text-top;"><b>Текст сообщения</b></td>
			<td>
				<div class = "counters">Длина текста: <span class = "fontColor" id = 'free-text-length'>0</span></div>
				<div class = "counters">Размер части: <span class = "fontColor" id = "free-part-size">160</span></div>
				<div class = "counters">Частей: <span class = "fontColor" id = "free-parts">0</span></div>
				<div style = "clear:both"></div>
				<div class = "counters">Будет списано SMS: <span class = "fontColor" id = "free-need-sms">0</span></div>
				<div style = "clear:both"></div>

				<textarea rows = '6' id="message" name = "message"
					
					onKeyUp="Counters('message', 'free-text-length', 'free-part-size', 'free-parts', 'free-need-sms', 'freeNums', 'freeNumbers')" 
					onkeypress="return this.onkeyup();" 
					cols = "50" rows = "4"><?=($arResult["RESULT_MESSAGE"]["TYPE"] == "ERROR" ? $mess : '')?></textarea>
				<br />
				<span style="color: #486DAA; cursor:pointer; text-decoration:underline;" onclick="document.getElementById('message').value = trans(document.getElementById('message').value); changetarea(document.getElementById('message'),document.getElementById('lengmess'));">Траснлитирировать в латиницу</span>
				<br />
				<span style="color: #486DAA; cursor:pointer; text-decoration:underline;" onclick="document.getElementById('message').value = trans_lat_to_kir(document.getElementById('message').value); changetarea(document.getElementById('message'),document.getElementById('lengmess'));">Траснлитирировать в киррилицу</span>
			</td>
		</tr>
		
		<tr>
			<td>
				<b>Начать рассылку с</b>
				<div class = "comments">Позволяет выполнить отложенную отправку</div>
			</td>
			<td>
				<input type="text" class="typeinput" id = 'BEGIN_SEND_AT' name="BEGIN_SEND_AT" size="20" value = "<?=$_REQUEST['BEGIN_SEND_AT'] ? $_REQUEST['BEGIN_SEND_AT'] : date("d-m-Y H:i:s")?>"/>
			</td>
		</tr>
		
		<tr>
			<td>
				<input type = checkbox id = 'ACTIVE_DATE_ACTUAL1' name = "ACTIVE_DATE_ACTUAL" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL1','DATE_ACTUAL1','');" 
				<?if ($_REQUEST["ACTIVE_DATE_ACTUAL"] == "Y"):?> checked <?endif;?> /> 
				<b><label for = "ACTIVE_DATE_ACTUAL1">Дата актуальности</label></b>
				<div class = "comments">Время, до которого имеет смысл доставка сообщения</div>
			</td>
			<td>
				<input type="text" class="typeinput" id = 'DATE_ACTUAL1' name="DATE_ACTUAL" size="20" value = "<?=$_REQUEST['DATE_ACTUAL'] ? $_REQUEST['DATE_ACTUAL'] : date("d-m-Y H:i:s", time() + 3600)?>" /> 
			</td>
		</tr>
		
		<tr>
			<td>
				<input type = checkbox id = "ACTIVE_NIGHT_TIME_NS1" name = "ACTIVE_NIGHT_TIME_NS" value="Y" onclick="activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS1','DATE_FROM_NS1','DATE_TO_NS1');" 
				<?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] == "Y"):?> checked <?endif;?> />
				<b><label for = 'ACTIVE_NIGHT_TIME_NS1'>Не отправлять в ночное время c</label></b>
				<div class = "comments">Позволяет временно остановить рассылку SMS в ночное время</div>
			</td>
			<td>
				<select id = 'DATE_FROM_NS1' name="DATE_FROM_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?>>
					<?
						if (!isset($_REQUEST["ACTIVE_NIGHT_TIME_NS"]) || !isset($_REQUEST["apply"]))
						{
							$checked_symbol_date_from_ns = chr(87);
						}
						else
						{
							$checked_symbol_date_from_ns = $_REQUEST["DATE_FROM_NS"];
						}
					?>
					<?for ($i = 0; $i < 24; $i++):?>
						<option value = "<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_from_ns):?> selected <?endif;?> ><?=$i?>:00</option>
					<?endfor;?>
				</select>
				&nbsp;
				по
				&nbsp;
				<select id = 'DATE_TO_NS1' name="DATE_TO_NS" <?if ($_REQUEST["ACTIVE_NIGHT_TIME_NS"] != "Y"):?> disabled <?endif;?> >
					<?
						if (!isset($_REQUEST["ACTIVE_NIGHT_TIME_NS"]) || !isset($_REQUEST["apply"]))
						{
							$checked_symbol_date_to_ns = chr(73);
						}
						else
						{
							$checked_symbol_date_to_ns = $_REQUEST["DATE_TO_NS"];
						}
					?>
					<?for ($i = 0; $i < 24; $i++):?>
						<option value = "<?=chr(65+$i)?>" <?if (chr(65+$i) == $checked_symbol_date_to_ns):?> selected <?endif;?> ><?=$i+1?>:00</option>
					<?endfor;?>
				</select>
			</td>
		</tr>
			
		<tr>
			<td colspan="2" style = "text-align:right" ><input type = "submit" value="Отправить" name="apply" /></td>
		</tr>
		<input type = "hidden" name='checking_f5' value="<?=md5(time())?>" />
</form>
</div>

<script>
	activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL1','DATE_ACTUAL1', '');
	activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS1','DATE_FROM_NS1','DATE_TO_NS1');
</script>
<style>
.counters
{
	float:left;
	margin: 0;
	padding: 0;
	margin-right: 5px;	
}
.fontColor
{
	color:#ff0000;
}
</style>

<h3>Демонстрационый пример от сайта <a href = "http://www.sms4b.ru">http://www.sms4b.ru</a></h3>	
</body>
