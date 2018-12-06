<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/9/4
 * Time: 15:58
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

use Goutte\Client;

$client = new Client();

//获取所有省
$crawler_provinces = $client->request('GET', 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2017');
$provinces = $crawler_provinces->filter('tr.provincetr > td')->each(function ($node) {
    return $node->text();
});
$provinces = array_filter($provinces);

if ($provinces){
    foreach ($provinces as $province){
        //获取该省所属市
        $crawler_cities = $client->click($crawler_provinces->selectLink($province)->link());
        $cities = $crawler_cities->filter('tr.citytr > td')->each(function ($node) {
            return $node->text();
        });
        $cities = array_chunk(array_filter($cities),2);
        if ($cities){
            foreach($cities as $city){
                $crawler_counties = $client->click($crawler_cities->selectLink($city[0])->link());
                $counties = $crawler_counties->filter('tr.countytr > td')->each(function ($node) {
                    return $node->text();
                });
                $counties = array_chunk(array_filter($counties),2);
                if ($counties){
                    foreach($counties as $county){
                        $crawler_towns = $client->click($crawler_counties->selectLink($county[0])->link());
                        $towns = $crawler_towns->filter('tr.towntr > td')->each(function ($node) {
                            return $node->text();
                        });
                        $towns = array_chunk(array_filter($towns),2);
                        var_dump($towns);exit;
                    }
                }
            }
        }
    }
}





//获取该县所有