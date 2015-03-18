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
 * 分类控制器
 * @author jry <598821125@qq.com>
 */
class CategoryController extends HomeController{
    /**
     * 分类详情
     * @author jry <598821125@qq.com>
     */
    public function detail($id){
        $category = D('Category')->find($id);
        $template = $category['template'] ? 'Category/'.$category['template'] : '';
        $this->assign('info', $category);
        $this->assign('__CURRENT_CATEGORY__', $category['id']);
        $this->assign('meta_title', $category['title']);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display($template);
    }
}
