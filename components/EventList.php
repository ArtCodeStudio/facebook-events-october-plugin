<?php namespace ArtAndCodeStudio\FaceBookEvents\Components;
use ArtAndCodeStudio\FaceBookEvents\Models\Settings;
use Cms\Classes\ComponentBase;
use Config;
use ArtAndCodeStudio\FaceBookEvents\Classes\FaceBookSDK;

class EventList extends ComponentBase {

  public function componentDetails() {
    return [
      'name'        => 'EventList Component',
      'description' => 'Displays a List of Facebook Page Events'
    ];
  }

  /**
   *  
   */
  public function isUpcoming($endTime) {
    $endTimestamp = date_timestamp_get($endTime);
    $interval =  $end_timestamp - time();
    if ($interval > 0) {
      return true;  // yes it is upcoming
    } else {
      return false; // no it is history
    }
  }

  /**
   * Define PlugIn Properties
   */
  public function defineProperties() {
    return [];
  }
  
  /**
   * becomes available in the component template as  {{ __SELF__.pluginSettings }} 
   * and on the page {{component.pluginSettings}}
   */
  public function pluginSettings() {
    return Settings::instance()->value;
  }

  /**
   * becomes available in the component template as  {{ __SELF__.events }} 
   * and on the page {{component.events}}
   */ 
  public function events() {
    // get manually created events from Settings
    $settings = $this->pluginSettings();
    $events = $settings['events'];

    foreach ($events as &$e) {
      // convert event times to times to DateTime objects (so they are of equal type with Facebook Events)
      $e['start_time'] = date_create($e['start_time']);
      $e['end_time'] = date_create($e['end_time']);
      // expand mediafinder path to a full URL, so image can be treated same as for Facebook Events in template
      $e['image'] = Config::get('cms.storage.media.path').$e['image'];
    }

    // If Facebook Events should be included, we merge them in...
    if ($settings['include_facebook_events']) {
      $FB_sdk = new FaceBookSDK();
      $fbEvents = $FB_sdk->getEvents();
      foreach ($fbEvents as &$e) {
        // same interface as for manually created events
        $e['image'] = $e['cover']['source'];
        $e['external_url'] = 'https://facebook.com/events/'.$e['id'];
      }
      $events = array_merge($events, $fbEvents);
    }

    // Sort events by start_time, descending order
    usort($events, function ($a, $b) {
      return date_timestamp_get($b['start_time']) - date_timestamp_get($a['start_time']);
    });

    return $events;
  }
}
