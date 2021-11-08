<?php

use Database\Seeders\UserSettingsSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_settings', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('user_id');
          $table->boolean('dark')->default(false);
          $table->string('view')->default('grid');
          $table->timestamps();
      });

      Schema::table('user_settings', function (Blueprint $table) {
        $table->foreign('user_id')
          ->references('id')
          ->on('users')
          ->onDelete('cascade')
          ->onUpdate('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
}
