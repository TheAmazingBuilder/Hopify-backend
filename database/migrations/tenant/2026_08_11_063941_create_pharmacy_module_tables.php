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
