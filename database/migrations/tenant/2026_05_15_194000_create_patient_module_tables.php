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
