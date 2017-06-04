<?php
require_once INCLUDE_DIR . 'class.plugin.php';
class PreventAutoscrollPluginConfig extends PluginConfig {
	// Provide compatibility function for versions of osTicket prior to
	// translation support (v1.9.4)
	function translate() {
		if (! method_exists ( 'Plugin', 'translate' )) {
			return array (
					function ($x) {
						return $x;
					},
					function ($x, $y, $n) {
						return $n != 1 ? $y : $x;
					} 
			);
		}
		return Plugin::translate ( 'prevent-autoscroll' );
	}
	
	/**
	 * Build an Admin settings page.
	 *
	 * {@inheritdoc}
	 *
	 * @see PluginConfig::getOptions()
	 */
	function getOptions() {
		list ( $__, $_N ) = self::translate ();
		return array (
				'ri' => new SectionBreakField ( array (
						'label' => $__ ( 'Configuration Unnecessary, simply disable the plugin to re-enable default auto-scroll.' ) 
				) ),
				't' => new SectionBreakField ( array (
						'label' => $__ ( 'Source: https://github.com/clonemeagain/osticket-plugin-preventautoscroll' ) 
				) ) 
		);
	}
}
