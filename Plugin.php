<?php namespace ArtAndCodeStudio\FacebookEvents;

use System\Classes\PluginBase;
use Backend;
class Plugin extends PluginBase
{
    /**
     * Plugin Details
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Facebook Events',
            'description' => 'Integrates Facebook Page Events',
            'author' => 'Art+Code.studio',
            'icon' => 'icon-facebook'
        ];
    }

    /**
     * Register EventList Component 
     */
    public function registerComponents()
    {
        return [
            'ArtAndCodeStudio\FacebookEvents\Components\EventList' => 'eventList'
        ];
    }

    /**
     * Register Backend Menu Item in October backnend Sidebar 
     * https://octobercms.com/docs/plugin/settings#link-registration
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Facebook Events',
                'description' => 'Manage Facebook Events Settings',
                'icon'        => 'icon-facebook',
                'class'       => 'ArtAndCodeStudio\FacebookEvents\Models\Settings',
                'order'       => 500,
            ],
        ];
    }

    public function registerNavigation()
    {
      return [
        'ManualEvents' => [
          'label'       => 'Manual Events',
          'url'         => Backend::url('artandcodestudio/facebookevents/manualevents'),
          'icon'        => 'icon-flash',
          'order'       => 400,
          'sideMenu' => [
          ]
        ]
      ];
    }
    public function registerFormWidgets()
    {
        return [
            'ArtAndCodeStudio\FacebookEvents\FormWidgets\AccessTokenInfo' => 'artandcodestudio_accesstokeninfo',
        ];
    }

    public function onInit()
    {
        
    }
}
