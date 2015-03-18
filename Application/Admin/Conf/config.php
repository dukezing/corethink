<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
return array(
    //模板相关配置
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__.'/Public',
        '__IMG__'    => __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/img',
        '__CSS__'    => __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/css',
        '__JS__'     => __ROOT__.'/Application/'.MODULE_NAME.'/View/Public/js',
    ),

    //前缀设置避免冲突
    'DATA_CACHE_PREFIX' => ENV_PRE.MODULE_NAME.'_', //缓存前缀
    'SESSION_PREFIX'    => ENV_PRE.MODULE_NAME.'_', //Session前缀
    'COOKIE_PREFIX'     => ENV_PRE.MODULE_NAME.'_', //Cookie前缀
);
