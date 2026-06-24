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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pemilik kendaraan
            $table->string('plate_number')->unique();        // Nomor polisi (e.g. B 1234 ABC)
            $table->string('brand');                         // Merek (e.g. Honda, Yamaha)
            $table->string('model');                         // Model (e.g. Vario 160, NMAX)
            $table->string('type')->nullable();              // Tipe/varian (e.g. CBS, ABS)
            $table->year('year');                            // Tahun kendaraan
            $table->string('color')->nullable();             // Warna
            $table->string('engine_number')->nullable();     // Nomor mesin
            $table->string('chassis_number')->nullable();    // Nomor rangka
            $table->unsignedInteger('current_odometer')->default(0); // Odometer saat ini (km)
            $table->unsignedInteger('next_service_odometer')->nullable(); // Odometer servis berikutnya
            $table->date('next_service_date')->nullable();   // Tanggal servis berikutnya
            $table->string('qr_code')->unique()->nullable(); // Kode unik QR
            $table->string('qr_code_url')->nullable();       // URL gambar QR (Supabase Storage)
            $table->string('photo_url')->nullable();         // Foto kendaraan
            $table->enum('health_status', ['good', 'warning', 'critical'])->default('good'); // Status kesehatan
            $table->unsignedTinyInteger('health_score')->default(100); // Skor kesehatan 0-100
            $table->boolean('is_active')->default(true);     // Status aktif
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
