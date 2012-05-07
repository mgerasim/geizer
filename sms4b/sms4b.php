<?php

	function eraseBrackets($str)
	{
		return str_replace(array("{","}"),"",$str);
	}
	
		/*from binary to hex number transformation*/
	function bin_to_hex($str)
	{
		switch ($str)
		{
			case "0000": return "0";
			case "0001": return "1";
			case "0010": return "2";
			case "0011": return "3";
			case "0100": return "4";
			case "0101": return "5";
			case "0110": return "6";
			case "0111": return "7";
			case "1000": return "8";
			case "1001": return "9";
			case "1010": return "A";
			case "1011": return "B";
			case "1100": return "C";
			case "1101": return "D";
			case "1110": return "E";
			case "1111": return "F";
			default:
			{
				return false;
			}
		}
	}
	//how to code message
	//if exist symbol with code bigger than 127 or meet inadmissible symbols,
	//so we code text as UTF-16 (function returns 1).
	//Another way we code as DefaultAlphabet (function returns 0)
	function get_type_of_encoding($message)
	{
		//������������ �������
		$inadmissible_symbols = array("[" , "]" , "\\" , "^" , "_" , "`" , "{", "}" , "|" , "~");

		if ($message == "")
		{
			print "message is empty\n";
			return false;
		}
		else
		{
			$type_of_encoding = 0;

			for($i = 0; $i < strlen($message);$i++)
			{
				if (ord($message[$i]) > 127 || in_array($message[$i],$inadmissible_symbols))
				{
					$type_of_encoding = 1;
					break;
				}
			}
			return $type_of_encoding;
		}
	}
	/*codes one symbol
	$symbol: symbol for coding (for example 'a')
	$type_of_encoding:
	 0-DefaultAlphabet
	 1-UTF16*/
	function enCoding($symbol,$type_of_encoding)
	{
		if (strlen($symbol)==0 || $type_of_encoding > 1  || $type_of_encoding < 0)
		{
			print "������� ������� ��������� ������ ������� enCoding\n";
			return false;
		}
		else
		{
			switch($type_of_encoding)
			{
				case 0:
					if ($symbol == "@")
					{
						return "00";
						break;
					}
					elseif ($symbol =="$")
					{
						return "02";
						break;
					}

					$code = ord($symbol);
					$str16x = "";

					for($i = 0; $i < 8; $i++)
					{
						$bit = $code & 1;
						switch ($bit)
						{
							case 0: $str16x.="0";break;
							case 1: $str16x.="1";break;
						}
						$code = $code >> 1;
					}

					$str16x = strrev($str16x);

					$high_part = $str16x[0].$str16x[1].$str16x[2].$str16x[3];
					$low_part  = $str16x[4].$str16x[5].$str16x[6].$str16x[7];

					$high_part = bin_to_hex($high_part);
					$low_part = bin_to_hex($low_part);

					$str16x = $high_part.$low_part;
					return $str16x;
					break;
				case 1:
					$symbol = mb_convert_encoding($symbol, "UTF-16", "Windows-1251");
print ord($symbol);print "\n";

					$code = (ord($symbol[0])*256+ord($symbol[1]));
					
					print ord($symbol[0]);
					print "\n";
					
					print ord($symbol[1]);
					print "\n";

					$str16x = "";

					for($i = 0; $i < 16; $i++)
					{
						$bit = $code & 1;
						switch ($bit)
						{
							case 0: $str16x.="0";break;
							case 1: $str16x.="1";break;
						}
						$code = $code >> 1;
					}


					$str16x = strrev($str16x);
					
					print $str16x;
					print "\n";

					$first_part = $str16x[0].$str16x[1].$str16x[2].$str16x[3];
					$second_part  = $str16x[4].$str16x[5].$str16x[6].$str16x[7];
					$third_part = $str16x[8].$str16x[9].$str16x[10].$str16x[11];
					$fourth_part = $str16x[12].$str16x[13].$str16x[14].$str16x[15];

					$first_part = bin_to_hex($first_part);
					$second_part = bin_to_hex($second_part);
					$third_part = bin_to_hex($third_part);
					$fourth_part = bin_to_hex($fourth_part);

					$str16x = $first_part.$second_part.$third_part.$fourth_part;
					return $str16x;
					break;
			}//end switch
		}//end if
	}

//function for coding the hole message
	function enCodeMessage($message)
	{
		if ($message == "")
		{
			
			print "message is empty\n";
			return false;
		}
		else
		{

			$type_of_encoding=get_type_of_encoding($message);

			for($i = 0; $i < strlen($message);$i++)
			{
				$encoded_string.=enCoding($message[$i],$type_of_encoding);
			}

			return $encoded_string;
		}
	}
def OpenSession
	
end

def SendMessage( message )
	
end

    echo "Enter message:";
	$msgtext = trim(fgets(STDIN));
	print enCodeMessage($msgtext);
		 
	
?>