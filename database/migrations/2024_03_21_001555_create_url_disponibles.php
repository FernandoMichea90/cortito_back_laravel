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
        Schema::create('url_disponibles', function (Blueprint $table) {
            $table->bigIncrements('id_url_disponible');
            $table->string('short_url', 3);
            $table->string('url_disponible', 255)->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_disponibles');
    }
};
