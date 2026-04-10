<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_061700_AddTeamsFields
{
    public function up()
    {
        // Add team_id to roles table
        Schema::table('roles', function ($table) {
            $table->unsignedBigInteger('team_id')->nullable();
            $table->index('team_id');
        });

        // Add team_id to model_has_permissions table
        Schema::table('model_has_permissions', function ($table) {
            $table->unsignedBigInteger('team_id')->nullable();
            $table->index('team_id');
        });

        // Add team_id to model_has_roles table
        Schema::table('model_has_roles', function ($table) {
            $table->unsignedBigInteger('team_id')->nullable();
            $table->index('team_id');
        });
    }

    public function down()
    {
        Schema::table('roles', function ($table) {
            $table->dropColumn('team_id');
        });

        Schema::table('model_has_permissions', function ($table) {
            $table->dropColumn('team_id');
        });

        Schema::table('model_has_roles', function ($table) {
            $table->dropColumn('team_id');
        });
    }
}
