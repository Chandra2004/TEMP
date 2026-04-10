<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_063500_AddSoftDeletesToCoreTables
{
    public function up()
    {
        // Tambah soft deletes ke tabel penting
        $tables = ['users', 'data_users', 'events', 'event_categories', 'registrations', 'clubs', 'categories'];
        foreach ($tables as $table) {
            Schema::table($table, function ($t) {
                $t->softDeletes(); // Tambah deleted_at
            });
        }
    }

    public function down()
    {
        $tables = ['users', 'data_users', 'events', 'event_categories', 'registrations', 'clubs', 'categories'];
        foreach ($tables as $table) {
            Schema::table($table, function ($t) {
                $t->dropSoftDeletes();
            });
        }
    }
}