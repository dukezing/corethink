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
class MenuController extends AdminController{
    /**
     * 菜单列表
     * @author jry <598821125@qq.com>
     */
    public function index($pid = null){
        $all_menu = D('Common/Tree')->toFormatTree(D('menu')->getAllMenu());
        $this->assign('volist', $this->int_to_icon($all_menu));
        $this->assign('meta_title', "菜单列表");
        $this->display();
    }

    /**
     * 新增菜单
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Menu = D('Menu');
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
            $all_menu = D('Common/Tree')->toFormatTree(D('Menu')->getAllMenu());
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
            $Menu = D('Menu');
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
            $info = D('Menu')->getMenuById($id);
            $all_menu = D('Common/Tree')->toFormatTree(D('Menu')->getAllMenu());
            $all_menu = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级菜单')), $all_menu);
            $this->assign('all_menu', $all_menu);
            $this->assign('info', $info);
            $this->meta_title = '编辑菜单';
            $this->display();
        }
    }
}
