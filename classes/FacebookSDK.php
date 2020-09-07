<?php namespace ArtAndCodeStudio\FacebookEvents\Classes;

use ArtAndCodeStudio\FacebookEvents\Classes\SessionHandler;
use ArtAndCodeStudio\FacebookEvents\Models\Settings;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookAuthenticationException;
use Cms\Classes\Theme;
use System\Classes\SettingsManager;
use Carbon\Carbon;
use Cache;

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
      return 'Error: '.$tokenDetails['error'];
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
      $loginUrl = $helper->getLoginUrl($this->facebook_callback, $permissions);
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
  private function convert_DateTime_to_DateString($dateTime) {
    return date_format($dateTime , $this->dateStringFormat);
  }

  /**
   * Converts DateTime timestamp
   */
  private function convert_DateTime_to_Timestamp($dateTime) {
    return date_timestamp_get($dateTime); 
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
        echo "FacebookSDK error: " . $error->getMessage() . "</script>";
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
        try {
          $graph_ql_query_string = '/'.  $this->event_page_name. '/events?fields=id, start_time, end_time, description, cover, name';
          $response = $this->fb->get(
            $graph_ql_query_string,
            $this->access_token
          );
        } catch (FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
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
        $error = 'FacebookSDK error: ' . $e->getMessage();
        return array (
          "error" => $error
        );
      }
      $graphNode = $response->getGraphNode();
      $graphArray = $graphNode->asArray();
      $expiresAt = $this->convert_DateTime_to_DateString($graphArray["expires_at"]);
      $dataAccessExpiresAt = $this->convert_DateTime_to_DateString($graphArray["data_access_expires_at"]);

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
    $this->backend_url =  "https://".$_SERVER['HTTP_HOST']."/backend/system/settings/update/artandcodestudio/facebookevents/settings"; 
    $this->facebook_callback = "https://".$_SERVER['HTTP_HOST'] ."/facebook_callback";
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
    } else {
      echo "please login to facebook";
    }
  }
}