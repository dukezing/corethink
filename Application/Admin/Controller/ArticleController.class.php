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
 * 后台文章控制器
 * @author jry <598821125@qq.com>
 */
class ArticleController extends AdminController{
    /**
     * 文章列表
     * @author jry <598821125@qq.com>
     */
    public function index($cid = 0){
        if($cid != 0){
            $map['cid'] = array('eq', $cid);
            $category = D('Category')->getCategoryById($cid);
        }

        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition, '_multi'=>true);

        //获取所有文档
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('Article')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort desc,id desc')->select();
        $page = new \Think\Page(D('Article')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title($category['title'])  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addRecycleButton() //添加回收按钮
                ->setSearch('请输入ID/文档名称', U('index'))
                ->addField('id', 'UID', 'text')
                ->addField('title', '标题', 'text')
                ->addField('ctime', '发布时间', 'time')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('recycle') //添加回收按钮
                ->setPage($page->show())
                ->setMove(true)
                ->display();
    }

    /**
     * 新增文章
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $article_model = D('Article');
            $data = $article_model->create();
            if($data){
                $id = $article_model->add();
                if($id){
                    $this->success('新增成功', U('index', array('cid' => $_POST['cid'])));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($article_model->getError());
            }
        }else{
            //获取当前分类
            $info['cid'] = I('get.cid');

            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('新增文章')  //设置页面标题
                    ->setUrl(U('add')) //设置表单提交地址
                    ->addItem('select', '分类', '文章所属的分类', 'cid', $this->selectListAsTree('Category'))
                    ->addItem('text', '文章标题', '文章标题', 'title')
                    ->addItem('textarea', '文章摘要', '文章摘要', 'abstract')
                    ->addItem('tag', '标签', '文章标签', 'tags')
                    ->addItem('kindeditor', '文章内容', '文章内容', 'content')
                    ->addItem('picture', '封面图片', '文章封面图片', 'cover')
                    ->addItem('time', '发布日期', '文章发布日期，默认当前时间', 'ctime')
                    ->addItem('num', '排序', '用于显示的顺序', 'sort')
                    ->setFormData($info)
                    ->display();
        }
    }

    /**
     * 编辑文章
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            //更新文章
            $article_model = D('Article');
            $data = $article_model->create();
            if($data){
                if($article_model->save()!== false){
                    $this->success('更新成功', Cookie('__forward__') ? : U('index', array('cid' => $_POST['cid'])));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($article_model->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('编辑文章')  //设置页面标题
                    ->setUrl(U('edit')) //设置表单提交地址
                    ->addItem('hidden', 'ID', 'ID', 'id')
                    ->addItem('select', '分类', '文章所属的分类', 'cid', $this->selectListAsTree('Category'))
                    ->addItem('text', '文章标题', '文章标题', 'title')
                    ->addItem('textarea', '文章摘要', '文章摘要', 'abstract')
                    ->addItem('tag', '标签', '文章标签', 'tags')
                    ->addItem('kindeditor', '文章内容', '文章内容', 'content')
                    ->addItem('picture', '封面图片', '文章封面图片', 'cover')
                    ->addItem('time', '发布日期', '文章发布日期，默认当前时间', 'ctime')
                    ->addItem('num', '排序', '用于显示的顺序', 'sort')
                    ->setFormData(D('Article')->find($id))
                    ->display();
        }
    }
}
