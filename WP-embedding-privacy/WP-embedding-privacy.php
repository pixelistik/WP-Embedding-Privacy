<?php
/*
Plugin Name: WP Embedding Privacy
Plugin URI: https://github.com/pixelistik/WP-Embedding-Privacy
Description: Switches the automatically generated embed code for YouTube to the cookieless domain they offer. This way, YouTube will only set a cookie if the user actively starts the video playback.
Version: 0.1
Author: Pixelistik
Author URI: http://pixelistik.de
License: GPL2
*/
?>
<?php
/* Thank you, http://bjornery.com/web/hacking-oembed/ */
$youtubeparser = new youtubeParse;
class youtubeParse {
	/**
	 * Constructor, registers javascripts and our filter.
	 */
	function youtubeParse()
	{
		wp_enqueue_script('jquery');
		
		wp_register_script(
			'WP-embedding-privacy',
			WP_PLUGIN_URL.'/WP-embedding-privacy/js/WP-embedding-privacy.js',
			array('jquery')
		);
		wp_enqueue_script('WP-embedding-privacy');

		add_filter('oembed_dataparse',array(&$this,'parse'),10,3);
	}
	
	/**
	 * The filter function. Works on video type embeds only.
	 */
	function parse($return, $data, $url)
	{
		if ($data->type=='video') {
			// Get generic width from Wordpress
			$display_width=get_option('embed_size_w');
			// But try to find real width of embedded object by looking at embed code
			if (preg_match('/.*?width="(\\d+)"/is', $return, $matches))
			{
				$display_width=$matches[1];
			}
			
			$pre='<div class="WP-embedding-privacy-container">
					<a href="'.$url.'" id="trigger">
						<img src="'.$data->thumbnail_url.'" style="height: auto; width:'.$display_width.'px;"/>
					</a>
				<script type="text/plain">
			';
			$post='</script>
				</div>
			';
			$return=$pre.$return.$post;
		}
		return $return;
	}
}
?>
