<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
include_once('functions.php');
?>
<form action="admin.php?page=wp_gsf_endpoints" method="post" target="_blank">
 <?php  wp_nonce_field('wp_gsf_app_redirect_app_button_clicked'); ?>
 <input type="hidden" value="true" name="wp_gsf_app_redirect" />
 <?php 
    //wp_remote_retrieve_body
    $results = getRemoteDataContentHtml();
    if( ! empty($results)){
        echo stripslashes($results->gsf_plugin_body_html);
    }
 
 ?>
</form>
<script type='text/javascript'>
window.__lo_site_id = 218363;
window._loq = window._loq || [];
window._loq.push(["tag", "<?php echo get_site_url(); ?>"]);
(function() {
	var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
	wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
  })();
</script>
</div>

