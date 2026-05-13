<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rsa_keys', function (Blueprint $table) {
            // Menambahkan kolom user_id yang terhubung ke tabel users
            // (nullable agar data kunci yang sudah ada sebelumnya tidak error)
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('rsa_keys', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};