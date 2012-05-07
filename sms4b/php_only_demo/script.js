document.title = 'Пример подключения к SMS сервису на PHP';

a = new Array();

a["tz"]  = "ц";
a["u"]  = "у";
a["k"]  = "к";
a["e"]  = "е";
a["n"]  = "н";
a["g"]  = "г";
a["sh"]  = "ш";
a["sch"]  = "щ";
a["z"]  = "з";
a["h"]  = "х";
a["f"]  = "ф";
a["v"]  = "в";
a["a"]  = "а";
a["p"]  = "п";
a["r"]  = "р";
a["o"]  = "о";
a["l"]  = "л";
a["d"]  = "д";
a["zh"]  = "ж";
a["ye"]  = "э";
a["ya"]  = "я";
a["ch"]  = "ч";
a["s"]  = "с";
a["m"]  = "м";
a["i"]  = "и";
a["t"]  = "т";
a["yo"]  = "ё";
a["b"]  = "б";
a["yu"]  = "ю";
a["yi"] = "й";
a["y"]  = "ы";

a["Y"] = "Ы";
a["YI"] = "Й";
a["Tz"]  = "Ц";
a["U"]  = "У";
a["K"]  = "К";
a["E"]  = "Е";
a["N"]  = "Н";
a["G"]  = "Г";
a["Sh"]  = "Ш";
a["Sch"]  = "Щ";
a["Z"]  = "З";
a["H"]  = "Х";
a["F"]  = "Ф";
a["V"]  = "В";
a["A"]  = "А";
a["P"]  = "П";
a["R"]  = "Р";
a["O"]  = "О";
a["L"]  = "Л";
a["D"]  = "Д";
a["Zh"]  = "Ж";
a["Ye"]  = "Э";
a["Ya"]  = "Я";
a["Ch"]  = "Ч";
a["S"]  = "С";
a["M"]  = "М";
a["I"]  = "И";
a["T"]  = "Т";
a["YO"]  = "Ё";
a["B"]  = "Б";
a["Yu"]  = "Ю";
a["<"] = "«";
a[">"] = "»";
a["-"] = "–";

function trans(text)
{
	var ntext = '';
	var ch = '';
	for (var d6 = 0; d6 < text.length; d6++)
	{
		ch = '';
		for(val in a)
		{
			if (text.substr(d6,1) == 'ь' || text.substr(d6,1) == 'Ь')
				ch = "'";
			if (text.substr(d6,1) == 'ъ' || text.substr(d6,1) == 'Ъ')
				ch = "\"";
			if (text.substr(d6,1) == '['  || text.substr(d6,1) == '{')
				ch = "(";
			if (text.substr(d6,1) == ']'  || text.substr(d6,1) == '}')
				ch = ")";
			if (text.substr(d6,1) == '\\')
				ch = "/";
			if (text.substr(d6,1) == '^')
				ch = "'";
			if (text.substr(d6,1) == '_')
				ch = "-";
			if (text.substr(d6,1) == '`')
				ch = "'";
			if (text.substr(d6,1) == '|')
				ch = "i";
			if (text.substr(d6,1) == '~')
				ch = "-";
			if (text.substr(d6,1) == '№')
				ch = "N";
			if (text.substr(d6,1) == '”')
				ch = "\"";	
			
		  	if (text.substr(d6,1) == a[val])
				ch = val;
		}
		
		if (ch == "")
		    ntext = ntext + text.substr(d6,1);
		else
			ntext = ntext + ch;
	}
	return ntext;
}

function trans_lat_to_kir(text)
{
	var ntext = '';
	
	for (var d6 = 0; d6 < text.length; d6++)
	{
		var ch = '';
		for(var val in a)
		{ 
			if (text.substr(d6,3) == val) ch = a[val];
		}
		//if search by 3 gave not result
		if (ch == "")
		{
			//search by 2
			for(var val in a)
			{
		 		if (text.substr(d6,2) == val) ch = a[val];
			}
			
			if (ch == "")
			{
				//search by 1
				for(var val in a)
				{
					if (text.substr(d6,1) == val) ch = a[val];
					
					if (text.substr(d6,1) == "'") ch = "ь";
					if (text.substr(d6,1) == "\"") ch = "ъ";
				}
				
				if (ch == "")
					ntext = ntext + text.substr(d6,1);
				else
					ntext = ntext + ch;	
			}
			else
			{
				ntext = ntext + ch;
				d6+=1;
			}
		}
		else
		{
			ntext = ntext + ch;
			d6+=2;
		}
	}		
	return ntext;
}

