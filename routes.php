<?php 
use ArtAndCodeStudio\FaceBookEvents\Classes\FaceBookSDK;
/**
 * 
 */
Route::get('/facebook_validate', function () 
{
    $FB_sdk = new FaceBookSDK();
    // $FB_sdk->validateToken();
    $FB_sdk->getTokenDetails();
});
/**
 * 
 */
Route::get('/facebook_login', function () {
    $FB_sdk = new FaceBookSDK();
    $FB_sdk->login();
});
/**
 * 
 */
Route::get('/facebook_callback', function ()  {
    $FB_sdk = new FaceBookSDK();
    $FB_sdk->loginCallback();
});
/**
 * 
 */
Route::get('/facebook_events', function () {
    $FB_sdk = new FaceBookSDK();
    $FB_sdk->getEvents();
});
