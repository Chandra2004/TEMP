<?php

namespace Database\Migrations;

use TheFramework\App\Schema;

class Migration_2026_04_06_061600_CreateSpatiePermissionsTable
{
    public function up()
    {
        // 1. Create Permissions Table
        Schema::create('permissions', function ($table) {
            $table->bigIncrements('id');
            $table->string('uid', 36)->unique();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();

            $table->unique('name'); // Simplified unique for native framework
        });

        // 2. Create Roles Table
        Schema::create('roles', function ($table) {
            $table->bigIncrements('id');
            $table->string('uid', 36)->unique();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();

            $table->unique('name');
        });

        // 3. Create Model Has Permissions (Pivot)
        Schema::create('model_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index('model_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->cascadeOnDelete();

            $table->compositePrimaryKey(['permission_id', 'model_id', 'model_type']);
        });

        // 4. Create Model Has Roles (Pivot)
        Schema::create('model_has_roles', function ($table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index('model_id');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->cascadeOnDelete();

            $table->compositePrimaryKey(['role_id', 'model_id', 'model_type']);
        });

        // 5. Create Role Has Permissions (Pivot)
        Schema::create('role_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->cascadeOnDelete();

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->cascadeOnDelete();

            $table->compositePrimaryKey(['permission_id', 'role_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
}
