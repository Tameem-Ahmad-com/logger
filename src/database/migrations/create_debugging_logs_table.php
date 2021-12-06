<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebuggingLogTable extends Migration
{
    public function up()
    {
        Schema::connection(config('debbuger.channels.database.connection'))->create(config('debbuger.channels.database.table'), function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->longText('message');
                $table->longText('context');
                $table->text('type');
               
        });
    }

    public function down()
    {
        Schema::connection(config('debbuger.channels.database.connection'))->dropIfExists(config('debbuger.channels.database.table'));
    }
}
