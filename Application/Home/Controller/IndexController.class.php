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
 * 前台默认控制器
 * @author jry <598821125@qq.com>
 */
class IndexController extends HomeController{
    /**
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $this->assign('meta_title', "首页");
        $this->display();
    }

    /**
     * 编辑器上传
     * @author jry <598821125@qq.com>
     */
    public function upload(){
        exit(D('Upload')->upload());
    }

    /**
     * KindEditor编辑器下载远程图片
     * @author jry <598821125@qq.com>
     */
    public function downremoteimg(){
        exit(D('Upload')->downremoteimg());
    }

    /**
     * KindEditor编辑器文件管理
     * @author jry <598821125@qq.com>
     */
    public function fileManager(){
        exit(D('Upload')->fileManager());
    }
}
