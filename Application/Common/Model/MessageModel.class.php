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
 * 消息模型
 * @author jry <598821125@qq.com>
 */
class MessageModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('title','require','消息必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,1024', '消息长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('to_uid','require','收信人必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('is_read', '0', self::MODEL_INSERT),
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('sort', '0', self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 根据ID获取消息
     * @author jry <598821125@qq.com>
     */
    public function getMessageById($id){
        $map['id'] = array('eq', $id);
        return $this->where($map)->find();
    }

    /**
     * 获取所有消息
     * @author jry <598821125@qq.com>
     */
    public function getAllMessage($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        return $this->where($map)->order('sort desc,id desc')->select();
    }

    /**
     * 根据分类获取所有消息
     * @author jry <598821125@qq.com>
     */
    public function getAllMessageByType($type, $status = '0,1'){
        $map['type'] = array('eq', $type);
        $map['status'] = array('in', $status);
        $map['to_uid'] = array('eq', is_login());
        return $this->where($map)->order('sort desc,id desc')->select();
    }

    /**
     * 设置消息状态已读
     * @author jry <598821125@qq.com>
     */
    public function readMessage($ids){
        $map['id'] = array('in', $ids);
        return $this->where($map)->setField('is_read', 1);
    }

    /**
     * 发送消息
     * @author jry <598821125@qq.com>
     */
    public function sendMessage($title, $to_uid, $type = 0, $from_uid = 0){
        $data['title'] = $title;
        $data['to_uid'] = $to_uid;
        $data['type'] = $type;
        $data['from_uid'] = $from_uid;
        $result = $this->create($data);
        if($result){
            return $this->add($result);
        }
    }
}
