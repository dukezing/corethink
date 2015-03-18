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
 * 导航链接控制器
 * @author jry <598821125@qq.com>
 */
class NavlinkController extends HomeController{
    /**
     * 导航链接详情
     * @author jry <598821125@qq.com>
     */
    public function detail($id){
        /* 获取详细信息 */
        $navlink = D('Navlink')->find($id);
        $template = $navlink['template'] ? 'Category/'.$navlink['template'] : 'Category/detail';
        $this->assign('info', $navlink);
        $this->assign('__CURRENT_NAVLINK__', $navlink['id']);
        $this->assign('meta_title', $navlink['title']);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display($template);
    }
}
