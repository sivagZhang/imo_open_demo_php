<?php

class Message
{
    public static function sendAppMsg($access_token, $data)
    {
        $url = OAPI_HOST . '/message/appsend?access_token=' . $access_token;

        $retStr = Http::post($url, json_encode($data));
        return $retStr;
    }
    
}