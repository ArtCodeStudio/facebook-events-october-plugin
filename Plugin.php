<?php namespace ArtAndCodeStudio\FacebookEvents;

use System\Classes\PluginBase;

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

            'manualEvents' => [
              'label'       => 'Manual Events',
              'description' => 'Manage Manual Events Settings',
              'icon'        => 'icon-facebook',
              'class'       => 'ArtAndCodeStudio\FacebookEvents\Models\ManualEventsSettings',
              'order'       => 500,
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
    // public function registerMarkupTags()
    // {
    //     return [
    //         'filters' => [
    //             // A global function, i.e str_plural()
    //             'plural' => 'str_plural',

    //             // A local method, i.e $this->makeTextAllCaps()
    //             'uppercase' => [$this, 'makeTextAllCaps']
    //         ],
    //         'functions' => [
    //             // A static method call, i.e Form::open()
    //             'form_open' => ['October\Rain\Html\Form', 'open'],

    //             // Using an inline closure
    //             'helloWorld' => function() { return 'Hello World!'; }
    //         ]
    //     ];
    // }

    // public function makeTextAllCaps($text)
    // {
    //     return strtoupper($text);
    // }

}
