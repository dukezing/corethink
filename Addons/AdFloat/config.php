<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
return array(
    'status'=>array(
        'title'=>'是否开启:',
        'type'=>'radio',
        'options'=>array(
            '1'=>'开启',
            '0'=>'关闭',
        ),
        'value'=>'0',
    ),
    'url'=>array(
        'title'=>'广告链接:',
        'type'=>'text',
        'value'=>'http://www.corethink.cn'
    ),
    'image'=>array(
        'title' => '漂浮图片:',
        'type'  => 'picture',
        'value' => ''
    ),
    'width'=>array(
        'title'=>'宽度:（单位：像素），默认为100',
        'type'=>'text',
        'value'=>'100'
    ),
    'height'=>array(
        'title'=>'高度:（单位：像素），默认为100',
        'type'=>'text',
        'value'=>'100'
    ),
    'speed'=>array(
        'title'=>'漂浮速度:（单位：毫秒），默认为10',
        'type'=>'text',
        'value'=>'10'
    ),
    'target'=>array(
        'title'=>'链接打开方式:',
        'type'=>'radio',
        'options'=>array(
            '0'=>'当前页打开',
            '1'=>'新窗口打开',
        ),
        'value'=>'1',
    ),
);
