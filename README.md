# CamelList轻量级邮件营销系统

### 系统简介

《CamelList轻量级邮件营销系统》是一套轻量级的电子邮件营销系统软件。

### 运行环境
php 7.4
mysql/MariaDB
Apache/Nginx Http Server
 
### 系统依赖
thinkphp 5.0 framework
php mailparse extension

### 部署步骤
1. 部署代码
2. 部署Apache/Nginx web server, document root 为src\public
2. 导入数据库配置wzyer_list.sql in src\db
3. 修改数据库配置文件src\application\database.php
4. Congratulation!, 访问系统，默认登录账号admin/123456, 切记：登录后立即修改密码

### 系统特点
- BS架构，云端部署，随时随地访问方便。
- 支持海量电子邮件自由分类管理。
- 高效发送引擎，最大保证投递的成功率。
- 用户行为跟踪，投递推广效果一目了然。
### 系统首页
![输入图片说明](https://images.gitee.com/uploads/images/2022/0307/222209_856af69e_10482337.jpeg "list.jpg")
### 投递邮箱管理
![输入图片说明](https://images.gitee.com/uploads/images/2022/0310/183100_c3b10012_10482337.png "subscribers.png")
### 投递活动管理
![输入图片说明](https://images.gitee.com/uploads/images/2022/0310/183225_32f5b6a0_10482337.png "campaigns.png")