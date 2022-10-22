<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (['posts', 'locations', 'medias', 'objects'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dateTime('created_at')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (['posts', 'locations', 'medias', 'objects'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->timestamp('created_at')->change();
            });
        }
    }
};
