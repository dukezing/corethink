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
 * 后台公共控制器
 * @author jry <598821125@qq.com>
 */
class PublicController extends Controller{
    /**
     * 登陆
     * @author jry <598821125@qq.com>
     */
    public function login(){
        if(IS_POST){
            $username = I('username');
            $password = I('password');
            $map['group'] = array('egt', 1); //后台部门
            $uid = D('User')->login($username, $password, $map);
            if(0 < $uid){
                $this->success('登录成功！', U('Index/index'));
            }else{
                $this->error('登录失败！');
            }
        }else{
            //读取数据库中的配置
            $config = S('DB_CONFIG_DATA');
            if(!$config){
                $config = D('SystemConfig')->lists();
                $config['DEFAULT_THEME'] = ''; //后台无模板主题
                S('DB_CONFIG_DATA',$config);
            }
            C($config); //添加配置
            $this->meta_title = '用户登录';
            $this->display();
        }
    }

    /**
     * 注销
     * @author jry <598821125@qq.com>
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
        $this->success('退出成功！', U('Public/login'));
    }
}
