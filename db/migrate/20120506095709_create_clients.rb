class CreateClients < ActiveRecord::Migration
  def change
    create_table :clients do |t|
      t.string :firstname
      t.string :lastname
      t.string :secondname
      t.string :smsmail
      t.string :email
      t.text :address

      t.timestamps
    end
  end
end
