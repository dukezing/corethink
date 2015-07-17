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
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 * @author jry <598821125@qq.com>
 */
class HomeController extends Controller{
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize(){
        //读取数据库中的配置
        $config = S('DB_CONFIG_DATA');
        if(!$config){
            $config = D('SystemConfig')->lists();
            //模板相关配置
            $config['TMPL_PARSE_STRING']['__PUBLIC__'] = __ROOT__.'/Public';
            $config['TMPL_PARSE_STRING']['__IMG__'] = __ROOT__.'/Application/Home/View/'.$config['DEFAULT_THEME'].'/Public/img';
            $config['TMPL_PARSE_STRING']['__CSS__'] = __ROOT__.'/Application/Home/View/'.$config['DEFAULT_THEME'].'/Public/css';
            $config['TMPL_PARSE_STRING']['__JS__']  = __ROOT__.'/Application/Home/View/'.$config['DEFAULT_THEME'].'/Public/js';
            $config['HOME_PAGE'] = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__;
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置

        if(!C('TOGGLE_WEB_SITE')){
            $this->error('站点已经关闭，请稍后访问~');
        }

        $this->assign('meta_keywords', C('WEB_SITE_KEYWORD'));
        $this->assign('meta_description', C('WEB_SITE_DESCRIPTION'));
        $this->assign('__USER__', session('user_auth')); //用户登录信息
        $table_list = array_flip(C('TABLE_LIST')); //交换数组的键值
        $this->assign('__CURRENT_TABLE_ID__', $table_list[CONTROLLER_NAME]); //根据当前控制器及配置数组获取评论数据表ID
    }

    /**
     * 用户登录检测
     * @author jry <598821125@qq.com>
     */
    protected function is_login(){
        //用户登录检测
        $uid = is_login();
        if($uid){
            return $uid;
        }else{
            $data['login'] = 1;
            $this->error('请先登陆', U('User/login'), $data);
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids    = I('request.ids');
        $status = I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        $model_primary_key = D($model)->getPk();
        $map[$model_primary_key] = array('in',$ids);
        switch($status){
            case 'forbid'  : //禁用条目
                $data = array('status' => 0);
                $this->editRow($model, $data, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                break;
            case 'resume'  : //启用条目
                $data = array('status' => 1);
                $this->editRow($model, $data, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            case 'delete'  : //删除条目
                $result = D($model)->where($map)->delete();
                if($result){
                    $this->success('删除成功，不可恢复！');
                }else{
                    $this->error('删除失败');
                }
                break;
            case 'recycle' : //移动至回收站
                $data['status'] = -1;
                $this->editRow($model, $data, $map, array('success'=>'成功移至回收站','error'=>'删除失败'));
                break;
            case 'restore' : //从回收站还原
                $data = array('status' => 1);
                $map  = array_merge(array('status' => -1), $map);
                $this->editRow($model, $data, $map, array('success'=>'恢复成功','error'=>'恢复失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $map   查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                      url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * @author jry <598821125@qq.com>
     */
    final protected function editRow($model, $data, $map, $msg){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        //如存在id字段，则加入该条件
        $fields = M($model)->getDbFields();
        if(in_array('id',$fields) && !empty($id)){
            $where = array_merge(array('id' => array('in', $id )) ,(array)$where);
        }
        $msg = array_merge(array('success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg);
        if(M($model)->where($map)->save($data) !== false){
            $this->success($msg['success'], $msg['url'], $msg['ajax']);
        }else{
            $this->error($msg['error'], $msg['url'], $msg['ajax']);
        }
    }

    /**
     * 获取所有数据并转换成一维数组
     * @author jry <598821125@qq.com>
     */
    public function selectListAsTree($model, $map = null, $extra = null){
        //获取列表
        $map['status'] = array('eq', 1);
        $list = D($model)->where($map)->select();

        //转换成树状列表
        $tree = new \Common\Util\Tree();
        $list = $tree->toFormatTree($list);

        if($extra){
            $result[0] = $extra;
        }

        //转换成一维数组
        foreach($list as $val){
            $result[$val['id']] = $val['title_show'];
        }
        return $result;
    }
}
