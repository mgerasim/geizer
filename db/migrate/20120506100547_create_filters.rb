class CreateFilters < ActiveRecord::Migration
  def change
    create_table :filters do |t|
      t.string :name
      t.integer :resource

      t.timestamps
    end
  end
end
