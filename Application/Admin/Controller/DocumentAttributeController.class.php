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
class DocumentAttributeController extends AdminController{
    /**
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index($doc_type){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|name|title'] = array($condition, $condition, $condition,'_multi'=>true);

        if($doc_type){
            $map['doc_type'] = $doc_type;
        }
        $map['status'] = array('egt', 0);
        $document_attribute_list = D('DocumentAttribute')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                                         ->order('id desc')->where($map)->select();
        $page = new \Common\Util\Page(D('DocumentAttribute')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        $attr['href'] = U('add', array('doc_type' => $doc_type));
        $attr['class'] = 'btn';

        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->title(字段管理) //设置页面标题
                ->AddButton('新 增', $attr) //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->setSearch('请输入ID/名称/标题', U('index'))
                ->addField('id', 'ID', 'text')
                ->addField('name', '名称', 'text')
                ->addField('title', '标题', 'text')
                ->addField('type', '类型', 'type')
                ->addField('ctime', '发布时间', 'time')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($document_attribute_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->setPage($page->show())
                ->display();
    }

    /**
     * 新增字段
     * @author jry <598821125@qq.com>
     */
    public function add($doc_type){
        if(IS_POST){
            $document_attribute_object = D('DocumentAttribute');
            $data = $document_attribute_object->create();
            if($data){
                $id = $document_attribute_object->add();
                if($id){
                    $result = $document_attribute_object->addField($data); //新增表字段
                    if($result){
                        $this->success('新增字段成功', U('index', array('doc_type' => I('doc_type'))));
                    }else{
                        $document_attribute_object->delete($id); //删除新增数据
                        $this->error('新建字段出错！');
                    }
                }else{
                    $this->error('新增字段出错！');
                }
            }else{
                $this->error($document_attribute_object->getError());
            }
        }else{
            $info['doc_type'] = $doc_type;
            $info['show'] = 1;
            //使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->title('新增字段')  //设置页面标题
                    ->setUrl(U('add')) //设置表单提交地址
                    ->addItem('doc_type', 'select', '文档类型', '文档类型', $this->selectListAsTree('DocumentType'))
                    ->addItem('name', 'text', '字段名称', '字段名称，如“title”')
                    ->addItem('title', 'text', '字段标题', '字段标题，如“标题”')
                    ->addItem('type', 'select', '字段类型', '字段类型', C('FORM_ITEM_TYPE'))
                    ->addItem('field', 'text', '字段定义', '字段定义')
                    ->addItem('value', 'text', '字段默认值', '字段默认值')
                    ->addItem('show', 'radio', '是否显示', '是否显示', array('1' => '显示', '0' => '不显示'))
                    ->addItem('options', 'textarea', '额外选项', '额外选项radio/select等需要配置此项')
                    ->addItem('tip', 'textarea', '字段补充说明', '字段补充说明')
                    ->setFormData($info)
                    ->display();
        }
    }

    /**
     * 编辑分类
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $document_attribute_object = D('DocumentAttribute');
            $data = $document_attribute_object->create();
            if($data){
                $result = $document_attribute_object->updateField($data); //更新字段
                if($result){
                    $status = $document_attribute_object->save(); //更新字段信息
                    if($status){
                        $this->success('更新字段成功', U('index', array('doc_type' => I('doc_type'))));
                    }else{
                        $this->error('更新属性出错！');
                    }
                }else{
                    $this->error('更新字段出错！');
                }
            }else{
                $this->error($document_attribute_object->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->title('编辑字段')  //设置页面标题
                    ->setUrl(U('edit')) //设置表单提交地址
                    ->addItem('id', 'hidden', 'ID', 'ID')
                    ->addItem('doc_type', 'select', '文档类型', '文档类型', $this->selectListAsTree('DocumentType'))
                    ->addItem('name', 'text', '字段名称', '字段名称，如“title”')
                    ->addItem('title', 'text', '字段标题', '字段标题，如“标题”')
                    ->addItem('type', 'select', '字段类型', '字段类型', C('FORM_ITEM_TYPE'))
                    ->addItem('field', 'text', '字段定义', '字段定义')
                    ->addItem('value', 'text', '字段默认值', '字段默认值')
                    ->addItem('show', 'radio', '是否显示', '是否显示', array('1' => '显示', '0' => '不显示'))
                    ->addItem('options', 'textarea', '额外选项', '额外选项radio/select等需要配置此项')
                    ->addItem('tip', 'textarea', '字段补充说明', '字段补充说明')
                    ->setFormData(D('DocumentAttribute')->find($id))
                    ->display();
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids    = I('request.ids');
        $status = I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        switch($status){
            case 'delete' : //删除条目
                $document_attribute_object = D('DocumentAttribute');
                $field = $document_attribute_object->find($ids);
                $result1 = $document_attribute_object->delete($ids);
                $result2 = $document_attribute_object->deleteField($field);
                if($result1 && $result2){
                    $this->success('删除成功，不可恢复！');
                }else{
                    $this->error('删除失败'.$document_attribute_object->getError());
                }
                break;
            default :
                parent::setStatus($model);
                break;
        }
    }
}
