#encoding: utf-8
require 'net/http'
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

def Send( smsmail, smstext )
	uri = URI('http://api.smsfeedback.ru/send')
            params = { :login => 'mgerasim', :password => 'zaq12wsx', :phone => smsmail, :text => smstext }
            uri.query = URI.encode_www_form(params)
 
 
        res = Net::HTTP.get_response(uri)
            puts res.body if res.is_a?(Net::HTTPSuccess)

end

def sms
	Send(client.smsmail, msgtext)
end


end
                                                    	