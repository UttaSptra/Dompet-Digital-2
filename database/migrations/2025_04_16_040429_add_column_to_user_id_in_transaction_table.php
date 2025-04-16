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
        Schema::table('transaction', function (Blueprint $table) {
            // Menambahkan kolom to_user_id jika belum ada
            $table->unsignedBigInteger('to_user_id')->nullable();

            // Tambahkan foreign key setelah kolom to_user_id ditambahkan
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            // Hapus foreign key dan kolom to_user_id saat rollback
            $table->dropForeign(['to_user_id']);
            $table->dropColumn('to_user_id');
        });
    }
};

