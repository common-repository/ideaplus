<?php

/**
 * Created by ylw
 * Email: 767502630@qq.com
 * Date: 2022/2/16
 * Time: 11:32:03
 */
if(!defined('ABSPATH')){ exit; }
class Ideaplus_Plugin_Cart extends Ideaplus_Plugin_Public
{
    protected $class_name = 'cart';

    public function __construct($loader, $ideaplus_plugin, $version)
    {
        parent::__construct($loader, $ideaplus_plugin, $version);
        //
        // $this->loader->add_filter('woocommerce_add_to_cart_validation', $this, '', 999, 2);
        $this->loader->add_filter('woocommerce_add_cart_item_data', $this, 'render_add_card_data', 999, 3);
        $this->loader->add_filter('woocommerce_get_item_data', $this, 'render_data', 999, 2);
        $this->loader->add_filter('woocommerce_add_to_cart_validation', $this, 'fields_check', 99, 2);
        $this->loader->add_action('woocommerce_new_order_item', $this, 'render_order_meta', 99, 3);
        $this->loader->add_action('woocommerce_new_order', $this, 'render_add_order_item', 99, 1);
    }

    /**
     * Add a custom field
     * @param $item_data
     * @param $cart_item_key
     * @param $cart_item
     * @return mixed
     */
    public function render_add_order_item($order_id)
    {
        $order = wc_get_order( $order_id );
        $order->add_meta_data('token',Ideaplus_Plugin_Func::get_option('token', ''), true);
        $order->save_meta_data();
    }

    /**
     * @description Render ideaplus card data
     * @author      ylw <767502630@qq.com>
     *
     * @param $_cart_item_data
     * @param $_product_id
     * @param $_variation_id
     *
     * @return mixed
     */
    public function render_add_card_data($_cart_item_data, $_product_id, $_variation_id)
    {
        $check = Ideaplus_Plugin_Func::check_product_valid($_product_id);
        if (!$check) {
            return $_cart_item_data;
        }
        $ideaplus_data = sanitize_text_field(isset($_REQUEST['ideaplus_data']) ? $_REQUEST['ideaplus_data'] : '');
        if (!$ideaplus_data) {
            return $_cart_item_data;
        }
        $ideaplus_data = stripslashes($ideaplus_data);
        $ideaplus_data = json_decode($ideaplus_data, true);
        $variations    = isset($ideaplus_data['variations']) ? $ideaplus_data['variations'] : [];
        $imageUploads = [];
        foreach ($variations as $varItem) {
            $key = 'ideaplus_' . $varItem['id'];
            // skip variations arttribute
            $variationName = 'attribute_' . str_replace(' ', '-', strtolower($varItem['name']));
            if ($varItem['type'] == 3) {
                $imageUploads[] = 'ideaplus_'.str_replace(' ', '', $varItem['name']).'_' . $varItem['id'];
            }
            if (isset($_REQUEST[$variationName])) {
                continue;
            }
            $_cart_item_data[$key] = $varItem;
        }

        foreach ($imageUploads as $uploadKeys) {
            if (!isset($_FILES[$uploadKeys])) {
                continue;
            }
            preg_match('/ideaplus_?(.*)_([0-9]+)$/i', $uploadKeys, $keyMatch);
            @list($match, $name, $id) = $keyMatch;
            $key = 'ideaplus_' . $id;
            $file_item = $_FILES[$uploadKeys];
            $upload_process_res = $this->process_file_upload($file_item);
            if (!isset($upload_process_res['error'])) {
                $_cart_item_data[$key]['custom_data'] = empty($_cart_item_data[$key]['value']) ? [] : json_decode($_cart_item_data[$key]['value'], true);
                $_cart_item_data[$key]['value'] = $upload_process_res['url'];
                do_action('wccpf_file_uploaded', $upload_process_res);
            } else {
                wc_add_wp_error_notices('faild upload files', 'error');
            }
        }
        return $_cart_item_data;
    }

    /**
     * @description Render card variations data with Ideaplus, Handler for 'woocommerce_get_item_data' filter
     * @author      ylw <767502630@qq.com>
     *
     * @param      $_cart_data
     * @param null $_cart_item
     */
    public function render_data($_cart_data, $_cart_item = null)
    {
        $product_id = $_cart_item['product_id'];
        $check      = Ideaplus_Plugin_Func::check_product_valid($product_id);
        if (!$check) {
            return $_cart_data;
        }
        foreach ($_cart_item as $key => $val) {
            if (strpos($key, 'ideaplus') === false) {
                continue;
            }
            if ($val['type'] == 3) {
                $_cart_data[] = [
                    'name'  => $val['name'],
                    'value' => '<img src="' . $val['value'] . '" width="120px" heigth="120px"/>',
                ];
            } else {
                $_cart_data[] = ['name' => $val['name'], 'value' => $val['value']];
            }
        }
        return $_cart_data;
    }

    public function render_order_meta($_item_id, $_values, $_cart_item_key)
    {
        if (empty($_values->legacy_values['product_id']) || !Ideaplus_Plugin_Func::check_product_valid($_values->legacy_values['product_id'])) {
            return;
        }
        $_ideaplus_meta_data = [];
        foreach ($_values->legacy_values as $key => $item) {
            // search ideaplus key
            if (strpos($key, 'ideaplus') === 0) {
                if ($item['type'] == 3) {
                    wc_add_order_item_meta($_item_id, $item['name'], '<img src="' . $item['value'] . '" width="120px" heigth="120px"/>');
                } else {
                    wc_add_order_item_meta($_item_id, $item['name'], $item['value']);
                }
                $_ideaplus_meta_data[$item['id']] = $item;
            }
        }
        wc_add_order_item_meta($_item_id, '_ideaplus_meta_data', $_ideaplus_meta_data);
    }

    /**
     * @description Verify card item
     * @author      ylw <767502630@qq.com>
     *
     * @param      $_passed
     * @param null $_product_id
     *
     * @return bool
     */
    public function fields_check($_passed, $_product_id = null)
    {
        $check = Ideaplus_Plugin_Func::check_product_valid($_product_id);
        if (!$check) {
            return true;
        }
        // Checking product is single sku
        if (Ideaplus_Plugin_Func::check_single_sku_valid($_product_id) && !isset($_REQUEST['ideaplus_data'])) {
            return true;
        }
        return isset($_REQUEST['ideaplus_data']);
    }

    /**
     * @description handle file upload process
     * @author      ylw <767502630@qq.com>
     *
     * @param $_file
     *
     * @return array|string[]
     */
    private function process_file_upload($_file)
    {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $movefile = wp_handle_upload($_file, [
            'test_form' => false,
        ]);
        return $movefile;
    }
}