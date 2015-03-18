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
 * 导航链接模型
 * @author jry <598821125@qq.com>
 */
class NavlinkModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('title', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('title', '', '名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('model', 'require', '内容模型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
     * 获取指定id导航链接
     * @author jry <598821125@qq.com>
     */
    public function getNavlinkById($id){
        $map = array('id' => $id);
        return $this->where($map)->find();
    }

    /**
     * 获取所有导航链接
     * @author jry <598821125@qq.com>
     */
    public function getAllNavlink($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        $list = $this->where($map)->select();
        foreach($list as $key => $val){
            switch($val['model']){
                case 1:
                    $list[$key]['link'] = '<a target="_blank" href="'.$val['url'].'">'.$val['title'].'</a>';
                    break;
                default:
                    $list[$key]['link'] = '<a href="'.U('Navlink/detail', array('id' => $val['id'])).'">'.$val['title'].'</a>';
            }
        }
        return $list;
    }
}
