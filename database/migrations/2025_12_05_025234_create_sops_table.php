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
        Schema::create('sops', function (Blueprint $table) {
            $table->id();
            $table->string('id_sop')->unique();
            $table->string('sk_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('id_unit');
            $table->foreign('id_unit')->references('id_unit')->on('units');
            $table->enum('type_sop', ['NonAP', 'AP']);
            $table->string('sop_name', 100);
            $table->string('file_path');
            $table->date('approval_date');
            $table->date('start_date');
            $table->date('expired');
            $table->text('desc')->nullable();
            $table->enum('status', ['Pending', 'In Review', 'Approve', 'Rejected', 'Expired'])->default('Pending');
            $table->integer('days_left');
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sops');
    }
};
