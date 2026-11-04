<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // Hapus dari certificates
    Schema::table('certificates', function (Blueprint $table) {
        $table->dropColumn(['start_date', 'end_date']);
    });

    // Tambah ke participants
    Schema::table('participants', function (Blueprint $table) {
        $table->date('start_date')->nullable()->after('study_program');
        $table->date('end_date')->nullable()->after('start_date');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            //
        });
    }
};
