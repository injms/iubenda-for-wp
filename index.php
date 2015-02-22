<?php
/**
 * Plugin Name: WP Iubenda Policy user
 * Plugin URI: http://inj.ms/iubenda
 * Description: Use your Iubenda privacy and cookies policy without JavaScript
 * Version: 0.85
 * Author: Ian James
 * Author URI: http://inj.ms
 */


// Variables and other settings
// =============================================================================

// Time when a file needs to be refetched from the JSON source
// -----------------------------------------------------------
$timelapse = 86400; // 24 hours in seconds ( 60 x 60 x 24 )

	
// Useful functions
// =============================================================================


// check the age of a file, and return true if less than $timelapse old
// --------------------------------------------------------------------
function injms_check_age($file_age, $timelapse) {
	$current_time = time();
	$time_difference = $current_time - $file_age;

	if ( $time_difference < $timelapse ) {
		return true;
	}
	else {
		return false;
	}
};


// GET the $url and returns it
// ---------------------------
function injms_curl($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}


// Save the supplied JSON
// ----------------------
function injms_save_json( $json, $filename ) {
	file_put_contents($filename, $json);
};


// The main function - call this to embed the policy anywhere in your theme
// eg echo injms_iubenda( 123456, true, 'black' );
//  - first variable is the policy ID
//	- the second is true / false for whether a table of contents is generated 
//	with some jQuery
//	- the third is what colour the fallback JavaScript button should be - black, 
// white, or nostyle. Note the nostyle needs a pro account.
// =============================================================================
function injms_iubenda( $policy_id, $toc, $theme ){

	$injms_upload_dir = wp_upload_dir();

	$injms_iubenda = new stdClass;
	$injms_iubenda->directory = $injms_upload_dir['basedir'] . '/injms-iubenda/';
	$injms_iubenda->file = $injms_iubenda->directory . $policy_id . '.json';

	// Check to make sure the directory that we're going to cache the JSON
	// file in exists
	if ( !file_exists( $injms_iubenda->directory ) ) {
		mkdir( $injms_iubenda->directory, '0755', true );
	}

	// Check to make sure a file isn't already cached
	if ( !file_exists( $injms_iubenda->file ) ) {

		$injms_iubenda->json = injms_curl( 'https://www.iubenda.com/api/privacy-policy/' . $policy_id . '/no-markup' );

		$injms_iubenda->output_php = json_decode( $injms_iubenda->json );

		// We only want to save this if it's been successful. This is the first part of the
		// return JSON : { success: 1, ...}
		// If not, we serve the JavaScript fallback and break
		if ( $injms_iubenda->output_php->success == true ) {
			injms_save_json( $injms_iubenda->json, $injms_iubenda->file );
		} else {
			return "<a href=\"//www.iubenda.com/privacy-policy/{$policy_id}\" class=\"iubenda-{$theme} iubenda-embed\" title=\"Privacy Policy\">Privacy Policy</a><script type=\"text/javascript\">(function (w,d) {var loader = function () {var s = d.createElement(\"script\"), tag = d.getElementsByTagName(\"script\")[0]; s.src = \"//cdn.iubenda.com/iubenda.js\"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener(\"load\", loader, false);}else if(w.attachEvent){w.attachEvent(\"onload\", loader);}else{w.onload = loader;}})(window, document);</script>";
		}
	}

	$injms_iubenda->output_json = file_get_contents( $injms_iubenda->file );
	$injms_iubenda->output_php = json_decode( $injms_iubenda->output_json );

	if ( $injms_iubenda->output_php->success == true AND $toc == true) {
		return preg_replace('/<br \\/>/', '</p><p>', $injms_iubenda->output_php->content) . 
			"<script>
				(function($){
					var policyHeadings = $('#policy-contents :header:not(h1)'),
						toc = $('#policy-table-of-contents');

					for (var i = 0; i < policyHeadings.length; i++) {
						var text = policyHeadings[i].innerHTML,
							id = encodeURIComponent( text ).replace(/[\(|\)|\*|%20|_|\.|\']/g, \"\" ),
							level = policyHeadings[i]

						policyHeadings[i].id = id;

						toc.append('<li class=\"indent-' + policyHeadings[i].localName + '\"><a href=\"#' + id + '\">' + text + '</a></li>')
					};

				})(jQuery);
			</script>";
	} elseif ( $injms_iubenda->output_php->success == true ) {
		return preg_replace('/<br \\/>/', '</p><p>', $injms_iubenda->output_php->content);
	}
}

?>
