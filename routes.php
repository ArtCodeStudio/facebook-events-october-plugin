<?php 
use ArtAndCodeStudio\FacebookEvents\Classes\FacebookSDK;
/**
 * 
 */
Route::get('/facebook_validate', function () 
{
    $FB_sdk = new FacebookSDK();
    $FB_sdk->getTokenDetails();
});
/**
 * 
 */
Route::get('/facebook_login', function () {
    $FB_sdk = new FacebookSDK();
    $FB_sdk->login();
});
/**
 * 
 */
Route::get('/facebook_callback', function ()  {
    $FB_sdk = new FacebookSDK();
    $FB_sdk->loginCallback();
});
/**
 * 
 */
Route::get('/facebook_events', function () {
    $FB_sdk = new FacebookSDK();
    $FB_sdk->getEvents();
});
