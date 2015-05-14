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
class UserGroupController extends AdminController{
    /**
     * 部门列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition, '_multi'=>true); //搜索条件

        //获取所有部门
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('UserGroup')->where($map)->order('sort asc, id asc')->select();

        //转换成树状列表
        $tree = new \Org\Util\Tree();
        $data_list = $tree->toFormatTree($data_list);

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('部门列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/部门名称', U('index'))
                ->addField('id', 'ID', 'text')
                ->addField('title_show', '标题', 'text')
                ->addField('icon', '图标', 'icon')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->display();
    }

    /**
     * 新增部门
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $group_model = D('UserGroup');
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
            $all_group = D('Tree')->toFormatTree(D('UserGroup')->getAllGroup());
            $all_group = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级部门')), $all_group);
            $this->assign('all_group', $all_group);
            $this->meta_title = '新增部门';
            $this->display('Public/group');
        }
    }

    /**
     * 编辑部门
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $group_model = D('UserGroup');
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
            $info = D('UserGroup')->getGroupById($id);
            $info['auth'] = explode(',', $info['auth']);
            $all_group = D('Tree')->toFormatTree(D('UserGroup')->getAllGroup());
            $all_group = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级部门')), $all_group);
            $this->assign('all_group', $all_group);
            $this->assign('info', $info);
            $this->meta_title = '编辑部门';
            $this->display('Public/group');
        }
    }
}
