# October CMS Facebook Events Plugin

This plugin allows you to announce upcoming events on your website, which can be either manually created or automatically pulled from a Facebook page. You can also mix both modes.
The plugin contains a single component, named 'eventlist', which is used to display the event timeline. The default template for it is very bare and raw. It is recommended to use your markup for it to make the design fit your needs.

## Settings
Under Settings/Misc. in the Backend you will find a new menu entry called 'Facebook Events'.
Click on it to configure the behavior of the Facebook Events plugin. There are three tabs of settings you can configure:

### General Settings

Here you can set up which features of Event data should be rendered in the eventlist component. Options are:

#### Include Past Events
If this is checked, events will still be displayed if they already lie in the past.
#### Name
Include the name of the event
#### Description
Include the description of the event
#### Cover Image
Include an image for the event
#### Event URL
Include a link to an external URL with possibly more information about the event. If it is an Event from Facebook, the link will point to the Event on the Facebook page. For manual events, an external URL can be added manually, but this is optional.

### Manual Events

This tab included only a repeater form where you can manually enter data for events. The fields are self-explanatory. Images can are uploaded using the OctoberCMS Media Manager.
At the very bottom of your list of already entered events, you will find the button 'Add new item' to add another event to your timeline.

### Facebook Setup

If you want to include Events from a Facebook page you have to register a Facebook 'app' for it and enter certain credentials here to authorize your app with the Facebook API server. The steps to complete for this are as follows:

#### 1.) Create a new App
First, we need to create a new Facebook app.
Create an account at https://developers.facebook.com, if you don't have one. Then, from https://developers.facebook.com/docs/apps/, navigate to the 'My Apps' section and add a new app.
![](https://github.com/ArtCodeStudio/facebookevents-october-plugin/blob/master/docs/images/Add_new_App.png)
Choose 'Manage Business Integrations' as the app type.
The app name is up to you.
Enter your email and set 'Who can use your app?' to 'Just me, ...'
Now you are ready to create the new app.

#### 2.) Setup Facebook Login
Once you created the app, set up the 'Facebook Login': Navigate to the settings.
The only thing we need to add here is the callback URL on your server.
https://yourdomain.net/facebook_callback
Save Changes

#### 3.) Obtain Credentials
Navigate to Settings / Basic.
There you find the required credentials.
Copy the 'App ID' and 'App Secret' to the OctoberCMS Facebook Events Plugin's appropriate inputs and add your Event Page name and save.

#### 4.) Obtain Credentials
Since the configuration is now done, you are ready to Login to Facebook and authorize your app.
Click 'Click here to login' this will take you to Facebook.
Here you select the page you like to use and click 'next'.
Confirm the process by clicking 'done' and 'OK' on the following pages.
After that, you get redirected to your 'Facebook Events' Plugin Settings.
Here you can verify that the APP TOKEN field is now populated as well as the Access and Data Access Token details.

## Custom Event Component
To override the Plugin default component view,  
copy the ```default.html``` from ```plugins/artandcodestudio/facebookevents/components/eventlist/default.htm``` to ```/themes/yourtheme/partials/eventlist/default.htm``` and change the markup according to your needs and wishes.

## Routes
Facebook Callback Route: /facebook_callback
