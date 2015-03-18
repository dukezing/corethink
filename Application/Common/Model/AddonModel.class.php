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
    public function getAllAddon($addon_dir = ''){
        if(!$addon_dir)
            $addon_dir = THINK_ADDON_PATH;
        $dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        $addons           =    array();
        $where['name']    =    array('in',$dirs);
        $list             =    $this->where($where)->field(true)->order('sort asc,id desc')->select();
        foreach($list as $addon){
            $addon['uninstall']        =    0;
            $addons[$addon['name']]    =    $addon;
        }
        foreach ($dirs as $value) {
            if(!isset($addons[$value])){
                $class = get_addon_class($value);
                if(!class_exists($class)){ // 实例化插件失败忽略执行
                    \Think\Log::record('插件'.$value.'的入口文件不存在！');
                    continue;
                }
                $obj = new $class;
                $addons[$value] = $obj->info;
                if($addons[$value]){
                    $addons[$value]['uninstall'] = 1;
                    unset($addons[$value]['status']);
                }
            }
        }
        return $addons;
    }

    /**
     * 获取插件的后台列表
     * @author jry <598821125@qq.com>
     */
    public function getAdminList(){
        $admin = array();
        $db_addons = $this->where("status=1 AND adminlist=1")->field('title,name')->select();
        if($db_addons){
            foreach ($db_addons as $value) {
                $admin[] = array('title'=>$value['title'],'url'=>"Addons/adminList?name={$value['name']}");
            }
        }
        return $admin;
    }

    /**
     * 插件显示内容里生成访问插件的url
     * @param string $url url
     * @param array $param 参数
     * @author jry <598821125@qq.com>
     */
    function getAddonUrl($url, $param = array()){
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

    /**
     * 解析插件数据列表定义规则
     * @author jry <598821125@qq.com>
     */
    public function getAddonAdminlistField($data, $grid,$addon){
        // 获取当前字段数据
        foreach($grid['field'] as $field){
            $array  =   explode('|',$field);
            $temp  =    $data[$array[0]];
            // 函数支持
            if(isset($array[1])){
                $temp = call_user_func($array[1], $temp);
            }
            $data2[$array[0]]    =   $temp;
        }
        if(!empty($grid['format'])){
            $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
        }else{
            $value  =   implode(' ',$data2);
        }

        // 链接支持
        if(!empty($grid['href'])){
            $links  =   explode(',',$grid['href']);
            foreach($links as $link){
                $array  =   explode('|',$link);
                $href   =   $array[0];
                if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                    $val[]  =   $data2[$matches[1]];
                }else{
                    $show   =   isset($array[1])?$array[1]:$value;
                    // 替换系统特殊字符串
                    $href   =   str_replace(
                        array('[DELETE]','[EDIT]','[ADDON]'),
                        array('del?ids=[id]&name=[ADDON]','edit?id=[id]&name=[ADDON]',$addon),
                        $href);
                    // 替换数据变量
                    $href   =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);
                    $val[]  =   '<a href="'.U($href).'">'.$show.'</a>';
                }
            }
            $value  =   implode(' ',$val);
        }
        return $value;
    }
}
