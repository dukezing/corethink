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
        'value'=>'1',
    ),
    'theme'=>array(
        'title'=>'主题:',
        'type'=>'select',
        'options'=>array(
            'rocket'=>'动感火箭',
            'flat'=>'侧边客服',
        ),
        'value'=>'rocket'
    ),
    'group'=>array(
        'type'=>'group',
        'options'=>array(
            'flat'=>array(
                'title'=>'侧边客服配置',
                'options'=>array(
                    'customer'=>array(
                        'title'=>'客服中心',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'',
                    ),
                    'case'=>array(
                        'title'=>'客户案例',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'',
                    ),
                    'qq'=>array(
                        'title'=>'QQ咨询',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'',
                    ),
                    'weibo'=>array(
                        'title'=>'新浪微博',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'',
                    )
                ),
            )
        )
    )
);
