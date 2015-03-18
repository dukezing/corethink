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
        'title'=>'是否开启邮件:',
        'type'=>'radio',
        'options'=>array(
            '1'=>'开启',
            '0'=>'关闭',
        ),
        'value'=>'1',
    ),
    'group'=>array(
        'type'=>'group',
        'options'=>array(
            'server'=>array(
                'title'=>'发信设置',
                'options'=>array(
                    'MAIL_SMTP_TYPE'=>array(
                        'title'=>'邮件发信类型：',
                        'type'=>'select',
                        'options'=>array(
                            '1'=>'内置函数发送',
                            '2'=>'SMTP模块发送',
                        ),
                        'value'=>'1',
                        'tip'=>'邮件发信类型',
                    ),
                    'MAIL_SMTP_SECURE'=>array(
                        'title'=>'安全协议类型：',
                        'type'=>'select',
                        'options'=>array(
                            '0'=>' 不使用 ',
                            'ssl'=>'SSL',
                        ),
                        'value'=>'0',
                        'tip'=>'安全协议类型',
                    ),
                    'MAIL_SMTP_PORT'=>array(
                        'title'=>'SMTP服务器端口：',
                        'type'=>'text',
                        'value'=>'25',
                        'tip'=>'普通端口一般为25，SSL端口为465',
                    ),
                    'MAIL_SMTP_HOST'=>array(
                        'title'=>'SMTP服务器地址：',
                        'type'=>'text',
                        'value'=>'smtp.qq.com',
                        'tip'=>'邮箱服务器名称[如：smtp.qq.com]',
                    ),
                    'MAIL_SMTP_USER'=>array(
                        'title'=>'SMTP服务器用户名：',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'SMTP服务器用户名',
                    ),
                    'MAIL_SMTP_PASS'=>array(
                        'title'=>'SMTP服务器密码：',
                        'type'=>'password',
                        'value'=>'',
                        'tip'=>'SMTP服务器密码',
                    ),
                ),
             ),
            'template'=>array(
                'title'=>'发信模版',
                'options'=>array(
                    'default'=>array(
                        'title'=>'默认发信模版：',
                        'type'=>'kindeditor',
                        'value'=>'',
                        'tip'=>'默认发信模版',
                    )
                )
            )
        )
    )
);
