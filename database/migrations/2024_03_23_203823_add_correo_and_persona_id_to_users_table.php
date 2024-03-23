<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCorreoAndPersonaIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('correo')->nullable();
            $table->bigInteger('persona_id')->unsigned()->nullable();
            $table->unique('persona_id');

            // Definir la clave foránea para persona_id
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('correo');
            $table->dropForeign(['persona_id']);
            $table->dropColumn('persona_id');
        });
    }
}
