<?php

error_reporting(0);
header("Content-Type: text/html; charset=utf-8");
session_save_path(__DIR__ . '/../data/');
session_start();

define('COMPANY_NO', '00001');
define('PAYMENT_API_DOMAIN', 'http://www.aoya.local'); // http://jsmw672.com
define('WECHATALIPAYQRCODEURL', 'http://www.aoya.local'); // https://51pphzp.com
function isJSON($str)
{
    json_decode($str);
    return (json_last_error() == JSON_ERROR_NONE);
}

function sendHttpRequest($url, $params, $method = 'post')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . "?_t=" . date('YmdHis'));
    curl_setopt($ch, CURLOPT_USERAGENT, 'WMNet Payment API');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    if ($method == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    }
    $output = curl_exec($ch);
    curl_close($ch);
    if ($output === false) {
        return false;
        // return curl_error($ch);
    }
    return $output;
}

function formatHtml($str)
{
    return preg_replace("~>\\s+<~", "><", preg_replace("~>\\s+~", ">", preg_replace("~\\s+<~", "<", $str)));
}

function getIp()
{
    $ip = false;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = false;
        }
        for ($i = 0; $i < count($ips); $i++) {
            if (!preg_match("/^(10│172.16│192.168)./i", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}
