class CreateMessages < ActiveRecord::Migration
  def change
    create_table :messages do |t|
      t.text :msgtext
      t.integer :client_id

      t.timestamps
    end
  end
end
