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
        Schema::create('certificates', function (Blueprint $table) {
        $table->id();
        // Foreign Keys
        $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Siapa admin yang upload
        $table->foreignId('key_id')->constrained('rsa_keys')->onDelete('cascade'); // Pakai key yang mana
        
        // Data Sertifikat
        $table->string('program'); // Nama program magang
        $table->string('file_path'); // Path PDF asli/hasil
        $table->text('document_hash'); // Hasil SHA-3
        $table->text('digital_signature'); // Hasil Enkripsi RSA
        $table->string('qr_code_path'); // Path gambar QR Code baru
        $table->date('publish_date');
        $table->string('status'); // misal: 'secured', 'revoked'
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
