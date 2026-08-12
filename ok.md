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
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Shared\Enums\{
    AllergySeverity,
    AllergyType,
    AntecedentType,
    BloodType,
    Gender
};

return new class extends Migration
{
    public function up(): void
    {
        // 0. Insurance Companies
        Schema::create('insurance_companies', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 1. Table principale des Patients
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('uuid')->primary(); 
            $table->foreignUuid('user_uuid')->nullable()->constrained('users', 'id')->nullOnDelete();

            $table->string('mrn', 20); 
            $table->string('fname', 100);
            $table->string('lname', 100);
            $table->date('dob')->nullable();
            $table->string('gender', 30)->default(Gender::Male->value);
            $table->string('blood_type', 30)->default(BloodType::O_Pos->value);
            
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('address')->nullable();
            $table->string('city')->nullable();
            $table->string('nationality', 100)->nullable();

            $table->boolean('is_deceased')->default(false);
            $table->timestamp('deceased_at')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['tenant_id', 'mrn']);
            $table->index(['tenant_id', 'lname', 'fname']);
        });

        // 2. Contacts d'urgence
        Schema::create('patient_contacts', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship');
            $table->string('phone');
            $table->boolean('is_emergency_contact')->default(false);
            $table->boolean('is_legal_guardian')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Assurances
        Schema::create('patient_insurances', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('insurance_company_id')->constrained('insurance_companies', 'uuid')->cascadeOnDelete();
            $table->string('policy_number');
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Allergies
        Schema::create('allergies', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->string('allergen');
            $table->string('type', 50)->default(AllergyType::Medication->value);
            $table->string('severity', 50)->default(AllergySeverity::Moderate->value);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Antécédents
        Schema::create('medical_antecedents', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->string('type', 50)->default(AntecedentType::Medical->value);
            $table->string('condition');
            $table->string('status', 50)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('medical_antecedents');
        Schema::dropIfExists('allergies');
        Schema::dropIfExists('patient_insurances');
        Schema::dropIfExists('patient_contacts');
        Schema::dropIfExists('insurance_companies');
        Schema::dropIfExists('patients');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid')->nullable()->index(); // L'utilisateur qui a fait l'action
            $table->string('action')->index(); // ex: patient.updated
            $table->string('model_type');
            $table->uuid('model_uuid');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};


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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('department_uuid')->nullable()
                  ->constrained('departments', 'uuid')->nullOnDelete();

            $table->string('employee_number')->unique();
            $table->string('fname', 100);
            $table->string('lname', 100);

            // Différenciateur de rôle — remplace les tables séparées
            $table->string('role_type', 30);
            // doctor | nurse | technician | pharmacist | receptionist | admin | director

            $table->string('specialization')->nullable();   // pertinent surtout pour doctor
            $table->string('license_number')->nullable();    // ordre professionnel (doctor/nurse/pharmacist)

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();

            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable(); // pour signer ordonnances/rapports

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['role_type', 'is_active']);
            $table->index('department_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

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
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignUuid('head_doctor_uuid')->nullable()->after('description')
                  ->constrained('employees', 'uuid')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments_tables', function (Blueprint $table) {
            $table->dropForeign(['head_doctor_uuid']);
            $table->dropColumn('head_doctor_uuid');
        });
    }
};


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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('employee_uuid')->nullable()->after('uuid')
                ->constrained('employees', 'uuid')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_tables', function (Blueprint $table) {
            $table->dropForeign(['employee_uuid']);
            $table->dropColumn('employee_uuid');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    /**
     * VERSION CORRIGÉE de 2026_05_16_064000_create_scheduling_module_tables.php
     *
     * Bug fixé : `doctor_uuid` référençait `users.uuid` directement.
     * Un médecin est un `employee` (role_type = 'doctor'), pas un `user`.
     * Le user est juste le compte de connexion optionnel lié à l'employee.
     * → doctor_uuid référence maintenant employees.uuid.
     */


    public function up(): void
    {
        // 1. Table des Rendez-vous
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('doctor_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();

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
            $table->foreignUuid('doctor_uuid')->constrained('employees', 'uuid')->cascadeOnDelete();

            $table->tinyInteger('day_of_week'); // 0 (Sunday) to 6 (Saturday)
            $table->time('start_time');
            $table->time('end_time');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['doctor_uuid', 'day_of_week', 'start_time', 'end_time'], 'idx_doctor_schedule_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
        Schema::dropIfExists('appointments');
    }
};


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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Grille tarifaire
        Schema::create('tariffs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('code', 30)->nullable();
            $table->string('type', 30); // consultation, act, room_day...
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('CAD');
            $table->boolean('is_taxable')->default(false);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Factures
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('patient_uuid')->constrained('patients', 'uuid')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('type', 30); // consultation, hospitalization, pharmacy, laboratory, imaging
            $table->string('status', 20)->default('draft'); // draft, issued, partially_paid, paid, overdue, cancelled
            $table->string('currency', 3)->default('CAD');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('insurance_amount', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by_uuid')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_uuid', 'created_at']);
            $table->index('status');
        });

        // 3. Lignes de facture
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('invoice_uuid')->constrained('invoices', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('tariff_uuid')->nullable()->constrained('tariffs', 'uuid')->nullOnDelete();
            $table->string('description');
            $table->string('reference_type')->nullable(); // consultation, lab_order...
            $table->uuid('reference_uuid')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        // 4. Paiements
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('invoice_uuid')->constrained('invoices', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('received_by_uuid')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method', 30); // cash, card, insurance, bank_transfer, check, online
            $table->string('reference')->nullable();
            $table->timestamp('paid_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('invoice_uuid');
        });

        // 5. Réclamations d'assurance
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('invoice_uuid')->constrained('invoices', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('insurance_company_uuid')->constrained('insurance_companies', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('patient_insurance_uuid')->nullable()->constrained('patient_insurances', 'uuid')->nullOnDelete();
            $table->string('claim_number')->nullable();
            $table->decimal('claimed_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->default(0);
            $table->decimal('rejected_amount', 12, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending, submitted, in_review, approved, rejected...
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['insurance_company_uuid', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('tariffs');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Stock (inventaire)
        Schema::create('medication_inventory', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('medication_uuid')->constrained('medications', 'uuid')->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('reorder_level', 10, 2)->default(10);
            $table->date('expires_at')->nullable();
            $table->date('received_at')->nullable();
            $table->string('supplier')->nullable();
            $table->string('storage_location')->nullable();
            $table->timestamps();

            $table->index('medication_uuid');
            $table->index('expires_at');
        });

        // 2. Transactions (entrées/sorties de stock)
        Schema::create('medication_transactions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('medication_uuid')->constrained('medications', 'uuid')->cascadeOnDelete();
            $table->string('type', 20); // in, out, adjustment, return, expired
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('reference_type')->nullable(); // prescription, hospitalization...
            $table->uuid('reference_uuid')->nullable();
            $table->string('batch_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by_uuid')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['medication_uuid', 'created_at']);
            $table->index('type');
        });

        // 3. Interactions médicamenteuses
        Schema::create('drug_interactions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('medication_uuid_1')->constrained('medications', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('medication_uuid_2')->constrained('medications', 'uuid')->cascadeOnDelete();
            $table->string('severity', 20); // minor, moderate, major, contraindicated
            $table->text('description');
            $table->text('clinical_management')->nullable();
            $table->timestamps();

            $table->unique(['medication_uuid_1', 'medication_uuid_2']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_interactions');
        Schema::dropIfExists('medication_transactions');
        Schema::dropIfExists('medication_inventory');
    }
};


