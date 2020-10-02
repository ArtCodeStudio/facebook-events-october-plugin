<?php namespace ArtAndCodeStudio\FacebookEvents\Components;
use ArtAndCodeStudio\FacebookEvents\Models\Settings;
use ArtAndCodeStudio\FacebookEvents\Models\ManualEvents;
use Cms\Classes\ComponentBase;
use Config;
use ArtAndCodeStudio\FacebookEvents\Classes\FacebookSDK;

class EventList extends ComponentBase {

  public function componentDetails() {
    return [
      'name'        => 'EventList Component',
      'description' => 'Displays a List of Facebook Page Events'
    ];
  }

  /**
   *  TODO: some events don't have an "end_time". They are "upcoming" before the start_time. Should other events treated the same?
   *  Maybe not just distinguish the "past" vs "upcoming", but "past" vs "happening_now" vs "upcoming".
   */
  public function isUpcoming($startTime, $endTime) {
    if (isset($endTime)) {
      $endTimestamp = date_timestamp_get($endTime);
    } else {
      $endTimestamp = date_timestamp_get($startTime);
    }
    $interval =  $endTimestamp - time();
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
    $settings = $this->pluginSettings();
    $events = array();
    if ($settings['include_manual_events']) {
      $events = ManualEvents::all()->toArray();
    }

    foreach ($events as &$e) {
      // convert event times to times to DateTime objects (so they are of equal type with Facebook Events)
      $e['start_time'] = date_create($e['start_time']);
      $e['end_time'] = date_create($e['end_time']);
      // expand mediafinder path to a full URL, so image can be treated same as for Facebook Events in template
      $e['image'] = Config::get('cms.storage.media.path').$e['image'];
    }

    // If Facebook Events should be included, we merge them in...
    if ($settings['include_facebook_events']) {
      $FB_sdk = new FacebookSDK();
      $fbEvents = $FB_sdk->getEvents();
      foreach ($fbEvents as &$e) {
        // same interface as for manually created events
        if (isset($e['cover'])) {
          $e['image'] = $e['cover']['source'];
        }
        if (isset($e['id'])) {
          $e['external_url'] = 'https://facebook.com/events/'.$e['id'];
        }
      }
      if (isset($fbEvents)) {
        $events = array_merge($events, $fbEvents);
      }
    }

    // Sort events by start_time, descending order
    usort($events, function ($a, $b) {
      return date_timestamp_get($b['start_time']) - date_timestamp_get($a['start_time']);
    });
    return $events;
  }
}
