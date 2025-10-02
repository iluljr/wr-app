<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('activity_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('title');
            $table->text('output')->nullable();
            $table->timestamps();

            $table->index(['activity_date', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_activities');
    }
};
