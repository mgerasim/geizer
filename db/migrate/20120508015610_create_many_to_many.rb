class CreateManyToMany < ActiveRecord::Migration
  def change
	create_table :clients_systems do |t|
		t.integer :client_id
		t.integer :system_id
	end

	create_table :filters_systems do |t|
		t.integer :filter_id
		t.integer :system_id
	end
  end


end
