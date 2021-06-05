<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name', 256);
            $table->string('avatar', 1024)->default('');
            $table->bigInteger('time');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->integer('album_id')->default(0);
            $table->string('name', 256)->default('');
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('albums');
    }
}
