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
class ArticleController extends HomeController{
    //初始化方法
    protected function _initialize(){
        parent::_initialize();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('__CURRENT_MODEL_ID__', D('CategoryModel')->getModelByName('article', 'id')); //当前模型ID
    }

    /**
     * 文章列表
     * @author jry <598821125@qq.com>
     */
    public function index($cid = 0){
        if($cid != 0){
            $map['cid'] = array('eq', $cid);
            $category = D('Category')->getCategoryById($cid);
            $template = $category['template'] ? 'Article/'.$category['template'] : '';
        }
        $lists = D('Article')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->getAllArticle($map);
        $page = new \Think\Page(D('Article')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('page', $page->show());
        $this->assign('volist', $lists);
        $this->assign('__CURRENT_CATEGORY__', $category['id']);
        $this->meta_title = $category['title'];
        $this->display($template);
    }

    /**
     * 文章信息
     * @author jry <598821125@qq.com>
     */
    public function detail($id = 0){
        if($id == 0){
            $this->error('文章不存在或已删除！');
        }
        $info = D('Article')->getArticleById($id);
        $category = D('Category')->getCategoryById($info['cid']);
        $next = D('Article')->getNextArticle($info);
        $previous = D('Article')->getPreviousArticle($info);
        $info['next'] = '<li class="next '.$next['disabled'].'"><a href="'.$next['link'].'">'.$next['title'].' <i class="icon-arrow-right"></i></a></li>';
        $info['previous'] = '<li class="previous '.$previous['disabled'].'"><a href="'.$previous['link'].'"><i class="icon-arrow-left"></i> '.$previous['title'].'</a></li>';
        $this->assign('info', $info);
        $this->assign('__CURRENT_CATEGORY__', $category['id']);
        $this->meta_title = $info['title'];
        $this->display();
    }

    /**
     * 新增菜单
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
            $all_category = D('Tree')->toFormatTree(D('Category')->getAllCategory());
            $this->assign('all_category', $all_category);
            $this->meta_title = '新增文章';
            $this->display('edit');
        }
    }

    /**
     * 编辑文章
     * @author jry <598821125@qq.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            //更新文章
            $Article = D('Article');
            $data = $Article->create();
            if($data){
                if($Article->save()!== false){
                    $this->success('更新成功', U('index', array('cid' => $_POST['cid'])));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($Article->getError());
            }
        }else{
            $all_category = D('Tree')->toFormatTree(D('Category')->getAllCategory());
            $this->assign('info', D('Article')->getArticleById($id));
            $this->assign('all_category', $all_category);
            $this->meta_title = '编辑文章';
            $this->display();
        }
    }
}
