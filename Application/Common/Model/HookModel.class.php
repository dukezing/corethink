<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Model;
use Think\Model;
/**
 * 插件模型
 * @author jry <598821125@qq.com>
 */
class HookModel extends Model {
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('name','require','钩子名称必须！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,32', '钩子名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', '', '钩子名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('description','require','钩子描述必须！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('sort', '0', self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 更新插件里的所有钩子对应的插件
     * @author jry <598821125@qq.com>
     */
    public function updateHooks($addons_name){
        $addons_class = get_addon_class($addons_name);//获取插件名
        if(!class_exists($addons_class)){
            $this->error = "未实现{$addons_name}插件的入口文件";
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->getField('name', true);
        $common = array_intersect($hooks, $methods);
        if(!empty($common)){
            foreach ($common as $hook) {
                $flag = $this->updateAddons($hook, array($addons_name));
                if(false === $flag){
                    $this->removeHooks($addons_name);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 更新单个钩子处的插件
     * @author jry <598821125@qq.com>
     */
    public function updateAddons($hook_name, $addons_name){
        $o_addons = $this->where("name='{$hook_name}'")->getField('addons');
        if($o_addons)
            $o_addons = explode(',', $o_addons);
        if($o_addons){
            $addons = array_merge($o_addons, $addons_name);
            $addons = array_unique($addons);
        }else{
            $addons = $addons_name;
        }
        $flag = D('Hook')->where("name='{$hook_name}'")
        ->setField('addons',implode(',', $addons));
        if(false === $flag)
            D('Hook')->where("name='{$hook_name}'")->setField('addons',implode(',', $o_addons));
        return $flag;
    }

    /**
     * 去除插件所有钩子里对应的插件数据
     * @author jry <598821125@qq.com>
     */
    public function removeHooks($addons_name){
        $addons_class = get_addon_class($addons_name);
        if(!class_exists($addons_class)){
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->getField('name', true);
        $common = array_intersect($hooks, $methods);
        if($common){
            foreach ($common as $hook) {
                $flag = $this->removeAddons($hook, array($addons_name));
                if(false === $flag){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 去除单个钩子里对应的插件数据
     * @author jry <598821125@qq.com>
     */
    public function removeAddons($hook_name, $addons_name){
        $o_addons = $this->where("name='{$hook_name}'")->getField('addons');
        $o_addons = explode(',', $o_addons);
        if($o_addons){
            $addons = array_diff($o_addons, $addons_name);
        }else{
            return true;
        }
        $flag = D('Hook')->where("name='{$hook_name}'")
                          ->setField('addons',implode(',', $addons));
        if(false === $flag)
            D('Hook')->where("name='{$hook_name}'")
                      ->setField('addons',implode(',', $o_addons));
        return $flag;
    }
}
