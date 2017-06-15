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
	function bootstrap() {
		
		// Need to see if we have the required plugin:
		if (! class_exists ( 'AttachmentPreviewPlugin' )) {
			global $ost;
			$ost->logError ( "Attachment Preview Plugin not enabled.", "To use plugin Prevent Autoscroll, you need to enable the Attachment Preview Plugin, you can get it here: https://github.com/clonemeagain/attachment_preview" );
			return;
		}
		
		// See if we are viewing a ticket
		if (AttachmentPreviewPlugin::isTicketsView ()) {
			// We want to make a script element to inject into the page
			// I've added some attributution attributes.. makes it easier to see the script in the generated source
			$dom = new DOMDocument ();
			$script = $dom->createElement ( 'script' );
			$script->setAttribute ( 'type', 'text/javascript' );
			$script->setAttribute ( 'name', 'Plugin: Prevent Autoscroll' );
			$script->setAttribute ( 'plugin-source', 'https://github.com/clonemeagain/osticket-plugin-preventautoscroll' );
			
			// Write our script.
			// This overrides the function thread.scrollTo with a new function that does nothing.
			$script->nodeValue = 'var thread = thread || {}; thread.scrollTo = function () { return ;};';
			
			// Let's build the required signal structure for where we want it to appear, inside the <body>, default is append, so
			// it will be at the bottom before the </body> tag. Passed by ref, so has to be defined first.
			$sendable = array (
					( object ) [ 
							'locator' => 'tag',
							'expression' => 'body',
							'element' => $script 
					] 
			);
			
			// Connect to the attachment_previews plugin and done!
			Signal::send ( 'attachments.wrapper', $this, $sendable );
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