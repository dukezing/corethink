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
 * 后台菜单控制器
 * @author jry <598821125@qq.com>
 */
class SystemMenuController extends AdminController{
    /**
     * 菜单列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        //搜索
        $keyword = (string)I('keyword');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition, '_multi'=>true); //搜索条件

        //获取所有菜单
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('SystemMenu')->where($map)->order('sort asc, id asc')->select();

        //转换成树状列表
        $tree = new \Org\Util\Tree();
        $data_list = $tree->toFormatTree($data_list);

        //使用Builder快速建立列表页面。
        $builder = new \Admin\Builder\AdminListBuilder();
        $builder->title('菜单列表')  //设置页面标题
                ->AddNewButton()    //添加新增按钮
                ->addResumeButton() //添加启用按钮
                ->addForbidButton() //添加禁用按钮
                ->addDeleteButton() //添加删除按钮
                ->setSearch('请输入ID/菜单名称', U('index'))
                ->addField('id', 'ID', 'text')
                ->addField('title_show', '标题', 'text')
                ->addField('url', '链接', 'text')
                ->addField('icon', '图标', 'icon')
                ->addField('sort', '排序', 'text')
                ->addField('status', '状态', 'status')
                ->addField('right_button', '操作', 'btn')
                ->dataList($data_list)    //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->display();
    }

    /**
     * 新增菜单
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Menu = D('SystemMenu');
            $data = $Menu->create();
            if($data){
                $id = $Menu->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($Menu->getError());
            }
        }else{
            $all_menu = D('Tree')->toFormatTree(D('SystemMenu')->getAllMenu());
            $all_menu = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级菜单')), $all_menu);
            $this->assign('all_menu', $all_menu);
            $this->meta_title = '新增菜单';
            $this->display('edit');
        }
    }

    /**
     * 编辑菜单
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $Menu = D('SystemMenu');
            $data = $Menu->create();
            if($data){
                if($Menu->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($Menu->getError());
            }
        }else{
            $info = D('SystemMenu')->getMenuById($id);
            $all_menu = D('Tree')->toFormatTree(D('SystemMenu')->getAllMenu());
            $all_menu = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级菜单')), $all_menu);
            $this->assign('all_menu', $all_menu);
            $this->assign('info', $info);
            $this->meta_title = '编辑菜单';
            $this->display();
        }
    }
}
