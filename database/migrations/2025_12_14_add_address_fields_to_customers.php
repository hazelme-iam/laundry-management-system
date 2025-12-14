<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'barangay')) {
                $table->string('barangay')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'purok')) {
                $table->string('purok')->nullable()->after('barangay');
            }
            if (!Schema::hasColumn('customers', 'street')) {
                $table->string('street')->nullable()->after('purok');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['barangay', 'purok', 'street']);
        });
    }
};
