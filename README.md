# October CMS Facebook Events Plugin

# Getting started
### 1.) Create a new App
First we need to create a new Facebook app at developer.facebook.com.  
Navigate to the My Apps section and add a new app.  
![](https://github.com/ArtCodeStudio/facebookevents-october-plugin/blob/master/docs/images/Add_new_App.png)
Choose 'Manage Business Integrations' as app type.  
The Appname is up to you.  
Enter your email and set 'Who can use your app to 'Just me,...'  
Now you are ready to create the new app.  

### 2.) Setup Facebook Login
Once you created the app setup the 'Facebook Login'.  
Navigate to the settings  
The only thing we need to add here is the callback url on your server.  
https://yourdomain.net/facebook_callback  
Save Changes

### 3.) Obtain Credentials
Navigate to Settings / Basic.  
There you find the required credentials.  
Copy the 'App ID' and 'App Secret' to the OctoberCMS Facebook Events Plugin's appropriate inputs and add your Event Page name either and save.  

### 4.) Obtain Credentials
Since the configuration now done, you are ready to Login to facebook and authorize your app.
Click 'Click here to login' this will take you to Facebook  
Here you select the page you like to use and click 'next'  
Confirm the process by clicking 'done' and 'OK' on the following pages.  
After that you get redirected to your 'Facebook Events' Plugin Settings.  
Here you can verify that the APP TOKEN field is no populated as well as the Access and Data Access Token details.  

#### Custom Event Component
To override the Plugin default component view,  
copy the ```default.html``` from ```plugins/artandcodestudio/facebookevents/components/eventlist/default.htm```  
to ```/themes/yourtheme/partials/eventlist/default.htm```

#### Settings
The settings tab lets you choos which items to include in the Events Component


#### Routes
Facebook Callback Route: /facebook_callback