<?php

require_once(__DIR__ . "/oapi/Auth.php");
require_once(__DIR__ . "/util/Log.php");

class JSSDK
{
    public static function getConfig()
    {
        $ticket = Auth::getJsTicket();
        $nonceStr = self::getNonceStr();
        $timeStamp = time();
        $url = self::curPageURL();
        $signature = self::sign($ticket, $nonceStr, $timeStamp, $url);
        
        $config = array(
            'url' => $url,
            'nonceStr' => $nonceStr,
            'agentId' => AGENT_ID,
            'timeStamp' => $timeStamp,
            'corpId' => CORP_ID,
            'signature' => $signature);
        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }
    
    // URL 必须要动态获取
    static function curPageURL()
    {    
        $protocol =  (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        return $url;
    }

    static function getNonceStr($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        $maxIdx = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) 
          $str .= $chars[mt_rand(0, $maxIdx)];
        
        return $str;
    }

    static function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }
    
}
