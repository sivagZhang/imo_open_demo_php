<?php

require_once(__DIR__ . "/../util/Log.php");
require_once(__DIR__ . "/../util/CurlHttp.php");
require_once(__DIR__ . "/../util/Cache.php");

const KEY_AUTH_CORPS = "IMO_AUTH_CORPS";
const KEY_SUITE_TOKEN = "suite_access_token";

/**
 * ISV token方法类
 */
class Service
{
    public static function get_jsapi_ticket($accessToken, $corpId)
    {
        $key = "js_ticket_" . $corpId;
        $jsTicket = Cache::get($key);

        if (!$jsTicket)
        {
            $url = OAPI_HOST . "/get_jsapi_ticket?access_token=" . $accessToken . "&type=jsapi";

            $retStr = Http::get($url);
            $retObj = json_decode($retStr, true);
            self::check($retObj);

            $jsTicket = $retObj['ticket'];
            Cache::set($key, $jsTicket, 7000);
        }
       
       return $jsTicket;
    }

    public static function get_suite_token()
    {
        $suiteToken = Cache::get(KEY_SUITE_TOKEN);
        if (!$suiteToken)
        {
            $url = OAPI_HOST . '/service/get_suite_token';
            $param = array(
                    "suite_key" => SUITE_KEY,
                    "suite_secret" => SUITE_SECRET,
                    );

            $retStr = Http::post($url, json_encode($param));
            $retObj = json_decode($retStr, true);
            self::check($retObj);

            $suiteToken = $retObj['suite_access_token'];
            Cache::set(KEY_SUITE_TOKEN, $suiteToken, 7000);
        }
        return $suiteToken;
    }

    public static function get_corp_token($suiteToken, $corpId, $permanentCode)
    {
        $key = "corp_access_token_" . $corpId;
        $corpAccessToken  = Cache::get($key);
        if (!$corpAccessToken)
        {
            $url = OAPI_HOST . "/service/get_corp_token?suite_access_token=" . $suiteToken;
            $param = array(
                    "auth_corpid" => $corpId,
                    "permanent_code" => $permanentCode
                    );
        
            $retStr = Http::post($url, json_encode($param));
            $retObj = json_decode($retStr, true);
            self::check($retObj);  
            
            $corpAccessToken = $retObj['access_token'];
            Cache::set($key, $corpAccessToken, 7000);
        }
        return $corpAccessToken;
    }

    public static function get_permanent_code($suiteToken, $tmpAuthCode)
    {
        $url = OAPI_HOST. "/service/get_permanent_code?suite_access_token=" . $suiteToken;
        $param = array(
                "tmp_auth_code" => $tmpAuthCode,
                );

        $retStr = Http::post($url, json_encode($param));
        $retObj = json_decode($retStr, true);

        self::check($retObj);
        Log::i("[get_permanent_code] result:" . json_encode($retObj));

        $permanentCodeInfo = array(
            "corp_id" => $retObj['auth_corp_info']['corpid'],
            "permanent_code" => $retObj['permanent_code'],
            "tmp_auth_code" => $tmpAuthCode,
            "create_tm" => time() );

        self::savePermanentCode($permanentCodeInfo);
        return $permanentCodeInfo;
    }
    
    static function savePermanentCode($permCodeInfo)
    {
        $corpList = json_decode(Cache::get(KEY_AUTH_CORPS), true);
        if (!$corpList)
        {
            $corpList = array();
            $corpList[] = $permCodeInfo;
        }
        else
        {
            $idx = self::checkCorpExist($corpList, $permCodeInfo['corp_id']);
            if ($idx >= 0)                       // 已经存在 替换
                $corpList[$idx] = $permCodeInfo;
            else
                $corpList[] = $permCodeInfo;     // 尚不存在 append
        }
        Cache::set(KEY_AUTH_CORPS, json_encode($corpList), "permanent");
    }
    
    public static function getCorpInfoById($corpId)
    {
        $corpList = json_decode(Cache::get(KEY_AUTH_CORPS), true);

        if(!is_array($corpList))
            return false;
    
        foreach($corpList as $corp)
        {
            if($corp['corp_id']==$corpId)
                return $corp;
        }

        return false;
    }

    static function checkCorpExist($corpList, $corpId)
    {
        if(!is_array($corpList) || count($corpList) <= 0)
            return -1;

        for ($i = 0; $i < count($corpList); $i++)
        {
            if($corpList[$i]["corp_id"] == $corpId )
                return $i;
        }
        return -1;
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
