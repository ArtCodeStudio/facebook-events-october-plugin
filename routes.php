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
/** 
 * 
 */
Route::get('/get_login_url', function () {
    $FB_sdk = new FacebookSDK();
    return $FB_sdk->getLoginURL();
});


/** 
 * 
 */
Route::get('/test', function () {
    $long_lived_token = 'EAAKXS6qjZAsQBAGvz5oKJf21nFb51Q0Q7aRu0IBu6JoUWoaBfpa6UFWaINLfcqBap7zOM9mzLITbvmWZAb6lWvUx0wVbZAbEgRx1SkuOfMKhrnSY4YtSiIiutRZCtXTgzheIOlwIu0STBcden1iFZCrMjzWeYNMjssgaUnvWKGK0FIHDvUlZAjcNnCoZBVYTpMZD' ;
    $user_id_request_url = "https://graph.facebook.com/v3.3/me?access_token=".$long_lived_token;

    $json = file_get_contents($user_id_request_url);
    $obj = json_decode($json);
    $user_id= $obj->id;
;
    $permanent_token_request_url = "https://graph.facebook.com/v3.3/".$user_id."/accounts?access_token=".$long_lived_token;
    $json = file_get_contents($permanent_token_request_url);
    $obj = json_decode($json, true);
    var_dump($obj['data'][0]['access_token']);

});

