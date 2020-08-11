<?php namespace ArtAndCodeStudio\FaceBookEvents\Classes;

use ArtAndCodeStudio\FaceBookEvents\Classes\SessionHandler;
use ArtAndCodeStudio\FaceBookEvents\Models\Settings;
use Facebook\Exception as E;
use Facebook\Exception\FacebookSDKException;
use Facebook\Exception\FacebookResponseException;
use Cms\Classes\Theme;
use System\Classes\SettingsManager;

class FaceBookSDK
{
    private $is_initalized;
    private $app_id;
    private $fb;
    private $access_token;
    private $app_secret;
    private $dateStringFormat;
    private $facebook_callback;
    private $backend_url;
    private $event_page_name;
    private $graph_ql_query_strings;
    private $include_event_url;
    
    // helper move out -----------------------------------------------------------------------------------
    /**
     * Just a helperfunction 
     */
    private static function print($value) 
    {
        echo "<pre>";
        print_r($value);
        echo "</pre>";
    }

    /**
     * Just a helperfunction 
     */
    private static function dump($value) 
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }
    //-----------------------------------------------------------------------------------
    
    /**
     * Converts DateTime object to date string  
     */
    private function convert_DateTime_to_DateString( $dateTime )
    {
        return date_format( $dateTime , $this->dateStringFormat ); 
    }

    /**
     * Converts DateTime timestamp  
     */
    private function convert_DateTime_to_Timestamp( $dateTime )
    {
        return date_timestamp_get( $dateTime ); 
    }

    /**
     * Facebook login handler, show login href
     * called from router and shows login link
     */
    public function login()
    {
        if( isset($this->app_id) )
        {
            $helper = $this->fb->getRedirectLoginHelper();
            $permissions = ['email, pages_read_engagement']; 

            $loginUrl = $helper->getLoginUrl( $this->facebook_callback, $permissions);
            /**
             * Show Login Button 
             */
            // echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

            /**
             * Automatic Redirect 
             */
            echo "<script>window.location = '".$loginUrl."'</script>";

        }else {
            echo "Login: Add Facebook App Id and App Secret to Plugin";
        }
    }

    /***
     * Returns Login Link
     */
    public function _getLoginLink()
    {
        if( isset($this->app_id) )
        {
            $helper = $this->fb->getRedirectLoginHelper();
            $permissions = ['email, pages_read_engagement']; 
            $loginUrl = $helper->getLoginUrl($this->facebook_callback, $permissions);
            return '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
        }else{
            return '<a href="/facebook_login">Log in with Facebook!</a>';
        }
    }
    /***
     * Returns Login URL, no HTML element
     */
    public function _getLoginURL()
    {
        if( isset($this->app_id)  )
        {
            $helper = $this->fb->getRedirectLoginHelper();
            $permissions = ['email, pages_read_engagement']; 
            $loginUrl = $helper->getLoginUrl($this->facebook_callback, $permissions);
            return $loginUrl;
        }
    }

    /**
     * Called from Facebook Login App (url need to be set in Facebook App) 
     * get access token and save to plugin settings database
     */
    public function loginCallback()
    {
       $helper = $this->fb->getRedirectLoginHelper();
       try {
           $accessToken = $helper->getAccessToken();
           Settings::set('access_token', (string)$accessToken);
           echo "<script>window.location = '".$this->backend_url."'</script>";

          // header("Location:". $this->backend_url);
        

        } catch( FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exception\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }

    /**
     * Check if given DateTime is in future
     * $end_time: DateTime
     */
    function checkIfEventIsUpcoming( $end_time ) 
    {
        $end_timestamp = date_timestamp_get ( $end_time );
        $interval =   $end_timestamp - time();
        if ($interval > 0) {
            return true;  // yes it is upcoming
        }else{
            return false; // no it is history
        }
    }

    /***
     * Prepair result
     * DRY function
     **/                
    private function prepareEventsResult( $result, $graphEdge, $graphNode, $key, $start_time, $end_time )
    {
        $result[$key]["start_time"] = $this->convert_DateTime_to_DateString( $start_time );
        $result[$key]["end_time"] = $this->convert_DateTime_to_DateString( $end_time );
        $result[$key]["name"] = $graphNode['name'];
        $result[$key]["description"] = $graphNode['description'];
        $result[$key]["graphEdge"] = $graphEdge;
        /**
         * Include Event URL ? 
         */
        if ($this->include_event_url) 
        {
            $result[$key]["url"] = "https://facebook.com/events/" . $graphNode['id'];
        }

        /**
         * Check if there is a cover available
         */
        if ( isset($graphNode['cover']) ){
            $result[$key]["cover"] = $graphNode['cover'];
            $result[$key]["cover_src"] = $graphNode['cover']['source'];      
        }
        return $result;
    }

    /**
     * Get all Page Events
     */
    public  function getEvents() 
    {
        if( isset($this->access_token) )
        {
            try {
                
                 //'/ansolas/events?fields=cover, description,name, start_time, end_time&limit=2&after=QVFIUlpfN2l6X3B4b01RQm1jYk5oZATFoSjI4RmZAhOUFUSlZAaeW9tdUdfVFphbVd2aVU0Q2F2Q1FHb3lra041VDNXRS0zX1lxWjViQ1ZANX0ZAvdVQxOHUyajRB', //Get events and specified fields
                $graph_ql_query_string = '/'.  $this->event_page_name. '/events?fields=id, start_time, end_time, ';

                // number of parameters, needed to determine the last element so that we dont add a comma after it
                $length = count(  $this->graph_ql_query_strings ); 
                foreach ( $this->graph_ql_query_strings as $key => $query_string ) 
                {
                    $graph_ql_query_string .= $query_string;
                    // Query strings can't have a trailing comma !
                    if ($key < $length - 1) { 
                        $graph_ql_query_string .= ' , ';
                    }
                }
                
                // $response = $this->fb->get(
                //   '/ansolas/events?fields=cover, description,name, start_time, end_time', //Get events and specified field
                //   $this->access_token
                // );
                //var_dump($graph_ql_query_string); die;
                $response = $this->fb->get(
                    $graph_ql_query_string,
                    $this->access_token
                );
            
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                    exit;
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                }
                $graphEdge = $response->getGraphEdge();
                $graphEdgeArray = $graphEdge->asArray();
        
                //self::dump($graphEdgeArray ); die;

                /**
                 * Create Filtered Result array 
                 * to be rendered in component
                 */
                $result = array(); 
                foreach ($graphEdgeArray as $key => $graphNode ) 
                {

                    $start_time = $graphNode['start_time'];
                    $end_time = $graphNode['end_time'];
                    $include_past_events = Settings::get('include_past_events');

                    $event_is_upcoming = $this-> checkIfEventIsUpcoming( $end_time );
                
                    /**
                     * Include this event if upcoming 
                     */
                    if ( $event_is_upcoming )
                    {
                        $result = $this->prepareEventsResult( $result, $graphEdge, $graphNode, $key, $start_time, $end_time );
                    } 

                    /**
                     * Include this event if it is NOT upcoming but past 
                     */
                    if ( !$event_is_upcoming && $include_past_events)
                    {
                        $result = $this->prepareEventsResult( $result, $graphEdge, $graphNode, $key, $start_time, $end_time );                 
                    }
                }
                
                // DEBUG
                //self::dump($result); die;

                // PAGINATION 
                // $nextPage = $this->fb->next($graphEdge);
                /*  foreach ($nextPage as $status) {
                    //self::dump($status->asArray());
                }
                $nextPage = $this->fb->next($graphEdge);

                foreach ($nextPage as $status) {
                    //self::dump($status->asArray());
                }*/

                return $result;
            }else{
                echo "not logged in";
            }
    }

    /**
     * Getter for Tokendetails, 
     * function start with _ for easy code completion list (at top) :) 
     */
    public function _getAccessToken_expiresAt() 
    {
        return $this->getTokenDetails()['expires_at'];
    }
    public function _getAccessToken_isValid() 
    {
        return $this->getTokenDetails()['is_valid'];
    }
    public function _getAccessToken_dataAccessExpiresAt() 
    {
        return $this->getTokenDetails()['data_access_expires_at'];
    }

    /**
     * Returns filtered access token details 
     * is_valid
     * expires_at
     * expires_at_timestamp
     * data_access_expires_at
     */
    public function getTokenDetails() 
    {
        if( strlen($this->access_token)>0 )
        {
            try {
                // Returns a `FacebookFacebookResponse` object
                $response = $this->fb->get(
                  '/debug_token?input_token='.$this->access_token,
                  $this->access_token
                );
              } catch(FacebookExceptionsFacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
              } catch(FacebookExceptionsFacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
              }
              $graphNode = $response->getGraphNode();
              $graphArray = $graphNode->asArray();
              $expires_at = $this->convert_DateTime_to_Timestamp($graphArray["expires_at"]);
        
             // var_dump($expires_at);die;
    
              $filtered_result = array(
                  "is_valid" => $graphArray["is_valid"],
                  "expires_at" =>   $expires_at ? $expires_at : "never",
                  "data_access_expires_at" => $graphArray["data_access_expires_at"],
              );
              return $filtered_result;
        }
    }

    /**
     * Validates is access token is still valid, if not redirect  
     * https://developers.facebook.com/docs/graph-api/reference/v7.0/debug_token
     */
    public function validateToken()
    {
        if( isset($this->access_token))
        {
            try {
                // Returns a `FacebookFacebookResponse` object
                $response = $this->fb->get(
                '/debug_token?input_token='.$this->access_token,
                $this->access_token
                );
            } catch(FacebookExceptionsFacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(FacebookExceptionsFacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
            $graphNode = $response->getGraphNode();
            $graphArray = $graphNode->asArray();

            self::dump(date_timestamp_get($graphArray['expires_at']));
        }else{
            return "no accesstoken";
        }

    }

    /**
     * Anything starts here
     */
    function __construct()
    {   
        // //https://octobercms.com/docs/api/system/classes/settingsmanager

        // $theme = Theme::getActiveTheme();
        // $settingsManager = SettingsManager::instance();

        // $settings = Settings::instance();
        // self::dump( $settings->attributes );
     
        $this->backend_url =  "https://".$_SERVER['HTTP_HOST']."/backend/system/settings/update/artandcodestudio/facebookevents/settings"; 
        $this->facebook_callback = "https://".$_SERVER['HTTP_HOST'] ."/facebook_callback";

        $this->event_page_name = Settings::get('event_page_name');
        $this->include_event_url = Settings::get('include_event_url');

        // Prepare Query String Parts
        $this->graph_ql_query_strings = array();

        /**
         * Is there a smarter way ?
         */
        if ( Settings::get('include_event_description') )
        {
            $this->graph_ql_query_strings[0] = 'description';
        }
        if ( Settings::get('include_event_cover') )
        {
            $this->graph_ql_query_strings[1] = 'cover';
        }
        if ( Settings::get('include_event_name') )
        {
            $this->graph_ql_query_strings[2] = 'name';
        }

        /***
         * Check if tehre is a Session running, if not start a new
         * session status:
         * _DISABLED = 0
         * _NONE = 1
         * _ACTIVE = 2
         * */  
        if (session_status() < 2)
        {
            session_start();
        }

        if ( Settings::get('app_id') && Settings::get('app_secret'))
        { 
            $this->access_token = Settings::get('access_token');
            $this->app_id = Settings::get('app_id');
            $this->app_secret = Settings::get('app_secret');
            $this->dateStringFormat = "d-m-Y H:i:s";

            $this->fb = new \Facebook\Facebook([
                'app_id' =>  $this->app_id, 
                'app_secret' => $this->app_secret,
                'default_graph_version' => 'v2.10',
                //'persistent_data_handler' => new SessionHandler()
            ]);
            
        }else{
           // echo "Constructor: login first <br>";
        }
    }
}