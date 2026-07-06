<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add unique constraint to chassis_number (VIN) column.
     * Per ERD spec: VIN must be unique across all vehicles.
     * Task 4.1.1 - Subtask 3.1.1b: VIN & Plate Number Uniqueness Validation
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Only add if not already unique
            $table->string('chassis_number', 17)->nullable()->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique(['chassis_number']);
        });
    }
};
