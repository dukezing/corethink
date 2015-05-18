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
class DocumentController extends AdminController{
    /**
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index($cid = null){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition,'_multi'=>true);

        if($cid){
            $map['cid'] = $cid;
        }
        $map['status'] = array('egt', 0);
        $document_list = D('Document')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                      ->order('sort desc,id desc')->where($map)->select();
        $page = new \Think\Page(D('Document')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //新增按钮属性
        $attr['class'] = 'btn';
        $attr['href'] = U('add', array('cid' => $cid));

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title($category['title'])  //设置页面标题
                ->AddButton('新增', $attr)    //添加新增按钮
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
                ->dataList($document_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('recycle') //添加回收按钮
                ->setPage($page->show())
                ->setMove(true)
                ->display();
    }

    /**
     * 新增文档
     * @author jry <598821125@qq.com>
     */
    public function add(){
        //获取当前分类
        $cid = I('get.cid');
        $category_info = D('Category')->find($cid);
        $doc_type = D('Type')->find($category_info['doc_type']);
        $field_sort = json_decode($doc_type['field_sort'], true);
        $field_group = parse_attr($doc_type['field_group']);

        //获取文档字段
        $map['status'] = array('eq', '1');
        $map['show'] = array('eq', '1');
        $map['doc_type'] = array('in', '0,'.$category_info['doc_type']);
        $attribute_list = D('Attribute')->where($map)->select();

        //解析字段options
        $new_attribute_list = array();
        foreach($attribute_list as $attr){
            if($attr['name'] == 'cid'){
                $con['doc_type'] = $category_info['doc_type'];
                $attr['value'] = $cid;
                $attr['options'] = $this->selectListAsTree('Category', $con);
            }else{
                $attr['options'] = parse_attr($attr['options']);
            }
            $new_attribute_list[$attr['id']] = $attr;
        }

        //表单字段排序及分组
        if($field_sort){
            $new_attribute_list_sort = array();
            foreach($field_sort as $k1 => &$v1){
                $new_attribute_list_sort[0]['type'] = 'group';
                $new_attribute_list_sort[0]['options']['group'.$k1]['title'] = $field_group[$k1];
                foreach($v1 as $k2 => $v2){
                    $new_attribute_list_sort[0]['options']['group'.$k1]['options'][] = $new_attribute_list[$v2];
                }
            }
            $new_attribute_list = $new_attribute_list_sort;
        }

        //使用FormBuilder快速建立表单页面。
        $builder = new \Admin\Builder\AdminFormBuilder();
        $builder->title('新增文章')  //设置页面标题
                ->setUrl(U('update')) //设置表单提交地址
                ->setExtraItems($new_attribute_list)
                ->display();
    }

    /**
     * 编辑文章
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        //获取文档信息
        $document_info = D('Document')->detail($id);

        //获取当前分类
        $category_info = D('Category')->find($document_info['cid']);
        $doc_type = D('Type')->find($category_info['doc_type']);
        $field_sort = json_decode($doc_type['field_sort'], true);
        $field_group = parse_attr($doc_type['field_group']);

        //获取文档字段
        $map['status'] = array('eq', '1');
        $map['show'] = array('eq', '1');
        $map['doc_type'] = array('in', '0,'.$category_info['doc_type']);
        $attribute_list = D('Attribute')->where($map)->select();

        //解析字段options
        $new_attribute_list = array();
        foreach($attribute_list as $attr){
            if($attr['name'] == 'cid'){
                $con['doc_type'] = $category_info['doc_type'];
                $attr['options'] = $this->selectListAsTree('Category', $con);
            }else{
                $attr['options'] = parse_attr($attr['options']);
            }
            $new_attribute_list[$attr['id']] = $attr;
            $new_attribute_list[$attr['id']]['value'] = $document_info[$attr['name']];
        }

        //表单字段排序及分组
        if($field_sort){
            $new_attribute_list_sort = array();
            foreach($field_sort as $k1 => &$v1){
                $new_attribute_list_sort[0]['type'] = 'group';
                $new_attribute_list_sort[0]['options']['group'.$k1]['title'] = $field_group[$k1];
                foreach($v1 as $k2 => $v2){
                    $new_attribute_list_sort[0]['options']['group'.$k1]['options'][] = $new_attribute_list[$v2];
                }
            }
            $new_attribute_list = $new_attribute_list_sort;
        }

        //使用FormBuilder快速建立表单页面。
        $builder = new \Admin\Builder\AdminFormBuilder();
        $builder->title('新增文章')  //设置页面标题
                ->setUrl(U('update')) //设置表单提交地址
                ->addItem('hidden', 'ID', 'ID', 'id')
                ->setExtraItems($new_attribute_list)
                ->setFormData($document_info)
                ->display();
    }

    /**
     * 新增或更新一个文档
     * @author jry <598821125@qq.com>
     */
    public function update(){
        $document_model = D('Document');
        $result = $document_model->update();
        if(!$result){
            $this->error($document_model->getError());
        }else{
            $this->success($result['id']?'更新成功':'新增成功', U('index', array('cid' => $cid)));
        }
    }
}
