<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/public/partials
 */

?>

    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$product = wc_get_product(get_the_ID());
echo esc_html($product->get_name());
?>