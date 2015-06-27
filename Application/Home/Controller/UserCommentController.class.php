<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
/**
 * 评论控制器
 * @author jry <598821125@qq.com>
 */
class UserCommentController extends HomeController{
    /**
     * 新增评论
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $this->is_login();
            $user_comment_object = D('UserComment');
            $data = $user_comment_object->create();
            if($data){
                $id = $user_comment_object->add();
                if($id){
                    D(C('TABLE_LIST.'.I('post.table')))->where(array('id'=> (int)$data['data_id']))->setInc('comment'); // 更新评论数
                    $this->success('提交成功');
                }else{
                    $this->error('提交失败');
                }
            }else{
                $this->error($user_comment_object->getError());
            }
        }
    }
}
