<?php
/**
 * Plugin Name: WP Iubenda policy embedder
 * Plugin URI: http://inj.ms/iubenda
 * Description: Use your Iubenda privacy and cookies policy without JavaScript
 * Version: 1.1.0
 * Author: Ian James
 * Author URI: http://inj.ms
 * GitHub Plugin URI: https://github.com/injms/iubenda-for-wp
 */

/**
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Sanity check.
 */
if ( ! function_exists( 'injms_iubenda' ) ) {

	/**
	 * The main function - call this to embed the policy anywhere in your theme
	 *
	 * @param  array  $atts same as shortcode attributes.
	 * @param  string $content same as shortcode content.
	 *
	 * @return string iubenda policy.
	 */
	function injms_iubenda( $atts, $content = null ) {
		// Read attributes.
		$policy_id    = $atts['policy_id'];
		$type         = $atts['type'];
		$iub_theme    = $atts['theme'];
		$text_only    = $atts['text_only'] ? 'no-markup' : '';
		$iframe       = $atts['iframe'];
		$max_age      = $atts['cache'];
		$label        = str_replace( [ ' E ', ' And ' ], [ ' e ', ' and ' ], ucwords( str_replace( '-', ' ', $type ) ) );
		$transient_id = "iubenda-{$type}-{$policy_id}-{$iub_theme}-{$text_only}";
		$cookie_fix   = '';

		// Fix cookie policy API.
		if ( 'cookie-policy' === $type ) {
			$type       = 'privacy-policy';
			$cookie_fix = 'cookie-policy';
			$text_only  = 'cookie-policy';
		}

		$embed_link = "<a href=\"//www.iubenda.com/{$type}/{$policy_id}/{$cookie_fix}\" class=\"iubenda-{$iub_theme} iubenda-embed\" title=\"{$label}\">{$label}</a><script type=\"text/javascript\">(function (w,d) {var loader = function () {var s = d.createElement(\"script\"), tag = d.getElementsByTagName(\"script\")[0]; s.src = \"//cdn.iubenda.com/iubenda.js\"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener(\"load\", loader, false);}else if(w.attachEvent){w.attachEvent(\"onload\", loader);}else{w.onload = loader;}})(window, document);</script>";

		// Link only.
		if ( $iframe ) {
			return $embed_link;
		}

		// Check cache.
		$content = get_transient( $transient_id );

		// Returns cached result.
		if ( false !== $content ) {
			return $content;
		}

		// Remote request.
		$response = wp_remote_request( "https://www.iubenda.com/api/{$type}/{$policy_id}/{$text_only}" );

		// Fallback to embed link.
		if ( is_wp_error( $response ) ) {
			return $embed_link;
		}

		// Decode result.
		$content = wp_remote_retrieve_body( $response );
		$content = json_decode( $content );

		// Update cache.
		if ( ! empty( $content ) && isset( $content->content ) ) {
			$content           = $content->content;
			$transient_updated = set_transient( $transient_id, $content, $max_age );
		} else {
			$content = $embed_link;
		}

		return preg_replace( '/<br \\/>/', '</p><p>', $content );
	}

	/**
	 * [injms_iubenda] shortcode  wrapper for injms_iubenda().
	 *
	 * @param  array  $atts    shortcode attributes.
	 * @param  string $content shortcode content.
	 *
	 * @return string          iubenda policy.
	 */
	function injms_iubenda_shortcode( $atts, $content = null ) {
		$values = shortcode_atts(
			array(
				'policy_id' => '',
				'type'      => 'privacy-policy', // privacy-policy | cookie-policy | terms-and-conditions.
				'theme'     => 'white', // black | white | nostyle.
				'text_only' => true,
				'iframe'    => false,
				'cache'     => 86400, // 24 hours in seconds ( 60 x 60 x 24 ).
			), $atts
		);

		return injms_iubenda( $values, $content );
	}

	add_shortcode( 'injms_iubenda', 'injms_iubenda_shortcode' );
}
