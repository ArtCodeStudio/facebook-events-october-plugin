<?php namespace ArtAndCodeStudio\FaceBookEvents\Components;
use ArtAndCodeStudio\FaceBookEvents\Models\Settings;
use Cms\Classes\ComponentBase;
use ArtAndCodeStudio\FaceBookEvents\Classes\FaceBookSDK;

class EventList extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'EventList Component',
            'description' => 'Displays a List of Facebook Page Events'
        ];
    }

    /**
     *  
     */
    public function isUpcoming( $end_time ) 
    {
        $end_timestamp = date_timestamp_get ( $end_time );
        $interval =  $end_timestamp - time();
        if ($interval > 0) {
            return true;  // yes it is upcoming
        }else{
            return false; // no it is history
        }
    }

    /**
     * Define PlugIn Properties
     */
    public function defineProperties()
    {
        return [];
    }
    
    /**
     * becomes available in the component template as  {{ __SELF__.pluginSettings }} 
     * and on the page {{component.pluginSettings}}
     */
    public function pluginSettings() 
    {
        $settings = Settings::instance();
      
        return $settings;
    }

    /**
     * becomes available in the component template as  {{ __SELF__.events }} 
     * and on the page {{component.events}}
     */ 
    public function events() 
    {   
        $FB_sdk = new FaceBookSDK();
        $FB_sdk->getEvents();
        return $FB_sdk->getEvents();
    }
}
