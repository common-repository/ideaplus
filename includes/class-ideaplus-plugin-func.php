<?php
/**
 * The file that defines the helper functions
 *
 * A class definition that regiest api route for interaction Ideaplus Server
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define helper functions
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @author     brazz <767502630@qq.com>
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/includes
 */
if(!defined('ABSPATH')){ exit; }
class Ideaplus_Plugin_Func
{
    const APP_SETTING_KEY = 'woocommerce-ideaplus-option';

    const SETTING_KEY_WC_KEY = 'wc_api_key';

    const SYNCED_GOODS_IDS_KEY = 'woocommerce-ideaplus-synced_goods_ids';

    const UPDATE_OPTION_PAGE = "options.php";

    /**
     * @description get all option data
     * @author      ylw <767502630@qq.com>
     * @return mixed
     */
    public static function get_all_options($namespace = self::APP_SETTING_KEY)
    {
        return get_option($namespace, []);
    }

    /**
     * @description get option data bu key
     * @author      ylw <767502630@qq.com>
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function get_option($key = '', $default = null, $namespace = self::APP_SETTING_KEY)
    {
        $options = self::get_all_options($namespace);
        $option  = $default;
        if (isset($options[$key])) {
            $option = $options[$key];
        }
        return $option;
    }

    /**
     * @description update option data by key
     * @author      ylw <767502630@qq.com>
     *
     * @param string $key
     * @param string $val
     */
    public static function update_option($key, $val = '', $namespace = self::APP_SETTING_KEY)
    {
        $options       = self::get_all_options($namespace);
        $options[$key] = $val;
        self::update_options($options, $namespace);
    }

    /**
     * @description update option datas
     * @author      ylw <767502630@qq.com>
     *
     * @param $option_map
     */
    public static function update_options($option_map, $namespace = self::APP_SETTING_KEY)
    {
        $options = self::get_all_options($namespace);
        $options = $option_map + $options;
        update_option($namespace, $options);
    }

    /**
     * @description get last consumer key
     * @author      ylw <767502630@qq.com>
     * @return string|null
     */
    public static function get_customer_key()
    {
        global $wpdb;
        // Get the API key
        $app_name     = ideaplus_get_config('APP_NAME');
        $consumer_key = $wpdb->get_var($wpdb->prepare("SELECT truncated_key FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE %s ORDER BY key_id DESC LIMIT 1", '%' . esc_sql($wpdb->esc_like(wc_clean($app_name))) . '%'));
        //if not found by description, it was probably manually created. try the last used key instead
        if (!$consumer_key) {
            $consumer_key = $wpdb->get_var("SELECT truncated_key FROM {$wpdb->prefix}woocommerce_api_keys ORDER BY key_id DESC LIMIT 1");
        }
        return $consumer_key;
    }

    public static function curPageURL()
    {
        $pageURL = 'http';
        $serverHttp = sanitize_text_field(isset($_SERVER["HTTPS"]) ? $_SERVER["HTTPS"] : 'off');
        if (isset($serverHttp) && $serverHttp == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        $serverPort = sanitize_text_field($_SERVER["SERVER_PORT"]);
        $serverName = sanitize_text_field($_SERVER["SERVER_NAME"]);
        $serverUri = sanitize_text_field($_SERVER["REQUEST_URI"]);
        if ($serverPort != "80") {
            $pageURL .= $serverName . ":" . $serverPort . $serverUri;
        } else {
            $pageURL .= $serverName . $serverUri;
        }
        return $pageURL;
    }

    /**
     * @description verify ideaplus plugin connection
     * @author      ylw <767502630@qq.com>
     *
     * @param false $is_force
     *
     * @return bool
     * @throws Exception
     */
    public static function is_connected($is_force = false)
    {
        $ideaplus_key = Ideaplus_Plugin_Func::get_option('token', '');
        $customer_key = Ideaplus_Plugin_Func::get_customer_key();
        if (empty($ideaplus_key) || empty($customer_key)) {
            return false;
        }
        
        $wc_api_key = Ideaplus_Plugin_Func::get_option(Ideaplus_Plugin_Func::SETTING_KEY_WC_KEY);
        if ($customer_key != $wc_api_key) {
            return false;
        }
        if ($is_force) {
            // check ideaplus key and woocommerce key validate
            $server = Ideaplus_Plugin_Server::getInstance();
            $server->get('shop/check');
            $connected_status = $server->isSuccess();
            return $connected_status;
        }

        return true;
    }

    /**
     * @description get asset url
     * @author      ylw <767502630@qq.com>
     * @return string
     */
    public static function get_asset_url($namespace = '')
    {
        return trailingslashit(plugin_dir_url(__FILE__)) . '../' . $namespace . '/';
    }

    /**
     * @description Get admin asset path
     * @author      ylw <767502630@qq.com>
     * @return string
     */
    public static function get_admin_asset_url($image_path = '')
    {
        return esc_url(self::get_asset_url('admin') . $image_path);
    }

    /**
     * @description Get public asset path
     * @author      ylw <767502630@qq.com>
     * @return string
     */
    public static function get_public_asset_url()
    {
        return self::get_asset_url('public');
    }

    /**
     * @description Check whether the product is from ideaplus
     * @author      ylw <767502630@qq.com>
     *
     * @param $product_id
     *
     * @return bool
     */
    public static function check_product_valid($product_id)
    {
        $synced_goods_ids = self::get_option('goods_ids', [], Ideaplus_Plugin_Func::SYNCED_GOODS_IDS_KEY);
        // This product does not belong to ideaplus platform
        if (!in_array($product_id, $synced_goods_ids)) {
            return false;
        }
        return true;
    }

    /**
     * @description Check whether the product is a single SKU
     * @author      ylw <767502630@qq.com>
     *
     * @param $product_id
     *
     * @return bool
     */
    public static function check_single_sku_valid($product_id)
    {
        $product   = wc_get_product($product_id);
        $data      = $product->get_meta_data();
        $array_values = array_values( wp_list_pluck( $data, 'value' ) );

        return (isset($array_values[0]) && $array_values[0] == 'false') ? true : false;
    }


    /**
     * @description get current page params
     * @author      ylw <767502630@qq.com>
     * @return false|mixed|string
     */
    public static function getCurrentPage()
    {
        $page = explode('/',$_SERVER['REQUEST_URI']);

        $page = end($page);

        return $page ? $page : '';
    }

    public static function get_ideaplus_token()
    {
        return self::get_option('token', '');
    }

    public static function ideaplus_redirect_jump($path)
    {
        $token = self::get_ideaplus_token();
        if (empty($token)) {
            //TODO::这里不确定跳到哪里
            return '';
        }
        $host = ideaplus_get_config('IDEAPLUS_API_HOST') . ideaplus_get_config('IDEAPLUS_API_VERSION') . '/auth/jump';
        $url = $host . '?token=' . $token . '&redirect_url=' . urlencode($path);
        return $url;
    }

    public static function get_client_headers()
    {
        $scheme = (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])&& $_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : (isset($_SERVER['REQUEST_SCHEME']) ?$_SERVER['REQUEST_SCHEME'] :  '');

        if (self::get_option('ideaplus_client_scheme') != $scheme && $scheme) {
            $server = Ideaplus_Plugin_Server::getInstance();
            $data = $server->get('shop/changeScheme', [], ['scheme'=>$scheme]);

            if ($data['code'] == 200) {

                $options['ideaplus_client_scheme'] = $scheme;

                Ideaplus_Plugin_Func::update_options($options);
            }
        }
    }
}