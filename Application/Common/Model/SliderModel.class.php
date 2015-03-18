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
 * 幻灯片模型
 * @author jry <598821125@qq.com>
 */
class SliderModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('title', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('title', '', '名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('cover', 'require', ' 图片不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
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
     * 根据ID获取幻灯片
     * @author jry <598821125@qq.com>
     */
    public function getSliderById($id){
        $map['id'] = array('eq', $id);
        return $this->where($map)->find();
    }

    /**
     * 获取所有幻灯片
     * @author jry <598821125@qq.com>
     */
    public function getAllSlider($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        return $this->where($map)->order('sort desc,id desc')->select();
    }

    /**
     * 根据分组获取不同级别的幻灯片
     * @author jry <598821125@qq.com>
     */
    public function getSliderByGroup($group = 0, $status = '0,1'){
        $map['status'] = array('in', $status);
        $map['group']  = array('eq', $group);
        return $this->where($map)->order('sort desc,id desc')->select();
    }
}
