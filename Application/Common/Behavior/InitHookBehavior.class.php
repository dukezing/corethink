<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Behavior;
use Think\Behavior;
use Think\Hook;
defined('THINK_PATH') or exit();
/**
 * 初始化钩子信息
 * @author jry <598821125@qq.com>
 */
class InitHookBehavior extends Behavior{
    /**
     * 行为扩展的执行入口必须是run
     * @author jry <598821125@qq.com>
     */
    public function run(&$content){
        //安装模式下直接返回
        if(defined('BIND_MODULE') && BIND_MODULE === 'Install') return;
        $data = S('hooks');
        if(!$data){
            $hooks = D('AddonHook')->getField('name,addons');
            foreach($hooks as $key => $value){
                if($value){
                    $map['status']  =   1;
                    $names          =   explode(',',$value);
                    $map['name']    =   array('IN',$names);
                    $data = D('Addon')->where($map)->getField('id,name');
                    if($data){
                        $addons = array_intersect($names, $data);
                        Hook::add($key, array_map('get_addon_class', $addons));
                    }
                }
            }
            S('hooks', Hook::get());
        }else{
            Hook::import($data,false);
        }
    }
}
