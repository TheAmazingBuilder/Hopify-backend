<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Types de shifts
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name'); // Matin, Soir, Nuit
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_hours')->nullable();
            $table->boolean('is_night_shift')->default(false);
            $table->timestamps();
        });

        // 2. Planning des employés
        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('employee_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('shift_uuid')->constrained('shifts', 'uuid')->cascadeOnDelete();
            $table->date('date');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->string('status', 20)->default('scheduled'); // scheduled, completed, missed
            $table->timestamps();

            $table->unique(['employee_uuid', 'date', 'shift_uuid']);
            $table->index('date');
        });

        // 3. Demandes de congé
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('employee_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('approved_by_uuid')->nullable()->constrained('employees', 'uuid')->nullOnDelete();
            $table->string('type', 30); // vacation, sick, maternity, paternity, emergency, unpaid
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count')->nullable();
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, cancelled
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['employee_uuid', 'start_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('employee_shifts');
        Schema::dropIfExists('shifts');
    }
};
