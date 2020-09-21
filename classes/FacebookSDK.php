<?php namespace ArtAndCodeStudio\FacebookEvents\Classes;

use ArtAndCodeStudio\FacebookEvents\Models\Settings;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookAuthenticationException;
use Cms\Classes\Theme;
use System\Classes\SettingsManager;
use Carbon\Carbon;
use Cache;
use \DateTime;

class FacebookSDK {
  private $app_id;
  private $fb;
  private $access_token;
  private $app_secret;
  private $dateStringFormat;
  private $facebook_callback;
  private $backend_url;
  private $event_page_name;
  public const CACHE_PREFIX = 'facebooksdk_';

  /**
   * Getter for Tokendetails
   */
  public function getTokenDetail($key) {
    $tokenDetails = $this->getTokenDetails();
    if (isset($tokenDetails[$key])) {
      return $tokenDetails[$key];
    } else if (isset($tokenDetails['error'])) {
      return $tokenDetails['error'];
    }
  }

  public function accessTokenExpiresAt() {
    return $this->getTokenDetail('expires_at');
  }
  public function accessTokenIsValid() {
    return $this->getTokenDetail('is_valid');
  }
  public function accessTokenDataAccessExpiresAt() {
    return $this->getTokenDetail('data_access_expires_at');
  }

  /***
   * Returns Login Link
   */
  public function getLoginLink() {
    if (isset($this->app_id)) {
      $helper = $this->fb->getRedirectLoginHelper();
      $permissions = ['email, pages_read_engagement'];
      return '<a href="' . $this->getLoginURL() . '">Click here to login</a>';
    } else {
      return '<a href="/facebook_login">Click here to login</a>';
    }
  }

  /***
   * Returns Login URL, no HTML element
   */
  public function getLoginURL() {
    if (isset($this->app_id)) {
      $helper = $this->fb->getRedirectLoginHelper();
      $permissions = ['email, pages_read_engagement']; 
      $loginUrl = $helper->getLoginUrl($this->facebook_callback, $permissions);
      return $loginUrl;
    }
  }

  /**
   * Converts DateTime object to date string
   */
  private function convertDatetimeToDatestring($dateTime) {
    return date_format($dateTime , $this->dateStringFormat);
  }

  /**
   * Converts DateTime to timestamp
   */
  private function convertDatetimeToTimestamp($dateTime) {
    return date_timestamp_get($dateTime); 
  }

  /**
   * Converts timestamp to DateTime
   */
  private function convertTimestampToDatetime($timestamp) {
    return new DateTime('@' . $timestamp); 
  }

  /**
   * Converts timestamp to date string
   */
  private function convertTimestampToDatestring($timestamp) {
    return $this->convertDatetimeToDatestring($this->convertTimestampToDatetime($timestamp));
  }

  /**
   * Facebook login handler, show login href
   * called from router and shows login link
   */
  public function login() {
    if (isset($this->app_id)) {
      $helper = $this->fb->getRedirectLoginHelper();
      $permissions = ['email, pages_read_engagement']; 
      $loginUrl = $helper->getLoginUrl($this->facebook_callback, $permissions);

      /**
       * Show Login Button 
       */
      // echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

      /**
       * Automatic Redirect 
       */
      echo "<script>window.location = '".$loginUrl."'</script>";
    } else {
      echo "Login: Add Facebook App Id and App Secret to Plugin";
    }
  }

