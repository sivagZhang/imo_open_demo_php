<?php

class User
{
    public static function getUserIdentity($accessToken, $code)
    {
        $url = OAPI_HOST . "/get_user_info?access_token=" . $accessToken . "&code=" . $code;

        $retStr = Http::get($url);
        return $retStr;
    }

    public static function getUserInfo($accessToken, $userid)
    {
        $url = OAPI_HOST . "/user/get?access_token=" . $accessToken . "&userid=" . $userid;
      
        $retStr = Http::get($url);  
        return $retStr;
    }

    public function createUser($accessToken, $data)
    {
        $url = OAPI_HOST . '/user/create?access_token=' . $access_token;

        $retStr = Http::post($url, json_encode($data));
        return $retStr;
    }

}