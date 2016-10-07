
imoPC.config({
    agentId: _config.agentId +'',
    corpId: _config.corpId + '',
    timeStamp: _config.timeStamp + '',
    nonceStr: _config.nonceStr,
    signature: _config.signature,
    jsApiList: [            
        // 所有要调用的API 需要加到这个列表中
        "biz.util.open",
        "biz.contact.choose",
    ]
});


imoPC.userid=0;
imoPC.ready(function() {
   logger.i('imoPC is ready!');

   imoPC.runtime.permission.requestAuthCode({
        corpId: _config.corpId,  
        onSuccess: function (info) {
            $.ajax({
                url: './appRequest.php',
                type: "POST",
                data: {"event":"get_userid", "code":info.code, "corpId":_config.corpId},
                dataType: 'json',
                timeout: 1000,
                success: function (data, status, xhr) {
                    if (data.errcode === 0) {
                        logger.i('userid: ' + data.userid);
                        imoPC.userid = data.userid; 
                    }
                    else {
                        logger.e('getUserByCode error: ' + JSON.stringify(data));
                    }
                },
                error: function (xhr, errorType, error) {
                    logger.e(errorType + ', ' + error);
                }
            });
        },
        onFail: function (err) {
            logger.e('requestAuthCode fail: ' + JSON.stringify(err));
        }
    });

    $('.showProfile').on('click', function() {
        imoPC.biz.util.open({
            name: 'profile',
            params: {
                id: 'admin',
                corpId: _config.corpId.toString(),
            },
            onSuccess : function() {},
            onFail : function(err) {
                logger.e(JSON.stringify(err));
            }
        });
    });


    $('.chooseEmpl').on('click', function() {
        imoPC.biz.contact.choose({
            corpId: _config.corpId, 
            onSuccess: function(data){
                if(data && data.length > 0) { 
                    var to_users = "";

                    for (var idx = 0; idx < data.length; idx++) {
                        to_users += data[idx].emplId;
                        if (idx != data.length -1)
                            to_users += "|";
                    }

                    $.ajax({
                        url: './appRequest.php',
                        type: "POST",
                        data: {"event":"send_appmsg", "to_users":to_users, "corpId":_config.corpId},
                        dataType: 'json',
                        timeout: 1000,
                        success: function (data, status, xhr) {
                            if (data.errcode === 0) {
                                logger.i("send_appmsg success!");
                            }
                            else {
                                logger.e('send_appmsg error: ' + JSON.stringify(data));
                            }
                        },
                        error: function (xhr, errorType, error) {
                            logger.e(errorType + ', ' + error);
                        }
                    });
                }
                
            },
            onFail : function(err) {
                logger.e(JSON.stringify(err));
            }
        });
    });

   
});

imoPC.error(function(err) {
    logger.i('imoPC error: ' + JSON.stringify(err));
});

