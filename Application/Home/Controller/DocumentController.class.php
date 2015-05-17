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
    //初始化方法
    protected function _initialize(){
        parent::_initialize();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
    }

    /**
     * 文章列表
     * @author jry <598821125@qq.com>
     */
    public function index($cid){
        //获取分类信息
        $map['cid'] = $cid;
        $category = D('Category')->find($cid);
        $template = $category['template'] ? 'Article/'.$category['template'] : '';

        $map['status'] = array('egt', 0);
        $document_list = D('Document')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))
                                      ->order('sort desc,id desc')->where($map)->select();
        $this->assign('volist', $document_list);

        //分页
        $page = new \Think\Page(D('Document')->where($map)->count(), C('ADMIN_PAGE_ROWS'));
        $this->assign('page', $page->show());

        $this->assign('__CURRENT_CATEGORY__', $category['id']);
        $this->meta_title = $category['title'];
        $this->display($template);
    }

    /**
     * 文章信息
     * @author jry <598821125@qq.com>
     */
    public function detail($id){
        $info = D('Document')->detail($id);
        $category = D('Document')->find($info['cid']);
        $this->assign('info', $info);
        $this->assign('__CURRENT_CATEGORY__', $category['id']);
        $this->meta_title = $info['title'];
        $this->display();
    }
}
