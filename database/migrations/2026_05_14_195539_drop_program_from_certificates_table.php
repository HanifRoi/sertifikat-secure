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
    Schema::table('certificates', function (Blueprint $table) {
        // Menghapus kolom program
        $table->dropColumn('program');
    });
}

public function down()
{
    Schema::table('certificates', function (Blueprint $table) {
        // Mengembalikan kolom program jika suatu saat di-rollback
        $table->string('program')->nullable();
    });
}
};
