<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeAttributablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute__attributables', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('attribute_id')->unsigned();
            $table->string('entity_type');

            $table->unique(['attribute_id', 'entity_type']);
            $table->foreign('attribute_id')->references('id')->on('attribute__attributes')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute__attributables');
    }
}
