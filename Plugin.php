<?php namespace ArtAndCodeStudio\FaceBookEvents;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    /**
     * Plugin Details
     */
    public function pluginDetails()
    {
        return [
            'name' => 'FaceBook Events',
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
            'ArtAndCodeStudio\FaceBookEvents\Components\EventList' => 'eventList'
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
                'class'       => 'ArtAndCodeStudio\FaceBookEvents\Models\Settings',
                'order'       => 500,
            ]
        ];
    }
    public function registerFormWidgets()
    {
        return [
            'ArtAndCodeStudio\FaceBookEvents\FormWidgets\TextDisplay' => 'artandcodestudio_textdisplay',
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
