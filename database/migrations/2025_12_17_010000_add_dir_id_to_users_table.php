<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan kolom dir_id pada tabel users agar user dengan role Direksi
     * dapat terhubung dengan directorate yang mereka kelola.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('dir_id')->nullable()->after('id_unit');
            $table->foreign('dir_id')->references('id')->on('directorates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dir_id']);
            $table->dropColumn('dir_id');
        });
    }
};
