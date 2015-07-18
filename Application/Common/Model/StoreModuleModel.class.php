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
use Think\Storage;
/**
 * 功能模块模型
 * @author jry <598821125@qq.com>
 */
class StoreModuleModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('name', 'require', '模块名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', 'require', '模块标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('description', 'require', '模块描述不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('developer', 'require', '模块开发者不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('version', 'require', '模块版本不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('icon', 'require', '模块图标不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('admin_menu', 'require', '模块菜单节点不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
     * 获取模块列表
     * @param string $addon_dir
     * @author jry <598821125@qq.com>
     */
    public function getAllModule(){
        //获取除了Common等系统模块外的用户模块（文件夹下必须有corethink.php）
        $dirs = array_map('basename', glob(APP_PATH.'*', GLOB_ONLYDIR));
        foreach($dirs as $dir){
            $config_file = realpath(APP_PATH.$dir).'/corethink.php';
            if(Storage::has($config_file)){
                $module_dir_list[] = $dir;
                $temp_arr = include $config_file;
                $temp_arr['info']['status'] = -1; //未安装
                $module_list[$temp_arr['info']['name']] = $temp_arr['info'];
            }
        }

        //获取系统已经安装的模块信息
        if($module_dir_list){
            $map['name'] = array('in', $module_dir_list);
        }
        $installed_module_list = $this->where($map)->field(true)->order('sort asc,id desc')->select();
        if($installed_module_list){
            foreach($installed_module_list as $module){
                $module_list[$module['name']] = $module;
            }
            //系统已经安装的模块信息与文件夹下模块信息合并
            $module_list = array_merge($module_list, $module_list);
        }

        foreach($module_list as &$val){
            switch($val['status']){
                case '-1': //未安装
                    $val['status'] = '<i class="glyphicon glyphicon-download-alt" style="color:green"></i>';
                    $val['right_button']  = '<a class="ajax-get" href="'.U('install?name='.$val['name']).'">安装</a>';
                    break;
                case '0': //禁用
                    $val['status'] = '<i class="glyphicon glyphicon-ban-circle" style="color:red"></i>';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('updateModuleInfo?id='.$val['id']).'">更新菜单</a> ';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('setStatus', array('status' => 'resume', 'ids' => $val['id'])).'">启用</a> ';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('setStatus', array('status' => 'uninstall', 'ids' => $val['id'])).'">卸载</a> ';
                    break;
                case '1': //正常
                    $val['status'] = '<i class="glyphicon glyphicon-ok" style="color:green"></i>';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('updateModuleInfo?id='.$val['id']).'">更新菜单</a> ';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('setStatus', array('status' => 'forbid', 'ids' => $val['id'])).'">禁用</a> ';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('setStatus', array('status' => 'uninstall', 'ids' => $val['id'])).'">卸载</a> ';
                    break;
            }
        }
        return $module_list;
    }
}
