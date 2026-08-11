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
