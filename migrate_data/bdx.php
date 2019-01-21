<?php
/**
 * 迁移北担协文章数据
 * cz_ecms_news 文章表
 * cz_ecms_news_data_1 文章内容表
 *
 */

require_once '../config/db.php';

$news = $db->select('cz_ecms_news',['id','classid','title']);

if ($news){
    foreach($news as $news_item){
        $news_data = $db->select('cz_ecms_news_data_1','id',['id'=>$news_item['id']]);
        if (!$news_data){
            //如果没有文章内容则需要添加
            $article = $db->get('jcms_module_article',['Content','Editor'],['Title'=>$news_item['title']]);
            $news_data_insert['id'] = $news_item['id'];
            $news_data_insert['classid'] = $news_item['classid'];
            $news_data_insert['writer'] = $article['Editor'];
            $news_data_insert['newstext'] = $article['Content'];
            $db->insert('cz_ecms_news_data_1',$news_data_insert);
            var_dump($news_data_insert['id']);
        }
    }
}