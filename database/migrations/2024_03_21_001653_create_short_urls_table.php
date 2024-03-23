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
        Schema::create('short_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('persona_id')->unsigned()->nullable();
            $table->bigInteger('url_disponible_id')->unsigned()->nullable();
            $table->string('long_url', 255)->nullable();
            
            // Foreign key constraints
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
            $table->foreign('url_disponible_id')->references('id_url_disponible')->on('url_disponibles')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
