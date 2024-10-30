<?php

    if(!defined('ABSPATH')){ exit; }
    class Ideaplus_Plugin_Shipping extends WC_Shipping_Method
    {
        public $show_warnings = false;
        public $override_defaults = true;
        private $last_error = false;

        const IDEAPLUS_SHIPPING = 'ideaplus_shipping';

        //Store whether currently processed package contains Ideaplus products (for WC<2.6)
        private $ideaplus_package = true;

        public static function init() {
            new self;
        }

        public function __construct() {
            $this->id                 = 'ideaplus_shipping';
            $this->method_title       = $this->title = 'Ideaplus Shipping';
            $this->method_description = 'Calculate live shipping rates based on actual Ideaplus shipping costs.';

            $this->enabled           = 'yes';
            $this->show_warnings     = true;
            $this->override_defaults = true;

            //Initialize shipping methods for specific package (or no package)
            add_filter( 'woocommerce_load_shipping_methods', array( $this, 'woocommerce_load_shipping_methods' ), 10000 );

            //Remove other shipping methods for Ideaplus package on WC < 2.6
            add_filter( 'woocommerce_shipping_methods', array( $this, 'woocommerce_shipping_methods' ), 10000 );

            add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'woocommerce_cart_shipping_packages' ), 10000 );

            // If there is not have shipping methods at the default shipping zone, it will be created.
            $zone_object = new WC_Shipping_Zone(0);
            if (count($zone_object->get_shipping_methods(true)) == 0) {
                $zone_object->add_shipping_method("flat_rate");
            }

        }

        /**
         * Enable only Ideaplus shipping method for Ideaplus packages
         * @param array $package
         */
        public function woocommerce_load_shipping_methods( $package = array() ) {

            $this->ideaplus_package = false;

            if ( $package && ! empty( $package['ideaplus'] ) ) {
                if ( $this->enabled == 'yes' ) {
                    $this->ideaplus_package = true;

                    WC()->shipping()->unregister_shipping_methods();
                    WC()->shipping()->register_shipping_method( $this );
                    $this->calculate_shipping($package);
                }
            }

        }

        /**
         * Remove non-Ideaplus methods for Ideaplus packages on WC < 2.6
         * @param $methods
         *
         * @return array
         */
        public function woocommerce_shipping_methods( $methods ) {

            if ( $this->override_defaults && $this->ideaplus_package && version_compare( WC()->version, '2.6', '<' ) ) {
                //For WC < 2.6 woocommerce_shipping_methods is executed after woocommerce_load_shipping_methods
                //So we need to clean up unnecessary methods from there
                return array();
            }

            return $methods;
        }

        /**
         * Split Ideaplus products to a separate package if there are any
         * @param array $packages
         *
         * @return array
         */
        public function woocommerce_cart_shipping_packages( $packages = array() ) {

            //Ideaplus rates are turned off, do not split products
            if ( $this->enabled !== 'yes' ) {
                return $packages;
            }

            $return_packages = array();

            foreach ( $packages as $package ) {
                $ids = array();
                foreach ( $package['contents'] as $key => $item ) {
                    $ids[ $key ] = $item['variation_id'] ? $item['variation_id'] : $item['product_id'];
                }

                $new_contents = array(
                    'ideaplus'    => array(),
                    'normal'     => array(),
                );

                foreach ( $ids as $key => $external_id ) {
                    $item = $package['contents'][ $key ];
                    if (Ideaplus_Plugin_Func::check_product_valid($item['product_id']))
                    {
                        $new_contents['ideaplus'][ $key ] = $item;
                    } else {
                        $new_contents['normal'][ $key ] = $item;
                    }
                }

                foreach ( $new_contents as $key => $contents ) {
                    if ( $contents ) {
                        $new_package                  = $package;
                        $new_package['contents_cost'] = 0;
                        $new_package['contents']      = $contents;
                        foreach ( $contents as $item ) {
                            if ( $item['data']->needs_shipping() ) {
                                if ( isset( $item['line_total'] ) ) {
                                    $new_package['contents_cost'] += $item['line_total'];
                                }
                            }
                        }
                        if ( $key == 'ideaplus' ) {
                            $new_package['ideaplus'] = true;
                        }
                        $return_packages[] = $new_package;
                    }
                }
            }

            return $return_packages;
        }

        /**
         * @param array $package
         *
         * @return bool
         */
        public function calculate_shipping( $package = array() ) {
            $request = array(
                'recipient' => array(
                    'address1'     => $package['destination']['address'],
                    'address2'     => $package['destination']['address_2'],
                    'city'         => $package['destination']['city'],
                    'state_code'   => $package['destination']['state'],
                    'country_code' => $package['destination']['country'],
                    'zip'          => $package['destination']['postcode'],
                ),
                'items'     => array(),
                'currency'  => get_woocommerce_currency(),
                'locale'    => get_locale()
            );

            foreach ( $package['contents'] as $item ) {
                if ( ! empty( $item['data'] ) && ( $item['data']->is_virtual() || $item['data']->is_downloadable() ) ) {
                    continue;
                }
                $request['items'] [] = array(
                    'external_variant_id' => $item['variation_id'] ? $item['variation_id'] : $item['product_id'],
                    'quantity'            => $item['quantity'],
                    'value'               => $item['line_total'] / $item['quantity'],
                );
            }

            if ( ! $request['items'] ) {
                return false;
            }

            try {

                $client = Ideaplus_Plugin_Server::getInstance();

                $response = $client->get('Shipping/shippingListByCountryCode', [], ['country_code'=>$package['destination']['country'], 'currency_code'=>get_woocommerce_currency()]);

                if (isset($response['data']) & is_array($response['data'])) {
                    foreach ($response['data'] as $rate) {
						
						if($item['line_total'] < $rate['base_price'] ) {
                            continue;
                        }
						
                        $rateData = [
                            'id' => $this->id . '_' . $rate['id'],
                            'label' => $rate['name'],
                            'cost' => $rate['rate'],
                            'calc_tax' => 'per_order',
                        ];

                        // Before 3.4.0 rate could be passed as ID, after it's set as method_id which refers to class ID
                        if (version_compare(WC()->version, '3.4.0', '>=')) {
                            $this->id = self::IDEAPLUS_SHIPPING . '_' . $rate['id'];
                        }
                        $this->add_rate($rateData);
                        // Reset class ID after adding rate so ID name does not stack as huge string in foreach
                        $this->id = self::IDEAPLUS_SHIPPING;
                    }
                }
            } catch ( Exception $e ) {
                $this->set_error( $e );
                return false;
            }

            return false;
        }

        /**
         * @param $error
         */
        private function set_error( $error ) {
            if ( $this->show_warnings ) {
                $this->last_error = $error;
                add_filter( 'woocommerce_cart_no_shipping_available_html', array( $this, 'show_error' ) );
                add_filter( 'woocommerce_no_shipping_available_html', array( $this, 'show_error' ) );
            }
        }

        /**
         * @param $data
         *
         * @return string
         */
        public function show_error( $data ) {
            $error   = $this->last_error;
            $message = $error->getMessage();

            return '<p>ERROR: ' . htmlspecialchars( $message ) . '</p>';
        }

        private function isBillingPhoneNumberRequired()
        {
            return get_option('woocommerce_checkout_phone_field', 'required') === 'required';
        }
    }