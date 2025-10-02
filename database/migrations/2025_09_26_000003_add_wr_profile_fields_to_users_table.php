<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable();
            $table->string('project_name')->nullable();
            $table->string('project_company')->nullable();
            $table->string('contractor_name')->nullable();
            $table->string('contractor_supervisor_name')->nullable();
            $table->string('contractor_supervisor_title')->nullable();
            $table->string('project_supervisor_name')->nullable();
            $table->string('project_supervisor_title')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'project_name',
                'project_company',
                'contractor_name',
                'contractor_supervisor_name',
                'contractor_supervisor_title',
                'project_supervisor_name',
                'project_supervisor_title',
            ]);
        });
    }
};
