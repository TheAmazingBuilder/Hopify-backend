<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Codes ICD (référentiel)
        Schema::create('icd_codes', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('code', 10)->unique();
            $table->text('description');
            $table->string('category', 10)->nullable();
            $table->string('chapter')->nullable();
            $table->string('version', 10)->default('ICD-10');
            $table->boolean('is_billable')->default(true);
            $table->timestamps();
        });

        // 2. Consultations
        Schema::create('consultations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('doctor_uuid')->constrained('employees', 'uuid')->restrictOnDelete();
            $table->foreignUuid('appointment_uuid')->nullable()->constrained('appointments', 'uuid')->nullOnDelete();
            $table->foreignUuid('hospitalization_uuid')->nullable()->constrained('hospitalizations', 'uuid')->nullOnDelete();

            $table->text('chief_complaint')->nullable();
            $table->text('history_of_illness')->nullable();
            $table->text('review_of_systems')->nullable();
            $table->text('physical_examination')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();

            $table->date('follow_up_date')->nullable();
            $table->text('follow_up_instructions')->nullable();

            $table->boolean('is_finalized')->default(false);
            $table->timestamp('finalized_at')->nullable();
            $table->foreignUuid('finalized_by_uuid')->nullable()->constrained('users', 'uuid')->nullOnDelete();

            $table->timestamp('consultation_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_uuid', 'consultation_date']);
            $table->index(['doctor_uuid', 'consultation_date']);
        });

        // 3. Signes vitaux
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('consultation_uuid')->nullable()->constrained('consultations', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('recorded_by_uuid')->nullable()->constrained('employees', 'uuid')->nullOnDelete();

            $table->decimal('temperature_celsius', 4, 1)->nullable();
            $table->smallInteger('blood_pressure_systolic')->nullable();
            $table->smallInteger('blood_pressure_diastolic')->nullable();
            $table->smallInteger('heart_rate')->nullable();
            $table->smallInteger('respiratory_rate')->nullable();
            $table->decimal('oxygen_saturation', 4, 1)->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->decimal('height_cm', 5, 1)->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->smallInteger('pain_scale')->nullable();
            $table->decimal('blood_glucose', 5, 1)->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['patient_uuid', 'recorded_at']);
        });

        // 4. Diagnostics
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('consultation_uuid')->constrained('consultations', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('icd_code_uuid')->constrained('icd_codes', 'uuid')->cascadeOnDelete();
            $table->string('type', 20)->default('primary'); // primary, secondary, differential
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Catégories de médicaments
        Schema::create('medication_categories', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 6. Médicaments (référentiel)
        Schema::create('medications', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('category_uuid')->nullable()->constrained('medication_categories', 'uuid')->nullOnDelete();
            $table->string('name');
            $table->string('generic_name');
            $table->string('brand_name')->nullable();
            $table->string('form', 30);         // tablet, capsule, liquid, injection...
            $table->string('strength')->nullable();
            $table->string('unit')->nullable();
            $table->string('dci_code')->nullable();
            $table->string('atc_code')->nullable();
            $table->boolean('is_controlled')->default(false);
            $table->boolean('requires_prescription')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('contraindications')->nullable();
            $table->text('side_effects')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        // 7. Ordonnances
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('consultation_uuid')->nullable()->constrained('consultations', 'uuid')->nullOnDelete();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('doctor_uuid')->constrained('employees', 'uuid')->restrictOnDelete();
            $table->string('prescription_number')->nullable()->unique();
            $table->string('status', 20)->default('active'); // active, dispensed, cancelled, expired
            $table->text('notes')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('dispensed_at')->nullable();
            $table->foreignUuid('dispensed_by_uuid')->nullable()->constrained('employees', 'uuid')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_uuid', 'created_at']);
        });

        // 8. Lignes d'ordonnance
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('prescription_uuid')->constrained('prescriptions', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('medication_uuid')->constrained('medications', 'uuid')->cascadeOnDelete();
            $table->string('dosage');
            $table->string('frequency');
            $table->string('route', 30)->nullable();
            $table->smallInteger('duration_days')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_substitutable')->default(true);
            $table->timestamps();
        });

        // 9. Tests labo (référentiel)
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('code', 30)->unique();
            $table->string('category')->nullable();
            $table->string('unit')->nullable();
            $table->string('reference_range_male')->nullable();
            $table->string('reference_range_female')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 10. Ordres de laboratoire
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('consultation_uuid')->nullable()->constrained('consultations', 'uuid')->nullOnDelete();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('ordered_by_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('status', 20)->default('pending');   // pending, in_progress, completed, cancelled
            $table->string('priority', 20)->default('routine'); // routine, urgent, stat
            $table->text('clinical_notes')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->foreignUuid('collected_by_uuid')->nullable()->constrained('employees', 'uuid')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_uuid', 'created_at']);
            $table->index(['status', 'priority']);
        });

        // 11. Lignes de l'ordre labo
        Schema::create('lab_order_items', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('lab_order_uuid')->constrained('lab_orders', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('lab_test_uuid')->constrained('lab_tests', 'uuid')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->timestamps();

            $table->unique(['lab_order_uuid', 'lab_test_uuid']);
        });

        // 12. Résultats labo
        Schema::create('lab_results', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('lab_order_item_uuid')->constrained('lab_order_items', 'uuid')->cascadeOnDelete();
            $table->string('value', 100);
            $table->string('unit', 30)->nullable();
            $table->string('reference_range', 50)->nullable();
            $table->boolean('is_abnormal')->default(false);
            $table->string('abnormality_level', 20)->nullable(); // low, high, critical
            $table->text('notes')->nullable();
            $table->timestamp('resulted_at')->useCurrent();
            $table->foreignUuid('resulted_by_uuid')->nullable()->constrained('employees', 'uuid')->nullOnDelete();
            $table->foreignUuid('validated_by_uuid')->nullable()->constrained('employees', 'uuid')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index(['lab_order_item_uuid', 'resulted_at']);
            $table->index('is_abnormal');
        });

        // 13. Ordres d'imagerie
        Schema::create('imaging_orders', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('consultation_uuid')->nullable()->constrained('consultations', 'uuid')->nullOnDelete();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('ordered_by_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();
            $table->string('modality', 30);   // x_ray, ct_scan, mri, ultrasound...
            $table->string('body_part');
            $table->string('urgency', 20)->default('routine'); // routine, urgent, emergency
            $table->string('status', 20)->default('pending');
            $table->text('clinical_indication')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_uuid', 'created_at']);
        });

        // 14. Résultats d'imagerie
        Schema::create('imaging_results', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('imaging_order_uuid')->constrained('imaging_orders', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('radiologist_uuid')->nullable()->constrained('employees', 'uuid')->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->text('report')->nullable();
            $table->string('impression')->nullable();
            $table->boolean('is_critical')->default(false);
            $table->timestamp('resulted_at')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imaging_results');
        Schema::dropIfExists('imaging_orders');
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('lab_order_items');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('lab_tests');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('medication_categories');
        Schema::dropIfExists('diagnoses');
        Schema::dropIfExists('vital_signs');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('icd_codes');
    }
};
