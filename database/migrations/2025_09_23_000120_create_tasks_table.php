<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_id')->constrained('uploads')->cascadeOnDelete();
            $table->string('task_id')->nullable();
            $table->string('request_id')->nullable();
            $table->string('problem_id')->nullable();
            $table->string('change_id')->nullable();
            $table->string('title')->nullable();
            $table->timestampTz('scheduled_start_at_src')->nullable();
            $table->timestampTz('actual_end_at_src')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
