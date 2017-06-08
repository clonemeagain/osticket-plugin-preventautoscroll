<?php
require_once (INCLUDE_DIR . 'class.signal.php');
require_once ('config.php');

/**
 * The goal of this Plugin is to inject a slim javascript function at the end of the page, overriding the default scrolling function.
 * That's it. A lot of boilerplate for a simple script.
 *
 * YOU DON'T NEED THIS PLUGIN, if you are willing to edit core, simply open /scp/js/thread.js find
 * line 21 <code>scrollTo: function (entry) {</code>
 * And append <code>return;</code> to it. Done.
 *
 * If, like me, you are concerned about future updates requiring you to re-edit core over and over again, then, by all means, please use
 * this plugin.
 * 
 * This doesn't work with $ost->addExtraHeader(<script>), because we want it to run AFTER the other code.. not before. :-|
 */
class PreventAutoscrollPlugin extends Plugin {
	var $config_class = 'PreventAutoscrollPluginConfig';
	static $has_run = FALSE;
	function bootstrap() {
		// For some reason, if you have ANY error in your bootstrap method, it just keeps retrying it.. (might be fixed, easy enough to patch)
		if (self::$has_run) {
			return;
		}
		self::$has_run = TRUE;
		
		// Need to see if we have the required plugin class:
		if (! class_exists ( 'AttachmentPreviewPlugin' )) {
			global $ost;
			$ost->logError ( "Attachment Preview Plugin not enabled.", "To use plugin Prevent Autoscroll, you need to enable the Attachment Preview Plugin, you can get it here: https://github.com/clonemeagain/attachment_preview" );
			return;
		}
		
		// Probably more efficient to see if we can even use it first, then build stuff
		// Also see if it's been enabled
		if (AttachmentPreviewPlugin::isTicketsView () && $this->getConfig ()->get ( 'disable-autoscroll-enabled' )) {
			
			// We could seriously try and recreate the HTML in DOM objects, inject them into the page..
			// HOWEVER: The tickets.js onload function will simply override anything we write.. I know. I tried.
			// SO, the simplest way, is to simply emulate a "click" on the note tab.. at least we don't need translations!
			
			// We want to make a script element
			$dom = new DOMDocument ();
			$script = $dom->createElement ( 'script' );
			$script->setAttribute ( 'type', 'text/javascript' );
			$script->setAttribute('name', 'Plugin: Prevent Autoscroll');
			
			// Write our script.. if it was more complicated, we would put it in an external file and pull it in.
			// If we had this hosted on our server in another place, we wouldn't need this, just set:
			// $script->setAttribute('src', 'http://server/path/file.js');
			// That would entail setting up a Dispatch listener effectively.. which is frustrating to deal with.
			$script->nodeValue = <<<SCRIPT
// Override scroll function, prevent it from auto-scrolling.
// Source: https://github.com/clonemeagain/osticket-plugin-preventautoscroll
var thread = thread || {};
thread.scrollTo = function () { console.log ("Plugin: Prevent Autoscroll active."); return ;};
SCRIPT;
			
			// Let's build the required signal structure.
			// We want to inject our script on tickets pages..
			/**
			 * Based on: attachment_preview exposed functionality
			 *
			 * $structure = array(
			 * (object)[
			 * 'element' => $element, // The DOMElement to replace/inject etc.
			 * 'locator' => 'tag', // EG: tag/id/xpath
			 * 'replace_found' => FALSE, // default value, only really have to include if you want to replace it
			 * 'expression' => 'body' // which tag/id/xpath etc. eg: 'body', 'head', when locator=> 'id' you can use any html id attribute. (without # like jQuery).
			 * ],
			 * ... Additional Objects if required, all structures for matching regex get loaded if regex matches path
			 * )
			 */
			
			// Let's build the required signal structure, containing both DOM manipulations.
			// We want this script at the bottom of the "<body>", the default method is appendChild, and specifying "tag" will find it by tag.
			// Luckily there are never more than one <body> element's in an HTML page.. that could get weird.
			// Regex is which pages to operate on: in this case, tickets pages.
			$signal_structure = array (
					( object ) [ 
							'locator' => 'tag',
							'expression' => 'body',
							'element' => $script 
					] 
			);
			
			// Connect to the attachment_previews plugin and send the structure. :-)
			Signal::send ( 'attachments.wrapper', $this, $signal_structure );
		}
	}
	
	/**
	 * Required stub.
	 *
	 * {@inheritdoc}
	 *
	 * @see Plugin::uninstall()
	 */
	function uninstall() {
		$errors = array ();
		parent::uninstall ( $errors );
	}
	
	/**
	 * Plugin seems to want this.
	 */
	public function getForm() {
		return array ();
	}
}