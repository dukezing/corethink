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
 * 内容模型
 * @author jry <598821125@qq.com>
 */
class ModelModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('name', 'require', '模型名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,16', '模型名称长度为1-16个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', '', '模型名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('title', 'require', '模型标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,16', '模型标题长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
        array('title', '', '模型标题已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
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
     * 根据ID获取模型
     * @author jry <598821125@qq.com>
     */
    public function getModelById($id, $field){
        $map['id'] = array('eq', $id);
        $result = $this->where($map)->find();
        if($field){
            return $result[$field];
        }
        return $result;
    }

    /**
     * 根据name获取模型
     * @author jry <598821125@qq.com>
     */
    public function getModelByName($name, $field){
        $map['name'] = array('eq', $name);
        $result = $this->where($map)->find();
        if($field){
            return $result[$field];
        }
        return $result;
    }

    /**
     * 获取所有模型
     * @author jry <598821125@qq.com>
     */
    public function getAllModel($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        return $this->where($map)->order('sort asc,id asc')->select();
    }

    /**
     * 根据条件获取模型名称
     * @author jry <598821125@qq.com>
     */
    public function getModelNameById($id){
        $model = $this->getModelById($id);
        return $model['name'];
    }
}
