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
        Schema::table('transaction', function(Blueprint $table){
            $table->string('account_number', 255);
            $table->foreign('account_number', 255)->references('account_number')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function(Blueprint $table){
            $table->dropForeign(['account_number']);
            $table->dropColumn(['account_number']);
        });
    }
};
