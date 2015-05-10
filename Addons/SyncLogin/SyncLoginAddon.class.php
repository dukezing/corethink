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
        'list_grid' => array(
            'uid:UID',
            'openid:openid',
            'type:类别',
        ),
        'model'=>'addon_sync_login',
        'fields'=>'*',
        'search_key'=>'uid',
        'map' => '',
        'order'=>'uid desc',
    );

    public function install(){
        $prefix = C("DB_PREFIX");
        $model = D();
        $model->execute("DROP TABLE IF EXISTS {$prefix}addon_sync_login;");
        $model->execute("CREATE TABLE {$prefix}addon_sync_login ( `uid` int(11) NOT NULL,  `openid` varchar(64) NOT NULL,  `type` varchar(4) NOT NULL,  `access_token` varchar(64) NOT NULL,  `refresh_token` varchar(64) NOT NULL  )");
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
