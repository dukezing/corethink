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
 * 后台标签控制器
 * @author jry <598821125@qq.com>
 */
class TagController extends AdminController{
    /**
     * 标签列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition,'_multi'=>true);
        $all_menu = D('Tag')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllTag($map);
        $page = new \Think\Page(D('Tag')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('volist', $this->int_to_icon($all_menu));
        $this->assign('page', $page->show());
        $this->assign('meta_title', "标签列表");
        $this->display();
    }

    /**
     * 新增标签
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Tag = D('Tag');
            $data = $Tag->create();
            if($data){
                $id = $Tag->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($Tag->getError());
            }
        }else{
            $this->meta_title = '新增标签';
            $this->display('edit');
        }
    }

    /**
     * 编辑标签
     * @author jry <598821125@qq.com>
     */
    public function edit(){
        if(IS_POST){
            $Tag = D('Tag');
            $data = $Tag->create();
            if($data){
                if($Tag->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($Tag->getError());
            }
        }else{
            $this->assign('info', D('Tag')->getTagById($id));
            $this->meta_title = '编辑标签';
            $this->display();
        }
    }

    /**
     * 搜索相关标签
     *@ param string 搜索关键字
     * @return array 相关标签
     * @author jry <598821125@qq.com>
     */
    public function searchTags(){
        $map["title"] = array("like", "%".I('get.q')."%");
        $tags = D('Tag')->field('id,title')->where($map)->select();
        foreach($tags as $value){
            $data[] = array('id' => $value['title'], 'title'=> $value['title']);
        }
        echo json_encode($data);
    }
}
