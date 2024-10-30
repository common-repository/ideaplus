<?php

include 'ideaplus-plugin-admin-header.php'; ?>
<section style="text-align: center; padding: 100px 0;">
    <div style="margin-bottom: 40px;">
        <p style="font-size: 30px;font-weight: 600; margin-bottom: 40px;">Connect to Ideaplus</p>
        <img src="<?php
        echo Ideaplus_Plugin_Func::get_admin_asset_url('images/u24.png'); ?>" alt="">
        <p style="font-size:16px">You’re almost done! Just 2 more steps to have your WooCommerce<br/> store connected to Ideaplus for automatic
            order fulfillment.</p>
    </div>
    <?php
    $app_name = ideaplus_get_config('APP_NAME');
    $authLink = ideaplus_get_config('IDEAPLUS_API_HOST') . 'v1/basic/auth?key=' . urlencode(Ideaplus_Plugin_Func::get_customer_key()) . '&site=' . urlencode(Ideaplus_Plugin_Func::curPageURL()) . '&token=' . urlencode(Ideaplus_Plugin_Func::get_option('token', '')) . '&return_url=' . urlencode(get_admin_url(null, 'admin.php?page=' . $app_name));
    echo '<a id="connect-button"  style="font-size:16px" href="' . esc_url($authLink) . '" class="button-primary' . esc_attr((!empty($issues) ? 'disabled' : '')) . '" target="_blank">Connect Now</a>';
    ?>
</section>
<script type="text/javascript">
	jQuery( document ).ready( function () {
		Ideaplus_Admin.init();
	} );
</script>
<?php
include 'ideaplus-plugin-admin-footer.php'; ?>
