<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsersFkOnNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('notes', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->after('id');
      });

      Schema::table('notes', function (Blueprint $table) {
        $table->foreign('user_id', 'fk_notes_user_id')
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
      Schema::table('notes', function (Blueprint $table) {
        $table->dropForeign('fk_notes_user_id');
      });

      Schema::table('notes', function (Blueprint $table) {
        $table->dropColumn('user_id');
      });
    }
}
