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
            $table->smallInteger('event_name');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('artandcodestudio_facebookevents_manualevents');
    }
}
