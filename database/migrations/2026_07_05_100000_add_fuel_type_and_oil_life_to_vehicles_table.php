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
        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'fuel_type')) {
                $table->enum('fuel_type', ['gasoline', 'diesel', 'electric', 'hybrid'])
                    ->default('gasoline')
                    ->after('color');
            }
            if (! Schema::hasColumn('vehicles', 'oil_life_percentage')) {
                $table->unsignedTinyInteger('oil_life_percentage')
                    ->default(100)
                    ->after('health_score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['fuel_type', 'oil_life_percentage']);
        });
    }
};
