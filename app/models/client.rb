#encoding: utf-8
class Client < ActiveRecord::Base
  HUMAN_ATTRIBUTE_NAMES = {
    :firstname => 'Имя'
  }
  class << self
    def human_attribute_name attribute_name
      HUMAN_ATTRIBUTE_NAMES[attribute_name.to_sym] || super
    end
  end
  
end
