<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\AdFloat;
use Common\Controller\Addon;
/**
 * 两侧浮动广告插件
 * @author jry <598821125@qq.com>
 */
class AdFloatAddon extends Addon{
    public $info = array(
        'name'=>'AdFloat',
        'title'=>'图片漂浮广告',
        'description'=>'图片漂浮广告',
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
        $config = $this->getConfig();
        if($config['status']){
            $this->assign('config', $config);
            $this->display('content');
        }
    }
}
