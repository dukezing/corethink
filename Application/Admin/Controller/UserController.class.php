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
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|username|email|mobile'] = array($condition, $condition, $condition, $condition,'_multi'=>true);

        //获取所有用户
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('User')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort desc,id desc')->select();
        $page = new \Common\Util\Page(D('User')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('用户列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/用户名/邮箱/手机号', U('index'))
                ->addField('id', 'UID', 'text')
                ->addField('username', '用户名', 'text')
                ->addField('email', '邮箱', 'text')
                ->addField('mobile', '手机号', 'text')
                ->addField('score', '积分', 'text')
                ->addField('money', '余额', 'text')
                ->addField('last_login_time', '最后登录时间时间', 'time')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->setPage($page->show())
                ->display();
    }

    /**
     * 新增用户
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $user_object = D('User');
            $data = $user_object->create();
            if($data){
                $id = $user_object->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($user_object->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('新增用户')  //设置页面标题
                    ->setUrl(U('add')) //设置表单提交地址
                    ->addItem('group', 'select', '部门', '所属部门', $this->selectListAsTree('UserGroup', null, '默认部门'))
                    ->addItem('username', 'text', '用户名', '用户名')
                    ->addItem('email', 'text', '邮箱', '邮箱')
                    ->addItem('mobile', 'text', '手机号码', '手机号码')
                    ->addItem('password', 'password', '密码', '密码')
                    ->addItem('avatar', 'picture', '用户头像', '用户头像')
                    ->display();
        }
    }

    /**
     * 编辑用户
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $user_object = D('User');
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
            if($user_object->save($_POST)){
                $this->success('更新成功', U('index'));
            }else{
                $this->error('更新失败', $user_object->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('编辑用户')  //设置页面标题
                    ->setUrl(U('edit')) //设置表单提交地址
                    ->addItem('id', 'hidden', 'ID', 'ID')
                    ->addItem('group', 'select', '部门', '所属部门', $this->selectListAsTree('UserGroup', null, '默认部门'))
                    ->addItem('username', 'text', '用户名', '用户名')
                    ->addItem('email', 'text', '邮箱', '邮箱')
                    ->addItem('mobile', 'text', '手机号码', '手机号码')
                    ->addItem('password', 'password', '密码', '密码')
                    ->addItem('avatar', 'picture', '用户头像', '用户头像')
                    ->setFormData(D('User')->find($id))
                    ->display();
        }
    }
}
