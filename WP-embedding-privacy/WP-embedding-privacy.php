<?php
/*
Plugin Name: WP Embedding Privacy
Plugin URI: https://github.com/pixelistik/WP-Embedding-Privacy
Description: Whenever you use the Wordpress automatic embedding feature, the embed code is deactivated, so when a visitor load the page, no communication with the third party takes place. Instead, the embedded video is replaced by a locally cached thumbnail of the video. Once the user clicks the thumbnail, the embed code is activated and the embedded video loads as usual.
Version: 0.2
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
		
		wp_register_style(
			'WP-embedding-privacy',
			WP_PLUGIN_URL.'/WP-embedding-privacy/css/WP-embedding-privacy.css'
		);
		wp_enqueue_style('WP-embedding-privacy');

		add_filter('oembed_dataparse',array(&$this,'parse'),10,3);
	}
	
	/**
	 * The filter function. Works on video type embeds only.
	 */
	function parse($return, $data, $url)
	{
		if ($data->type=='video') {
			// Cache the image
			$upload_dir=wp_upload_dir();
			$temp_url=parse_url($data->thumbnail_url);
			$destination_filename='embed-'.md5($data->thumbnail_url).basename($temp_url['path']);
			$destination_file_local=$upload_dir['path'].'/'.$destination_filename;
			$destination_file_url=$upload_dir['url'].'/'.$destination_filename;
			if(!file_exists($destination_file_local)){
				copy($data->thumbnail_url,$destination_file_local);
			}
			// Get generic width from Wordpress
			$display_width=get_option('embed_size_w');
			$display_height='auto';
			// But try to find real width of embedded object by looking at embed code
			if (preg_match('/.*?width="(\\d+)"/is', $return, $matches))
			{
				$display_width=$matches[1].'px';
			}
			if (preg_match('/.*?height="(\\d+)"/is', $return, $matches))
			{
				$display_height=$matches[1].'px';
			}
			// Add YouTube autostart
			if ($data->provider_name=='YouTube' && preg_match('#(?<=youtube\.com/e/)(.*)"#U', $return, $matches))
			{
				$return=str_replace($matches[1],$matches[1].'?autoplay=1',$return);
			}
			// Add Vimeo autostart
			if ($data->provider_name=='Vimeo' && preg_match('#(?<=vimeo\.com/video/)(.*)"#U', $return, $matches))
			{
				$return=str_replace($matches[1],$matches[1].'?autoplay=1',$return);
			}
			// Need to adjust for YouTube widescreen thumb?
			// Calculate how much CSS will resize the thumb
			$thumbnailUpscaleFactor=$display_width/$data->thumbnail_width;
			$thumbnailDisplayHeight=$data->thumbnail_height * $thumbnailUpscaleFactor; 
			$verticalOffset=($thumbnailDisplayHeight-$display_height)/2;
			$pre='<div class="WP-embedding-privacy-container">
					<a href="'.$url.'" style=" height: '.$display_height.'; width:'.$display_width.';">
						<img src="'.$destination_file_url.'" style="margin-top: -'.$verticalOffset.'px;" />
						<span>'.$data->provider_name.'</span>
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
