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
            $category = D('Category')->find($cid);
        }
        $map['status'] = array('egt', 0);
        $document_list = D('Document')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                      ->order('sort desc,id desc')->where($map)->select();
        $page = new \Common\Util\Page(D('Document')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //新增按钮属性
        $add_attr['class'] = 'btn btn-primary';
        $add_attr['href'] = U('add', array('cid' => $cid));

        //移动按钮属性
        $move_attr['class'] = 'btn btn-info';
        $move_attr['onclick'] = 'move()';

        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->title($category['title'])  //设置页面标题
                ->addButton('新 增', $add_attr)    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addRecycleButton() //添加回收按钮
                ->AddButton('移 动', $move_attr) //添加移动按钮
                ->setSearch('请输入ID/标题', U('index'))
                ->addField('id', 'ID', 'text')
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
                ->setExtra('move')
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
        $doc_type = D('DocumentType')->find($category_info['doc_type']);
        $field_sort = json_decode($doc_type['field_sort'], true);
        $field_group = parse_attr($doc_type['field_group']);

        //获取文档字段
        $map['status'] = array('eq', '1');
        $map['show'] = array('eq', '1');
        $map['doc_type'] = array('in', '0,'.$category_info['doc_type']);
        $attribute_list = D('DocumentAttribute')->where($map)->select();

        //解析字段options
        $new_attribute_list = array();
        foreach($attribute_list as $attr){
            if($attr['name'] == 'cid'){
                $con['group'] = $category_info['group'];
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
        $builder = new \Common\Builder\FormBuilder();
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
        $doc_type = D('DocumentType')->find($category_info['doc_type']);
        $field_sort = json_decode($doc_type['field_sort'], true);
        $field_group = parse_attr($doc_type['field_group']);

        //获取文档字段
        $map['status'] = array('eq', '1');
        $map['show'] = array('eq', '1');
        $map['doc_type'] = array('in', '0,'.$category_info['doc_type']);
        $attribute_list = D('DocumentAttribute')->where($map)->select();

        //解析字段options
        $new_attribute_list = array();
        foreach($attribute_list as $attr){
            if($attr['name'] == 'cid'){
                $con['group'] = $category_info['group'];
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
        $builder = new \Common\Builder\FormBuilder();
        $builder->title('编辑文章')  //设置页面标题
                ->setUrl(U('update')) //设置表单提交地址
                ->addItem('id', 'hidden', 'ID', 'ID')
                ->setExtraItems($new_attribute_list)
                ->setFormData($document_info)
                ->display();
    }

    /**
     * 新增或更新一个文档
     * @author jry <598821125@qq.com>
     */
    public function update(){
        $document_object = D('Document');
        $result = $document_object->update();
        if(!$result){
            $this->error($document_object->getError());
        }else{
            $this->success($result['id']?'更新成功':'新增成功', U('index', array('cid' => $cid)));
        }
    }

    /**
     * 移动文档
     * @author jry <598821125@qq.com>
     */
    public function move(){
        if(IS_POST){
            $ids = I('post.ids');
            $from_cid = I('post.from_cid');
            $to_cid = I('post.to_cid');
            if($from_cid === $to_cid){
                $this->error('目标分类与当前分类相同');
            }
            if($to_cid){
                $category_model = D('Category');
                $form_category_type = $category_model->getFieldById($from_cid, 'doc_type');
                $to_category_type = $category_model->getFieldById($to_cid, 'doc_type');
                if($form_category_type === $to_category_type){
                    $map['id'] = array('in',$ids);
                    $data = array('cid' => $to_cid);
                    $this->editRow('Document', $data, $map, array('success'=>'移动成功','error'=>'移动失败'));
                }else{
                    $this->error('该分类模型不匹配');
                }
            }else{
                $this->error('请选择目标分类');
            }
        }
    }

    /**
     * 回收站
     * @author jry <598821125@qq.com>
     */
    public function recycle(){
        $map['status'] = array('eq', '-1');
        $document_list = D('Document')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->select();
        $page = new \Common\Util\Page(D('Document')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->title('回收站')  //设置页面标题
                ->addDeleteButton() //添加删除按钮
                ->addRestoreButton() //添加还原按钮
                ->setSearch('请输入ID/文档名称', U('recycle'))
                ->addField('id', 'ID', 'text')
                ->addField('title', '标题', 'text')
                ->addField('ctime', '发布时间', 'time')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($document_list)    //数据列表
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->setPage($page->show())
                ->display();
    }
}
