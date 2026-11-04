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
            
            // --- PENGAMANAN FOREIGN KEYS ---
            // Diubah dari 'cascade' menjadi 'restrict' agar data sertifikat tidak bisa ikut terhapus.
            
            // 1. Jika data peserta dihapus, sistem akan menolak jika dia sudah punya sertifikat.
            $table->foreignId('participant_id')->constrained('participants')->onDelete('restrict');
            
            // 2. Jika akun admin dihapus, sertifikat yang pernah dia buat tetap aman.
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); 
            
            // 3. Jika kunci RSA tidak sengaja dihapus, sertifikat lama tidak akan ikut lenyap.
            $table->foreignId('key_id')->constrained('rsa_keys')->onDelete('restrict'); 
            
            // --- DATA SERTIFIKAT ---
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