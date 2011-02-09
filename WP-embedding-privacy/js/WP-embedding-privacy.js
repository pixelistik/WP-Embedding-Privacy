jQuery(document).ready(function() {
	jQuery(".WP-embedding-privacy-container").click(function(){
		jQuery(this).html(jQuery(this).children("script").html());
		return false;
	});
});