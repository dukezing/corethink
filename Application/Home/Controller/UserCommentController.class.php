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
            $this->login();
            preg_match_all("/([\一-\龥]){1}/u", $_POST['content'], $num);
            if(2 > count($num[0])){
                $this->error('评论至少包含2个中文字符！');
            }
            $comment = D('UserComment');
            $data = $comment->create();
            if($data){
                $id = $comment->add();
                if($id){
                    M(D('CategoryModel')->getModelNameById($data['model']))->where(array('id'=> (int)$data['doc_id']))->setInc('comment'); // 更新评论数
                    $this->success('评论成功');
                }else{
                    $this->error('评论失败');
                }
            }else{
                $this->error($comment->getError());
            }
        }
    }
}
