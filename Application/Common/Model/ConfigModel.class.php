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
 * 配置模型
 * @author jry <598821125@qq.com>
 */
class ConfigModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('name', 'require', '配置名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,32', '配置名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', '', '配置名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('title','require','配置标题必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '配置标题长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('title', '', '配置标题已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('sort', '0', self::MODEL_INSERT),
        array('status', '1', self::MODEL_BOTH),
    );

    /**
     * 根据ID获取配置
     * @author jry <598821125@qq.com>
     */
    public function getConfigById($id){
        $map['id'] = array('eq', $id);
        return $this->where($map)->find();
    }

    /**
     * 获取所有配置
     * @author jry <598821125@qq.com>
     */
    public function getAllConfig($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        return $this->where($map)->order('id desc')->select();
    }

    /**
     * 根据分组获取所有配置
     * @author jry <598821125@qq.com>
     */
    public function getAllConfigByGroup($group, $status = '0,1'){
        $map['status'] = array('in', $status);
        $map['group']  = array('eq', $group);
        $list = $this->where($map)->order('sort asc,id asc')->select();
        foreach($list as $key => $val){
            $list[$key]['options'] = $this->parse_attr($val['options']);
        }
        return $list;
    }

    /**
     * 获取配置列表与ThinkPHP配置合并
     * @return array 配置数组
     * @author jry <598821125@qq.com>
     */
    public function lists(){
        $map  = array('status' => 1);
        $list = $this->where($map)->field('name,value,type')->select();
        foreach ($list as $key => $val){
            if($val['type'] === 'array'){ //数组类型需要解析配置的value
                $config[$val['name']] = $this->parse_attr($val['value']);
            }else{
                $config[$val['name']] = $val['value'];
            }
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param  string $type  配置类型
     * @param  string  $value 配置值
     * @author jry <598821125@qq.com>
     */
    public function parse_attr($value, $type){
        switch ($type) {
            default: //解析"1:1\r\n2:3"格式字符串为数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if(strpos($value,':')){
                    $value  = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = $v;
                    }
                }else{
                    $value = $array;
                }
                break;
        }
        return $value;
    }
}
