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
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition, '_multi'=>true);
        $lists = D('Article')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllArticle($map);
        $page = new \Think\Page(D('Article')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('all_category', D('Common/Tree')->toFormatTree(D('Category')->getAllCategory()));
        $this->assign('volist', $this->int_to_icon($lists));
        $this->assign('page', $page->show());
        $this->meta_title = $category['title'];
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }

    /**
     * 新增文章
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Article = D('Article');
            $data = $Article->create();
            if($data){
                $id = $Article->add();
                if($id){
                    $this->success('新增成功', U('index', array('cid' => $_POST['cid'])));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($Article->getError());
            }
        }else{
            $all_category = D('Common/Tree')->toFormatTree(D('Category')->getAllCategory());
            $this->assign('all_category', $all_category);
            $info['cid'] = I('get.cid'); //获取当前分类
            $this->assign('info', $info);
            $this->meta_title = '新增文章';
            $this->display('edit');
        }
    }

    /**
     * 编辑文章
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            //更新文章
            $Article = D('Article');
            $data = $Article->create();
            if($data){
                if($Article->save()!== false){
                    $this->success('更新成功', Cookie('__forward__') ? : U('index', array('cid' => $_POST['cid'])));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($Article->getError());
            }
        }else{
            $all_category = D('Common/Tree')->toFormatTree(D('Category')->getAllCategory());
            $this->assign('info', D('Article')->getArticleById($id));
            $this->assign('all_category', $all_category);
            $this->meta_title = '编辑文章';
            $this->display();
        }
    }
}
