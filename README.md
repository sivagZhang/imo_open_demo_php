## imo_open_demo_php

### 目录结构：
- corp目录：企业内部应用示例
- isv目录： isv应用接入示例

### 环境配置：
配置PHP服务器环境（php+apache/nginx），安装mcrypt扩展（用于加解密函数），保证web服务根目录有可写权限。

### 运行步骤
运行demo前，请先查看对应的开发文档：http://wiki.open.imoffice.com/

======================================================================

**企业内部应用**

1. 按照[创建企业自有应用](http://wiki.open.imoffice.com/pages/viewpage.action?pageId=3244078)中的步骤进行操作。其中应用首页地址为：`工程地址/index.php`（举例来说，将demo中的corp目录部署到 ip地址为222.73.30.39的主机上，则pc和移动端对应的首页地址分别为：`http://222.73.30.39/corp/indexpc.php` 和 `http://222.73.30.39/corp/index.php` 如果你有域名，也可把IP地址换成域名 ).

2. 将所建应用对应的CORP_ID，CORP_SECRET，AGENT_ID，填写到corp目录下的config.php里。

   ```php
   define("CORP_ID", "");
   define("CORP_SECRET", "");
   define("AGENT_ID", "");
   ```
3. 打开对应的客户端，即可看到新建应用。

======================================================================

**ISV应用**

1. 按照[创建开放应用](http://wiki.open.imoffice.com/pages/viewpage.action?pageId=3244119)中的步骤进行操作。其中：

- 首页地址：`工程地址/index.php?corpid=$CORPID$`（举例来说，将demo中的isv目录部署到 ip地址为222.73.30.39的主机上, 则pc端首页地址为：`http://222.73.30.39/isv/indexpc.php?corpid=$CORPID$` 如果你有域名，也可把IP地址换成域名)

- 回调地址：`工程地址/imoCallback.php`，比如说`http://222.73.30.39/isv/imoCallback.php`.

2. 将所建应用对应的参数，填写到corp目录下的config.php里。

   ```php
   define("SUITE_KEY", "");
   define("SUITE_SECRET", "");
   define("TOKEN", "");
   define("AGENT_ID", "");
   ```

3. 用**审核邮件中的测试企业账号**登录班聊pc客户端，到管理后台“应用中心”的“全部应用”，可以看到步骤1中创建的isv应用，点击添加进行授权，则会触发imoCallback.php里的授权流程。

4. 授权成功后，即可在测试企业的工作台看到该isv应用。
