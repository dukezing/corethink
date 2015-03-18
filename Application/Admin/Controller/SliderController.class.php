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
 * 幻灯片控制器
 * @author jry <598821125@qq.com>
 */
class SliderController extends AdminController{
    /**
     * 幻灯片列表
     * @author jry <598821125@qq.com>
     */
    public function index($group = 1){
        $map['group'] = array('eq', $group);
        $all_slider = D('Slider')->getAllSlider($map);
        $this->assign('volist', $this->int_to_icon($all_slider));
        $this->assign('slider_group_list', C('SLIDER_GROUP_LIST'));
        $this->assign('group', $group);
        $this->assign('meta_title', "幻灯片列表");
        $this->display();
    }

    /**
     * 新增幻灯片
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Slider = D('Slider');
            $data = $Slider->create();
            if($data){
                $id = $Slider->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($Slider->getError());
            }
        }else{
            $this->assign('slider_group_list', C('SLIDER_GROUP_LIST'));
            $info['group'] = I('get.group');
            $this->assign('info', $info);
            $this->meta_title = '新增幻灯片';
            $this->display('edit');
        }
    }

    /**
     * 编辑幻灯片
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $Slider = D('Slider');
            $data = $Slider->create();
            if($data){
                if($Slider->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($Slider->getError());
            }
        }else{
            $this->assign('info', D('Slider')->getSliderById($id));
            $this->assign('slider_group_list', C('SLIDER_GROUP_LIST'));
            $this->meta_title = '编辑幻灯片';
            $this->display();
        }
    }
}
