<?php namespace ArtAndCodeStudio\FaceBookEvents\Widgets;

use Backend\Classes\WidgetBase;

class TextDisplay extends WidgetBase
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'artandcodestudio_textdisplay';

    public function render()
    {
        $this->vars['test'] = "test";
        return $this->makePartial('textdisplay');
    }
}