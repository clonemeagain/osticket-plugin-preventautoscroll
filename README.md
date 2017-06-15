# osTicket plugin: Prevent Autoscroll

Stops agent ticket views from auto-scrolling to the last thread-entry. 

Possibly overkill, but by making a plugin I am stopping myself from editing core.

Requirements: Install the AttachmentPreview plugin: https://github.com/clonemeagain/attachment_preview

That plugin exposes an API that lets other plugins edit the output of osTicket before it reaches the browser, this plugin needs that API.


## How to enable

* Download and unzip this plugin into your /include/plugins folder [v1.0 zip](https://github.com/clonemeagain/osticket-plugin-preventautoscroll/archive/v1.0.zip) [v1.0 gzip](https://github.com/clonemeagain/osticket-plugin-preventautoscroll/archive/v1.0.tar.gz)
* Use an admin account to visit /scp/plugins.php (Admin Panel => Manage => Plugins)
* Select "Add new Plugin"
* Press "Install" next to "Prevent Autoscroll" 
* Click the checkbox next to "Prevent Autoscroll" 
* From the "More dropdown" select "Enable"

Now test by visiting a ticket, due to pjax, you might need to refresh the page. The only change should be that the viewport no longer scrolls to the last thread entry. All other functionality should still work, let me know if it doesn't.


## This plugin is not actually required

If you want this functionality without installing a plugin, simply edit ```/scp/js/thread.js``` 

```javascript
var thread = {

    options: {
        autoScroll: true,
        showimages: false
    },

    scrollTo: function (entry) {

       if (!entry) return;

       var frame = 0;
... snip ...
```

Modify it to:

```javascript
var thread = {

    options: {
        autoScroll: true,
        showimages: false
    },

    scrollTo: function (entry) {
    
    	   return; // Prevent Autoscroll MOD

       if (!entry) return;

... snip ...
```