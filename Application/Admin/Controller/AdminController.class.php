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
 * 这样做会导致一个问题就是当AdminController被官方修改后AdminBuilder不会同步更新，从而导致错误。
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
            $config = D('SystemConfig')->lists();
            $config['DEFAULT_THEME'] = ''; //后台无模板主题
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置

        //获取菜单导航
        $map['status'] = array('eq', 1);
        if(!C('DEVELOP_MODE')){ //是否开启开发者模式
            $map['dev'] = array('neq', 1);
        }
        $all_menu = D('Tree')->list_to_tree(D('SystemMenu')->where($map)->select($map, $status="1")); //所有菜单
        foreach($all_menu as $key => $val){
            $all_menu_list[$val['id']] = $val;
        }
        $current_menu = D('SystemMenu')->getMenuByControllerAndAction(); //当前菜单
        $parent_menu = D('SystemMenu')->getParentMenu($current_menu['id']);
        foreach($parent_menu as $key => $val){
            $parent_menu_id[] = $val['id'];
        }
        $current_root_menu = D('SystemMenu')->getRootMenuById($current_menu['id']); //当前菜单的顶级菜单

        $this->assign('__ALLMENULIST__', $all_menu_list); //所有菜单
        $this->assign('__PARENT_MENU__', $parent_menu); //所有父级菜单
        $this->assign('__PARENT_MENU_ID__', $parent_menu_id); //所有父级菜单的ID
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
            case 'Group':
                if(in_array(1, $ids, true) || 1 == $ids)
                    $this->error('不允许更改超级管理员组状态');
                break;
        }
        $map['id'] = array('in',$ids);
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
    public function selectListAsTree($model, $map = null){
        //获取列表
        $map['status'] = array('eq', 1);
        $list = D($model)->where($map)->select();

        //转换成树状列表
        $tree = new \Common\Util\Tree();
        $list = $tree->toFormatTree($list);

        //转换成一维数组
        foreach($list as $val){
            $result[$val['id']] = $val['title_show'];
        }
        return $result;
    }

    /**
     * 通用分页列表数据集获取方法
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @return array|false 返回数据集
     * @author jry <598821125@qq.com>
     */
    protected function lists($model, $where=array(), $order='', $field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('ADMIN_PAGE_ROWS') > 0 ? C('ADMIN_PAGE_ROWS') : 10;
        }
        $page = new \Common\Util\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        return $model->field($field)->select();
    }

    /**
     * select返回的数组进行整数映射转换
     * @param array $map  映射关系二维数组  array('字段名1'=>array(映射关系数组), '字段名2'=>array(映射关系数组), ...)
     * @return array  array(array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常', ...))
     * @author jry <598821125@qq.com>
     */
    function int_to_icon(&$data, $map = array('status' => array(
                            1  => '<i class="icon-ok" style="color:green"></i>',
                            -1 => '<i class="icon-trash" style="color:red"></i>',
                            0  => '<i class="icon-ban-circle" style="color:red"></i>'))) {
        if($data === false || $data === null ){
            return $data;
        }
        $data = (array)$data;
        foreach ($data as $key => $row){
            foreach ($map as $col=>$pair){
                if(isset($row[$col]) && isset($pair[$row[$col]])){
                    $data[$key][$col.'_text'] = $pair[$row[$col]];
                }
            }
        }
        return $data;
    }
}
