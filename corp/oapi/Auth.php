<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../util/Cache.php");
require_once(__DIR__ . "/../util/CurlHttp.php");
require_once(__DIR__ . "/../util/Log.php");

const KEY_ACCESS_TOKEN = "corp_access_token";
const KEY_JSAPI_TICKET = "jsapi_ticket";

class Auth
{
    public static function getAccessToken()
    {
        $accessToken = Cache::get(KEY_ACCESS_TOKEN);
        if (!$accessToken)
        {
            $url = OAPI_HOST . "/gettoken?corpid=" . CORP_ID . "&corpsecret=" . CORP_SECRET;

            $retStr = Http::get($url);
            $retObj = json_decode($retStr, true);
            self::check($retObj);

            $accessToken = $retObj['access_token'];
            Cache::set(KEY_ACCESS_TOKEN, $accessToken, 7000);
        }
        return $accessToken;
    }
    
    public static function getJsTicket()
    {
        $jsTicket = Cache::get(KEY_JSAPI_TICKET);
        if (!$jsTicket)
        {
            $access_token = self::getAccessToken();
            $url = OAPI_HOST. "/get_jsapi_ticket?access_token=" . $access_token . "&type=jsapi";

            $retStr = Http::get($url);
            $retObj = json_decode($retStr, true);
            self::check($retObj);

            $jsTicket = $retObj['ticket'];
            Cache::set(KEY_JSAPI_TICKET, $jsTicket, 7000);
        }
        return $jsTicket;
    }

    static function check($res)
    {
        if ($res['errcode'] != 0)
        {
            $trace = debug_backtrace();
            $callerFunc = $trace[1]['function'];

            Log::e("{$callerFunc} ERROR: " . json_encode($res));
            exit("Failed. " . json_encode($res));
        }
    }

}
