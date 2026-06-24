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
        Schema::create('service_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');  // Kendaraan
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('cascade'); // Bengkel
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null'); // Teknisi

            // Jenis service
            $table->enum('service_type', [
                'oil_change',       // Ganti oli
                'tune_up',          // Tune up
                'periodic_service', // Servis berkala
                'repair',           // Perbaikan
                'tire_change',      // Ganti ban
                'brake_service',    // Servis rem
                'other',            // Lainnya
            ])->default('periodic_service');

            $table->string('service_type_label')->nullable(); // Label jenis service jika "other"
            $table->date('service_date');                     // Tanggal service
            $table->unsignedInteger('odometer_in');           // Odometer saat masuk (km)
            $table->unsignedInteger('odometer_out')->nullable(); // Odometer saat keluar (km)
            $table->unsignedInteger('next_service_odometer')->nullable(); // Rekomendasi odometer servis berikutnya
            $table->date('next_service_date')->nullable();    // Rekomendasi tanggal servis berikutnya
            $table->decimal('cost', 12, 2)->default(0);       // Biaya service (Rp)
            $table->text('notes')->nullable();                // Catatan servis
            $table->text('parts_replaced')->nullable();       // Komponen yang diganti (JSON string)
            $table->string('invoice_number')->nullable();     // Nomor invoice/kwitansi
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index(['vehicle_id', 'service_date']);
            $table->index(['workshop_id', 'service_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_histories');
    }
};
