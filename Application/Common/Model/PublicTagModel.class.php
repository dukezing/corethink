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
 * 标签模型
 * @author jry <598821125@qq.com>
 */
class PublicTagModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('title','require','标签必须填写', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '标签长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('title', '', '标签已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 根据ID获取标签
     * @author jry <598821125@qq.com>
     */
    public function getTagById($id, $field){
        $map['id'] = array('eq', $id);
        $tag_info = $this->where($map)->find();
        if($field){
            return $tag_info[$field];
        }
        return $tag_info;
    }

    /**
     * 获取所有标签
     * @author jry <598821125@qq.com>
     */
    public function getAllTag($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        return $this->where($map)->order('sort desc,id desc')->select();
    }
}
