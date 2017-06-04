# osTicket plugin: Prevent Austoscroll

Stops agent ticket views from auto-scrolling to the last thread-entry. 

Possibly overkill, but by making a plugin I am stopping myself from editing core.

Requirements: Install (you don't need to enable) the AttachmentPreview plugin: https://github.com/clonemeagain/attachment_preview

That plugin exposes an API that lets other plugins edit the output of osTicket before it reaches the browser, this plugin needs that API.