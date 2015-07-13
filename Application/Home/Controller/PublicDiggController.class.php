<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
/**
 * 万能Digg控制器
 * @author jry <598821125@qq.com>
 */
class PublicDiggController extends HomeController{
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize(){
        parent::_initialize();
        $this->is_login();
    }

    /**投票
     * @param $type  数据表ID
     * @param $data_id  数据ID
     * @param $type  Digg类型
     * @author jry <598821125@qq.com>
     */
    public function digg(){
        $uid = $this->is_login();
        $public_digg_object = D('PublicDigg');

        //查找是否已经Digg过
        $con['table']   = I('table');
        $con['data_id'] = I('data_id');
        $con['type']    = I('type');
        $con['uid']     = $uid;
        $result = $public_digg_object->where($con)->find();
        if($result){ //曾经Digg过
            if($result['status']){ //已经Digg过，执行取消Digg操作
                $status = $public_digg_object->where($con)->setField('status', 0);
                $return['digg_status'] = 0;
            }else{ //取消状态恢复Digg
                $status = $public_digg_object->where($con)->setField('status', 1);
                $return['digg_status'] = 1;
            }
            $return['status'] = 1;
            $return['info'] = '操作成功';
        }else{ //还没Digg过
            $data = $public_digg_object->create($con);
            if($data){
                $id = $public_digg_object->add();
                if($id){
                    $return['status'] = 1;
                    $return['info'] = '操作成功';
                    $return['digg_status'] = 1;
                    $this->ajaxReturn($return);
                }else{
                    $this->error('操作失败');
                }
            }else{
                $this->error($public_digg_object->getError());
            }
        }

        //获取Digg(比如点赞)计数
        unset($con['uid']);
        $con['status'] = 1;
        $return['digg_count'] = $public_digg_object->where($con)->count();
        D(C('TABLE_LIST.'.I('table')))->where(array('id' => (int)I('data_id')))
                                      ->setField(C('DIGG_TYPE_LIST.'.I('type')), $return['digg_count']); // 更新Digg计数
        $this->ajaxReturn($return);
    }

    /**获取投票信息
     * @param $type  数据表ID
     * @param $data_id  数据ID
     * @param $type  Digg类型
     * @author jry <598821125@qq.com>
     */
    public function getDiggStatus(){
        $con['table']   = I('table');
        $con['data_id'] = I('data_id');
        $con['type']    = I('type');
        $con['uid']     = $this->is_login();
        $digg = D('PublicDigg')->where($con)->find();
        if($digg && $digg['status']){
            $this->success();
        }
    }
}
