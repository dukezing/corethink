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
 * 评论模型
 * @author jry <598821125@qq.com>
 */
class UserCommentModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('type', 'require', '内容模型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('doc_id', 'require', '文档ID不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '评论内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', '1,1280', '评论内容长度不多于1280个字符', self::VALUE_VALIDATE, 'length'),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('uid', 'is_login', self::MODEL_INSERT, 'function'),
        array('content', 'html2text', self::MODEL_BOTH, 'function'),
        array('ctime', 'time', self::MODEL_INSERT, 'function'),
        array('utime', 'time', self::MODEL_BOTH, 'function'),
        array('sort', '0', self::MODEL_INSERT),
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
    );

    /**
     * 根据ID获取评论
     * @author jry <598821125@qq.com>
     */
    public function getCommentById($id, $field){
        $map['id'] = array('eq', $id);
        $comment_info = $this->where($map)->find();
        if($field){
            return $comment_info[$field];
        }
        return $comment_info;
    }

    /**
     * 获取所有评论
     * @author jry <598821125@qq.com>
     */
    public function getAllComment($map, $status = '0,1'){
        $map['status'] = array('in', $status);
        return $this->where($map)->order('sort desc,id desc')->select();
    }

    /**
     * 根据文档获取评论列表
     * @author jry <598821125@qq.com>
     */
    public function getAllCommentByDocument($model, $doc_id, $map){
        $map['model'] = $model;
        $map['doc_id'] = $doc_id;
        $map['status'] = 1;
        $comments = $this->where($map)->order('sort desc,id asc')->select();
        foreach($comments as $key => $val){
            $comments[$key]['ctime'] = friendly_date($val['ctime']);
            $comments[$key]['username'] = get_user_info($val['uid'], 'username');
            $comments[$key]['avatar'] = get_user_info($val['uid'], 'avatar');
            if($comments[$key]['pid'] > 0){
                $parent_comment = $this->find($comments[$key]['pid']);
                $comments[$key]['parent_comment_username'] = get_user_info($parent_comment['uid'], 'username');
            }
        }
        return $comments;
    }
}
