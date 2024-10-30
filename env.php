<?php

/**
 * Created by ylw
 * Email: 767502630@qq.com
 * Date: 2022/1/20
 * Time: 16:07:51
 */
function ideaplus_get_config($key = '')
{
    $config = [
        'APP_NAME'             => 'ideaplus',
        'APP_VERSION'          => '1.0.0',
        'IDEAPLUS_API_HOST'    => 'https://www.ideaplus.com/wcapi/',
        'IDEAPLUS_API_VERSION' => 'v1',
        'IDEAPLUS_HOST'        => 'https://www.ideaplus.com/',
        'AUTH_RETURN_URL'      => 'https://www.ideaplus.com/wcapi/',
        'AUTH_REDIRECT_URL'    => 'https://www.ideaplus.com/wcapi/',
    ];
    return isset($config[$key]) ? $config[$key] : '';
}

