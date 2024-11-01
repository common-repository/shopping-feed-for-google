<?php
// my-plugin-name/views/missing-dependencies-admin-notice.php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @var string[] $missing_plugin_names */
$link = admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' );
?>

<div class="error notice">
    <p>
        <strong>Error:</strong>
        The <em>Shopping Feed for Google</em> plugin can not activate because the following required plugins are not installed or active: WooCommerce. Please activate this plugin if available or <a href='<?php echo $link; ?>'>Install WooCommerce!</a>
    </p>
</div>