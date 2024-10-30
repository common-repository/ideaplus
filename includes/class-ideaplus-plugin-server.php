<?php
/**
 * The file that defines the helper functions
 *
 * A class definition that regiest api route for interaction Ideaplus Server
 *
 * @link       http://www.ideaplus.com/
 * @since      1.0.0
 *
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to request ideaplus server
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @author     brazz <767502630@qq.com>
 * @package    Ideaplus_Plugin
 * @subpackage Ideaplus_Plugin/includes
 * @since      1.0.0
 */
if(!defined('ABSPATH')){ exit; }
class Ideaplus_Plugin_Server
{
    protected static $instance     = null;
    protected        $client;
    protected        $requestToken = '';
    private          $userAgent    = 'Ideaplus WooCommerce Client Plugin';
    private          $requestHost  = '';
    private          $version      = '';

    const REQUEST_SUCCESS = '200';

    private $lastResponseBody = null;
    private $lastResponseData = null;

    public function __construct()
    {
        $this->requestHost  = ideaplus_get_config('IDEAPLUS_API_HOST');
        $this->version      = ideaplus_get_config('IDEAPLUS_API_VERSION');
        $this->requestToken = Ideaplus_Plugin_Func::get_option('token', '');
    }

    /**
     * @description get class instance
     * @author      ylw <767502630@qq.com>
     * @return Ideaplus_Plugin_Server|null
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Ideaplus_Plugin_Server();
        }
        return self::$instance;
    }

    /**
     * @description make ideaplus get api request
     * @author      ylw <767502630@qq.com>
     *
     * @param string $endPoint
     * @param array  $data
     * @param array  $params
     * @param string $version
     *
     * @return array
     * @throws Exception
     */
    public function get($endPoint = '', $data = [], $params = [], $version = '')
    {
        return $this->request('GET', $endPoint, $data, $params, $version);
    }

    /**
     * @description make ideaplus post api request
     * @author      ylw <767502630@qq.com>
     *
     * @param string $endPoint
     * @param array  $data
     * @param array  $params
     * @param string $version
     *
     * @return array
     * @throws Exception
     */
    public function post($endPoint = '', $data = [], $params = [], $version = '')
    {
        return $this->request('POST', $endPoint, $data, $params, $version);
    }

    /**
     * @description clear last response data
     * @author      ylw <767502630@qq.com>
     */
    private function clearResponse()
    {
        $this->lastResponseBody = $this->lastResponseData = null;
    }

    /**
     * @description make request url
     * @author      ylw <767502630@qq.com>
     *
     * @param string $endPoint
     * @param string $params
     * @param string $version
     *
     * @return string
     */
    public function makeUrl($endPoint = '', $params = '', $version = '')
    {
        $url = $this->requestHost . (empty($version) ? $this->version : $version) . '/' . trim($endPoint, '/');
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * @description make ideaplus api request
     * @author      ylw <767502630@qq.com>
     *
     * @param string $method
     * @param string $endPoint
     * @param array  $data
     * @param array  $params
     * @param string $version
     *
     * @return array
     * @throws Exception
     */
    public function request($method = '', $endPoint = '', $data = [], $params = [], $version = '')
    {
        $this->clearResponse();
        $url     = $this->makeUrl($endPoint, $params, $version);
        $request = [
            'timeout'    => 5,
            'user-agent' => $this->userAgent,
            'method'     => $method,
            'headers'    => [
                'Token'        => $this->requestToken,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'body'       => ($data !== null && $data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : null,
        ];
        $result  = wp_remote_get($url, $request);
        if (is_wp_error($result)) {
            throw new Exception("API request error - " . $result->get_error_message());
        }
        $this->lastResponseBody = $result['body'];
        $this->lastResponseData = $response = json_decode($result['body'], true);
        return $response;
    }

    /**
     * @description verify request was successful
     * @author      ylw <767502630@qq.com>
     * @return bool
     */
    public function isSuccess()
    {
        if (!isset($this->lastResponseData['code'])) {
            return false;
        }
        if ($this->lastResponseData['code'] == self::REQUEST_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * @description get last response body
     */
    public function getData()
    {
        return $this->lastResponseData['data'];
    }

}