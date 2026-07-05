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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Akun bengkel
            $table->string('name');                          // Nama bengkel
            $table->string('phone')->nullable();             // Nomor telepon
            $table->string('email')->nullable();             // Email bengkel
            $table->text('address')->nullable();             // Alamat
            $table->string('city')->nullable();              // Kota
            $table->string('province')->nullable();          // Provinsi
            $table->string('postal_code', 10)->nullable();   // Kode pos
            $table->text('description')->nullable();         // Deskripsi bengkel
            $table->string('logo_url')->nullable();          // Logo bengkel (Supabase Storage)
            $table->string('owner_name')->nullable();        // Nama pemilik bengkel
            $table->string('owner_ktp_number')->nullable(); // Nomor KTP pemilik
            $table->string('legal_document_url')->nullable(); // URL dokumen legalitas
            $table->decimal('latitude', 10, 8)->nullable();  // Latitude geolokasi
            $table->decimal('longitude', 11, 8)->nullable(); // Longitude geolokasi
            $table->decimal('rating_average', 3, 2)->default(0.00); // Rating rata-rata
            $table->string('operational_hours')->nullable(); // Jam operasional
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
        Schema::dropIfExists('workshops');
    }
};
