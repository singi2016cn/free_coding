<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/4/27
 * Time: 11:17
 */

require 'vendor/autoload.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

$signer = new Sha256();

$token = (new Builder())->setIssuer('http://example.com') // Configures the issuer (iss claim)
->setAudience('http://example.org') // Configures the audience (aud claim)
->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
->set('uid', 1202) // Configures a new claim, called "uid"
->set('name', 'singi') // Configures a new claim, called "uid"
->sign($signer, 'singi') // creates a signature using "testing" as key
->getToken(); // Retrieves the generated token


var_dump($token->verify($signer, 'testing 1')); // false, because the key is different
var_dump($token->verify($signer, 'testing')); // true, because the key is the same
var_dump($token->verify($signer, 'singi'));

print_r($token->getClaim('name'));

