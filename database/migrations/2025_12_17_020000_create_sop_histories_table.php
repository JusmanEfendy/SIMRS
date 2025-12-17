<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel sop_histories untuk menyimpan log perubahan SOP.
     * Setiap perubahan pada SOP akan dicatat di sini.
     */
    public function up(): void
    {
        Schema::create('sop_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sop_id');
            $table->foreign('sop_id')->references('id')->on('sops')->onDelete('cascade');
            
            // User yang melakukan perubahan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Tipe aksi: created, updated, status_changed, deleted, restored
            $table->string('action', 50);
            
            // Deskripsi perubahan yang mudah dibaca
            $table->text('description');
            
            // Data sebelum perubahan (JSON)
            $table->json('old_values')->nullable();
            
            // Data setelah perubahan (JSON)
            $table->json('new_values')->nullable();
            
            // Field yang berubah (JSON array)
            $table->json('changed_fields')->nullable();
            
            $table->timestamps();
            
            // Index untuk query yang lebih cepat
            $table->index(['sop_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sop_histories');
    }
};
