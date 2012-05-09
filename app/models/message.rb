#encoding: utf-8
require "savon_model"
require "Iconv"
class Message < ActiveRecord::Base

#  has_one :client, :foreign_key => "client_id"
   belongs_to :client, :class_name => "Client"

  HUMAN_ATTRIBUTE_NAMES = {
    :msgtext => 'Текст сообщения',
    :client_id => 'Клиент'  
  }
  class << self
    def human_attribute_name attribute_name
      HUMAN_ATTRIBUTE_NAMES[attribute_name.to_sym] || super
    end
  end



def bin_to_hex(str)
  case str
  when "0000"
    return "0"
  when "0001"
    return "1"
  when "0010"
    return "2"
  when "0011"
    return "3"
  when "0100" 
    return "4"
  when "0101" 
    return "5"
  when "0110" 
    return "6"
  when "0111" 
    return "7"
  when "1000" 
    return "8"
  when "1001" 
    return "9"
  when "1010" 
    return "A"
  when "1011" 
    return "B"
  when "1100" 
    return "C"
  when "1101" 
    return "D"
  when "1110" 
    return "E"
  when "1111" 
    return "F"
  else
    return "0"
  end
  
end
      
  
def get_type_of_encoding(msgtext)
  inadmissible_symbols = ["[" , "]" , "\\" , "^" , "_" , "`" , "{", "}" , "|" , "~"]
  type_of_encoding = 0
  if msgtext == nil then
    print 'Message is empty\n'    
  else
    for i in (0..msgtext.chomp.length-1) do
      
      if (msgtext[i].ord > 127 || inadmissible_symbols.include?(msgtext[i])) then
        
        type_of_encoding = 1;
        break
      end
    end
      
  end
  return type_of_encoding
end

def enCoding(symbol, type_of_encoding)
#  puts  symbol.encoding
  if (symbol.length==0 || type_of_encoding>1 || type_of_encoding<0)
    puts 0
  else
    case type_of_encoding
    when 0
      if symbol=='@'
        return "00"
      else
        if symbol=='?'
          return "02"
        end
      end
      code = symbol.ord
      str16x = ""
      for i in (0..7) do
        bit = code & 1
        case bit
          when 0 
            str16x += "0"
          when 1
            str16x += "1"
        end
        code = code >> 1                   
      end
      
      str16x = str16x.reverse
      
      
      
      high_part = str16x[0]+str16x[1]+str16x[2]+str16x[3];
      low_part  = str16x[4]+str16x[5]+str16x[6]+str16x[7];

      high_part = bin_to_hex(high_part);
      low_part = bin_to_hex(low_part);

      str16x = high_part.to_s+low_part.to_s;
      return str16x;
    when 1
#puts symbol.encoding
#puts "\n"
	ic = Iconv.new("UCS-2", symbol.encoding.to_s)
	symbol = ic.iconv(symbol)
puts symbol[0].ord
puts "\n"
puts symbol[1].ord
puts "\n"
#	symbol = symbol.encode("UTF-8")
#	symbol = symbol.encode("Windows-1251")
#	symbol = symbol.encode("UTF-16")
#	puts symbol.encoding
#	puts "\n"
#      symbol.encode("UTF-8")          
      
      code = ((symbol[0].ord)*256+symbol[1].ord);

      str16x = "";
      
      for i in (0..15) do
        bit = code & 1
        case bit
          when 0 
            str16x += "0"
          when 1
            str16x += "1"
        end
        code = code >> 1      
      end
      str16x = str16x.reverse
      puts str16x
      puts "\n"
      
      first_part = str16x[0]+str16x[1]+str16x[2]+str16x[3];
      second_part  = str16x[4]+str16x[5]+str16x[6]+str16x[7];
      third_part = str16x[8]+str16x[9]+str16x[10]+str16x[11];
      fourth_part = str16x[12]+str16x[13]+str16x[14]+str16x[15];

      first_part = bin_to_hex(first_part);
      second_part = bin_to_hex(second_part);
      third_part = bin_to_hex(third_part);
      fourth_part = bin_to_hex(fourth_part);

      str16x = first_part+second_part+third_part+fourth_part;
      return str16x;
      
    end
  end
end

def enCodeMessage(message)
  
    if (message.length == 0)
    
      
      print "message is empty\n";
      return false;
    
    else
      type_of_encoding=get_type_of_encoding(message);

      encoded_string = ""
      for i in (0..message.length-1) do
        encoded_string = encoded_string + enCoding(message[i],type_of_encoding);
      end

      return encoded_string;
    end
