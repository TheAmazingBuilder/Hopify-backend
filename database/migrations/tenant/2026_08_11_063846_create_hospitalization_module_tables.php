<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('department_uuid')->constrained('departments', 'uuid')->cascadeOnDelete();
            $table->string('name', 50);
            $table->string('type', 30)->default('consultation'); // consultation, recovery, surgery
            $table->integer('floor')->nullable();
            $table->integer('capacity')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['department_uuid', 'is_active']);;
        });

        // 2. Lits
        Schema::create('beds', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('room_uuid')->constrained('rooms', 'uuid')->cascadeOnDelete();
            $table->string('name', 20);
            $table->string('type', 30)->default('standard'); // standard, icu, pediatric, bariatric
            $table->string('status', 20)->default('available'); // available, occupied, maintenance, reserved
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamps();

            $table->index(['room_uuid', 'status']);
            $table->index('status');
        });

        // 3. Hospitalisations
        Schema::create('hospitalizations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('bed_uuid')->constrained('beds', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('admitted_by_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('attending_doctor_uuid')->nullable()->constrained('employees', 'uuid')->nullOnDelete();

            $table->text('admission_diagnosis');
            $table->text('discharge_diagnosis')->nullable();
            $table->timestamp('admitted_at');
            $table->timestamp('discharged_at')->nullable();
            $table->string('status', 20)->default('active'); // active, discharged, transferred, deceased
            $table->text('discharge_notes')->nullable();
            $table->string('discharge_type', 20)->nullable(); // planned, ama, transfer, deceased

            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_uuid', 'admitted_at']);
            $table->index(['bed_uuid', 'status']);
            $table->index('status');
        });

        // 4. Notes infirmières
        Schema::create('nursing_notes', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('hospitalization_uuid')->constrained('hospitalizations', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('nurse_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();
            $table->string('type', 30)->default('general'); // general, medication, observation, wound
            $table->text('note');
            $table->timestamp('noted_at')->useCurrent();
            $table->timestamps();

            $table->index(['hospitalization_uuid', 'noted_at']);
        });

        // 5. Visites médicales (doctor rounds)
        Schema::create('doctor_rounds', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('hospitalization_uuid')->constrained('hospitalizations', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('doctor_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['hospitalization_uuid', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_rounds');
        Schema::dropIfExists('nursing_notes');
        Schema::dropIfExists('hospitalizations');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('rooms');
    }
};
