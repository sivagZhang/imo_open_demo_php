<?php

require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../util/CurlHttp.php");
require_once(__DIR__ . "/../util/Cache.php");
require_once(__DIR__ . "/../util/Log.php");
require_once(__DIR__ . "/ISVService.php");


/**
 * ISV 权限管理类
 */
class ISVClass
{
    /**
     * 当某个对应用授权时，
     * 开放平台会推送临时授权码，isv需要根据此临时授权码，获取到企业的永久授权码。
     */
    public static function handleCorpAuth($tmpAuthCode)
    {
        $suiteAccessToken = Service::get_suite_token();
        Log::i("[handleCorpAuth] suiteAccessToken: " . $suiteAccessToken);

        $permanetCodeInfo = Service::get_permanent_code($suiteAccessToken, $tmpAuthCode);
        Log::i("[handleCorpAuth] permanetCodeInfo: " . json_encode($permanetCodeInfo));
        
        $authCorpId = $permanetCodeInfo['corp_id'];
        $permanentCode = $permanetCodeInfo['permanent_code'];

        $corpAccessToken = Service::get_corp_token($suiteAccessToken, $authCorpId, $permanentCode);
        Log::i("[handleCorpAuth] corpAccessToken: " . $corpAccessToken);

        return true;    //操作成功
    }

    public static function getCorpAccessToken($corpId)
    {
        $corpInfo = Service::getCorpInfoById($corpId);
        
        $suiteAccessToken = Service::get_suite_token();
        $corpAccessToken = Service::get_corp_token($suiteAccessToken, $corpId, $corpInfo['permanent_code']);

        return $corpAccessToken;
    }

    public static function getJsTicket($corpId)
    {
        $corpAccessToken = self::getCorpAccessToken($corpId);
        return Service::get_jsapi_ticket($corpAccessToken, $corpId);
    }
}
