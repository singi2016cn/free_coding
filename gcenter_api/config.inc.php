<?php

define('UC_CONNECT', 'NULL');				// 连接 UCenter 的方式: mysql/NULL, 默认为空时为 fscoketopen()
							// mysql 是直接连接的数据库, 为了效率, 建议采用 mysql

//数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
/*define('UC_DBHOST', '127.0.0.1');
define('UC_DBUSER', 'root');
define('UC_DBPW', 'Ga77&43f&d3sw&f7650fg');
define('UC_DBNAME', 'gcenter_gongzhu');
define('UC_DBCHARSET', 'utf8');
define('UC_DBTABLEPRE', '`gcenter_gongzhu`.uc_');
define('UC_DBCONNECT', '0');

//通信相关
define('UC_KEY', 'b9d3o6QHtw1QPciOq5R5y/KlQ8LN3KTPlETOOippmCaj+t8sT7mdg+pjVdnW9gDhSyk4XMaipavzm5I49oALoo5cZMc');
define('UC_API', 'http://gcenter.conzhu.net');
define('UC_CHARSET', 'utf-8');
define('UC_IP', '');
define('UC_APPID', '3');
define('UC_PPP', '20');*/

define('UC_DBHOST', '127.0.0.1');
define('UC_DBUSER', 'root');
define('UC_DBPW', 'Ga77&43f&d3sw&f7650fg');
define('UC_DBNAME', 'gcenter_gongzhu');
define('UC_DBCHARSET', 'utf8');
define('UC_DBTABLEPRE', '`gcenter_gongzhu`.uc_');
define('UC_DBCONNECT', '0');
define('UC_KEY', 'DLZx2B3YHKR20aT354uYfNPMwcXixA6kWEbUbGUs/rfJOqoobsLg7JO+Rkga4IXxtEC76A4h');
define('UC_API', 'http://gcenter.conzhu.net');
define('UC_CHARSET', 'utf-8');
define('UC_IP', '');
define('UC_APPID', '8');
define('UC_PPP', '20');



//同步登录 Cookie 设置
$cookiedomain = ''; 			// cookie 作用域
$cookiepath = '/';			// cookie 作用路径

?>