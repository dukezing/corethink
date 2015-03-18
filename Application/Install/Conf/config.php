<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
/**
 * 安装程序配置文件
 */
return array(
    //产品配置
    'INSTALL_PRODUCT_NAME'   => 'CoreThink', //产品名称
    'INSTALL_WEBSITE_DOMAIN' => 'http://www.corethink.cn', //官方网址
    'INSTALL_COMPANY_NAME'   => '南京科斯克网络科技有限公司', //公司名称
    'ORIGINAL_TABLE_PREFIX'  => 'ct_', //默认表前缀

    //模板相关配置
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__.'/Public',
        '__IMG__' => __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/img',
        '__CSS__' => __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/css',
        '__JS__'  => __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/js',
    ),

    //前缀设置避免冲突
    'DATA_CACHE_PREFIX' => ENV_PRE.MODULE_NAME.'_', //缓存前缀
    'SESSION_PREFIX'    => ENV_PRE.MODULE_NAME.'_', //Session前缀
    'COOKIE_PREFIX'     => ENV_PRE.MODULE_NAME.'_', //Cookie前缀
);
