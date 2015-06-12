<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\SyncLogin;
use Common\Controller\Addon;
/**
 * 同步登陆插件
 * @author jry <598821125@qq.com>
 */
class SyncLoginAddon extends Addon{
    public $info = array(
        'name' => 'SyncLogin',
        'title' => '第三方账号登陆',
        'description' => '第三方账号登陆',
        'status' => 1,
        'author' => 'CoreThink',
        'version' => '0.1'
    );

    public $admin_list = array(
        '1' => array(
            'title' => '第三方登录列表',
            'model' => 'sync_login',
        )
    );

    public function install(){
        $prefix = C("DB_PREFIX");
        $sql = <<<sql
            DROP TABLE IF EXISTS {$prefix}addon_sync_login;
            CREATE TABLE `ct_addon_sync_login` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
                `uid` int(11) unsigned NOT NULL COMMENT '用户ID',
                `type` varchar(15) NOT NULL DEFAULT '' COMMENT '类别',
                `openid` varchar(64) NOT NULL DEFAULT '' COMMENT 'OpenID',
                `access_token` varchar(64) NOT NULL DEFAULT '' COMMENT 'AccessToken',
                `refresh_token` varchar(64) NOT NULL DEFAULT '' COMMENT 'RefreshToken',
                `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
                `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
                `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
                `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='第三方登陆插件表';
sql;
        M()->execute(sql);
        return true;
    }

    public function uninstall(){
        $prefix = C("DB_PREFIX");
        $model->execute("DROP TABLE IF EXISTS {$prefix}addon_sync_login;");
        return true;
    }

    //登录按钮钩子
    public function SyncLogin($param){
        $this->assign($param);
        $config = $this->getConfig();
        $this->assign('config',$config);
        $this->display('login');
    }

    /**
     * meta代码钩子
     * @param $param
     */
    public function PageHeader($param){
        $platform_options = $this->getConfig();
        echo $platform_options['meta'];
    }
}
