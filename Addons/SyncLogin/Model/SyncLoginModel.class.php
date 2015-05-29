<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Addons\SyncLogin\Model;
use Think\Model;
/**
 * 第三方登陆模型
 */
class SyncLoginModel extends Model{
    /**
     * 数据库表名
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'addon_sync_login'; 

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('uid', 'require', 'UID不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('type','require','type不能为空！', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('openid','require','openid不能为空！', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('access_token','require','access_token不能为空！', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('refresh_token','require','refresh_token不能为空！', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 新增SNS登录账号
     */
    public function update($id){
        $token = session('token');
        $user_sns_info = session('user_sns_info');
        $data['uid'] = $id;
        $data['type'] = $user_sns_info['type'];
        $data['openid'] = $token['openid'];
        $data['access_token'] = $token['access_token'];
        $data['refresh_token'] = $token['refresh_token'];
        $data = $this->create($data);
        return $this->add($data);
    }

    /**
     * 根据openid等参数查找同步登录表中的用户信息
     */
    public function getUserByOpenidAndType($openid, $type){
        $condition = array(
            'openid' => $openid,
            'type' => $type,
        );
        return $this->where($condition)->find();
    }

    /**
     * 更新Token
     */
    public function updateTokenByTokenAndType($token, $type){
        $condition = array(
            'openid' => $token['openid'],
            'type' => $type,
        );
        $data['access_token'] = $token['access_token'];
        $data['refresh_token'] = $token['refresh_token'];
        if($this->where($condition)->save($data)){
            return true;
        }
    }
}
