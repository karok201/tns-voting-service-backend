<?php

use App\Models\Voting;
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
        Schema::create(\App\Models\Voting::TABLE_NAME, function (Blueprint $table) {
            $table->id(Voting::FIELD_ID);

            $table->string(Voting::FIELD_NAME);
            $table->text(Voting::FIELD_DESCRIPTION);
            $table->string(Voting::FIELD_STATUS);
            $table->date(Voting::FIELD_END_DATE);
            $table->string(Voting::FIELD_TYPE);
            $table->string(Voting::FIELD_WAY);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votings');
    }
};
