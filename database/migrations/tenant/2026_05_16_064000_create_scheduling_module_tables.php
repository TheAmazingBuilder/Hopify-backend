<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Table des Rendez-vous
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('doctor_uuid')->constrained('users', 'uuid')->cascadeOnDelete();
            
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            
            $table->string('status', 30)->default('pending'); // pending, confirmed, cancelled, completed, no_show
            $table->string('type', 50)->default('consultation'); // consultation, follow_up, emergency, surgery
            
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['doctor_uuid', 'start_time']);
            $table->index(['patient_uuid', 'start_time']);
            $table->index('status');
        });

        // 2. Table des Disponibilités des médecins
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('doctor_uuid')->constrained('users', 'uuid')->cascadeOnDelete();
            
            $table->tinyInteger('day_of_week'); // 0 (Sunday) to 6 (Saturday)
            $table->time('start_time');
            $table->time('end_time');
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->unique(['doctor_uuid', 'day_of_week', 'start_time', 'end_time'], 'idx_doctor_schedule_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
        Schema::dropIfExists('appointments');
    }
};
