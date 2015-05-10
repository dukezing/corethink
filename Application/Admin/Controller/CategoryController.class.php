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
    public function index($pid = null){
        $all_category = D('Tree')->toFormatTree(D('Category')->getAllCategory());
        $this->assign('volist', $this->int_to_icon($all_category));
        $this->assign('meta_title', "分类列表");
        $this->display();
    }

    /**
     * 新增分类
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $category = D('Category');
            $data = $category->create();
            if($data){
                $id = $category->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($category->getError());
            }
        }else{
            $all_category = D('Tree')->toFormatTree(D('Category')->getAllCategory($map));
            $all_category = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级分类')), $all_category);
            $this->assign('all_category', $all_category);
            $this->assign('all_model', D('CategoryModel')->getAllModel());
            $this->meta_title = '新增分类';
            $this->display('edit');
        }
    }

    /**
     * 编辑分类
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $category = D('Category');
            $data = $category->create();
            if($data){
                if($category->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($category->getError());
            }
        }else{
            $info = D('Category')->getCategoryById($id);
            $all_category = D('Tree')->toFormatTree(D('Category')->getAllCategory());
            $all_category = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级分类')), $all_category);
            $this->assign('all_category', $all_category);
            $this->assign('all_model', D('CategoryModel')->getAllModel());
            $this->assign('info', $info);
            $this->meta_title = '编辑分类';
            $this->display();
        }
    }

    /**
     * 删除分类
     * @author jry <598821125@qq.com>
     */
    public function del($id){
        $category = D('Category');
        $category_model = $category->getCategoryById($id, 'model');
        $category_model_name = D('CategoryModel')->getModelNameById($category_model);
        $condition['cid'] = $id;
        $category_list_count = D($category_model_name)->where($condition)->count();
        if($category_list_count == 0){
            $result = $category->delete($id);
            if($result){
                $this->success('删除分类成功');
            }
        }else{
            $this->error('请先删除或移动该分类下文档');
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
                $category = D('Category');
                $form_category_model = $category->getCategoryById($from_cid, 'model');
                $to_category_model = $category->getCategoryById($to_cid, 'model');
                if($form_category_model === $to_category_model){
                    $map['id'] = array('in',$ids);
                    $data = array('cid' => $to_cid);
                    $category_model_name = D('CategoryModel')->getModelNameById($to_category_model);
                    $this->editRow($category_model_name, $data, $map, array('success'=>'移动成功','error'=>'移动失败'));
                }else{
                    $this->error('该分类模型不匹配');
                }
            }else{
                $this->error('请选择目标分类');
            }
        }
    }
}
