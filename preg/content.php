<?php


/**
 * 根据合同字段匹配相关内容
 * 适用于不超过一行的情况，匹配规则 $filed_name.'.+'.$suffix
 * 例如 “项目名称：深圳13号地铁第3标段。” 会返回 “深圳13号地铁第3标段”
 * @param string $filed_name 匹配字段（匹配开头字段）
 * @param string $content 要搜索的内容
 * @param string $suffix 匹配的结尾字符，默认 。（中文输入法下的句号）
 * @return mixed|string
 */
function find_field_by_preg($filed_name, $content, $suffix='。'){
    $pattern = '/'.$filed_name.'.+'.$suffix.'/';
    if (preg_match($pattern,$content,$matches)) {
        return str_replace([$filed_name,$suffix],'',trim($matches[0]));
    } else {
        return '';
    }
}

//调用示例
$content = <<<D
    safswgwegweg
    项目名称：深圳13号地铁第3标段。哥哥我舒服舒服望各
    wgwegwe
    we
    gwe
    g
    weg
    ew
    g
    weg
    we
    g
    egwgewg
D;

var_dump(find_field_by_preg('项目名称：',$content));