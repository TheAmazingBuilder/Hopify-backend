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
