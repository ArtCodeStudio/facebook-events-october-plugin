<?php namespace ArtAndCodeStudio\FaceBookEvents\Components;

use Cms\Classes\ComponentBase;
use ArtAndCodeStudio\FaceBookEvents\Classes\FaceBookSDK;

class EventList extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'EventList Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    // This array becomes available in the component template as  {{ __SELF__.posts }} and on teh page {{component.posts}}
    public function posts()
    {
        return ['First Post', 'Second Post', 'Third Post'];
    }

    public function events() 
    {
        $FB_sdk = new FaceBookSDK();
        $FB_sdk->getEvents();
        return $FB_sdk->getEvents();
    }
}
