<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env(){
    $items = array(
        'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'ok'),
        'php'     => array('PHP版本', '5.3', '5.3+', PHP_VERSION, 'ok'),
        'upload'  => array('附件上传', '不限制', '2M+', '未知', 'ok'),
        'gd'      => array('GD库', '2.0', '2.0+', '未知', 'ok'),
        'disk'    => array('磁盘空间', '5M', '不限制', '未知', 'ok'),
    );

    //PHP环境检测
    if($items['php'][3] < $items['php'][1]){
        $items['php'][4] = 'remove';
        session('error', true);
    }

    //附件上传检测
    if(@ini_get('file_uploads'))
        $items['upload'][3] = ini_get('upload_max_filesize');

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if(empty($tmp['GD Version'])){
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'remove';
        session('error', true);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    //磁盘空间检测
    if(function_exists('disk_free_space')) {
        $items['disk'][3] = floor(disk_free_space('./') / (1024*1024)).'M';
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile(){
    $items = array(
        array('dir',  '可写', 'ok', './Conf'),
        array('dir',  '可写', 'ok', './Runtime'),
        array('dir',  '可写', 'ok', './Uploads'),
        array('file', '可写', 'ok', './Conf/config.php'),
    );

    foreach ($items as &$val){
        $item = $val[3];
        if('dir' == $val[0]){
            if(!is_writable($item)){
                if(is_dir($item)) {
                    $val[1] = '不可写';
                    $val[2] = 'remove';
                    session('error', true);
                }else{
                    $val[1] = '不存在';
                    $val[2] = 'remove';
                    session('error', true);
                }
            }
        }else{
            if(file_exists($item)){
                if(!is_writable($item)){
                    $val[1] = '不可写';
                    $val[2] = 'remove';
                    session('error', true);
                }
            }else{
                if(!is_writable(dirname($item))) {
                    $val[1] = '不存在';
                    $val[2] = 'remove';
                    session('error', true);
                }
            }
        }
    }
    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func(){
    $items = array(
        array('mysql_connect',     '支持', 'ok'),
        array('file_get_contents', '支持', 'ok'),
        array('mb_strlen',         '支持', 'ok'),
    );
    foreach ($items as &$val) {
        if(!function_exists($val[0])){
            $val[1] = '不支持';
            $val[2] = 'remove';
            $val[3] = '开启';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config, $auth){
    if(is_array($config)){
        //读取配置内容
        $conf = file_get_contents(MODULE_PATH . 'Data/config.tpl');
        //替换配置项
        foreach ($config as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }
        $conf = str_replace('[AUTH_KEY]', $auth, $conf);
        //写入应用配置文件

        if(file_put_contents('./Conf/config.php', $conf)){
            show_msg('配置文件写入成功');
        }else{
            show_msg('配置文件写入失败！', 'error');
            session('error', true);
        }
        return '';
    }
}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db, $prefix = ''){
    //读取SQL文件
    $sql = file_get_contents(MODULE_PATH . 'Data/install.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    $orginal = C('ORIGINAL_TABLE_PREFIX');
    $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);

    //开始安装
    show_msg('开始安装数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if(empty($value)) continue;
        if(substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $msg  = "创建数据表{$name}";
            if(false !== $db->execute($value)){
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        } else {
            $db->execute($value);
        }

    }
}

/**
 * 注册超级管理员账号
 */
function register_administrator($db, $prefix, $admin){
    show_msg('开始注册创始人帐号...');
    $sql = "DELETE FROM {$prefix}user;" .
           "ALTER TABLE {$prefix}user AUTO_INCREMENT = 1;" .
           "INSERT INTO `{$prefix}user` (`id`, `username`, `email`, `mobile`, `password`, `group`, `status`) VALUES " .
           "('1', '[NAME]', '[EMAIL]', '[MOBILE]','[PASS]', '1', '1')";
    $password = user_md5($admin['password']);
    $sql = str_replace(array('[NAME]', '[EMAIL]', '[MOBILE]', '[PASS]'), array($admin['username'], $admin['email'], $admin['mobile'], $password), $sql);
    $db->execute($sql);
    show_msg('创始人帐号注册完成！');
}

/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = ''){
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    flush();
    ob_flush();
}

/**
 * 生成系统AUTH_KEY
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function build_auth_key(){
    $chars  = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
    $chars  = str_shuffle($chars);
    return substr($chars, 0, 40);
}
