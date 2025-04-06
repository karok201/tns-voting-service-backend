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
        Schema::dropIfExists('questions');
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // это unsignedBigInteger и auto increment
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('options')->nullable();
            $table->enum('voting_type', ['com', 'bod'])->default('bod');
            $table->enum('voting_way', ['majority', 'unanimous'])->default('majority');
            $table->string('conference_link')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
