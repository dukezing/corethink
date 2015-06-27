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
        array('table', 'require', '数据表ID不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('data_id', 'require', '数据ID不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '内容不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', '1,1280', '内容长度不多于1280个字符', self::VALUE_VALIDATE, 'length'),
        array('content', 'checkContent', '至少包含2个中文字符', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
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
     * 验证评论内容
     * @author jry <598821125@qq.com>
     */
    public function checkContent($map){
        preg_match_all("/([\一-\龥]){1}/u", $_POST['content'], $num);
        if(2 > count($num[0])){
            return false;
        }
        return true;
    }

    /**
     * 根据条件获取评论列表
     * @author jry <598821125@qq.com>
     */
    public function getCommentList($map){
        $map['status'] = 1;
        $comments = $this->where($map)->order('sort desc,id asc')->select();
        foreach($comments as $key => $val){
            $comments[$key]['ctime'] = friendly_date($val['ctime']);
            $comments[$key]['username'] = D('User')->getFieldById($val['uid'], 'username');
            $comments[$key]['avatar'] = D('User')->getFieldById($val['uid'], 'avatar');
            if($comments[$key]['pictures']){
                $comments[$key]['pictures'] = explode(',', $comments[$key]['pictures']); //解析图片列表
            }
            if($comments[$key]['pid'] > 0){
                $parent_comment = $this->find($comments[$key]['pid']);
                $comments[$key]['parent_comment_username'] = D('User')->getFieldById($parent_comment['uid'], 'username');
            }
        }
        return $comments;
    }
}
