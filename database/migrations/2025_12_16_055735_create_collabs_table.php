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
        Schema::create('collabs', function (Blueprint $table) {
            $table->char('id_sop', 15);
            $table->char('id_unit', 6);

            $table->foreign('id_sop')
                ->references('id_sop')
                ->on('sops')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_unit')
                ->references('id_unit')
                ->on('units')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->primary(['id_sop', 'id_unit']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collabs');
    }
};
