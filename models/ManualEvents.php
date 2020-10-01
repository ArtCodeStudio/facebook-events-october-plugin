<?php namespace ArtAndCodeStudio\FacebookEvents\Models;

use Model;

/**
 * ManualEvents Model
 */
class ManualEvents extends Model
{
  use \October\Rain\Database\Traits\Validation;
  /*
   * Disable timestamps by default.
   * Remove this line if timestamps are defined in the database table.
   */
  public $timestamps = false;


  /**
   * @var string The database table used by the model.
   */
  public $table = 'artandcodestudio_facebookevents_manualevents';

  /**
   * @var array Validation rules
   */
  public $rules = [
  ];
}
