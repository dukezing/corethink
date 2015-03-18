<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

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
    return D('User')->getUserById($id, $field);
}

/**
 * 获取上传文件路径
 * @param  int $id 文件ID
 * @return string
 * @author jry <598821125@qq.com>
 */
function get_cover($id, $type){
    $url = D('Upload')->getPathOrUrlById($id);
    if(!$url){
        switch($type){
            case 'avatar' : //用户头像
                $url = __ROOT__.'/Application/Home/View/'.C('DEFAULT_THEME').'/Public/img/avatar/'.rand(1,7).'.png';
                break;
            default: //文档列表默认图片
                break;
        }
    }
    return $url;
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author jry <598821125@qq.com>
 */
function time_format($time = NULL, $format='Y-m-d H:i'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 友好的时间显示
 * @author jry <598821125@qq.com>
 */
function friendly_date($time){
    return \Org\Util\Date::friendly_date($time);
}

/**
 * 过滤标签，输出纯文本
 * @param string $str 文本内容
 * @return string 处理后内容
 * @author jry <598821125@qq.com>
 */
function html2text($str){
    return \Org\Util\String::html2text($str);
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $str 字符串
 * @param string $length 截取长度
 * @author jry <598821125@qq.com>
 */
function get_str($str, $length){
    return \Org\Util\String::get_str($str, 0, $length, $charset='utf-8', $suffix=true);
}

/**
 *  带格式生成随机字符 支持批量生成
 *  但可能存在重复
 * @param string $format 字符格式
 *     # 表示数字 * 表示字母和数字 $ 表示字母
 * @param integer $number 生成数量
 * @return string | array
 * @author jry <598821125@qq.com>
 */
function randString($len = 6, $type){
    return \Org\Util\String::randString($len, $type);
}

/**
 * 敏感词过滤
 * @param  string $text 待检测内容
 * @param  array $sensitive 待过滤替换内容
 * @param  string $suffix 替换后内容
 * @return bool
 * @author jry <598821125@qq.com>
 */
function sensitive_filter($text){
    $string = new \Org\Util\String();
    $sensitive = C('SENSITIVE_WORDS');
    return $string->sensitive_filter($text, $sensitive, $suffix = '**');
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 * @author jry <598821125@qq.com>
 */
function hook($hook, $params = array()){
    \Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 * @author jry <598821125@qq.com>
 */
function get_addon_class($name){
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author jry <598821125@qq.com>
 */
function addons_url($url, $param = array()){
    return D('Addon')->getAddonUrl($url, $param);
}

/**
 * 解析插件数据列表定义规则
 * @author jry <598821125@qq.com>
 */
function get_addon_adminlist_field($data, $grid, $addon){
    return D('Addon')->getAddonAdminlistField($data, $grid, $addon);
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

/**
 * 解析文档内容
 * @param string $str 待解析内容
 * @return string
 * @author jry <598821125@qq.com>
 */
function parse_content($str){
    return preg_replace('/(<img.*?)src=/i', "$1 data-original=", $str);//将img标签的src改为data-origin用户前台图片lazyload加载
}
