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
