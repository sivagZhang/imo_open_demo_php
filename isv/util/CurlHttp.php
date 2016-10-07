<?php

class Http 
{
    public static function get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public static function post($url, $jsonStr) 
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        
        ob_start();
        $return = curl_exec($ch);
        $content = ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        return $content;
    } 
}


