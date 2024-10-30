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
$product   = wc_get_product(get_the_ID());
$data      = $product->get_meta_data();
$meta_data = [];
foreach ($data as $key => $item) {
    $meta_data[$item->key] = json_decode($item->value, true);
}
$meta_data = json_encode($meta_data);

echo '<input type="text" id="meta_data" style="display:none" value="'.esc_attr( $meta_data ).'">';
?>

<script type="text/javascript">
    var meta_data = JSON.parse(document.getElementById('meta_data').value);
	jQuery( document ).ready( function () {
		Ideaplus_Plugin_Goods.init( meta_data );
		Ideaplus_Plugin_Goods.render();
	} );
</script>