end






  
    include Savon::Model

  document "https://sms4b.ru/webservices/sms.asmx?wsdl"
          

  def CloseSession(sid)
  
        client.http.auth.ssl.verify_mode = :none
    Savon.env_namespace = :soap;

    response  = client.request "CloseSession" do 
      http.headers.delete("SOAP request")

      client.http.headers["Host"] = "sms4b.ru"
      client.http.headers["SOAPAction"] = "\"SMS client/CloseSession\""

      soap.namespaces = {
            "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance",
            "xmlns:xsd" => "http://www.w3.org/2001/XMLSchema",
        "xmlns:soap" => "http://schemas.xmlsoap.org/soap/envelope/"
      }
      soap.input = ["CloseSession", {"xmlns" => "SMS client"}]
      soap.body do |xml|
        xml.CloseSession(sid)            
      end
      soap.element_form_default = :unqualified
    end
    response.body[:close_session_response][:close_session_result]
  end
  
    def Auth(login, password)
#   client = Savon::Client.new("https://sms4b.ru/webservices/sms.asmx?wsdl")
    client.http.auth.ssl.verify_mode = :none


#   soap.xml = "<custom><soap>request</soap></custom>"
    
#   client.request
#
#   client.request :http, "StartSession",  :Login=>login, :Password=>password, :Gmt=>11
#   client.wsdl.soap_actions
    Savon.env_namespace = :soap;
#   response  = client.request(:StartSession, :xmlns => "SMS client") do
#   response  = client.request :StartSession do
    response  = client.request "StartSession" do 
#     client.http.headers.add("Host")
      http.headers.delete("SOAP request")

      client.http.headers["Host"] = "sms4b.ru"
      client.http.headers["SOAPAction"] = "\"SMS client/StartSession\""

      soap.namespaces = {
            "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance",
            "xmlns:xsd" => "http://www.w3.org/2001/XMLSchema",
        "xmlns:soap" => "http://schemas.xmlsoap.org/soap/envelope/"
  }

#     soap.header = {
#       "StartSession", :attributes! => {"StartSession" => { :xmlns => "SMS client } }, }

#soap.body = {
 # "StartSession" => {
#    "Login" => login,
#    "Password" => password,
#    "Gmt"=> '11'
#  },
#  :attributes! => {"StartSession" => { :xmlns => 'SMS client' } }, }


      soap.input = ["StartSession", {"xmlns" => "SMS client"}]



#     soap.body = { :Login=>login, :Password=>password, :Gmt=>11 }
      soap.body do |xml|
        xml.Login(login)
        xml.Password(password)
        xml.Gmt(11)
      end
      soap.element_form_default = :unqualified
    end
    
    response.body[:start_session_response][:start_session_result]
#   response.body
    
  end    



def SaveMessage( sid, smsmail, message_text )

    client.http.auth.ssl.verify_mode = :none
    Savon.env_namespace = :soap;


    response  = client.request "SaveMessage" do 
      http.headers.delete("SOAP request")

      client.http.headers["Host"] = "sms4b.ru"
      client.http.headers["SOAPAction"] = "\"SMS client/SaveMessage\""

      soap.namespaces = {
            "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance",
            "xmlns:xsd" => "http://www.w3.org/2001/XMLSchema",
        "xmlns:soap" => "http://schemas.xmlsoap.org/soap/envelope/"
      }


      soap.input = ["SaveMessage", {"xmlns" => "SMS client"}]
                               
      soap.body do |xml|
        xml.SessionID(sid)
        xml.guid(UUIDTools::UUID.random_create.to_s)
	xml.Destination( smsmail )
	xml.Source('GEIZER')
	xml.Body( enCodeMessage( message_text ) )
	xml.Encoded( get_type_of_encoding( message_text ) )
	xml.dton(2)
	xml.dnpi(0)
	xml.ston(2)
	xml.snpi(0)
	xml.TimeOff('0')
	xml.Priority(0)
	xml.NoRequest('true')        
      end  
      soap.element_form_default = :unqualified
    end
    
    response.body[:save_message_response][:save_message_result]

    
  end 

  def sms
	sid = Auth('mgerasim', 'zaq12wsx')
	
	SaveMessage(sid, Client.find(1).smsmail, msgtext)
	CloseSession( sid )
	
  end

end
                                                    	