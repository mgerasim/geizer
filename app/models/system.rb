#encoding: utf-8
class System < ActiveRecord::Base
  HUMAN_ATTRIBUTE_NAMES = {
    :name => 'Наименование'  
  }
  class << self
    def human_attribute_name attribute_name
      HUMAN_ATTRIBUTE_NAMES[attribute_name.to_sym] || super
    end
  end


  has_and_belongs_to_many :filters
  has_and_belongs_to_many :clients

end
