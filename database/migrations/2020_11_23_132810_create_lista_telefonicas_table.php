<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListaTelefonicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lista_telefonicas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('code');
            $table->string('name');
            $table->string('alias');
            $table->string('party', 50);
            $table->string('address');
            $table->string('phone', 20);
            $table->string('fax', 20);
            $table->string('email');
            $table->string('born_city');
            $table->string('born_state');
            $table->date('birth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lista_telefonicas');
    }
}
