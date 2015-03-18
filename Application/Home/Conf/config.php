<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
return array(
    //预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'Home\\TagLib\\Corethink',

    //默认主题
    'DEFAULT_THEME'   => 'default',

    //前缀设置避免冲突
    'DATA_CACHE_PREFIX' => ENV_PRE.MODULE_NAME.'_', //缓存前缀
    'SESSION_PREFIX'    => ENV_PRE.MODULE_NAME.'_', //Session前缀
    'COOKIE_PREFIX'     => ENV_PRE.MODULE_NAME.'_', //Cookie前缀

    //静态缓存配置
    'HTML_CACHE_ON'     => false,     //开启静态缓存
    'HTML_CACHE_TIME'   => 60,       //全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'  => '.shtml', //设置静态缓存文件后缀
    'HTML_CACHE_RULES'  =>  array(   //定义静态缓存规则
         '*'=>array('{:module}/{:controller}/{:action}/{$_SERVER.REQUEST_URI|md5}')
    )
);