<?php

/**
 * 将索引数组转化为以某键的值为索引的数组
 * @param array  $list 要进行转换的数据集
 * @param string $key  以该key为索引
 */
function array_to_map($list, $key='id'){
    $result = array();
    if(is_array($list)){
        foreach($list as $rs){
            $result[$rs[$key]] = $rs;
        }
    }
    return $result;
}

/**
 * 搜索高亮显示关键字
 *
 * @param string $string 原字符串
 * @param string $keyword 搜索关键字字符串，默认为keyword, 可不传
 *
 * @return string $string 返回高亮后的字符串
 */
function highlight($string , $keyword =''){
    if($keyword == ''){
        $keyword = 'keyword' ; // 默认搜索关键字为 keyword
    }

    if(isset($_GET[$keyword]) && $_GET[$keyword]){
        $keyword_value = $_GET[$keyword];
        return preg_replace ("/($keyword_value)/i", "<span style=\"color:#dd4b39\">\\1</span>", $string);
    }
    return $string;
}

?>