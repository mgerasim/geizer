#encoding: utf-8
class Client < ActiveRecord::Base

  attr_accessible  :firstname, :lastname, :secondname, :smsmail, :email, :address, :system_ids

  HUMAN_ATTRIBUTE_NAMES = {
    :firstname => 'Имя',
    :lastname => 'Фамилия',
    :secondname  => 'Отчество',
    :smsmail => "Сотовый номер", 
    :email => "Электронный адрес", 
    :address => "Почтовый адрес"  
  }
  class << self
    def human_attribute_name attribute_name
      HUMAN_ATTRIBUTE_NAMES[attribute_name.to_sym] || super
    end
  end

  has_and_belongs_to_many :systems
  has_many :messages

  def client_name
    "#{lastname} #{firstname} #{secondname} <#{smsmail}>"
  end  
end
