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
        Schema::create('characters', function (Blueprint $table) {
            $table->id(); // ID
            $table->string('name', 128)->nullable(false); // Character name
            $table->date('birth_date')->nullable(false); // Character DOB
            $table->string('kingdom', 128)->nullable(false); // Character kingdom
            $table->unsignedBigInteger('equipment_id')->nullable(false); // Equipments FK
            $table->unsignedBigInteger('faction_id')->nullable(false); // Factions FK
            $table->timestamps(); // created_at & updated_at

            // FK
            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade');
            $table->foreign('faction_id')->references('id')->on('factions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
