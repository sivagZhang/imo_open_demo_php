<?php

error_reporting(E_ALL);
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/util/Log.php");
require_once(__DIR__ . "/oapi/ISVClass.php");
require_once(__DIR__ . "/crypto/IMOCryptAES.php");


$signature = $_GET["msgSignature"];
$timeStamp = $_GET["timeStamp"];
$nonce = $_GET["nonce"];

$postdata = file_get_contents("php://input");
Log::i("request:" . json_encode($_REQUEST));

$encrypt = $_REQUEST['encrypt'];

$IMOCryptAES = new IMOCryptAES(TOKEN, SUITE_KEY);
$decodeRet = $IMOCryptAES->decrypt($signature, $timeStamp, $nonce, $encrypt);

if ($decodeRet === false)
{
    Log::e(json_encode($_GET) . "Decrypt error!");
    exit("error");
}
else
{
    Log::i("DECRYPT MSG SUCCESS " . json_encode($_GET) . "  " . $decodeRet);
    $eventObj = json_decode($decodeRet);
    $eventType = $eventObj->event;
    
    //临时授权码
    if ("tmp_auth_code" === $eventType)
    {
        $tmpAuthCode = $eventObj->AuthCode;
        Log::e("tmpAuthCode:" . $tmpAuthCode);
        
        ISVClass::handleCorpAuth($tmpAuthCode);
        exit("ok");
    }
}
