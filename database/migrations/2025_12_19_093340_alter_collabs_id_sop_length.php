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
        Schema::table('collabs', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['id_sop']);
        });

        Schema::table('collabs', function (Blueprint $table) {
            // Change id_sop from char(15) to char(20) to accommodate SOP ID format
            $table->char('id_sop', 20)->change();
        });

        Schema::table('collabs', function (Blueprint $table) {
            // Add foreign key back
            $table->foreign('id_sop')
                ->references('id_sop')
                ->on('sops')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collabs', function (Blueprint $table) {
            $table->dropForeign(['id_sop']);
        });

        Schema::table('collabs', function (Blueprint $table) {
            $table->char('id_sop', 15)->change();
        });

        Schema::table('collabs', function (Blueprint $table) {
            $table->foreign('id_sop')
                ->references('id_sop')
                ->on('sops')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
};
