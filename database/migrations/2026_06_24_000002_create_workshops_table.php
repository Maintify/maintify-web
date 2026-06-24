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
