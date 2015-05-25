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
 * 后台分类控制器
 * @author jry <598821125@qq.com>
 */
class CategoryController extends AdminController{
    /**
     * 分类列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition,'_multi'=>true);

        //获取所有
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $map['pid'] = array('eq', I('get.pid') ? : '0'); //父分类ID
        $data_list = D('Category')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->order('sort asc,id asc')->select();

        //转换成树状列表
        $tree = new \Common\Util\Tree();
        $data_list = $tree->toFormatTree($data_list);
        $data_list = D('Category')->getLinkByModel($data_list);

        $attr['href'] = 'Category/del?id=';
        $attr['class'] = 'ajax-get confirm';

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('分类列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->setSearch('请输入ID/分类名称', U('index'))
                ->addField('id', 'ID', 'text')
                ->addField('title_link', '分类', 'text')
                ->addField('url', '链接', 'text')
                ->addField('icon', '图标', 'icon')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('self', '删除', $attr) //添加删除按钮
                ->display();
    }

    /**
     * 新增分类
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $category_object = D('Category');
            $data = $category_object->create();
            if($data){
                $id = $category_object->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($category_object->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('新增分类')  //设置页面标题
                    ->setUrl(U('add')) //设置表单提交地址
                    ->addItem('pid', 'select', '上级分类', '所属的上级分类', $this->selectListAsTree('Category', null, '顶级分类'))
                    ->addItem('title', 'text', '分类标题', '分类标题')
                    ->addItem('doc_type', 'select', '分类内容模型', '分类内容模型', $this->selectListAsTree('DocumentType'))
                    ->addItem('url', 'text', '链接', 'U函数解析的URL或者外链', null, 'hidden')
                    ->addItem('content', 'kindeditor', '内容', '单页模型填写内容', null, 'hidden')
                    ->addItem('template', 'text', '模版', '单页使用的模版或其他模型文档列表模版')
                    ->addItem('icon', 'icon', '图标', '菜单图标')
                    ->addItem('sort', 'num', '排序', '用于显示的顺序')
                    ->setExtra('category')
                    ->display();
        }
    }

    /**
     * 编辑分类
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $category_object = D('Category');
            $data = $category_object->create();
            if($data){
                if($category_object->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($category_object->getError());
            }
        }else{
            $info = D('Category')->find($id);
            //使用FormBuilder快速建立表单页面。
            $builder = new \Admin\Builder\AdminFormBuilder();
            $builder->title('编辑分类')  //设置页面标题
                    ->setUrl(U('edit')) //设置表单提交地址
                    ->addItem('id', 'hidden', 'ID', 'ID')
                    ->addItem('pid', 'select', '上级分类', '所属的上级分类', $this->selectListAsTree('Category', null, '顶级分类'))
                    ->addItem('title', 'text', '分类标题', '分类标题')
                    ->addItem('doc_type', 'select', '分类内容模型', '分类内容模型', $this->selectListAsTree('DocumentType'))
                    ->addItem('url', 'text', '链接', 'U函数解析的URL或者外链', null, $info['model'] == 1 ? : 'hidden')
                    ->addItem('content', 'kindeditor', '内容', '单页模型填写内容', null, $info['model'] == 2 ? : 'hidden')
                    ->addItem('template', 'text', '模版', '单页使用的模版或其他模型文档列表模版')
                    ->addItem('icon', 'icon', '图标', '菜单图标')
                    ->addItem('sort', 'num', '排序', '用于显示的顺序')
                    ->setFormData($info)
                    ->setExtra('category')
                    ->display();
        }
    }

    /**
     * 删除分类
     * @author jry <598821125@qq.com>
     */
    public function del($id){
        $category_object = D('Category');
        $category_info = $category_object->find($id);
        $con['id'] = $category_info['doc_type'];
        $category_type = D('DocumentType')->where($con)->getField('name');
        $condition['cid'] = $id;
        $count = D('Document')->where($condition)->count();
        if($count == 0){
            $result = $category_object->delete($id);
            if($result){
                $this->success('删除分类成功');
            }
        }else{
            $this->error('请先删除或移动该分类下文档');
        }
    }
}
