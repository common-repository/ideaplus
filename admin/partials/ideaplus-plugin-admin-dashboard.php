<?php

include 'ideaplus-plugin-admin-header.php'; ?>
    <style>
        section:nth-of-type(1) .grid-container {
            grid-template-columns: repeat(auto-fill, 240px);
        }

        section:nth-of-type(2) .grid-container {
            grid-template-columns: repeat(auto-fill, 150px);
        }

        section:nth-of-type(3) .grid-container {
            grid-template-columns: repeat(auto-fill, 597px);
            justify-content: left;
        }

        section:nth-of-type(3) {
            margin-bottom: 100px
        }

        .order-item {
            border: 1px solid #D8DCE6;
            border-radius: 4px;
            padding: 20px 12px 12px;
            text-align: center;
            color: #000000;
        }

        .order-item-text, .main-item-title {
            font-size: 16px;
            font-weight: bold;
            line-height: 1;
            margin: 0 0 20px 0;
            vertical-align: middle;
        }

        .order-item-num {
            font-size: 30px;
            font-weight: bold;
            line-height: 1;
            margin: 0 0 30px 0;
        }

        .order-item-btn {
            cursor: pointer;
        }

        .icon {
            vertical-align: middle;
            width: 40px;
        }

        .order-item:hover {
            border-color: #FFD31C;
        }

        section:nth-of-type(2) .order-item {
            cursor: pointer;
        }

        section:nth-of-type(1) .order-item:hover .order-item-btn,
        section:nth-of-type(2) .order-item:hover .order-item-text {
            color: #FFD31C;
        }

        section:nth-of-type(2) .order-item:hover #orders.icon {
            content: url(<?php
        echo Ideaplus_Plugin_Func::get_admin_asset_url(); ?>/images/u61_mouseover.png)
        }

        section:nth-of-type(2) .order-item:hover #products.icon {
            content: url(<?php
        echo Ideaplus_Plugin_Func::get_admin_asset_url(); ?>/images/u65_mouseover.png)
        }

        section:nth-of-type(2) .order-item:hover #billing.icon {
            content: url(<?php
        echo Ideaplus_Plugin_Func::get_admin_asset_url(); ?>/images/u69_mouseover.png)
        }

        section:nth-of-type(2) .order-item:hover #account.icon {
            content: url(<?php
        echo Ideaplus_Plugin_Func::get_admin_asset_url(); ?>/images/u73_mouseover.png)
        }

        section:nth-of-type(2) .order-item:hover #shipping.icon {
            content: url(<?php
        echo Ideaplus_Plugin_Func::get_admin_asset_url(); ?>/images/u77_mouseover.png)
        }

        .main-item-col {
            display: flex;
            display: -webkit-flex;
            display: -moz-flex;
            align-items: center;
            border: 1px solid #D8DCE6;
            box-shadow: 0px 2px 12px rgba(48, 49, 51, 0.1);
            border-radius: 4px;
            padding: 12px;
            justify-content: space-between;
        }

        .main-item-col > div, .main-item-col > img {
            flex: 1;
            width: 50%;
        }

        .main-item-col > img {
            margin-right: 20px;
        }
    </style>
    <?php
    $orders = Ideaplus_Plugin_Server::getInstance()->get('order/statusCount');
    ?>
    <section>
        <div class="title">
            <span>Orders</span>
        </div>
        <div class="grid-container">
            <a class="order-item" href="<?php
            echo esc_url(Ideaplus_Plugin_Func::ideaplus_redirect_jump('/views/user/orders/list?status=1&shop_id=' . Ideaplus_Plugin_Func::get_option('shop_id', ''))) ?>"
               target="_blank">
                <p class="order-item-text">Need approval</p>
                <p class="order-item-num"><?php echo esc_html(isset($orders['data'][1]['order_count']) ? $orders['data'][1]['order_count'] : 0)?></p>
                <p class="order-item-btn">View full</p>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(Ideaplus_Plugin_Func::ideaplus_redirect_jump('/views/user/orders/list?status=3&shop_id=' . Ideaplus_Plugin_Func::get_option('shop_id'))); ?>"
               target="_blank">
                <p class="order-item-text">On hold</p>
                <p class="order-item-num"><?php echo esc_html(isset($orders['data'][3]['order_count']) ? $orders['data'][3]['order_count'] : 0)?></p>
                <p class="order-item-btn">View full</p>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(Ideaplus_Plugin_Func::ideaplus_redirect_jump('/views/user/orders/list?status=20&shop_id=' . Ideaplus_Plugin_Func::get_option('shop_id', ''))); ?>"
               target="_blank">
                <p class="order-item-text">Waiting for fulfillment</p>
                <p class="order-item-num"><?php echo esc_html(isset($orders['data'][20]['order_count']) ? $orders['data'][20]['order_count'] : 0)?></p>
                <p class="order-item-btn">View full</p>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(Ideaplus_Plugin_Func::ideaplus_redirect_jump('/views/user/orders/list?status=21&shop_id=' . Ideaplus_Plugin_Func::get_option('shop_id', ''))); ?>"
               target="_blank">
                <p class="order-item-text">Being fulfilled</p>
                <p class="order-item-num"><?php echo esc_html(isset($orders['data'][21]['order_count']) ? $orders['data'][21]['order_count'] : 0)?></p>
                <p class="order-item-btn">View full</p>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(Ideaplus_Plugin_Func::ideaplus_redirect_jump('/views/user/orders/list?status=30&shop_id=' . Ideaplus_Plugin_Func::get_option('shop_id', ''))); ?>"
               target="_blank">
                <p class="order-item-text">In transit</p>
                <p class="order-item-num"><?php echo esc_html(isset($orders['data'][30]['order_count']) ? $orders['data'][30]['order_count'] : 0)?></p>
                <p class="order-item-btn">View full</p>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(Ideaplus_Plugin_Func::ideaplus_redirect_jump('/views/user/orders/list?status=31&shop_id=' . Ideaplus_Plugin_Func::get_option('shop_id', ''))); ?>"
               target="_blank">
                <p class="order-item-text">Delivered</p>
                <p class="order-item-num"><?php echo esc_html(isset($orders['data'][31]['order_count']) ? $orders['data'][31]['order_count'] : 0)?></p>
                <p class="order-item-btn">View full</p>
            </a>
        </div>
    </section>
    <section>
        <div class="title">
            <span>Quick links</span>
        </div>
        <div class="grid-container">
            <a class="order-item" href="<?php
            echo esc_url(ideaplus_get_config('IDEAPLUS_HOST') . 'views/user/Orders/list') ?>">
                <img class="icon" id="orders" src="<?php
                echo Ideaplus_Plugin_Func::get_admin_asset_url('images/u61.png'); ?>" alt="">
                <span class="order-item-text">Orders</span>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(ideaplus_get_config('IDEAPLUS_HOST') . 'views/user/Product/product-list'); ?>" target="_blank">
                <img class="icon" id="products" src="<?php
                echo Ideaplus_Plugin_Func::get_admin_asset_url('images/u65.png'); ?>" alt="">
                <span class="order-item-text">Products</span>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(ideaplus_get_config('IDEAPLUS_HOST') . 'views/user/Billing/list') ?>" target="_blank">
                <img class="icon" id="billing" src="<?php
                echo Ideaplus_Plugin_Func::get_admin_asset_url('images/u69.png'); ?>" alt="">
                <span class="order-item-text">Billing</span>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(ideaplus_get_config('IDEAPLUS_HOST') . 'views/user/Setting/my-account') ?>" target="_blank">
                <img class="icon" id="account" src="<?php
                echo Ideaplus_Plugin_Func::get_admin_asset_url('images/u73.png'); ?>" alt="">
                <span class="order-item-text">Account</span>
            </a>
            <a class="order-item" href="<?php
            echo esc_url(ideaplus_get_config('IDEAPLUS_HOST') . 'views/user/Setting/shippingProfiles') ?>" target="_blank">
                <img class="icon" id="shipping" src="<?php
                echo Ideaplus_Plugin_Func::get_admin_asset_url('images/u77.png'); ?>" alt="">
                <span class="order-item-text">Shipping</span>
            </a>
        </div>
    </section>
    <section>
        <div class="title">
            <span>Get the help you need</span>
        </div>
        <div class="grid-container">
            <a class="main-item-col" href="<?php
            echo esc_url(ideaplus_get_config('IDEAPLUS_HOST') . 'views/user/Help/contactUs') ?>" target="_blank">
                <img src="<?php
                echo Ideaplus_Plugin_Func::get_admin_asset_url('images/u83.png'); ?>">
                <div class="main-item-text">
                    <p class="main-item-title">Send Email</p>
                    <span>Go to Ideaplus to fill out the form and submit the question.</span>
                </div>
            </a>
            <a class="main-item-col" href="<?php
            echo esc_url(ideaplus_get_config('IDEAPLUS_HOST') . 'views/user/Help/category') ?>" target="_blank">
                <img src="<?php
                echo Ideaplus_Plugin_Func::get_admin_asset_url('images/u88.png'); ?>">
                <div class="main-item-text">
                    <p class="main-item-title">How to use Ideaplus?</p>
                    <span>View help document.</span>
                </div>
            </a>
        </div>
    </section>
<?php
include 'ideaplus-plugin-admin-footer.php'; ?>