<?php

class Department
{
    public static function createDept($accessToken, $dept)
    {
        $url = OAPI_HOST . '/dept/create?access_token=' . $access_token;

        $retStr = Http::post($url, json_encode($dept));
        return $retStr;
    }
    
    public static function listDept($accessToken, $parentDid)
    {
        $url = OAPI_HOST . '/dept/list?access_token='. $accessToken . "&did=" . $parentDid;
      
        $retStr = Http::get($url);  
        return $retStr;
    }
    
}