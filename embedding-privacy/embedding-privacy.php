<?php
/*
Plugin Name: Embedding Privacy
Plugin URI: http://pixelistik.de
Description: Switches the automatically generated embed code for YouTube to the cookieless domain they offer. This way, YouTube will only set a cookie if the user actively starts the video playback.
Version: 0.1
Author: Pixelistik
Author URI: http://pixelistik.de
License: GPL2
*/
?>
<?php
function switch_to_nocookie_youtube($html, $url, $attr) {
     if ( strpos($html, "http://www.youtube.com" ) !== false) {
          return str_replace("http://www.youtube.com","http://www.youtube-nocookie.com",$html);
     } else {
          return $html;
     }
}
add_filter('embed_oembed_html', 'switch_to_nocookie_youtube', 10, 3);
?>

