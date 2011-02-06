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
	function parse($return, $data, $url)
	{
		if (true/*$data->type=='photo' /* && preg_match('flickr',$url) */ ) {
			$pre = '<div id="putmein">
					<a href="'.$url.'" id="trigger">
						<img src="'.$data->thumbnail_url.'" />
					</a>
				</div>
				<script>
				code=\'
			';
			$post = '
				\';
				jQuery(document).ready(function() {
					jQuery("#trigger").click(function(){
						jQuery("#putmein").html(code);	
						return false;
					});
				});
				</script>
			';
			$return = $pre . $return . $post;
		}
		return $return;
	}
	function youtubeParse()
	{
		wp_enqueue_script('jquery');
		add_filter('oembed_dataparse',array(&$this,'parse'),10,3);
	}
}
?>
