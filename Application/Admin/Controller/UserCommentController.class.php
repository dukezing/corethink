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
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|content'] = array($condition, $condition,'_multi'=>true);

        //获取所有评论
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('UserComment')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort desc,id desc')->select();
        $page = new \Think\Page(D('UserComment')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('评论列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/评论关键字', U('index'))
                ->addField('id', 'ID', 'text')
                ->addField('content', '评论', 'text')
                ->addField('ctime', '创建时间', 'time')
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
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('新增评论')  //设置页面标题
                    ->setUrl(U('add')) //设置表单提交地址
                    ->addItem('select', '内容模型', '内容模型', 'model', $this->selectListAsTree('CategoryModel'))
                    ->addItem('num', '文档ID', '文档ID', 'title')
                    ->addItem('textarea', '评论内容', '评论内容', 'content')
                    ->addItem('num', '父评论ID', '父评论ID', 'pid')
                    ->addItem('num', '排序', '用于显示的顺序', 'sort')
                    ->display();
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
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('编辑评论')  //设置页面标题
                    ->setUrl(U('edit')) //设置表单提交地址
                    ->addItem('hidden', 'ID', 'ID', 'id')
                    ->addItem('select', '内容模型', '内容模型', 'model', $this->selectListAsTree('CategoryModel'))
                    ->addItem('num', '文档ID', '文档ID', 'title')
                    ->addItem('textarea', '评论内容', '评论内容', 'content')
                    ->addItem('num', '父评论ID', '父评论ID', 'pid')
                    ->addItem('num', '排序', '用于显示的顺序', 'sort')
                    ->setFormData(D('UserComment')->find($id))
                    ->display();
        }
    }
}
