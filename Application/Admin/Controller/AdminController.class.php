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
 * 为什么要继承AdminController？
 * 因为AdminController的初始化函数中读取了顶部导航栏和左侧的菜单，
 * 如果不继承的话，只能复制AdminController中的代码来读取导航栏和左侧的菜单。
 * 这样做会导致一个问题就是当AdminController被官方修改后AdminController不会同步更新，从而导致错误。
 * 所以综合考虑还是继承比较好。
 * @author jry <598821125@qq.com>
 */
class AdminController extends Controller{
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize(){
        //登录检测
        if(!is_login()){ //还没登录跳转到登录页面
            $this->redirect('Public/login');
        }

        //权限检测
        if(!D('UserGroup')->checkAuth()){
            $this->error('权限不足！');
        }

        //读取数据库中的配置
        $config = S('DB_CONFIG_DATA');
        if(!$config){
            //获取所有系统配置
            $config = D('SystemConfig')->lists();

            //后台无模板主题
            $config['DEFAULT_THEME'] = '';

            //模板相关配置
            $config['TMPL_PARSE_STRING']['__PUBLIC__'] = __ROOT__.'/Public';
            $config['TMPL_PARSE_STRING']['__IMG__'] = __ROOT__.'/Application/Admin/View/Public/img';
            $config['TMPL_PARSE_STRING']['__CSS__'] = __ROOT__.'/Application/Admin/View/Public/css';
            $config['TMPL_PARSE_STRING']['__JS__']  = __ROOT__.'/Application/Admin/View/Public/js';

            //缓存配置
            S('DB_CONFIG_DATA', $config, 3600);
        }
        C($config); //添加配置

        //获取系统菜单导航
        $map['status'] = array('eq', 1);
        if(!C('DEVELOP_MODE')){ //是否开启开发者模式
            $map['dev'] = array('neq', 1);
        }
        $tree = new \Common\Util\Tree();
        $all_admin_menu_list = $tree->list_to_tree(D('SystemMenu')->where($map)->select()); //所有系统菜单

        //设置数组key为菜单ID
        foreach($all_admin_menu_list as $key => $val){
            $all_menu_list[$val['id']] = $val;
        }

        //获取功能模块的后台菜单列表
        $moule_list = D('StoreModule')->where(array('status' => 1))->select(); //获取所有安装并启用的功能模块
        $all_module_menu_list = array();
        foreach($moule_list as $key => $val){
            $menu_list_item = $tree->list_to_tree(json_decode($val['admin_menu'], true));
            $all_module_menu_list[] = $menu_list_item[0];
        }

        //设置数组key为菜单ID
        foreach($all_module_menu_list as &$menu){
            $new_all_module_menu_list[$menu['id']] = $menu;
        }

        //合并系统核心菜单与功能模块菜单
        if($new_all_module_menu_list){
            $all_menu_list += $new_all_module_menu_list;
        }

        $current_menu = D('SystemMenu')->getCurrentMenu(); //当前菜单
        $parent_menu = D('SystemMenu')->getParentMenu($current_menu['id']); //获取面包屑导航
        foreach($parent_menu as $key => $val){
            $parent_menu_id[] = $val['id'];
        }
        $current_root_menu = D('SystemMenu')->getRootMenuById($current_menu['id']); //当前菜单的顶级菜单
        $side_menu_list = $all_menu_list[$current_root_menu['id']]['_child']; //左侧菜单

        $this->assign('__ALL_MENU_LIST__', $all_menu_list); //所有菜单
        $this->assign('__SIDE_MENU_LIST__', $side_menu_list); //左侧菜单
        $this->assign('__PARENT_MENU__', $parent_menu); //当前菜单的所有父级菜单
        $this->assign('__PARENT_MENU_ID__', $parent_menu_id); //当前菜单的所有父级菜单的ID
        $this->assign('__CURRENT_ROOTMENU__', $current_root_menu['id']); //当前主菜单
        $this->assign('__USER__', session('user_auth')); //用户登录信息
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
        //特殊情况处理
        switch($model){
            case 'User':
                if(in_array(1, $ids, true) || 1 == $ids)
                    $this->error('不允许更改超级管理员状态');
                break;
            case 'UserGroup':
                if(in_array(1, $ids, true) || 1 == $ids)
                    $this->error('不允许更改超级管理员组状态');
                break;
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
