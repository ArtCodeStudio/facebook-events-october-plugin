<?php namespace ArtAndCodeStudio\FaceBookEvents\FormWidgets;

use Backend\Classes\FormWidgetBase;
use System\Behaviors\SettingsModel;
//use ArtAndCodeStudio\FaceBookEvents\Classes\FaceBookSDK;

/**
 * TextDisplay Form Widget
 */
class TextDisplay extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'artandcodestudio_facebookevents_text_display';

    /**
     * @inheritDoc
     */
    public function init()
    {

    }
    
    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['custom_label'] =  $this->formField->config['custom_label'];
        
        if( isset($this->formField->config['class']) )
        {
            $class = $this->formField->config['class'];
            $c = new $class();
            $function = $this->formField->config['function'];
            $this->vars['value'] = $c->$function();
        }

        if( isset($this->formField->config['echo_text']) )
        {
            $this->vars['value'] = $this->formField->config['echo_text'];
        }

        if( isset($this->formField->config['echo_url']) )
        {
            
            $this->vars['value'] = "<a href='". $this->formField->config['echo_url']. "' target='_blank'>".$this->formField->config['echo_url']."</a>";
        }


    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('textdisplay');
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/textdisplay.css', 'artandcodestudio.FaceBookEvents');
        $this->addJs('js/textdisplay.js', 'artandcodestudio.FaceBookEvents');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return $value;
    }
}
