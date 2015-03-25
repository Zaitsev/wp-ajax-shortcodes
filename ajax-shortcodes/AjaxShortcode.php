<?php

/*
Plugin Name: Ajax Shortcode
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: VLZ
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class AjaxShortcode {
	public function __construct() {
		add_action( 'wp_ajax_nopriv_ajax_shortcode', array( __CLASS__, 'ajax_shortcode_callback' ) );
		add_action( 'wp_ajax_ajax_shortcode', array( __CLASS__, 'ajax_shortcode_callback' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'ajax_shortcode_JS_CSS' ) );

	}

	public static function ajax_shortcode_JS_CSS() {
		$url = admin_url( 'admin-ajax.php' );
		$JS  = <<<EOL
jQuery(document).ready(function($){
	banners=$('.sys-ajax-shortcode');
	banners.each(function(){
		var data = {
			 target:this.dataset.ajx_target
			 ,action:'ajax_shortcode'
			 ,shortcode:this.dataset.ajx_shortcode
		};
		$.get(
			'$url'
			,data
			,function(resp){
			try {
				var target  = resp.target
				$(resp.target).html(resp.data);
				}catch(e) {}

			}
		,'json'
		);
	})
});
EOL;

		self::enqueue_inline_script( 'ajax_shortcode_JS_CSS', $JS, array( 'jquery' ), true );
	}
	/* ------------------------------------------- */
	/**
	 * Enqueue inline Javascript. @see wp_enqueue_script().
	 *
	 * KNOWN BUG: Inline scripts cannot be enqueued before
	 *  any inline scripts it depends on, (unless they are
	 *  placed in header, and the dependant in footer).
	 *
	 * @param string      $handle    Identifying name for script
	 * @param string      $src       The JavaScript code
	 * @param array       $deps      (optional) Array of script names on which this script depends
	 * @param bool        $in_footer (optional) Whether to enqueue the script before </head> or before </body>
	 *
	 * @return null
	 */
	function enqueue_inline_script( $handle, $js, $deps = array(), $in_footer = false ){
		// Callback for printing inline script.
		$cb = function()use( $handle, $js ){
			// Ensure script is only included once.
			if( wp_script_is( $handle, 'done' ) )
				return;
			// Print script & mark it as included.
			echo "<script type=\"text/javascript\" id=\"js-$handle\">\n$js\n</script>\n";
			global $wp_scripts;
			$wp_scripts->done[] = $handle;
		};
		// (`wp_print_scripts` is called in header and footer, but $cb has re-inclusion protection.)
		$hook = $in_footer ? 'wp_print_footer_scripts' : 'wp_print_scripts';

		// If no dependencies, simply hook into header or footer.
		if( empty($deps)){
			add_action( $hook, $cb );
			return;
		}

		// Delay printing script until all dependencies have been included.
		$cb_maybe = function()use( $deps, $in_footer, $cb, &$cb_maybe ){
			foreach( $deps as &$dep ){
				if( !wp_script_is( $dep, 'done' ) ){
					// Dependencies not included in head, try again in footer.
					if( ! $in_footer ){
						add_action( 'wp_print_footer_scripts', $cb_maybe, 11 );
					}
					else{
						// Dependencies were not included in `wp_head` or `wp_footer`.
					}
					return;
				}
			}
			call_user_func( $cb );
		};
		add_action( $hook, $cb_maybe, 0 );
	}
	private static function safe_die(){
		exit(0);
	}
	private static function send_json($resp){
		return wp_send_json($resp); //return used fo PHPUnitTest
		//self::safe_die(); //Auto Died in wp_send_json
	}

	static public function ajax_shortcode_callback(){
		return self::send_json(self::callback());
	}

	static public function callback(){
		global $shortcode_tags;;
		$resp         = new stdClass();
		$target           = $_GET['target'];
		$shortcode    = $_GET['shortcode'];
		$resp->target = $target;
		$resp->data   = '';
		$atts = array();
		if (!empty($_GET['dataset']) && is_array($_GET['dataset']))
			{
				foreach ($_GET['dataset'] as $k=>$v){
					if ($k=='ajx_target' || $k=='ajx_shortcode'){
						continue;
					}
					$atts[]=$k.'="'.($v).'"';
				}
			}
		$atts = count($atts) ? ' '.implode(' ',$atts) : '';
//		emsgd($shortcode_tags);
		foreach ( array_keys( $shortcode_tags ) as $s ) {
			if ( strpos( $s, 'ajax_' ) !== false && $s == $shortcode ) {
				$sh = "[".$s.$atts."]";
//			emsgd($sh);
				if (defined("UNIT_TESTING")) {
					do_shortcode( $sh );
				}else {
					ob_start();
					echo do_shortcode( $sh );
					$resp->data = ob_get_clean();
				}
				break;
			}
		}
		return $resp;
	}
}
new AjaxShortcode();
