<?php

if(!defined('ABSPATH')){ exit; }
class Ideaplus_Plugin_Order
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $ideaplus_plugin The ID of this plugin.
     */
    private $ideaplus_plugin;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * plugin loader
     */
    private $loader;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $ideaplus_plugin The name of this plugin.
     * @param string $version         The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct($ideaplus_plugin, $version, $loader)
    {
        $this->ideaplus_plugin = $ideaplus_plugin;
        $this->version         = $version;
        $this->loader          = $loader;
    }

    public function load()
    {
        $this->loader->add_action( 'add_meta_boxes', $this, 'add_meta_box' );	
    }

    public function add_meta_box()
    {
        $instance = new Ideaplus_Plugin_Order($this->ideaplus_plugin, $this->version, null);
        // add meta box for order
        add_meta_box( 'woocommerce-advanced-shipment-tracking', __( 'Shipment Tracking', 'woo-advanced-shipment-tracking' ), [$instance, 'meta_box'], 'shop_order', 'side', 'high' );
    }

    public function meta_box()
    {
        global $post;
        $tracking_items = $this->get_order_track_info($post->ID);
        if ($tracking_items) {
            echo '<div class="tracking-info">';
            echo '<p>Ideaplus Tracking info</p>';
            echo '<p>'.esc_html($tracking_items['company_code'] . ' ' . $tracking_items['company'] . '-' . $tracking_items['url']) .'</p>';
            echo '<a class="button button-primary btn_ast2 button-save-form" href="' . esc_url($tracking_items['url']) . '">Tracking detail</a>';
            echo '</div>';
        }
        
    }

    /**
     * @description get order track info
     */
    private function get_order_track_info($order_id)
    {
        if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
            $tracking_items = get_post_meta( $order_id, '_ideaplus_track_number', true );
        } else {
            $order          = new WC_Order( $order_id );
            $tracking_items = $order->get_meta( '_ideaplus_track_number', true );			
        }
        
        if ( is_array( $tracking_items ) ) {
            return $tracking_items;
        } else {
            return array();
        }
    }
}