<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsOnNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('notes', function (Blueprint $table) {
        $table->dropColumn('pinned');
        $table->dateTime('pinned_at')->nullable()->after('color');
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
          $table->dropColumn('pinned_at');
          $table->boolean('pinned')->default('false')->after();
        });
    }
}
