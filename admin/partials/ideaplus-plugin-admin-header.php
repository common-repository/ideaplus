<div class="wrap">
<!--    <link rel="stylesheet" href="--><?php
//    echo Ideaplus_Plugin_Func::get_admin_asset_url('css/ideaplus-plugin-admin.css'); ?><!--">-->
<!--    <script type="text/javascript" src="--><?php
//    echo Ideaplus_Plugin_Func::get_admin_asset_url('js/ideaplus-plugin-admin.js'); ?><!--"></script>-->
<!--    <h2>Ideaplus Options</h2>-->
    <?php
    $active_tab   = sanitize_text_field(isset($_GET['tab']) ? $_GET['tab'] : 'display_options');
    $is_connected = Ideaplus_Plugin_Func::is_connected();
    ?>

    <h2 class="nav-tab-wrapper">
        <?php
        if (!$is_connected): ?>
            <?php
            $active_tab = 'connect'; ?>
            <a href="?page=ideaplus" class="nav-tab nav-tab-active">Connect</a>
        <?php
        endif; ?>
        <a href="?page=ideaplus&tab=dashboard" class="nav-tab <?php
        echo esc_attr($active_tab == 'dashboard' ? 'nav-tab-active' : ''); ?>">Dashboard</a>
    </h2>
