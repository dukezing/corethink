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
 * 导航链接控制器
 * @author jry <598821125@qq.com>
 */
class NavlinkController extends AdminController{
    /**
     * 导航链接列表
     * @author jry <598821125@qq.com>
     */
    public function index($pid = null){
        $all_navlink = D('Common/Tree')->toFormatTree(D('Navlink')->getAllNavlink());
        $this->assign('volist', $this->int_to_icon($all_navlink));
        $this->assign('meta_title', "导航链接列表");
        $this->display();
    }

    /**
     * 新增导航链接
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Navlink = D('Navlink');
            $data = $Navlink->create();
            if($data){
                $id = $Navlink->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($Navlink->getError());
            }
        }else{
            $all_navlink = D('Common/Tree')->toFormatTree(D('Navlink')->getAllNavlink($map));
            $all_navlink = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级导航链接')), $all_navlink);
            $this->assign('all_navlink', $all_navlink);
            $this->assign('all_model', D('Model')->getAllModel(array('id' => array('in', '1,2'))));
            $this->meta_title = '新增导航链接';
            $this->display('edit');
        }
    }

    /**
     * 编辑导航链接
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $Navlink = D('Navlink');
            $data = $Navlink->create();
            if($data){
                if($Navlink->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($Navlink->getError());
            }
        }else{
            $info = D('Navlink')->getNavlinkById($id);
            $all_navlink = D('Common/Tree')->toFormatTree(D('Navlink')->getAllNavlink($map));
            $all_navlink = array_merge(array(0 => array('id'=>0, 'title_show'=>'顶级导航链接')), $all_navlink);
            $this->assign('all_navlink', $all_navlink);
            $this->assign('all_model', D('Model')->getAllModel(array('id' => array('in', '1,2'))));
            $this->assign('info', $info);
            $this->meta_title = '编辑导航链接';
            $this->display();
        }
    }
}
