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
