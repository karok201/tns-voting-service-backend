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
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('options'); // ['за', 'против', 'воздержался']
            $table->enum('voting_type', ['com', 'bod']);
            $table->enum('voting_way', ['majority', 'unanimous']);
            $table->string('conference_link')->nullable();
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
