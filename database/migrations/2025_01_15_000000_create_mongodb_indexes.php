<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMongodbIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create indexes for messages collection
        DB::connection('mongodb')->collection('messages')->createIndex(['from' => 1, 'to' => 1]);
        DB::connection('mongodb')->collection('messages')->createIndex(['to' => 1, 'read' => 1]);
        DB::connection('mongodb')->collection('messages')->createIndex(['created_at' => 1]);
        
        // Create indexes for users collection
        DB::connection('mongodb')->collection('users')->createIndex(['email' => 1], ['unique' => true]);
        DB::connection('mongodb')->collection('users')->createIndex(['name' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop indexes
        DB::connection('mongodb')->collection('messages')->dropIndex(['from' => 1, 'to' => 1]);
        DB::connection('mongodb')->collection('messages')->dropIndex(['to' => 1, 'read' => 1]);
        DB::connection('mongodb')->collection('messages')->dropIndex(['created_at' => 1]);
        
        DB::connection('mongodb')->collection('users')->dropIndex(['email' => 1]);
        DB::connection('mongodb')->collection('users')->dropIndex(['name' => 1]);
    }
}

