<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/util/Log.php");
require_once(__DIR__ . "/oapi/Auth.php");
require_once(__DIR__ . "/oapi/User.php");
require_once(__DIR__ . "/oapi/Message.php");

// 此处仅作示例，实际开发的话，需要校验session的合法性。
Log::i("[appRequest] ". json_encode($_POST));

$event = $_POST["event"];
switch($event){
    case 'send_appmsg':
        $accessToken = Auth::getAccessToken();

        $to_users = $_POST['to_users'];
        $to_depts = $_POST['to_depts'];

        $msgInfo = getRandMsg();        
        $data = array(
            "to_users" => $to_users,
            "to_depts" => $to_depts,
            "agentid"  => AGENT_ID,
            "msg_type" => $msgInfo['msg_type'],
            "message" => $msgInfo['message'],
        );
        $ret = Message::sendAppMsg($accessToken, $data);
        Log::i("[send_appmsg] ". $ret);
        echo $ret;
        break;
    case 'get_userid':
        $accessToken = Auth::getAccessToken();
        $code = $_POST["code"];
        
        $ret = User::getUserIdentity($accessToken, $code);
        Log::i("[get_userid] ". $ret);
        echo $ret;
        break;
    case 'get_userinfo':
        $accessToken = Auth::getAccessToken();
        $userid = $_POST["userid"];
        
        $ret = User::getUserInfo($accessToken, $userid);
        Log::i("[get_userinfo] ".$ret);
        echo $ret;
        break;

    default:
        echo json_encode(array("errcode"=>"9999", "errmsg"=>"不支持的event"));
        break;
}

function getRandMsg()
{  
    $msgObj = array(); 
    $msg_type = rand(1, 3);
    
    if ($msg_type == 1)
    {
        $msgObj = array(
            "msg_type" => "text",
            "message" => array("content" => "今天天气太热，高温补贴100。")
        );
    }
    else if ($msg_type == 2)
    {
        $msgObj = array(
            "msg_type" => "image",
            "message" => array("media_id" => "H3naM1D1YqtXruO2")
        );
    }
    else if ($msg_type == 3)
    {
        $msgObj = array(
            "msg_type" => "oa",
            "message" => array(
                "message_url" => "http://workchat.com", 
                "pc_message_url" => "http://www.baidu.com",
                "head" => array(
                    "bgcolor"=> "FF0022FF",
                    "text"=> "公告公告"
                    ),
                "body"=> array(
                    "title"=> "我是标题",
                    "form"=> array(
                        array("key" => "姓名", "value" => "haha"),
                        array("key" => "年龄", "value" => "18"),
                        array("key" => "身高", "value" => "180cm"),
                    ),
                    "rich"=> array(
                        "text"=> "我是单行富文本",
                        "bgcolor"=> "ff0000"
                    ),
                    "content"=> "您有一个新的消息",
                    "image"=> "H3naM1D1YqtXruO2",
                    "author"=> "张老板",
                )
            ),
        );
    }
    return $msgObj;
}


