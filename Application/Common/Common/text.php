<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

/**
 *  带格式生成随机字符 支持批量生成
 *  但可能存在重复
 * @param string $format 字符格式
 * 0 字母 1 数字 其它 混合
 * @param integer $number 生成数量
 * @return string | array
 * @author jry <598821125@qq.com>
 */
function randString($len = 6, $type = 1){
    return \Org\Util\String::randString($len, $type);
}

/**
 * 解析文档内容
 * @param string $str 待解析内容
 * @return string
 * @author jry <598821125@qq.com>
 */
function parse_content($str){
    return preg_replace('/(<img.*?)src=/i', "$1 data-original=", $str);//将img标签的src改为data-origin用户前台图片lazyload加载
}

/**
 * 敏感词过滤替换
 * @param  string $text 待检测内容
 * @param  array $sensitive 待过滤替换内容
 * @param  string $suffix 替换后内容
 * @return bool
 * @author jry <598821125@qq.com>
 */
function sensitive_filter($text, $sensitive = null, $suffix = '**'){
    if(!$sensitive){
        $sensitive = C('SENSITIVE_WORDS');
    }
    $sensitive_words = explode(',', $sensitive);
    $sensitive_words_replace = array_combine($sensitive_words,array_fill(0,count($sensitive_words), $suffix));
    return strtr($text, $sensitive_words_replace);
}

/**
 * 过滤标签，输出纯文本
 * @param string $str 文本内容
 * @return string 处理后内容
 * @author jry <598821125@qq.com>
 */
function html2text($str){
    $str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
    $alltext = "";
    $start = 1;
    for($i=0;$i<strlen($str);$i++){
        if($start==0 && $str[$i]==">"){
            $start = 1;
        }
        else if($start==1){
            if($str[$i]=="<"){
                $start = 0;
                $alltext .= " ";
            }
            else if(ord($str[$i])>31){
                $alltext .= $str[$i];
            }
        }
    }
    $alltext = str_replace("　"," ",$alltext);
    $alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
    $alltext = preg_replace("/[ ]+/s"," ",$alltext);
    return $alltext;
}

/**
 * 字符串截取(中文按2个字符数计算)，支持中文和其他编码
 * @static
 * @access public
 * @param str $str 需要转换的字符串
 * @param str $start 开始位置
 * @param str $length 截取长度
 * @param str $charset 编码格式
 * @param str $suffix 截断显示字符
 * @return str
 * @author jry <598821125@qq.com>
 */
function get_str($str, $start, $length, $charset='utf-8', $suffix=true) {
    $str = trim($str);
    $length = $length * 2;
    if($length){
        //截断字符
        $wordscut = '';
        if(strtolower($charset) == 'utf-8'){
            //utf8编码
            $n = 0;
            $tn = 0;
            $noc = 0;
            while($n < strlen($str)){
                $t = ord($str[$n]);
                if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)){
                    $tn = 1;
                    $n++;
                    $noc++;
                }elseif(194 <= $t && $t <= 223){
                    $tn = 2;
                    $n += 2;
                    $noc += 2;
                }elseif(224 <= $t && $t < 239){
                    $tn = 3;
                    $n += 3;
                    $noc += 2;
                }elseif(240 <= $t && $t <= 247){
                    $tn = 4;
                    $n += 4;
                    $noc += 2;
                }elseif(248 <= $t && $t <= 251){
                    $tn = 5;
                    $n += 5;
                    $noc += 2;
                }elseif($t == 252 || $t == 253){
                    $tn = 6;
                    $n += 6;
                    $noc += 2;
                }else{
                    $n++;
                }
                if ($noc >= $length){
                    break;
                }
            }
            if($noc > $length){
                $n -= $tn;
            }
            $wordscut = substr($str, 0, $n);
        }else{
            for($i = 0; $i < $length - 1; $i++){
                if(ord($str[$i]) > 127) {
                    $wordscut .= $str[$i].$str[$i + 1];
                    $i++;
                } else {
                    $wordscut .= $str[$i];
                }
            }
        }
        if($wordscut == $str){
            return $str;
        }
        return $suffix ? trim($wordscut).'...' : trim($wordscut);
    }else{
        return $str;
    }
}
