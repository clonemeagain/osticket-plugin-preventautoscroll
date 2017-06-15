# osTicket plugin: Prevent Autoscroll

Stops agent ticket views from auto-scrolling to the last thread-entry. 

Possibly overkill, but by making a plugin I am stopping myself from editing core.

Requirements: Install the AttachmentPreview plugin: https://github.com/clonemeagain/attachment_preview

That plugin exposes an API that lets other plugins edit the output of osTicket before it reaches the browser, this plugin needs that API.


## How to enable

* Download this plugin into your /include/plugins folder, I use osticket-plugin-preventautoscroll as a foldername, but you can call it whatever you wish. 
* Use an admin account to visit /scp/plugins.php (Admin Panel => Manage => Plugins)
* Select "Add new Plugin"
* Press "Install" next to "Prevent Autoscroll" 
* Click the checkbox next to "Prevent Autoscroll" 
* From the "More dropdown" select "Enable"

Now test by visiting a ticket, due to pjax, you might need to refresh the page.


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
       $('html, body').animate({
            scrollTop: entry.offset().top - 50,
       }, {
            duration: 400,
            step: function(now, fx) {
                // Recalc end target every few frames
                if (++frame % 6 == 0)
                    fx.end = entry.offset().top - 50;
            }
        });
    },
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

       var frame = 0;
       $('html, body').animate({
            scrollTop: entry.offset().top - 50,
       }, {
            duration: 400,
            step: function(now, fx) {
                // Recalc end target every few frames
                if (++frame % 6 == 0)
                    fx.end = entry.offset().top - 50;
            }
        });
    },
```