<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\SyncLogin\Controller;
use Think\Hook;
use Home\Controller\AddonController;
require_once(dirname(dirname(__FILE__))."/ThinkSDK/ThinkOauth.class.php");
require_once(dirname(dirname(__FILE__))."/ThinkSDK/ThinkOauthInfo.class.php");
/**
 * 第三方登录控制器
 */
class LoginController extends AddonController{
    /**
     * 登录地址
     */
    public function login(){
        $type= I('get.type');
        empty($type) && $this->error('参数错误');
        $sns  = \ThinkOauth::getInstance($type); //加载ThinkOauth类并实例化一个对象
        redirect($sns->getRequestCodeURL()); //跳转到授权页面
    }

    /**
     * 登陆后回调地址
     */
    public function callback(){
        $code =  I('get.code');
        $type= I('get.type');
        $sns  = \ThinkOauth::getInstance($type);

        //腾讯微博需传递的额外参数
        $extend = null;
        if($type == 'tencent'){
            $extend = array('openid' => I('get.openid'), 'openkey' =>  I('get.openkey'));
        }

        $token = $sns->getAccessToken($code , $extend); //获取第三方Token
        $user_sns_info = \ThinkOauthInfo::$type($token); //获取第三方传递回来的用户信息
        $user_sync_info = D('Addons://SyncLogin/SyncLogin')->getUserByOpenidAndType($token['openid'], $type); //根据openid等参数查找同步登录表中的用户信息
        $user_sys_info = D('User')->getUserById($user_sync_info ['uid']); //根据UID查找系统用户中是否有此用户
        if($user_sync_info['uid'] && $user_sys_info['id'] && $user_sync_info['uid'] == $user_sys_info['id']) { //曾经绑定过
            D('Addons://SyncLogin/SyncLogin')->updateTokenByTokenAndType($token, $type);
            D('User')->autoLogin($user_sys_info);
            redirect('http://'.$_SERVER['HTTP_HOST'].__ROOT__);
        }else{ //没绑定过，去注册页面
            session('token', $token);
            session('user_sns_info', $user_sns_info);
            $this->assign('user_sns_info', $user_sns_info);
            $this->assign('meta_title', "登陆" );
            $this->display(T('Addons://SyncLogin@./default/reg'));
        }
    }

    /**
     * 第三方帐号集成 - 注册新账号
     */
    public function doregister(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $upload_data['url'] = $_POST['avatar'];
        $upload_data['ext'] = 'png';
        $upload_data['status'] = 1;
        $_POST['avatar'] = M('Upload')->add($upload_data);
        $user = D('User');
        $data = $user->create();
        if($data){
            $id = $user->add();
            if($id){
                //新增SNS登录账号
                D('Addons://SyncLogin/SyncLogin')->update($id);
                //登录用户
                $uid = D('User')->login($username, $password);
                $this->success('注册成功', U('Home/Index/index'));
            }else{
                $this->error('注册失败');
            }
        }else{
            $this->error($user->getError());
        }
    }

    /**
     * 绑定本地帐号
     */
    public function dobind(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $uid = D('User')->login($username, $password);
        if($uid > 0){
            //新增SNS登录账号
            if(D('Addons://SyncLogin/SyncLogin')->update($uid)){
                $this->success('第三方账号绑定成功', U('Home/Index/index'));
            }else{
                $this->error('新增SNS登录账号失败');
            }
        }else{
            $this->error('绑定失败，请确认帐号密码正确'); // 注册失败
        }
    }

    /**
     * 取消绑定本地帐号
     */
    public function cancelbind($uid){
        $condition['uid'] = $uid;
        $condition['type'] = $_GET['type'];
        $ret = D('Addons://SyncLogin/SyncLogin')->where($condition)->delete();
        if($ret){
            $this->success('取消绑定成功');
        }else{
            $this->error('取消绑定失败');
        }
    }
}
