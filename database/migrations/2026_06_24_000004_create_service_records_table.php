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
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('cascade');
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');

            $table->string('service_type');
            $table->unsignedInteger('odometer_at_service');
            $table->text('mechanic_notes')->nullable();
            $table->enum('status', ['completed', 'in_progress'])->default('completed');
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->timestamp('service_date');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'service_date']);
            $table->index(['workshop_id', 'service_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
