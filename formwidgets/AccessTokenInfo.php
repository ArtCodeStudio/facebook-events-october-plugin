<?php namespace ArtAndCodeStudio\FacebookEvents\FormWidgets;

use Backend\Classes\FormWidgetBase;
use System\Behaviors\SettingsModel;
use ArtAndCodeStudio\FacebookEvents\Classes\FacebookSDK;

/**
 * AccessTokenInfo Form Widget
 */
class AccessTokenInfo extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'artandcodestudio_facebookevents_accesstokeninfo';

    /**
     * @inheritDoc
     */
    public function init()
    {

    }

    public function prepareVars()
    {
        $sdk = new FacebookSDK();
        $this->vars['loginURL'] = $sdk->getLoginURL();
        $this->vars['token'] = $sdk->getTokenDetails();
        $this->vars['loginButtonImage'] = url('/plugins/artandcodestudio/facebookevents/formwidgets/accesstokeninfo/assets/images/facebooklogin.png');
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('accesstoken_info');
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/accesstokeninfo.css', 'artandcodestudio.FacebookEvents');
        $this->addJs('js/accesstokeninfo.js', 'artandcodestudio.FacebookEvents');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return $value;
    }
}
