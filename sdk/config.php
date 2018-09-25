<?php

/*
 * 调用接口前请配置好
 * */

define('API_KEY','29ff0022ce4781734931c6f1677331889b20c03b87d6815646ab95865fa41d21');
define('API_SECRET','4e72fbb80f399581ec4c49c48ff0aed6be924fb12f946cd58fbb1a80f99ec2eb');
define('RSA_PK','-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDAMhZLw2QPzTufxaRGWEZGprD7
3hC66mxXieNH168Hj9gcPreEME1SU6TzHqPfZdKzNGFgBJbkeIMJbPxaZNRPSxAP
07BWkAUdbXfnyR5FBKUp/xfk+6ZL53Tlg3eQCjaSRfefTXkvHb9YkOq0D5XquanX
0HMi4viY4PwteLHoVwIDAQAB
-----END PUBLIC KEY-----');//请求业务接口使用的加密公钥
define('RSA_HASH','sha256');//请求业务接口使用的加密哈希方法

define('API_RSA_PK','-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCM/77+WI7K0xNCntEenfR116qA
xw6X8POQjUbT3G/xNWfvfQYyWCbu/v1Hx2UlTMvY52OV//K21NtAFB7xEKJ1qsV0
YoC38SzoPEQgt2wO/WRqZ7ePrIj8rpOMFL8EcaWFi3WZOdD4wGIFoitLGxolG5ZQ
fWJbvEKYPvv0xfHNGQIDAQAB
-----END PUBLIC KEY-----');//请求access_token使用的加密公钥
define('API_RSA_HASH','sha256');//请求access_token使用的加密哈希方法
define('REQUEST_API_URL','http://edg.conzhu.net.test/e/api/doaction.php');//接口请求url

