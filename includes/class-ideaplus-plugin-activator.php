<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @author     brazz <767502630@qq.com>
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/includes
 */
if(!defined('ABSPATH')){ exit; }
class Ideaplus_Plugin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$server = Ideaplus_Plugin_Server::getInstance();
		$server->get('/webhook/shop/activate');
		if (!$server->isSuccess()) {
			return;
		}
		$data = $server->getData();
        $request_goods_ids = isset($data['synced_goods_ids']) ? $data['synced_goods_ids'] : [];
		if (empty($request_goods_ids)) {
			return;
		}
        $exists_goods_ids  = Ideaplus_Plugin_Func::get_option('goods_ids', [], Ideaplus_Plugin_Func::SYNCED_GOODS_IDS_KEY);
        $all_goods_ids     = array_merge($request_goods_ids, $exists_goods_ids);
        Ideaplus_Plugin_Func::update_option('goods_ids', $all_goods_ids, Ideaplus_Plugin_Func::SYNCED_GOODS_IDS_KEY);

		// update last sync wc key
		Ideaplus_Plugin_Func::update_option(Ideaplus_Plugin_Func::SETTING_KEY_WC_KEY, $data['last_sync_wc_key']);
		//add_dashboard_page('Ideaplus', 'Ideaplus Setting', 'administrator','display_copyright', 'display_copyright_html_page');
	}

}