  /**
   * Called from Facebook Login App (url need to be set in Facebook App) 
   * get access token and save to plugin settings database
   */
  public function loginCallback() {
    $helper = $this->fb->getRedirectLoginHelper();
    try {
      $accessToken = $helper->getAccessToken();
    } catch (FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch (FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    if (isset($accessToken)) {
      Settings::set('access_token', (string)$accessToken);
      echo "<script>window.location = '".$this->backend_url."'</script>";
    } else {
      $error = $helper->getError();
      if ($error) {
        echo "FacebookSDK error: " . $error;
        exit;
      }
    }
  }

  /**
   * Get all Page Events
   */
  public function getEvents() {
    if (isset($this->access_token)) {
      $hash = md5($this->event_page_name . '|' . $this->access_token);
      $cacheKey = static::CACHE_PREFIX . $hash;
      $cacheTime = Carbon::now()->addSeconds($this->cache_ttl);

      // returns result from cache, stored under $cacheKey, if exists and not expired
      // otherwise returns result from encapsulated function and stores it under $cacheKey with expiry set to $cacheTime
      return Cache::remember($cacheKey, $cacheTime, function () {
        $defaultFields = explode(", ", "id, start_time, end_time, description, cover, name, is_canceled, scheduled_publish_time, timezone");
        $optionalFields = explode(", ", "attending_count, can_guests_invite, category, declined_count, discount_code_enabled, event_times, guest_list_enabled, interested_count, is_draft, is_online, is_page_owned, maybe_count, noreply_count, online_event_format, online_event_third_party_url, owner, parent_group, scheduled_publish_time, start_time, ticket_uri, ticket_uri_start_sales_time, ticketing_privacy_uri, ticketing_terms_uri, type, updated_time");
        $selectedFields = array_filter($optionalFields, function($field) {
          return Settings::get("include_" . $field) == 1;
        });
        $fieldsString = implode(", ", array_merge($defaultFields, $selectedFields));
        $graph_ql_query_string = '/'.  $this->event_page_name. '/events?fields=' . $fieldsString;
        try {
          $response = $this->fb->get(
            $graph_ql_query_string,
            $this->access_token
          );
        } catch (FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          echo 'Query String: '.$graph_ql_query_string;
          exit;
        }
        $graphEdge = $response->getGraphEdge();
        $graphEdgeArray = $graphEdge->asArray();
        return $graphEdgeArray;
      });
    } else {
      return [];
    }
  }

  /**
   * Returns filtered access token details 
   * is_valid
   * expires_at
   * expires_at_timestamp
   * data_access_expires_at
   */
  public function getTokenDetails() {
    if (strlen($this->access_token) > 0) {
      try {
        //`FacebookFacebookResponse` object
        $response = $this->fb->get(
          '/debug_token?input_token='.$this->access_token,
          $this->access_token
        );
      } catch (FacebookSDKException $e) {
        return array (
          "error" => $e->getMessage()
        );
      }
      $graphNode = $response->getGraphNode();
      $graphArray = $graphNode->asArray();
      $expiresAt = $this->convertDatetimeToDatestring($graphArray["expires_at"]);
      $dataAccessExpiresAt = $this->convertTimestampToDatestring($graphArray["data_access_expires_at"]);

      $filtered_result = array(
        "is_valid" => $graphArray["is_valid"],
        "expires_at" => $expiresAt ? $expiresAt : "never",
        "data_access_expires_at" => $dataAccessExpiresAt ? $dataAccessExpiresAt : "never",
      );
      return $filtered_result;
    }
  }

  /**
   * Anything starts here
   */
  function __construct() {
    $protocol = isset($_SERVER['HTTPS']) ? "https" : "http";
    $this->backend_url =  $protocol . "://".$_SERVER['HTTP_HOST']."/backend/system/settings/update/artandcodestudio/facebookevents/settings"; 
    $this->facebook_callback = $protocol . "://".$_SERVER['HTTP_HOST'] ."/facebook_callback";
    $this->event_page_name = Settings::get('event_page_name');
    $this->cache_ttl = Settings::get('cache_ttl');

    /***
     * Check if there is a Session running, if not start a new
     * session status:
     * _DISABLED = 0
     * _NONE = 1
     * _ACTIVE = 2
     * */  
    if (session_status() < 2) {
      session_start();
    }

    if (Settings::get('app_id') && Settings::get('app_secret')) {
      $this->access_token = Settings::get('access_token');
      $this->app_id = Settings::get('app_id');
      $this->app_secret = Settings::get('app_secret');
      $this->dateStringFormat = "d-m-Y H:i:s";

      $this->fb = new Facebook([
        'app_id' =>  $this->app_id, 
        'app_secret' => $this->app_secret,
        'default_graph_version' => 'v2.10'
      ]);
    }
  }
}