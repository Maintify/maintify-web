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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');                   // e.g. created, updated, deleted, login, logout
            $table->string('model_type')->nullable();   // e.g. Vehicle, Workshop, ServiceHistory
            $table->unsignedBigInteger('model_id')->nullable(); // ID dari model terkait
            $table->text('description')->nullable();    // Deskripsi aktivitas
            $table->json('old_values')->nullable();     // Nilai sebelum diubah
            $table->json('new_values')->nullable();     // Nilai setelah diubah
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
