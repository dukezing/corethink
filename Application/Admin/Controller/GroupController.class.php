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
 * 部门控制器
 * @author jry <598821125@qq.com>
 */
class GroupController extends AdminController{
    /**
     * 部门列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition, '_multi'=>true);
        $all_group = D('Common/Tree')->toFormatTree(D('group')->getAllGroup($map));
        $this->assign('volist', $this->int_to_icon($all_group));
        $this->assign('meta_title', "部门列表");
        $this->display();
    }

    /**
     * 新增部门
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $group_model = D('Group');
            $_POST['auth']= implode(',', I('post.auth'));
            $data = $group_model->create();
            if($data){
                $id = $group_model->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($group_model->getError());
            }
        }else{
            $all_group = D('Common/Tree')->toFormatTree(D('Group')->getAllGroup());
            $all_group = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级部门')), $all_group);
            $this->assign('all_group', $all_group);
            $this->meta_title = '新增部门';
            $this->display('edit');
        }
    }

    /**
     * 编辑部门
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $group_model = D('Group');
            $_POST['auth']= implode(',', I('post.auth'));
            $data = $group_model->create();
            if($data){
                if($group_model->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($group_model->getError());
            }
        }else{
            $info = D('Group')->getGroupById($id);
            $info['auth'] = explode(',', $info['auth']);
            $all_group = D('Common/Tree')->toFormatTree(D('Group')->getAllGroup());
            $all_group = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级部门')), $all_group);
            $this->assign('all_group', $all_group);
            $this->assign('info', $info);
            $this->meta_title = '编辑部门';
            $this->display();
        }
    }
}
