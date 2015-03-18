<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\ReturnTop;
use Common\Controller\Addon;
/**
 * 返回顶部插件
 * @jry <598821125@qq.com>
 */
class ReturnTopAddon extends Addon{
    public $info = array(
        'name'=>'ReturnTop',
        'title'=>'返回顶部',
        'description'=>'返回顶部',
        'status'=>1,
        'author'=>'CoreThink',
        'version'=>'1.0'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的PageFooter钩子方法
    public function PageFooter($param){
        $addons_config = $this->getConfig();
        if($addons_config['status']){
            $this->assign('addons_config', $addons_config);
            $this->display($addons_config['theme']);
        }
    }
}
