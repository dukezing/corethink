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
 * 后台用户控制器
 * @author jry <598821125@qq.com>
 */
class UserController extends AdminController{
    /**
     * 用户列表
     * @author jry <598821125@qq.com>
     */
    public function index($status = '0,1'){
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|username|email|mobile'] = array($condition, $condition, $condition, $condition,'_multi'=>true);
        $all_user = D('User')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllUser($map, $status);
        $page = new \Think\Page(D('User')->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('page', $page->show());
        $this->assign('volist', $this->int_to_icon($all_user));
        $this->assign('meta_title', "用户列表");
        $this->display();
    }

    /**
     * 新增用户
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $user = D('User');
            $data = $user->create();
            if($data){
                $id = $user->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($user->getError());
            }
        }else{
            $all_group = D('Tree')->toFormatTree(D('UserGroup')->getAllGroup());
            $all_group = array_merge(array(0 => array('id'=>0, 'title_show'=>'游荡中')), $all_group);
            $this->assign('all_group', $all_group);
            $this->meta_title = '新增用户';
            $this->display('edit');
        }
    }

    /**
     * 编辑用户
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $user = D('User');
            //不修改密码时销毁变量
            if($_POST['password'] == ''){
                unset($_POST['password']);
            }else{
                $_POST['password'] = user_md5($_POST['password']);
            }
            //不允许更改超级管理员用户组
            if($_POST['id'] == 1){
                unset($_POST['group']);
            }
            if($_POST['extend']){
                $_POST['extend'] = json_encode($_POST['extend']);
            }
            if($user->save($_POST)){
                $user->updateUserCache($_POST['id']);
                $this->success('更新成功', U('index'));
            }else{
                $this->error('更新失败', $user->getError());
            }
        }else{
            $all_group = D('Tree')->toFormatTree(D('UserGroup')->getAllGroup());
            $all_group = array_merge(array(0 => array('id'=>0, 'title_show'=>'游荡中')), $all_group);
            $this->assign('all_group', $all_group);
            $this->assign('info', D('User')->getUserById($id));
            $this->meta_title = '编辑用户';
            $this->display();
        }
    }
}
