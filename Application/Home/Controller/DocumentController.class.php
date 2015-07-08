<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
/**
 * 文章控制器
 * @author jry <598821125@qq.com>
 */
class DocumentController extends HomeController{
    /**
     * 文档列表
     * @author jry <598821125@qq.com>
     */
    public function index($cid){
        //获取分类信息
        $map['cid'] = $cid;
        $category = D('Category')->find($cid);
        switch($category['doc_type']){
            case 1: //链接
                if(stristr($category['url'], 'http://')){
                    redirect($category['url']);
                }else{
                    $this->redirect($category['url']);
                }
                break;
            case 2: //单页
                $this->redirect('Category/detail/id/'.$category['id']);
                break;
            default :
                $template = $category['index_template'] ? 'Document/'.$category['index_template'] : 'Document/index_default';
                $map['status'] = array('eq', 1);
                $document_list = D('Document')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                              ->order('sort desc,id desc')->where($map)->select();
                $page = new \Common\Util\Page(D('Document')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

                //如果当前分类下无文档则获取子分类文档
                if(!$document_list){
                    $child_cagegory_id_list = D('Category')->where(array('pid' => $cid))->getField('id',true);
                    if($child_cagegory_id_list){
                        $map['cid'] = array('in', $child_cagegory_id_list);
                        $document_list = D('Document')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                                     ->order('sort desc,id desc')->where($map)->select();
                        $page = new \Common\Util\Page(D('Document')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
                    }
                }

                //获取扩展表的信息
                foreach($document_list as &$doc){
                    $doc_type_name = D('DocumentType')->getFieldById($doc['doc_type'], 'name');
                    $temp = array();
                    $temp = D('DocumentExtend'.$doc_type_name)->find($doc['id']);
                    $doc = array_merge($doc, $temp);
                }

                $this->assign('__CURRENT_CATEGORY__', $category['id']);
                $this->assign('__CURRENT_CATEGORY_GROUP__', $category['group']);
                $this->assign('info', $category);
                $this->assign('volist', $document_list);
                $this->assign('page', $page->show());
                $this->meta_title = $category['title'];
                Cookie('__forward__', $_SERVER['REQUEST_URI']);
                $this->display($template);
        }
    }

    /**
     * 我的文档列表
     * @author jry <598821125@qq.com>
     */
    public function mydoc(){
        $uid = $this->is_login();

        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition,'_multi'=>true);

        //获取分类ID
        if(I('doc_type')){
            $con['doc_type'] = I('doc_type');
            $cid_list = D('Category')->where($con)->getField('id', true);
            if($cid_list){
                $map['cid'] = array('in', $cid_list);
            }
        }

        $map['uid'] = $uid;
        $map['status'] = array('egt', 0);
        $document_list = D('Document')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                      ->order('sort desc,id desc')->where($map)->select();
        $page = new \Common\Util\Page(D('Document')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->title('我的文档') //设置页面标题
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addRecycleButton() //添加回收按钮
                ->setSearch('请输入ID/标题', U('Document/mydoc', array('doc_type' => I('doc_type'))))
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
                ->setTemplate('Builder/listbuilder_user')
                ->display();
    }

    /**
     * 新增文档
     * @author jry <598821125@qq.com>
     */
    public function add(){
        $this->is_login();

        if(I('get.doc_type')){
            $map['doc_type'] = I('get.doc_type');
            $category_info = D('Category')->where($map)->order('id asc')->find();
        }elseif(I('get.cid')){
            $category_info = D('Category')->find(I('get.cid'));
        }
        //获取当前分类
        if(!$category_info['post_auth']){
            $this->error('该分类禁止投稿');
        }
        $doc_type = D('DocumentType')->find($category_info['doc_type']);
        $field_sort = json_decode($doc_type['field_sort'], true);
        $field_group = parse_attr($doc_type['field_group']);

        //获取文档字段
        $map = array();
        $map['status'] = array('eq', '1');
        $map['show'] = array('eq', '1');
        $map['doc_type'] = array('in', '0,'.$category_info['doc_type']);
        $attribute_list = D('DocumentAttribute')->where($map)->select();

        //解析字段options
        $new_attribute_list = array();
        foreach($attribute_list as $attr){
            if($attr['name'] == 'cid'){
                $con = array();
                $con['group'] = $category_info['group'];
                $con['doc_type'] = $category_info['doc_type'];
                $attr['value'] = $category_info['id'];
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
            $new_attribute_list = $new_attribute_list_sort[0]['options']['group1']['options'];
        }

        //使用FormBuilder快速建立表单页面。
        $builder = new \Common\Builder\FormBuilder();
        $builder->title('新增文章')  //设置页面标题
                ->setUrl(U('update')) //设置表单提交地址
                ->setExtraItems($new_attribute_list)
                ->setTemplate('Builder/formbuilder_user')
                ->display();
    }

    /**
     * 编辑文章
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        $this->is_login();
        //获取文档信息
        $document_info = D('Document')->detail($id);

        //获取当前分类
        $category_info = D('Category')->find($document_info['cid']);
        if(!$category_info['post_auth']){
            $this->error('该分类禁止投稿');
        }
        $doc_type = D('DocumentType')->find($category_info['doc_type']);
        $field_sort = json_decode($doc_type['field_sort'], true);
        $field_group = parse_attr($doc_type['field_group']);

        //获取文档字段
        $map = array();
        $map['status'] = array('eq', '1');
        $map['show'] = array('eq', '1');
        $map['doc_type'] = array('in', '0,'.$category_info['doc_type']);
        $attribute_list = D('DocumentAttribute')->where($map)->select();

        //解析字段options
        $new_attribute_list = array();
        foreach($attribute_list as $attr){
            if($attr['name'] == 'cid'){
                $con = array();
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
            $new_attribute_list = $new_attribute_list_sort[0]['options']['group1']['options'];
        }

        //使用FormBuilder快速建立表单页面。
        $builder = new \Common\Builder\FormBuilder();
        $builder->title('编辑文章')  //设置页面标题
                ->setUrl(U('update')) //设置表单提交地址
                ->addItem('id', 'hidden', 'ID', 'ID')
                ->setExtraItems($new_attribute_list)
                ->setFormData($document_info)
                ->setTemplate('Builder/formbuilder_user')
                ->display();
    }

    /**
     * 新增或更新一个文档
     * @author jry <598821125@qq.com>
     */
    public function update(){
        $this->is_login();
        $document_object = D('Document');
        $result = $document_object->update();
        if(!$result){
            $this->error($document_object->getError());
        }else{
            $this->success($result['id']?'更新成功':'新增成功', Cookie('__forward__') ? : C('HOME_PAGE'));
        }
    }

    /**
     * 文章信息
     * @author jry <598821125@qq.com>
     */
    public function detail($id){
        $info = D('Document')->detail($id);
        $result = D('Document')->where(array('id' => $id))->SetInc('view'); //阅读量加1
        $category = D('Category')->find($info['cid']);
        $template = $category['detail_template'] ? 'Document/'.$category['detail_template'] : 'Document/detail_default';
        $this->assign('info', $info);
        $this->assign('__CURRENT_CATEGORY__', $category['id']);
        $this->meta_title = $info['title'];
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display($template);
    }
}
