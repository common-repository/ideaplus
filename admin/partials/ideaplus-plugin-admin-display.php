<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1>hello world!!</h1>
<!-- Create a header in the default WordPress 'wrap' container -->
<div class="wrap">

    <h2>Ideaplus Options</h2>

    <?php
    $active_tab = sanitize_text_field(isset($_GET['tab']) ? $_GET['tab'] : 'display_options');
    ?>

    <h2 class="nav-tab-wrapper">
        <?php
        if (!$is_connected): ?>
            <?php
            $active_tab = ''; ?>
            <a href="?page=ideaplus" class="nav-tab nav-tab-active">Connect</a>
        <?php
        endif; ?>
        <a href="?page=ideaplus&tab=display_options" class="nav-tab <?php
        echo esc_attr($active_tab == 'display_options' ? 'nav-tab-active' : ''); ?>">Dashboard</a>
        <a href="?page=ideaplus&tab=setting_options" class="nav-tab <?php
        echo esc_attr($active_tab == 'setting_options' ? 'nav-tab-active' : ''); ?>">Settings</a>
        <a href="?page=ideaplus&tab=status_options" class="nav-tab <?php
        echo esc_attr($active_tab == 'status_options' ? 'nav-tab-active' : ''); ?>">Status</a>
    </h2>

    <form method="post" action="">
        <?php
        if (!$is_connected): ?>
            <div id='connect-content'>
                <?php
                $app_name = ideaplus_get_config('APP_NAME');
                $authLink = ideaplus_get_config('IDEAPLUS_API_HOST') . 'v1/basic/auth?key=' . urlencode($customer_key) . '&site=' . urlencode(Ideaplus_Plugin_Func::curPageURL()) . '&token=' . urlencode($ideaplus_key) . '&return_url=' . urlencode(get_admin_url(null, 'admin.php?page=' . $app_name));
                echo '<a href="' . esc_url($authLink) . '" class="button button-primary printful-connect-button ' . esc_attr((!empty($issues) ? 'disabled' : '')) . '" target="_blank">' . 'Connect' . '</a>';
                ?>
            </div>
            <img src="<?php
            echo esc_url(admin_url('images/spinner-2x.gif')) ?>" class="loader" width="20px" height="20px"
                 alt="loader"/>
        <?php
        endif; ?>
    </form>
    <!--    <button id="clear-cache-btn">删除授权缓存</button>-->

    <script type="text/javascript">
		jQuery( document ).ready( function () {
			Ideaplus_Admin.init();
		} );
    </script>
</div><!-- /.wrap -->