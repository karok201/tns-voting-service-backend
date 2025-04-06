<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('eth_account')->nullable(); // Ethereum адрес
            $table->string('eth_password')->nullable(); // Пароль для аккаунта Ethereum
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['eth_account', 'eth_password']);
        });
    }
};
