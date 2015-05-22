<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\Email\Controller;
use Home\Controller\AddonController;
/**
 * 邮件控制器
 * @author jry <598821125@qq.com>
 */
class EmailController extends AddonController{
    /**
     * 系统邮件发送函数
     * @param string $receiver 收件人
     * @param string $subject 邮件主题
     * @param string $body 邮件内容
     * @param string $attachment 附件列表
     * @return boolean
     * @author jry <598821125@qq.com>
     */
    function sendMail($receiver, $subject = '', $body = '', $attachment = null){
        $addon_config = \Common\Controller\Addon::getConfig('Email');
        if($addon_config['status']){
            if(1 == $addon_config['MAIL_SMTP_TYPE']){
                $mailHeader = "MIME-Version: 1.0" . "\r\n";
                $mailHeader .= "Content-type:text/html;charset=utf-8" . "\r\n";
                $mailHeader .= 'From: '. C('WEB_SITE_TITLE') .'<'. $addon_config['MAIL_SMTP_USER'] .'>' . "\r\n";
                return mail($receiver, $subject, $body, $mailHeader) ? true : "邮件发送错误";
            }else{
                $mail_body_template = $addon_config['default']; //获取邮件模版配置
                $mail_body = str_replace("[MAILBODY]", $body, $mail_body_template); //使用邮件模版
                $mail = new \Common\Util\Email();
                $mail->setServer($addon_config['MAIL_SMTP_HOST'], $addon_config['MAIL_SMTP_USER'], $addon_config['MAIL_SMTP_PASS'], $addon_config['MAIL_SMTP_PORT'], $addon_config['MAIL_SMTP_SECURE']); //设置smtp服务器
                $mail->setFrom($addon_config['MAIL_SMTP_USER']); //设置发件人
                $mail->setReceiver($receiver); //设置收件人，多个收件人，调用多次
                $mail->setMail($subject.'｜'. C('WEB_SITE_TITLE'), $mail_body); //设置邮件主题、内容
                return $mail->sendMail() ? true : false; //发送
            }
        }else{
            return false;
        }
    }
}
