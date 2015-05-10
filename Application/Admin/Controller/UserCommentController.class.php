<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Think\Controller;
/**
 * 后台评论控制器
 * @author jry <598821125@qq.com>
 */
class UserCommentController extends AdminController{
    /**
     * 评论列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|content'] = array($condition, $condition,'_multi'=>true);
        $all_menu = D('UserComment')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllComment($map);
        $page = new \Think\Page(D('UserComment')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('volist', $this->int_to_icon($all_menu));
        $this->assign('page', $page->show());
        $this->assign('meta_title', "评论列表");
        $this->display();
    }

    /**
     * 新增评论
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $comment = D('UserComment');
            $data = $comment->create();
            if($data){
                $id = $comment->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($comment->getError());
            }
        }else{
            $this->assign('all_model', D('CategoryModel')->getAllModel());
            $this->meta_title = '新增评论';
            $this->display('edit');
        }
    }

    /**
     * 编辑评论
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $comment = D('UserComment');
            $data = $comment->create();
            if($data){
                if($comment->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($comment->getError());
            }
        }else{
            $this->assign('info', D('UserComment')->getCommentById($id));
            $this->assign('all_model', D('CategoryModel')->getAllModel());
            $this->meta_title = '编辑评论';
            $this->display();
        }
    }
}