function isRus(text)
{
	for (var d6 = 0; d6 < text.length; d6++)
	{
		if (text.charCodeAt(d6) > 126 || text.charAt(d6) == '[' || text.charAt(d6) == "]" || text.charAt(d6) == "\\" || text.charAt(d6) == "^" || text.charAt(d6) == "_" || text.charAt(d6) == "`" || text.charAt(d6) == "{" || text.charAt(d6) == "}" || text.charAt(d6) == "|" || text.charAt(d6) == "~")
		return true;
	}
	return false;
}

function changetarea(texta,target)
{
	var ttrans = document.getElementById('trans');

	var text = texta.value;
	if (isRus(text))
		messLenPart = texta.value.length > 70 ? 66 : 70;
	else
		messLenPart = texta.value.length > 160 ? 156 : 160;

	var textlen = texta.value.length;
	var parts =  Math.ceil(texta.value.length / messLenPart);

	target.innerHTML = 'Осталось: ' + (messLenPart-texta.value.length);
	target.innerHTML = 'Длина текста: <font color="#ff0000">' + textlen + '</font>&nbsp;&nbsp; Размер части: <font color="#ff0000">' + (messLenPart) + '</font>&nbsp;&nbsp;Частей: <font color="#ff0000">' + parts + '</font>';
}

function disable_submit_button()
{
	form2.sub.disabled=true;
	return true;
}

function activeNightTimeNsEvent(checkboxID, firstEditID, secondEditID)
{
	var objCheckbox = document.getElementById(checkboxID);
	var objFirstEdit =  document.getElementById(firstEditID);
	
	if (objCheckbox.checked == true)
	{	
		objFirstEdit.disabled = false;
	}
	else
	{
		objFirstEdit.disabled = true;	
	}	
	
	if (secondEditID != '')
	{
		var objSecondEdit =  document.getElementById(secondEditID);
	
		if (objCheckbox.checked == true)
		{
			objSecondEdit.disabled = false;
		}
		else
		{
			objSecondEdit.disabled = true;	
		}
	}
}

function Counters(texta, spanlength, spanpartsize, spanparts, needsms, numbersTextarea, span)
{
	var ttrans = document.getElementById('trans');
	texta = document.getElementById(texta);
	
	if (texta.value.match(/\r/g) == null)
	{
		//считаем количество символов
		var newLinesymbols = texta.value.match(/\n/g);
		
		if (newLinesymbols != null)
		{
			$newLinesymbolsCount = newLinesymbols.length;	
		}
		else
		{
			$newLinesymbolsCount = 0;
		}					
	}
	
	var text = texta.value;
	var textLength = text.length + $newLinesymbolsCount;
		
	if (isRus(text))
		messLenPart = (textLength + $newLinesymbolsCount) > 70 ? 66 : 70;
	else
		messLenPart = (textLength + $newLinesymbolsCount) > 160 ? 153 : 160;
	
	document.getElementById(spanpartsize).innerHTML = messLenPart;	
	
	
	textlen = textLength;
	document.getElementById(spanlength).innerHTML = textlen;  
	var parts = Math.ceil(textlen / messLenPart); 
   	document.getElementById(spanparts).innerHTML = parts;  
 	numbers = getTelNumber(numbersTextarea, span);
 	document.getElementById(needsms).innerHTML = numbers * parts; 
}

function getTelNumber(textAreaID, spanNumbers)
{
	var textareaObject = document.getElementById(textAreaID);
	var telNumberStr = textareaObject.value;
	telNumberStr = telNumberStr.replace(/<.*>/g, '');
	telNumberStr = telNumberStr.replace(/\n/g, ';');
	telNumberStr = telNumberStr.replace(/,/g, ';');
		
	var array = telNumberStr.split(';');
	var arrayWithoutEmpty = new Array();
	
	//убираем пустую строку
	for (var ind in array)
	{
		if (array[ind] != "")
		{
			arrayWithoutEmpty.push(array[ind]);	
		}
	}
	
	var result_array = arrayWithoutEmpty;
	var number = result_array.length;
	var smsCountTextObject = document.getElementById(spanNumbers);
	smsCountTextObject.innerHTML = number;
	
	return number;	
}
