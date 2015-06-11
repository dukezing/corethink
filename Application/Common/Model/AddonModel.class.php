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
class AddonModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('name', 'require', '插件名称不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,32', '插件名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', '', '插件名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('description','require','钩子描述必须！', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
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
     * 获取插件列表
     * @param string $addon_dir
     * @author jry <598821125@qq.com>
     */
    public function getAllAddon($addon_dir = THINK_ADDON_PATH){
        $dirs = array_map('basename', glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        $addons           =    array();
        $map['name']    =    array('in', $dirs);
        $list             =    $this->where($map)->field(true)->order('sort asc,id desc')->select();
        foreach($list as $addon){
            $addons[$addon['name']]    =    $addon;
        }
        foreach ($dirs as $value){
            if(!isset($addons[$value])){
                $class = get_addon_class($value);
                if(!class_exists($class)){ // 实例化插件失败忽略执行
                    \Think\Log::record('插件'.$value.'的入口文件不存在！');
                    continue;
                }
                $obj = new $class;
                $addons[$value] = $obj->info;
                if($addons[$value]){
                    $addons[$value]['status'] = -1; //未安装
                }
            }
        }
        foreach($addons as &$val){
            switch($val['status']){
                case '-1': //未安装
                    $val['status'] = '<i class="icon-trash" style="color:red"></i>';
                    $val['right_button']  = '<a class="ajax-get" href="'.U('install?addon_name='.$val['name']).'">安装</a>';
                    break;
                case '0': //禁用
                    $val['status'] = '<i class="icon-ban-circle" style="color:red"></i>';
                    $val['right_button']  = '<a href="'.U('config',array('id'=>$val['id'])).'">设置</a> ';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('setStatus',array('status'=>'resume', 'ids' => $val['id'])).'">启用</a> ';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('uninstall?id='.$val['id']).'">卸载</a> ';
                    if($val['adminlist']){
                        $val['right_button'] .= '<a href="'.U('adminlist',array('name'=>$val['name'])).'">管理</a>';
                    }
                    break;
                case '1': //正常
                    $val['status'] = '<i class="icon-ok" style="color:green"></i>';
                    $val['right_button']  = '<a href="'.U('config',array('id'=>$val['id'])).'">设置</a> ';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('setStatus',array('status'=>'forbid', 'ids' => $val['id'])).'">禁用</a> ';
                    $val['right_button'] .= '<a class="ajax-get" href="'.U('uninstall?id='.$val['id']).'">卸载</a> ';
                    if($val['adminlist']){
                        $val['right_button'] .= '<a href="'.U('adminlist',array('name'=>$val['name'])).'">管理</a>';
                    }
                    break;
            }
        }
        return $addons;
    }

    /**
     * 插件显示内容里生成访问插件的url
     * @param string $url url
     * @param array $param 参数
     * @author jry <598821125@qq.com>
     */
    public function getAddonUrl($url, $param = array()){
        $url        = parse_url($url);
        $case       = C('URL_CASE_INSENSITIVE');
        $addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
        $controller = $case ? parse_name($url['host']) : $url['host'];
        $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');
        /* 解析URL带的参数 */
        if(isset($url['query'])){
            parse_str($url['query'], $query);
            $param = array_merge($query, $param);
        }
        /* 基础参数 */
        $params = array(
            '_addons'     => $addons,
            '_controller' => $controller,
            '_action'     => $action,
        );
        $params = array_merge($params, $param); //添加额外参数
        return U('Addon/execute', $params);
    }
}
