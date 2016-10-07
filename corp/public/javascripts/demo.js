
imoMobile.config({
    agentId: _config.agentId,
    corpId: _config.corpId,
    timeStamp: _config.timeStamp,
    nonceStr: _config.nonceStr,
    signature: _config.signature,
    jsApiList: [
        'biz.util.open',
        'device.geolocation.get',
        'biz.contact.complexChoose',
    ]
});


imoMobile.userid=0;
imoMobile.ready(function() {
    logger.i('imoMobile is ready!');
    
    imoMobile.runtime.permission.requestAuthCode({
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
                        imoMobile.userid = data.userid; 
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
        imoMobile.biz.util.open({
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

    $('.getLocation').on('click', function() {
        imoMobile.device.geolocation.get({
            targetAccuracy: 20,     //定位范围，如 100米内
            coordinate: 1,          //坐标类型 0为百度bd09ll坐标, 1为wgs84坐标
            withReGeocode : true,   //是否需要反地理信息编码
            onSuccess: function(result) {
                logger.i('当前位置:' + result.address);
            },
            onFail: function(err) {
                logger.e('获取位置失败:' + JSON.stringify(err));
            }
        });
    }); 

    $('.chooseEmpl').on('click', function() {
        imoMobile.biz.contact.complexChoose({
            startWithDepartmentId: 0, // -1表示从自己所在部门开始, 0表示从企业最上层开始，其他数字表示从该部门开始
            corpId: _config.corpId,
            onSuccess: function(data){
                if(data) { 
                    var to_users = "";
                    var to_depts = "";

                    for (var idx = 0; idx < data.users.length; idx++) {
                        to_users += data.users[idx].emplId;
                        if (idx != data.length -1)
                            to_users += "|";
                    }
                    for (var idx = 0; idx < data.department.length; idx++) {
                        to_users += data.department[idx].id;
                        if (idx != data.length -1)
                            to_depts += "|";
                    }

                    $.ajax({
                        url: './appRequest.php',
                        type: "POST",
                        data: {"event":"send_appmsg", "to_users":to_users, "to_depts": to_depts, "corpId":_config.corpId},
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

imoMobile.error(function(err) {
    logger.i('imoMobile error: ' + JSON.stringify(err));
});

