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
    // 1. Tabel untuk menampung akun Google (Bisa banyak)
    Schema::create('social_accounts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('provider_id');   // ID Google
        $table->string('provider_name')->default('google'); // google, facebook, dll
        $table->string('email')->nullable(); // Email akun tersebut
        $table->timestamps();
    });

    // 2. Tambah kolom PIN ke tabel users
    Schema::table('users', function (Blueprint $table) {
        $table->string('pin')->nullable()->after('password'); // PIN ter-hash
    });
}

public function down(): void
{
    Schema::dropIfExists('social_accounts');
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('pin');
    });
}
};
