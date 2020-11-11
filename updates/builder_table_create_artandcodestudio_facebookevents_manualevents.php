<?php namespace artandcodestudio\Facebookevents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateArtandcodestudioFacebookeventsManualevents extends Migration
{
    public function up()
    {
        Schema::create('artandcodestudio_facebookevents_manualevents', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('external_url')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('artandcodestudio_facebookevents_manualevents');
    }
}
