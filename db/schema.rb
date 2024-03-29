# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended to check this file into your version control system.

ActiveRecord::Schema.define(:version => 20120508015610) do

  create_table "clients", :force => true do |t|
    t.string   "firstname"
    t.string   "lastname"
    t.string   "secondname"
    t.string   "smsmail"
    t.string   "email"
    t.text     "address"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
  end

  create_table "clients_systems", :force => true do |t|
    t.integer "client_id"
    t.integer "system_id"
  end

  create_table "filters", :force => true do |t|
    t.string   "name"
    t.integer  "resource"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
  end

  create_table "filters_systems", :force => true do |t|
    t.integer "filter_id"
    t.integer "system_id"
  end

  create_table "messages", :force => true do |t|
    t.text     "msgtext"
    t.integer  "client_id"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
  end

  create_table "systems", :force => true do |t|
    t.string   "name"
    t.datetime "created_at", :null => false
    t.datetime "updated_at", :null => false
  end

end
