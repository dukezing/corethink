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
 * 前台投票控制器
 * @author ijry <ijry@qq.com>
 */
class UserDiggController extends HomeController{
    /**投票
     * @param $model   Digg模型标识ID
     * @param $type  Digg类别 good bad mark
     * @param $doc_id    文档内容ID
     * @author jry <598821125@qq.com>
     */
    public function digg($model, $type, $doc_id){
        $uid = $this->login();
        $map['type'] = $model;
        $map['doc_id'] = $doc_id;
        $digg_info = D('UserDigg')->where($map)->find();
        if(!$digg_info){ //创建Digg记录
            $data['model'] = $model;
            $data['doc_id'] = $doc_id;
            $data[$type] = $uid;
            $status = "yes";
            $count = sizeof($digg);
            $ret = D('UserDigg')->add($data);
        }else{
            if(!$digg_info[$type]){
                $count = 1;
                M(D('Type')->getTypeNameById($model))->where(array('id'=> (int)$doc_id))->setField($type, $count);
                $map['type'] = $model;
                $map['doc_id'] = $doc_id;
                $data[$type] = $uid;
                $ret = D('UserDigg')->where($map)->save($data);
                $status = "yes";
            }else{
                $digg = explode(',', $digg_info[$type]);
                $key  = array_search($uid, $digg); //是否已经Digg过，返回key
                if($key !== NULL && $key !== false){ //取消Digg
                    unset($digg[$key]);
                    $status = "no";
                }else{
                    $digg[] = (string)$uid;
                    $status = "yes";
                }
                $count = sizeof($digg);
                M(D('Type')->getTypeNameById($model))->where(array('id' => (int)$doc_id))->setField($type, $count);
                $map['type'] = $model;
                $map['doc_id'] = $doc_id;
                $data[$type] = trim(implode(',', array_values(array_unique($digg))), ',');
                $ret = D('UserDigg')->where($map)->save($data);
            }
        }
        if($ret){
            $this->success($status . "." . $count);
        }else{
            $this->error("投票出错");
        }
    }

    /**获取投票信息
     * @param $model   Digg模型标识ID
     * @param $type  Digg类别 good bad mark
     * @param $doc_id    文档内容ID
     * @author jry <598821125@qq.com>
     */
    public function getDiggStatus($model, $type, $doc_id){
        $map['type'] = $model;
        $map['doc_id'] = $doc_id;
        $digg = D('UserDigg')->where($map)->getField($type);
        $digg_info = explode(',', $digg);
        $result = in_array(session('user_auth.uid'), $digg_info);
        if($result){
            $this->success();
        }
    }
}
