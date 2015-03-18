<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\Email;
use Common\Controller\Addon;
/**
 * 邮件插件
 * @author jry <598821125@qq.com>
 */
class EmailAddon extends Addon{
    public $info = array(
        'name' => 'Email',
        'title' => '邮件插件',
        'description' => '实现系统发邮件功能',
        'status' => 1,
        'author' => 'CoreThink',
        'version' => '1.0'
    );

    public function install(){
        $prefix = C("DB_PREFIX");
        $model = D();
        $model->execute("DROP TABLE IF EXISTS {$prefix}email;");
        return true;
    }

    public function uninstall(){
        $prefix = C("DB_PREFIX");
        $model->execute("DROP TABLE IF EXISTS {$prefix}email;");
        return true;
    }
}
