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
 * 消息控制器
 * @author jry <598821125@qq.com>
 */
class UserMessageController extends HomeController{
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize(){
        parent::_initialize();
        $this->is_login();
    }
    /**
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index($type = 0){
        $message_list = D('UserMessage')->getAllMessageByType($type);
        $this->assign('volist', $message_list);
        $this->assign('__CURRENT_MESSAGE_TYPE', $type);
        $this->assign('meta_title', "消息中心");
        $this->display('User/message');
    }
}
