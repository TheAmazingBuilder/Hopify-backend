<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cette migration contient le coeur du système SaaS (Central Database).
     * Elle gère les Super-Admins, les abonnements Stripe et la gestion des tenants.
     */
    public function up(): void
    {
        // 1. Super-Admins de la plateforme Hopify
        Schema::create('central_users', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('admin');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Définition des Plans tarifaires
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('stripe_id')->unique()->nullable(); // ID Produit Stripe
            $table->integer('price_monthly')->default(0);
            $table->integer('price_yearly')->default(0);
            $table->json('features')->nullable(); // Ex: { "max_patients": 1000, "telemedicine": true }
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Abonnements Stripe (SaaS level)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('stripe_id')->unique();
            $table->string('stripe_status');
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // 4. Lignes d'abonnement (multi-prix/add-ons)
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('subscription_uuid')->constrained('subscriptions', 'uuid')->cascadeOnDelete();
            $table->string('stripe_id')->unique();
            $table->string('stripe_product');
            $table->string('stripe_price');
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });

        // 5. Factures SaaS (Paiements d'abonnement)
        Schema::create('tenant_invoices', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('stripe_id')->unique();
            $table->integer('amount');
            $table->string('currency');
            $table->string('status');
            $table->string('invoice_url')->nullable();
            $table->string('pdf_url')->nullable();
            $table->timestamps();
        });

        // 6. Audit Logs SaaS (Actions des tenants ou super-admins)
        Schema::create('tenant_audit_logs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignUuid('user_uuid')->nullable()->constrained('central_users', 'uuid')->nullOnDelete();
            $table->string('action'); // Ex: plan_upgraded, tenant_suspended
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();

            $table->timestamps();
        });

        // 7. Feature Flags (Contrôle d'accès aux fonctionnalités)
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('feature_name');
            $table->string('description')->nullable();
            $table->boolean('is_enabled')->default(false);

            $table->jsonb('config')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'feature_name']);
        });

        // 8. Annonces globales aux tenants
        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 200);
            $table->text('content');
            $table->string('type', 30)->default('info'); // info, warning, emergency
            $table->timestampTz('expires_at')->nullable();
            $table->string('target_plan', 30)->nullable(); // Ex: 'starter' uniquement
            $table->timestampTz('published_at')->nullable();
            $table->timestamps();
        });

        // 9. Support Tickets (Gestion client SaaS)
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('subject', 250);
            $table->text('message');
            $table->string('status', 30)->default('open'); // open, in_progress, resolved,closed
            $table->string('priority', 30)->default('normal');
            $table->foreignUuid('assigned_to')->nullable()->constrained('central_users', 'uuid')->nullOnDelete();
            $table->timestampTz('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('tenant_audit_logs');
        Schema::dropIfExists('tenant_invoices');
        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('central_users');
    }
};
