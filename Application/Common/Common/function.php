<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

require_once(APP_PATH . '/Common/Common/text.php');
require_once(APP_PATH . '/Common/Common/time.php');
require_once(APP_PATH . '/Common/Common/addon.php');

/**
 * 解析数据库语句函数
 * @param string $sql  sql语句   带默认前缀的
 * @param string $tablepre  自己的前缀
 * @return multitype:string 返回最终需要的sql语句
 */
function sql_split($sql, $tablepre){
    if($tablepre != "ct_"){
        $sql = str_replace("ct_", $tablepre, $sql);
    }
    $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
    if($r_tablepre != $s_tablepre){
        $sql = str_replace($s_tablepre, $r_tablepre, $sql);
    }
    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesarray = explode(";\n", trim($sql));
    unset($sql);
    foreach($queriesarray as $query){
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        $queries = array_filter($queries);
        foreach($queries as $query){
            $str1 = substr($query, 0, 1);
            if($str1 != '#' && $str1 != '-'){
                $ret[$num] .= $query;
            }
        }
        $num++;
    }
    return $ret;
}

/**
 * 执行文件中SQL语句函数
 * @param string $file sql语句文件路径
 * @param string $tablepre  自己的前缀
 * @return multitype:string 返回最终需要的sql语句
 */
function execute_sql_from_file($file){
    $sql_data = file_get_contents($file);
    $sql_format = sql_split($sql_data, C('DB_PREFIX'));
    $counts = count($sql_format);
    for ($i = 0; $i < $counts; $i++) {
        $sql = trim($sql_format[$i]);
        D()->execute($sql);
    }
    return true;
}

/**
 * 根据配置类型解析配置
 * @param  string $type  配置类型
 * @param  string  $value 配置值
 * @author jry <598821125@qq.com>
 */
function parse_attr($value, $type){
    switch ($type) {
        default: //解析"1:1\r\n2:3"格式字符串为数组
            $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
            if(strpos($value,':')){
                $value  = array();
                foreach ($array as $val) {
                    list($k, $v) = explode(':', $val);
                    $value[$k]   = $v;
                }
            }else{
                $value = $array;
            }
            break;
    }
    return $value;
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author jry <598821125@qq.com>
 */
function is_login(){
    return D('User')->isLogin();
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 * @author jry <598821125@qq.com>
 */
function user_md5($str, $key = 'CoreThink'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 根据用户ID获取用户信息
 * @param  integer $id 用户ID
 * @param  string $field
 * @return array  用户信息
 * @author jry <598821125@qq.com>
 */
function get_user_info($id, $field){
    $userinfo = D('User')->find($id);
    if($field){
        $userinfo[$field];
    }
    return $userinfo;
}

/**
 * 获取上传文件路径
 * @param  int $id 文件ID
 * @return string
 * @author jry <598821125@qq.com>
 */
function get_cover($id, $type){
    $url = D('Upload')->getPath($id);
    if(!$url){
        switch($type){
            case 'avatar' : //用户头像
                $url = C('TMPL_PARSE_STRING.__IMG__').'/avatar'.rand(1,7).'.png';
                break;
            default: //文档列表默认图片
                break;
        }
    }
    return $url;
}

/**
 * 系统邮件发送函数
 * @param string $receiver 收件人
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 * @author jry <598821125@qq.com>
 */
function send_mail($receiver, $subject, $body, $attachment){
    return R('Addons://Email/Email/sendMail', array($receiver, $subject, $body, $attachment));
}

/**
 * 短信发送函数
 * @param string $receiver 接收短信手机号码
 * @param string $body 短信内容
 * @return boolean
 * @author jry <598821125@qq.com>
 */
function send_mobile_message($receiver, $body){
    return false; //短信功能待开发
}
